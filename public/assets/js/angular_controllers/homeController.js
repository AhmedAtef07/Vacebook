angular.module('app').controller('homeController', function($rootScope, $scope, $http) {

  if (!$rootScope.user) {
    var req = {
      method: 'GET',
      url: 'home/getUserInfo',
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
      }
    }, function error(response) {
      console.log("Coudn't get user info!");
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
    var req = {
      method: 'GET',
      url: 'home/getPosts',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    console.log(req);
    $http(req).then(function success(response) {
      // console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.userId = response.data.user_id;
        $scope.posts = response.data.posts;
        $scope.posts.forEach(function(entry) {
          entry.comment = '';
        });
        console.log($scope.posts);
      }
    }, function error(response) {
      console.log("Coudn't get posts!");
    });
  }

});
