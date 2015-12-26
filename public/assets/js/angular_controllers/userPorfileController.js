angular.module('app').controller('userPorfileController', function($rootScope, $scope, $http,
    $stateParams, $sce) {

  console.log($stateParams.userId);
  update();

  if (!$rootScope.user) {
    $http.get('home/getUserInfo').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $rootScope.user = response.user;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });
  }

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
    console.log(req);
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
    console.log(req);
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
    console.log(req);
    $http(req).then(function success(response) {
      console.log(response.data);
      if (response.data.signed) {
        $rootScope.visitedUser.state = '';
      }
    }, function error(response) {
      alert("Coudn't Delete!");
    });
  };


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
    console.log(req);
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
