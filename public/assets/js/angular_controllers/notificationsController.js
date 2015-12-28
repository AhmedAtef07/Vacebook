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
    console.log(req);
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
  initImages();


  function initImages () {
    console.log("In");
    $(function () {
      $('.circle-image-fh').each(function() {
        $(this).css({
          width: $(this).height() + 'px'
        });
      });
    });
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
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.userId = response.data.user_id;
        $scope.friends = response.data.friends;
      }
    }, function error(response) {
      console.log("Coudn't get friends!");
    });
  }

});
