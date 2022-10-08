// Checks for placeholder image
const isDummyPlaceholder =
  $("#client-experience-details img").attr("src").indexOf("no.png") > -1;

// Checks for client experience slug
const isClientSlug = window.location.href.indexOf("/?experience_slug") > -1;

let hideIfDummy = (isDummyPlaceholder, isClientSlug) => {
  isDummyPlaceholder == true;
  isClientSlug == true;
  $(".attachment_div").hide();
};
hideIfDummy();
