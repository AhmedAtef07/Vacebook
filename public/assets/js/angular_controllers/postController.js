angular.module('app').controller('postController', function($rootScope, $scope, $http,
  $stateParams, $sce) {

  console.log($stateParams.postId);
  if (!$rootScope.user) {
    var req = {
      method: 'GET',
      url: 'users/getUserInfo',
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
        $rootScope.user = response.data.user;
        $rootScope.visitedUser = $rootScope.user;
        initializePusher();
      }
    }, function error(response) {
      console.log("Coudn't get user info!");
    });
  }

  $rootScope.visitedUser = $rootScope.user;
  update();

  function update() {
    var req = {
      method: 'GET',
      url: 'posts/getPost/' + $stateParams.postId,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };

    $http(req).then(function success(response) {
      // console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.userId = response.data.user_id;
        $scope.post = response.data.post;
        $scope.link = true;
        $scope.post.comment = '';
        console.log($scope.post);
      }
    }, function error(response) {
      console.log("Coudn't get posts!");
    });
  }

});
