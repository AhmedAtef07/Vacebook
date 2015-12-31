angular.module('app').controller('newPostBlockController', function($rootScope, $scope, $http, $sce) {

  $scope.caption = '';
  $scope.privacy = 'Private';

  $scope.addPost = function () {
    console.log($scope.caption);
    if ($scope.privacy == 'Private') {
      $scope.is_private = 1;
    } else {
      $scope.is_private = 0;
    }
    var req = {
      method: 'POST',
      url: 'posts/addNewPost',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        caption: $scope.caption,
        is_private: $scope.is_private
      }
    };
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        // window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.caption = '';
        $scope.posts.push(response.data.post);
        console.log($scope.posts.length);
      }
    }, function error(response) {
      console.log("Coudn't post for some strange reason!");
    });
  };

  $scope.togglePrivacy = function () {
    console.log($scope.caption);
    if ($scope.privacy == 'Private') {
      $scope.privacy = 'Public';
    } else {
      $scope.privacy = 'Private';
    }
  };
});
