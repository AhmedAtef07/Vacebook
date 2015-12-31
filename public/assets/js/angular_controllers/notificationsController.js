angular.module('app').controller('notificationsController', function($rootScope, $scope, $http) {

  $rootScope.visitedUser = $rootScope.user;

  requestNotifications();

  function requestNotifications() {
    var req = {
      method: 'GET',
      url: 'posts/getNotifications',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        if (response.data.succeeded) {
          $scope.notifications = response.data.notifications;
        }
      }
    }, function error(response) {
      console.log("Coudn't get friends!");
    });
  }

});
