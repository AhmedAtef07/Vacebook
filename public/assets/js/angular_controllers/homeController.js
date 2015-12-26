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
  update();

  $scope.deleteComment = function(commentId) {
    console.log('commentId: ' + commentId);
    var req = {
      method: 'POST',
      url: 'home/deleteComment',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        comment_id: commentId
      }
    };

    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        update();
      }
    }, function error(response) {
      console.log("Coudn't post for some strange reason!");
    });

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
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        update();
      }
    }, function error(response) {
      console.log("Coudn't post for some strange reason!");
    });
  };

  $scope.keyPressed = function(event, postId, commentCaption) {
    if (event.keyCode == 13) {
      console.log(postId);
      $scope.addComment(postId, commentCaption);
    }
  };


  function update() {
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
  }

});
