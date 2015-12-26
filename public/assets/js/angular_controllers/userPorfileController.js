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
      alert("Coudn't post for some strange reason!");
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
      alert("Coudn't post for some strange reason!");
    });
  };


  function update() {
    $http.get('home/getUserInfo/' + $stateParams.userId).
      success(function(response, status, headers, config) {
        console.log(response);
        if (!response.signed) {
          window.location.href = '/vacebook/public/homepage.html';
        } else {
          $rootScope.visitedUser = response.user;
          if (!$stateParams.userId) {
            $rootScope.user = response.user;
          }
        }
      }).
      error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      }).
      then(function() {
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
  }

});
