
var register_login = angular.module('register_login',[]);

register_login.controller('register_login_control',function($scope, $http){

  $http.get('php/check_login.php').
    success(function(response, status, headers, config) {
      console.log(response);
      if (response.signed) {
        window.location.href = "user_profile.html";
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });

});
