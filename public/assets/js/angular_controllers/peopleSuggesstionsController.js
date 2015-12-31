angular.module('app').controller('peopleSuggesstionsController', function($rootScope, $scope, $http) {

  requestSuggestions();

  function requestSuggestions() {
    var req = {
      method: 'GET',
      url: 'users/peopleSuggesstionFromHometown',
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
          $scope.suggesstions = response.data.suggesstions;
        }
      }
    }, function error(response) {
      console.log("Coudn't fetch friend suggesstions!");
    });
  }

});
