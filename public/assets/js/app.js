////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Angular /////////////////////////////////
//////////////////////////////////////////////////////////////////////////

angular.module('app', ['ui.router', 'ngSanitize', 'emojiApp', 'ngFileUpload'])
.filter('html',function($sce){
    return function(input){
        return $sce.trustAsHtml(input);
    };
})

////////////////////////////////////////////////////////////////////////////
/////////////////////////// CONFIGURING ROUTES ////////////////////////////
//////////////////////////////////////////////////////////////////////////

.config(function($stateProvider, $urlRouterProvider) {

  $urlRouterProvider.otherwise('/home');
  $stateProvider
    .state('friends', {
      url: '/friends',
      templateUrl: 'partials/friends.html',
      controller: 'friendsController'
    })
    .state('home', {
      url: '/home',
      templateUrl: 'partials/home.html',
      controller: 'homeController'
    })
    .state('profile', {
      url: '/people/:userId',
      templateUrl: 'partials/profile.html',
      controller: 'userPorfileController'
    })
    .state('requests', {
      url: '/requests',
      templateUrl: 'partials/requests.html',
      controller: 'requestsController'
    })
    .state('notifications', {
      url: '/notifications',
      templateUrl: 'partials/notifications.html',
      controller: 'notificationsController'
    })
    .state('post', {
      url: '/post/:postId',
      templateUrl: 'partials/post.html',
      controller: 'postController'
    });

})

////////////////////////////////////////////////////////////////////////////
/////////////////////////// Define Directives /////////////////////////////
//////////////////////////////////////////////////////////////////////////

.directive('suggestedFriend', function() {
  return {
    templateUrl: 'directives/suggestion_friend_block.html'
  };
})
.directive('post', function() {
  return {
    templateUrl: 'directives/post.html',
    controller: 'postBlockController'
  };
})
.directive('newPost', function() {
  return {
    templateUrl: 'directives/new_post.html',
    controller: 'newPostBlockController'
  };
})
.directive('comment', function() {
  return {
    templateUrl: 'directives/comment.html'
  };
})
.directive('notification', function() {
  return {
    templateUrl: 'directives/notification.html'
  };
})
.directive('friend', function() {
  return {
    templateUrl: 'directives/friend_block.html'
  };
});
