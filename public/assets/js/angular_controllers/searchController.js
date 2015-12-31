angular.module('app').controller('searchController', function($rootScope, $scope, $http, $sce) {

  $scope.searchText = "";
  $scope.keyPressed = function () {
    if ($scope.searchText.length > 2) {
      console.log($scope.caption);
      var req = {
        method: 'POST',
        url: 'search/searchByText/' + $scope.searchText,
        header: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        data: {
          caption: $scope.caption
        }
      };
      console.log(req);
      $http(req).then(function success(response) {
        console.log(response.data);
        if (!response.data.signed) {
          // window.location.href = '/vacebook/public/homepage.html';
        } else {
          $scope.hints = response.data.hints;
          console.log($scope.hints);
        }
      }, function error(response) {
        console.log("Coudn't get hints for some strange reason!");
      });
    } else {
      $scope.hints = [];
    }
  };

  $scope.clearSearch = function () {
    $scope.searchText = "";
    $scope.hints = [];
  };

});
