angular.module('app').controller('appController', ['$rootScope', '$scope', '$http', 'Upload', function
  ($rootScope,$scope, $http, Upload) {
  // app.controller('MyCtrl', ['$scope', 'Upload', function ($rootScope, $scope, $http, Upload) {

  loadUserInfo();
  initLiveCounters();

  $scope.$on('$viewContentLoaded', function(){
    initImages();
  });


  function initLiveCounters() {
    var req = {
      method: 'GET',
      url: 'posts/getRequstsCount',
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
        $rootScope.friendRequestsCount = response.data.count;
      }
    }, function error(response) {
      console.log("Coudn't get user info!");
    });

    var req = {
      method: 'GET',
      url: 'posts/getNotificationsCount',
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
        $rootScope.notificationRequestsCount = response.data.count;
      }
    }, function error(response) {
      console.log("Coudn't get user info!");
    });
  }


  function loadUserInfo() {
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
      console.log(req);
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
  }


  function initializePusher() {
    if (!$rootScope.pusher) {
      $rootScope.pusher = new Pusher('f17087409b6bc1746d6e');
      console.log(''+$rootScope.user.id);
      $rootScope.notificationsChannel = $rootScope.pusher.subscribe(''+$rootScope.user.id);

      $rootScope.notificationsChannel.bind('new_notification', function(notificationMSG) {
        initLiveCounters();
        var message = notificationMSG;
        console.log((message));
        (function() {
          $scope.notificationUI = new NotificationFx({
            message :
            '<p><i class="fa fa-comments-o lg"></i> ' +
              notificationMSG.full_name + ' ' + notificationMSG.action +
              ' a post you are following. Go <a href="#/post/'
               + notificationMSG.post_id + '"> check it out </a> now.' +
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

      $rootScope.notificationsChannel.bind('new_friend', function(notificationMSG) {
        initLiveCounters();
        var message = notificationMSG;
        console.log((message));
        (function() {
          setTimeout( function() {
            $scope.notification = new NotificationFx({
              message : '<div class="ns-thumb"><img src="' + notificationMSG.profile_pic + '"' +
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

  function initImages() {
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

  $scope.upload = function (file) {
    Upload.upload({
        url: 'users/uploadProfilePic',
        data: {file: file}
    }).then(function (resp) {
        // console.log(resp.config.data);
        console.log(resp.data);
        if (resp.data.path && resp.data.succeeded) {
          $rootScope.visitedUser.profile_pic = resp.data.path;
        }
        // console.log('Success ' + resp.config.data.file.name + 'uploaded. Response: ' + resp.data);
    }, function (resp) {
        console.log('Error status: ' + resp.status);
    }, function (evt) {
        var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
        // console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
    });
  };

  $scope.uploadPost = function (file, caption) {
    console.log(caption);
    Upload.upload({
        url: 'posts/uploadPostPic/' + caption + '/' + '1',
        data: {
          file: file
        }
    }).then(function (resp) {
        // console.log(resp.config.data);
        console.log(resp.data);
        // console.log('Success ' + resp.config.data.file.name + 'uploaded. Response: ' + resp.data);
    }, function (resp) {
        console.log('Error status: ' + resp.status);
    }, function (evt) {
        var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
        console.log('progress: ' + progressPercentage + '% ' + evt.config.data.file.name);
    });
  };

  $scope.removePic = function () {
    console.log("Remove Pic");
    var req = {
      method: 'GET',
      url: 'users/removProfilePic',
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
      }
    }, function error(response) {
      console.log("Coudn't remove Pic!");
    });
  };

  $scope.logout = function () {
    console.log("Logout");
    var req = {
      method: 'GET',
      url: 'users/logout',
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
      }
    }, function error(response) {
      console.log("Coudn't remove Pic!");
    });
  };

}]);
