angular.module('app').controller('postBlockController', function($rootScope, $scope, $http, $sce) {


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
          $scope.post.comments.forEach(function(commentElement, commentIndex){
            if(commentElement.id == comment.id){
                $scope.post.comments.splice(commentIndex, 1);
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

});
