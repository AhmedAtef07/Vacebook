function runOnce() {
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

  jQuery('img.svg').each(function(){
    var $img = jQuery(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgURL = $img.attr('src');

    jQuery.get(imgURL, function(data) {
      // Get the SVG tag, ignore the rest
      var $svg = jQuery(data).find('svg');

      // Add replaced image's ID to the new SVG
      if(typeof imgID !== 'undefined') {
        $svg = $svg.attr('id', imgID);
      }
      // Add replaced image's classes to the new SVG
      if(typeof imgClass !== 'undefined') {
        $svg = $svg.attr('class', imgClass+' replaced-svg');
      }

      // Remove any invalid XML tags as per http://validator.w3.org
      $svg = $svg.removeAttr('xmlns:a');

      // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
      if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
        $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'));
      }

      // Replace image with new SVG
      $img.replaceWith($svg);
    }, 'xml');
  });
}

function showInfoPoints(event) {
  $(".info-point").first().show("fast", function showNext() {
    $(this).next(".info-point").show("fast", showNext);
  });
}

function hideInfoPoints(event) {
  $(".info-point").last().hide("fast", function hidePrevious() {
    $(this).prev(".info-point").hide("fast", hidePrevious);
  });
}

function toggleInfoPoints(target) {
  if (target.innerHTML === "Show More") {
    showInfoPoints();
    target.innerHTML = "Show Less";
  } else {
    hideInfoPoints();
    target.innerHTML = "Show More";
  }
}


////////////////////////////////////////////////////////////////////////////
///////////////////////////////// Angular /////////////////////////////////
//////////////////////////////////////////////////////////////////////////



angular.module('app', ['ui.router'])

.config(function($stateProvider, $urlRouterProvider) {

  $urlRouterProvider.otherwise("/home");

  $stateProvider
    .state('friends', {
      url: "/friends",
      templateUrl: "partials/friends.html"
      // template: "<h1>Hello</h1>"
    })
    .state('profile', {
      url: "/profile",
      templateUrl: "partials/profile.html"
    })
    .state('home', {
      url: "/home",
      templateUrl: "partials/home.html"
    });
})

.controller('friendsController', function($scope, $http, $interval) {
  $http.get('php/friends.php')
    .success(function(response, status, headers, config) {
      // console.log(response);
      $scope.userId = response.user_id;
      $scope.friends = response.friends;
      console.log($scope.friends.length);
    })
    .error(function(response, status, headers, config) {
      console.log(response);
      console.log(status);
    });

  $interval(function() {
    load_friends();
  }, 5000);

  function load_friends() {
    $http.get('php/friends.php')
      .success(function(response, status, headers, config) {
        $scope.userId = response.user_id;
        $scope.friends = response.friends;
      })
      .error(function(response, status, headers, config) {
        console.log(response);
        console.log(status);
      });
  }
})

.controller('postsController', function($scope, $http, $interval) {
  $http.get('php/user_posts.php').
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
    load_posts();
  },5000);
  function load_posts(){
    $http.get('php/user_posts.php').
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
})

.directive('suggestedFriend', function() {
  return {
    templateUrl: 'directives/suggestion_friend_block.html'
  };
})
.directive('post', function() {
  return {
    templateUrl: 'directives/post.html'
  };
})
.directive('friend', function() {
  return {
    templateUrl: 'directives/friend_block.html'
  };
});
