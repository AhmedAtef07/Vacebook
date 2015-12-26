angular.module('app').controller('userPorfileController', function($rootScope, $scope, $http, $stateParams, $sce) {

  console.log($stateParams.userId);
  update();




  function update() {
    $http.get('home/getUserInfo/' + $stateParams.userId).
      success(function(response, status, headers, config) {
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
