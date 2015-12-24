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
.controller('home_controller', ['$scope', function($scope) {
  $scope.customer = {
    name: 'Naomi',
    address: '1600 Amphitheatre'
  };
}])
.directive('suggestedFriend', function() {
  return {
    templateUrl: 'directories/suggestion_friend_block.html'
  };
})
.directive('post', function() {
  return {
    templateUrl: 'directories/post.html'
  };
})
.directive('friend', function() {
  return {
    templateUrl: 'directories/friend_block.html'
  };
});
