angular.module('app').controller('userPorfileController', function($rootScope, $scope, $http,
    $stateParams, $sce) {

  console.log($stateParams.userId);

  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $rootScope.user = response.user;
        initializePusher();
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });
  }

  update();

  $rootScope.addFriend = function(friendId) {
    var req = {
      method: 'POST',
      url: 'home/addNewFriend',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
        requester_id: $rootScope.user.id,
        user_id: $rootScope.visitedUser.id
      }
    };
    // console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (response.data.signed) {
        $rootScope.visitedUser.state = 'waitingAccept';
      }
    }, function error(response) {
      alert("Coudn't add friend!");
    });
  };

  $rootScope.acceptFriendRequest = function(friendId) {
    var req = {
      method: 'POST',
      url: 'home/acceptFriendRequest/' + friendId,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    // console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (response.data.signed) {
        $rootScope.visitedUser.state = 'friend';
      }
    }, function error(response) {
      alert("Coudn't post for some strange reason!");
    });
  };

  $rootScope.removeRelation = function(friendId) {
    var req = {
      method: 'POST',
      url: 'home/removeRelation/' + friendId,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    // console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (response.data.signed) {
        $rootScope.visitedUser.state = 'none';
      }
    }, function error(response) {
      alert("Coudn't Delete!");
    });
  };

  $scope.$on('$viewContentLoaded', function(){
    initImages();
  });


  function initImages () {
    $(function () {
      $('.circle-image').each(function() {
        $(this).css({
          height: $(this).width() + 'px'
        });
      });
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

      $rootScope.notificationsChannel.bind('new_notification', function(notificationMSG){
        var message = notificationMSG;
        console.log((message));
        (function() {
          $scope.notificationUI = new NotificationFx({
            message :
            '<p><i class="fa fa-comments-o lg"></i> ' +
              notificationMSG.full_name + ' ' + notificationMSG.action + ' a post you are following. Go <a href="#/post/' + notificationMSG.post_id + '"> check it out </a> now.' +
            '</p>',
            layout : 'other',
            effect : 'boxspinner',
            ttl : 9000,
            type : 'notice', // notice, warning or error
            onClose : function() {
            }
          });
          $scope.notificationUI.show();
        })();
      });

      $rootScope.notificationsChannel.bind('new_friend', function(notificationMSG){
        var message = notificationMSG;
        console.log((message));
        (function() {
          setTimeout( function() {
            $scope.notification = new NotificationFx({
              message : '<div class="ns-thumb"><img src="assets/images/cat.jpg"' +
                'class="accept-image"/></div><div class="ns-content">' +
                  '<p><a href="#/people/'  + notificationMSG.user_id + '">' +
                  notificationMSG.full_name + '</a> ' + notificationMSG.action + '.</p>' +
                '</div>',
              layout : 'other',
              ttl : 7000,
              effect : 'thumbslider',
              type : 'notice', // notice, warning, error or success
              onClose : function() {
              }
            });
            $scope.notification.show();
          }, 500);
        })();
      });
    }
  }


  function update() {
    var req = {
      method: 'GET',
      url: 'home/getUserInfo/' + $stateParams.userId,
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };
    // console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $rootScope.visitedUser = response.data.user;
        if (!$stateParams.userId) {
          $rootScope.user = response.data.user;
        }
      }
    }, function error(response) {
      alert("Coudn't get user info!");
    }).
    then(function () {
      var req2 = {
        method: 'GET',
        url: 'home/getUserPostswithComments/' + $rootScope.visitedUser.id,
        header: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        data: {
        }
      };
      $http(req2).then(function success(response) {
        console.log(response.data);
        $scope.posts = response.data.posts;
      }, function error(response) {
        alert("Coudn't get posts with comments!");
      });
    });
  }

});
