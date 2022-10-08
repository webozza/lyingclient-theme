// Checks for placeholder image
let isDummy =
  $("#client-experience-details img").attr("src").indexOf("no.png") > -1;

// Checks for client experience slug
let isClientExp = window.location.href.indexOf("/?experience_slug") > -1;

if (isDummy && isClientExp) {
  $(".attachment_div").hide();
}
