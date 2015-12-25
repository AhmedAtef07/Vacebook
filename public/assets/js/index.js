$(document).ready(function() {

  $.fn.serializeObject = function()
  {
      var o = {};
      var a = this.serializeArray();
      $.each(a, function() {
          if (o[this.name] !== undefined) {
              if (!o[this.name].push) {
                  o[this.name] = [o[this.name]];
              }
              o[this.name].push(this.value || '');
          } else {
              o[this.name] = this.value || '';
          }
      });
      return o;
  };

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
    var jsonData = JSON.stringify($('#registration_form').serializeObject());
    if (checkRegistration()) {
      console.log(formData);
      console.log(jsonData);
      $.ajax({
        url: "home/register",
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        dataType: "JSON",
        data: jsonData,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.registered) {
            window.location.href = "/vacebook/public";
          }
        },
      });
    }
  });

  $(".login_form").submit(function(event) {
    event.preventDefault();
    var formData = $("#login_form").serializeArray();
    var jsonDate = JSON.stringify($('#login_form').serializeObject());
    if (checkLogin()) {
      console.log("login");
      console.log(formData);
      console.log(jsonDate);
      $.ajax({
        url: "home/login",
        type: "POST",
        contentType: "application/x-www-form-urlencoded",
        dataType: "JSON",
        data: jsonDate,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.logined) {
            window.location.href = "/vacebook/public";
          }
        },
      });
    }
  });

  $("#mock_data").click(function() {
    $.ajax({
      url: "php/mock_data.php",
      type: "GET",
      dataType: "JSON",
      success: function(response) {
        console.log(response);
        if (response.succeeded && response.logined) {
          window.location.href = "/vacebook/public";
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
