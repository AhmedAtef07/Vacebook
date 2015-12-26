////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Angular /////////////////////////////////
//////////////////////////////////////////////////////////////////////////

angular.module('app', ['ui.router', 'ngSanitize', 'emojiApp'])
.filter('html',function($sce){
    return function(input){
        return $sce.trustAsHtml(input);
    };
})

////////////////////////////////////////////////////////////////////////////
/////////////////////////// CONFIGURING ROUTES //////////////////////////
//////////////////////////////////////////////////////////////////////////

.config(function($stateProvider, $urlRouterProvider) {

  $urlRouterProvider.otherwise('/home');
  $stateProvider
    .state('friends', {
      url: '/friends',
      templateUrl: 'partials/friends.html',
      controller: 'friendsController'
      // template: '<h1>Hello</h1>'
    })
    // .state('profile', {
    //   url: '/profile',
    //   templateUrl: 'partials/profile.html',
    //   controller: 'userPorfileController'
    // })
    .state('home', {
      url: '/home',
      templateUrl: 'partials/home.html',
      controller: 'postsController'
    })
    .state('profile', {
        url: '/people/:userId',
        templateUrl: 'partials/profile.html',
        controller: 'userPorfileController'
      });
})

////////////////////////////////////////////////////////////////////////////
/////////////////////////// DEFINING CONTROLLERS //////////////////////////
//////////////////////////////////////////////////////////////////////////

.controller('friendsController', function($rootScope, $scope, $http) {
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
  $http.get('home/getFriends')
    .success(function(response, status, headers, config) {
      // console.log(response);
      $scope.userId = response.user_id;
      $scope.friends = response.friends;
      console.log($scope.friends.length);
    })
    .error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });
})

.controller('newPostController', function($rootScope, $scope, $http, $sce) {
  $scope.caption = '';

  $scope.addPost = function () {
    console.log($scope.caption);
    var req = {
      method: 'POST',
      url: 'home/addNewPost',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        caption: $scope.caption
      }
    };

    console.log(req);
    $http(req).then(function success(response) {
      $scope.caption = '';
      console.log(response);
    }, function error(response) {
      alert("Coudn't post for some strange reason!");
    });
  };
})

.controller('postsController', function($rootScope, $scope, $http) {
  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
      success(function(response, status, headers, config) {
        console.log(response);
        if (!response.signed) {
          // window.location.href = '/vacebook/public/homepage.html';
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
      console.log(response);
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
    // console.log($scope.caption);
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
    // console.log(req);
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
})

.controller('userPorfileController', function($rootScope, $scope, $http, $stateParams, $sce) {
  console.log($stateParams.userId);
  $http.get('home/getUserInfo/' + $stateParams.userId)
    .success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $rootScope.visitedUser = response.user;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    })
  .then(function() {
    $http.get('home/getUserPostswithComments/' + $rootScope.visitedUser.id)
      .success(function(response, status, headers, config) {
        $scope.posts = response.posts;
        // console.log("##", response.posts);
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }, function() {
    console.log('Error while getting user info');
  });

  $scope.keyPressed = function(event){
    console.log($scope.caption);
  };

  $scope.deletePost = function(postId){
    console.log(postId);
  };
})

////////////////////////////////////////////////////////////////////////////
/////////////////////////// DEFINING DIRECTIVES ///////////////////////////
//////////////////////////////////////////////////////////////////////////

.directive('suggestedFriend', function() {
  return {
    templateUrl: 'directives/suggestion_friend_block.html'
  };
})
.directive('post', function() {
  return {
    templateUrl: 'directives/post.html'
  };
})
.directive('newPost', function() {
  return {
    templateUrl: 'directives/new_post.html',
    controller: 'newPostController'
  };
})
.directive('comment', function() {
  return {
    templateUrl: 'directives/comment.html'
  };
})
.directive('friend', function() {
  return {
    templateUrl: 'directives/friend_block.html'
  };
});
