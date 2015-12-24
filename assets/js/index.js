$(document).ready(function() {


  function checkRegistration() {
    var firstName = $('.signup #first_name').val();
    var lastName = $('.signup #last_name').val();
    var username = $('.signup #username').val();
    var email = $('.signup #email').val();
    var password = $('.signup #password').val();
    var gender = $('.signup #gender').val();
    var birthdate = $('.signup #birthdate').val();
    var username_regex = /^[\w.-]*$/;
    var email_regex = /\S+@\S+\.\S+/;
    if (firstName === null || firstName.trim().length === 0) {
      alert("Please Enter Valid First Name");
      return false;
    } else if (lastName === null || lastName.trim().length === 0) {
      alert("Please Enter Valid Last Name");
      return false;
    } else if (username === null || username.trim().length === 0 ||
        !username_regex.test(username.trim())) {
      alert("Please Enter Valid Username");
      return false;
    } else if (password === null || password.length === 0) {
      alert("Please Enter Valid Password");
      return false;
    } else if (email === null || email.trim().length === 0 || !email_regex.test(email)) {
      alert("Please Enter Valid Email");
      return false;
    } else if (gender === null || gender.trim().length === 0) {
      alert("Please Choose Gander");
      return false;
    } else if (birthdate === null) {
      alert("Please Enter Your Birthdate");
      return false;
    }
    return true;
  }

  function checkLogin() {
    var email_or_username = $('.login #email_or_username').val();
    var password = $('.login #password').val();
    var username_or_email_regex = /^[\w.-]*$|\S+@\S+\.\S+/;
    if (email_or_username === null || email_or_username.trim().length === 0 ||
    (!username_or_email_regex.test(email_or_username))) {
      alert("Please Enter Valid Email or Username");
      return false;
    } else if (password === null || password.length === 0) {
      alert("Please Enter Valid Password");
      return false;
    }
    return true;
  }

  $(".registration_form").submit(function(event) {
    event.preventDefault();
    var formData = $("#registration_form").serializeArray();
    if (checkRegistration()) {
      console.log(formData);
      $.ajax({
        url: "php/register.php",
        type: "POST",
        dataType: "JSON",
        data: formData,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.registered) {
            window.location.href = "/vacebook";
          } else {
            response.errors.forEach(function(element) {
              alert(element);
            });
          }
        },
      });
    }
  });

  $(".login_form").submit(function(event) {
    event.preventDefault();
    var formData = $("#login_form").serializeArray();
    if (checkLogin()) {
      console.log("login");
      console.log(formData);
      $.ajax({
        url: "php/login.php",
        type: "POST",
        dataType: "JSON",
        data: formData,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.logined) {
            window.location.href = "/vacebook";
          } else {
            response.messages.forEach(function(element) {
              console.log(element);
            });
            response.errors.forEach(function(element) {
              console.log(element);
            });
          }
        },
      });
    }
  });

  $('.form').find('input, textarea').on('keyup blur focus', function (e) {
    var $this = $(this),
        label = $this.prev('label');
        if (e.type === 'keyup') {
          if ($this.val() === '') {
            label.removeClass('active highlight');
        } else {
          label.addClass('active highlight');
        }
      } else if (e.type === 'blur') {
        if( $this.val() === '' ) {
          label.removeClass('active highlight');
        } else {
          label.removeClass('highlight');
        }
      } else if (e.type === 'focus') {

        if( $this.val() === '' ) {
          label.removeClass('highlight');
        }
        else if( $this.val() !== '' ) {
          label.addClass('highlight');
        }
      }
  });

  $('.tab a').on('click', function (e) {
    e.preventDefault();
    $(this).parent().addClass('active');
    $(this).parent().siblings().removeClass('active');
    target = $(this).attr('href');
    $('.tab-content > div').not(target).hide();
    $(target).fadeIn(600);
  });


});
