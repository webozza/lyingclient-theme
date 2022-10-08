// Checks for placeholder image
const isDummy =
  $("#client-experience-details img").attr("src").indexOf("no.png") > -1;

// Checks for client experience slug
const isClientExp = window.location.href.indexOf("/?experience_slug") > -1;

if (isDummy && isClientExp) {
  $(".attachment_div").hide();
}
