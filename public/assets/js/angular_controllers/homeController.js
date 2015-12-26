angular.module('app').controller('homeController', function($rootScope, $scope, $http) {

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

  $http.get('home/getPosts').
  success(function(response, status, headers, config) {
    // console.log(response);
    if (!response.signed) {
      window.location.href = '/vacebook/public/homepage.html';
    } else {
      $scope.userId = response.user_id;
      $scope.posts = response.posts;
      $scope.posts.forEach(function(entry) {
        entry.comment = '';
      });
      console.log($scope.posts);
    }
  }).
  error(function(response, status, headers, config) {
    console.log(response);
    console.log(status);
  });

  $scope.deleteComment = function(commentId){
    console.log(commentId);
  };

  $scope.addComment = function (postId, commentCaption) {
    console.log(postId);
    var req = {
      method: 'POST',
      url: 'home/addNewComment',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        post_id: postId,
        caption: commentCaption
      }
    };

    $http(req).then(function success(response) {
      $scope.posts.forEach(function(entry) {
        if (entry.id == postId) {
          entry.comment = '';
        }
      });
      console.log(response);
    }, function error(response) {
      console.log("Coudn't post for some strange reason!");
    });
  };

  $scope.keyPressed = function(event, postId, commentCaption){
    // console.log(event.keyCode);
    if (event.keyCode == 13) {
      console.log(postId);
      $scope.addComment(postId, commentCaption);
    }
  };

});
