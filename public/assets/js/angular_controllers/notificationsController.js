angular.module('app').controller('notificationsController', function($rootScope, $scope, $http) {

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
  initImages();


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
      url: 'home/getNotifications',
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
        if (response.data.succeeded) {
          $scope.notifications = response.data.notifications;
        }
      }
    }, function error(response) {
      console.log("Coudn't get friends!");
    });
  }

});
