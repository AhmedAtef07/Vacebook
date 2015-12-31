angular.module('app').controller('friendsController', function($rootScope, $scope, $http) {


  $rootScope.visitedUser = $rootScope.user;
  update();
  
  function update() {
    var req = {
      method: 'GET',
      url: 'users/getFriends',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.userId = response.data.user_id;
        $scope.friends = response.data.friends;
      }
    }, function error(response) {
      console.log("Coudn't get friends!");
    });
  }

});
