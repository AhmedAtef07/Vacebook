angular.module('app').controller('newPostBlockController', function($rootScope, $scope, $http, $sce) {

  $scope.caption = '';

  $scope.addPost = function () {
    console.log($scope.caption);
    var req = {
      method: 'POST',
      url: 'posts/addNewPost',
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
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.caption = '';
      }
    }, function error(response) {
      console.log("Coudn't post for some strange reason!");
    });
  };

});
