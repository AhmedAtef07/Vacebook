angular.module('app').controller('friendsController', function($rootScope, $scope, $http) {

  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $rootScope.user = response.user;
        $rootScope.visitedUser = $rootScope.user;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });
  }

  $rootScope.visitedUser = $rootScope.user;
  update();


  function update() {
    $http.get('home/getFriends').
      success(function(response, status, headers, config) {
        console.log(response);
        $scope.userId = response.user_id;
        $scope.friends = response.friends;
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }

});
