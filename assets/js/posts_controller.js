
var posts = angular.module('posts',[]);

posts.controller('posts_control',function($scope, $http, $interval){


  $http.get('user_posts.php').
    success(function(response, status, headers, config) {
      console.log(response);
      if (!response.signed) {
        window.location.href = "index.html";
      } else {
        $scope.userId = response.user_id;
        $scope.posts = response.posts;
      }
    }).
    error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });

  $interval(function(){
    load_pictures();
  },5000);
  function load_pictures(){
    $http.get('user_posts.php').
      success(function(response, status, headers, config) {
        // console.log(response);
        if (!response.signed) {
          window.location.href = "index.html";
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

  // $http({
  // method: 'GET',
  // url: 'friends.php'
  // }).then(function successCallback(response) {
  //   console.log(response);
  //   $scope.friends = response;
  // }, function errorCallback(response) {
  // });

});
