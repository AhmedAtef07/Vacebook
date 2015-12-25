////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Angular /////////////////////////////////
//////////////////////////////////////////////////////////////////////////

angular.module('app', ['ui.router'])


.config(function($stateProvider, $urlRouterProvider) {

  $urlRouterProvider.otherwise("/home");
  $stateProvider
    .state('friends', {
      url: "/friends",
      templateUrl: "partials/friends.html",
      controller: "friendsController"
      // template: "<h1>Hello</h1>"
    })
    .state('profile', {
      url: "/profile",
      templateUrl: "partials/profile.html"
    })
    .state('home', {
      url: "/home",
      templateUrl: "partials/home.html",
      controller: "postsController"
    });
})


.controller('friendsController', function($rootScope, $scope, $http, $interval) {

  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
      success(function(response, status, headers, config) {
        console.log(response);
        if (!response.signed) {
          window.location.href = "/vacebook/public/homepage.html";
        } else {
          $rootScope.user = response.user;
        }
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }
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

  $interval(function() {
    // load_friends();
  }, 5000);

  function load_friends() {
    $http.get('home/getFriends')
      .success(function(response, status, headers, config) {
        $scope.userId = response.user_id;
        $scope.friends = response.friends;
      })
      .error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }
})
.controller('newPostController', function($rootScope, $scope, $http) {

  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
      success(function(response, status, headers, config) {
        console.log(response);
        if (!response.signed) {
          window.location.href = "/vacebook/public/homepage.html";
        } else {
          $rootScope.user = response.user;
        }
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }
  $scope.caption = "";

  $scope.addPost = function () {
    console.log($scope.caption);
    var req = {
      method: 'POST',
      url: 'home/add',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        caption: $scope.caption
      }
    };

    console.log(req);

    $http(req).then(function success(response) {
      $scope.caption = "";
      console.log(response);
    }, function error(response) {
      alert("Coudn't post for some strange reason!");
    });
  };
})
.controller('postsController', function($rootScope, $scope, $http, $interval) {
  $http.get('home/getUserInfo').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = "/vacebook/public/homepage.html";
      } else {
        $rootScope.user = response.user;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });

  $http.get('home/getPosts').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = "/vacebook/public/homepage.html";
      } else {
        $scope.userId = response.user_id;
        $scope.posts = response.posts;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });

    $scope.deletePost = function(commentId){
      console.log(commentId);
    };

  $interval(function(){
    // load_posts();
  },5000);

  function load_posts(){
    $http.get('home/getUserInfo').
      success(function(response, status, headers, config) {
        // console.log(response);
        if (!response.signed) {
          window.location.href = "/vacebook/public/homepage.html";
        } else {
          $scope.userId = response.user_id;
          $scope.posts = response.posts;
        }
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }
})


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
