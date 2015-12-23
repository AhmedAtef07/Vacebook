function runOnce() {
  $('.circle-image').each(function() {
    $(this).css({
      height: $(this).width() + 'px'
    });
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