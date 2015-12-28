angular.module('app').controller('postController', function($rootScope, $scope, $http,
  $stateParams, $sce) {

  console.log($stateParams.postId);
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
        initializePusher();
      }
    }, function error(response) {
      console.log("Coudn't get user info!");
    });
  }

  $rootScope.visitedUser = $rootScope.user;
  update();

  var pusher = new Pusher('f17087409b6bc1746d6e');
  var notificationsChannel = pusher.subscribe('notifications');
  notificationsChannel.bind('new_notification', function(notification){
    var message = notification.comment;
    console.log(JSON.parse(message));
  });


  $scope.addComment = function (post) {
    console.log('comment ' + post.id);
    var req = {
      method: 'POST',
      url: 'home/addNewComment',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        post_id: post.id,
        caption: post.comment
      }
    };
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        // update();
        if (response.data.succeeded) {
          post.comments.push(response.data.comment);
          post.comment = '';
        }
      }
    }, function error(response) {
      console.log("Coudn't comment for some strange reason!");
    });
  };

  $scope.deleteComment = function(comment) {
    var req = {
      method: 'POST',
      url: 'home/deleteComment/' + comment.id,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        // update();
        if (response.data.succeeded) {
          $scope.posts.forEach(function(postElement, postIndex) {
            if (postElement.id == comment.post_id) {
              postElement.comments.forEach(function(commentElement, commentIndex){
                if(commentElement.id == comment.id){
                    postElement.comments.splice(commentIndex, 1);
                }
              });
            }
          });
        }
      }
    }, function error(response) {
      console.log("Coudn't delete comment for some strange reason!");
    });
  };

  $scope.keyPressed = function(event, post) {
    if (event.keyCode == 13) {
      console.log(post.id);
      $scope.addComment(post);
    }
  };

  $scope.likePost = function(post) {
    console.log('like ' + post.id);
    var req = {
      method: 'POST',
      url: 'home/addLike/' + post.id,
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
        // update();
        if (response.data.succeeded) {
          post.liked = true;
          post.likes.push(response.data.like);
        }
      }
    }, function error(response) {
      console.log("Coudn't like post for some strange reason!");
    });
  };

  $scope.unlikePost = function(post) {
    console.log('unlike ' + post.id);
    var req = {
      method: 'POST',
      url: 'home/deleteLike/' + post.id,
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
        // update();
        if (response.data.succeeded) {
          post.liked = false;
          post.likes.forEach(function(likeElement, likeIndex) {
            if (likeElement.user_id == $rootScope.user.id) {
              post.likes.splice(likeIndex, 1);
            }
          });
        }
      }
    }, function error(response) {
      console.log("Coudn't unlike post for some strange reason!");
    });
  };


  function initImages () {
    $(function () {
      $('.circle-image-fh').each(function() {
        $(this).css({
          width: $(this).height() + 'px'
        });
      });
    });
  }


  function initializePusher() {
    if (!$rootScope.pusher) {
      $rootScope.pusher = new Pusher('f17087409b6bc1746d6e');
      console.log(''+$rootScope.user.id);
      $rootScope.notificationsChannel = $rootScope.pusher.subscribe(''+$rootScope.user.id);
      $rootScope.notificationsChannel.bind('new_notification', function(notification){
        var message = notification;
        console.log((message));
      });
    }
  }

  function update() {
    var req = {
      method: 'GET',
      url: 'home/getPost/' + $stateParams.postId,
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
