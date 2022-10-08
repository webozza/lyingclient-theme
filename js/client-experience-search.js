setTimeout(function () {
  if (
    // ...checks for image src and slug in url
    $("#client-experience-details img").attr("src").indexOf("no.png") > -1 &&
    window.location.href.indexOf("/?experience_slug") > -1
  ) {
    $(".attachment_div").hide();
  } else {
    console.log("is false");
  }
}, 1000);
