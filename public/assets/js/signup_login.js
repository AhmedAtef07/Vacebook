
function checkRegistration() {
  var firstName = document.getElementById('register_first_name').value;
  var lastName = document.getElementById('register_last_name').value;
  var username = document.getElementById('register_username').value;
  var email = document.getElementById('register_email').value;
  var password = document.getElementById('register_password').value;
  var gender = document.getElementById('register_gender').value;
  var birthdate = document.getElementById('register_birthdate').value;
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
  var email_username = document.getElementById('login_email_username').value;
  var password = document.getElementById('login_password').value;
  var username_regex = /^[\w.-]*$/;
  var email_regex = /\S+@\S+\.\S+/;
  if (email_username === null || email_username.trim().length === 0 ||
      (!email_regex.test(email_username) && !username_regex.test(email_username))) {
    alert("Please Enter Valid Email or Username");
    return false;
  } else if (password === null || password.length === 0) {
    alert("Please Enter Valid Password");
    return false;
  }
  return true;
}

$(document).ready(function() {

  $("#registration_form").submit(function(event) {
    event.preventDefault();
    var formData = $("#registration_form").serializeArray();
    if (checkRegistration()) {
      console.log(formData);
      $.ajax({
        url: "register.php",
        type: "POST",
        dataType: "JSON",
        data: formData,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.registered) {
            window.location.href = "profile.php";
          } else {
            response.errors.forEach(function(element) {
              alert(element);
            });
          }
        },
      });
    }
  });

  $("#login_form").submit(function(event) {
    event.preventDefault();
    var formData = $("#login_form").serializeArray();
    if (checkLogin()) {
      console.log(formData);
      $.ajax({
        url: "login.php",
        type: "POST",
        dataType: "JSON",
        data: formData,
        success: function(response) {
          console.log(response);
          if (response.succeeded && response.logined) {
            window.location.href = "profile.php";
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

});
