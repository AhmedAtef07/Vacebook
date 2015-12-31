angular.module('app').controller('requestsController', function($rootScope, $scope, $http) {

  $rootScope.visitedUser = $rootScope.user;

  requestAwaitedUsers();
  requestPendingUsers();

  ///////////////////////// HTTP REQUST TO GET requestPendingUsers /////////////////////////
  function requestPendingUsers() {
    var req = {
      method: 'GET',
      url: 'users/pendingRequestsForCurrentUser',
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
          $scope.pendingRequests = response.data.requests;
        }
      }
    }, function error(response) {
      console.log("Coudn't get pending requests!");
    });
  }

  ///////////////////////// HTTP REQUST TO GET requestAwaitedUsers /////////////////////////
  function requestAwaitedUsers() {
    var req = {
      method: 'GET',
      url: 'users/awaitedRequestsForCurrentUser',
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
          $scope.awaitedRequests = response.data.requests;
        }
      }
    }, function error(response) {
      console.log("Coudn't get pending requests!");
    });
  }

});
