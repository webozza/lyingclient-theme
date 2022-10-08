setTimeout(function () {
  if (
    window.location.pathname.indexOf("client-experience") > -1 &&
    $("#client-experience-details img").attr("src").indexOf("no.png") > -1
  ) {
    $(".attachment_div").hide();
  } else {
    console.log("is false");
  }
}, 1000);
