angular.module('app').controller('homeController', function($rootScope, $scope, $http) {

  $rootScope.visitedUser = $rootScope.user;
  update();

  function update() {
    var req = {
      method: 'GET',
      url: 'posts/getPosts',
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      data: {
      }
    };

    $http(req).then(function success(response) {
      // console.log(response.data);
      if (!response.data.signed) {
        window.location.href = '/vacebook/public/homepage.html';
      } else {
        $scope.userId = response.data.user_id;
        $scope.posts = response.data.posts;
        $scope.posts.forEach(function(entry) {
          entry.comment = '';
        });
        console.log($scope.posts);
      }
    }, function error(response) {
      console.log("Coudn't get posts!");
    });
  }

});
