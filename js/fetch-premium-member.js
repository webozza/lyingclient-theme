// MUTATION OBSERVER -- OBSERVE CATEGORY ID COMING FROM $_POST PHP METHOD

// FETCH PREMIUM MEMBERS
async function getUsers() {
  const catId = $("h1 strong").data("cat-id");
  const theUrl = "/wp-json/geodir/v2/places/?gd_placecategory=" + catId + "";
  let url = theUrl;
  try {
    let res = await fetch(url);
    return await res.json();
  } catch (error) {
    console.log(error);
  }
}

// RENDER PREMIUM MEMBERS
async function renderUsers() {
  let users = await getUsers();
  let html = "";
  users.forEach((user) => {
    if (user.website !== window.location.pathname) {
      let htmlSegment = `<div class="busiess_div_premium">
  
			  <div class="business_details_div_parent">
				  <div class="business_details_div_flex">
					  <div class="business_details_div">
						  <p class="business_name">${user.business_name}</p>
						  <p class="business_category">${$("h1 strong").text()}</p>
					  </div>
				  </div>
				  <div class="business_brand_div_parent">
					  <div class="business_brand_logo">
						  <img class="business_brand_logo_img" src="/wp-content/plugins/lcms-geodirectory-custom-functions/shortcodes/assets/lying_client_1.png">
					  </div>
				  </div>
			  </div>
			  <div class="business_contact_info_div">
				  <div class="business_contact_info_div_flex">
					  <div class="business_logo">
						  <img class="business_logo_img" src="/wp-content/plugins/lcms-geodirectory-custom-functions/shortcodes/assets/lying_client.png">
					  </div>
					  <div class="business_contact_info">
						  <p class="business_phone"></p>
						  <p class="business_location">${user.clients_zip_code} ${
        user.business_name
      }</p>
					  </div>
				  </div>
				  <div class="business_website_btn_div">
					  <a class="business_website_btn" href="${
              user.website
            }" target="_blank" rel="nofollow" >Visit Website</a>
					<p>${user.website}</p>
				  </div>
			  </div>
  
		  </div>`;

      html += htmlSegment;
    }
  });

  let container = document.querySelector(".premium-members-container");
  container.innerHTML = html;
}

// SORT PREMIUM MEMBERS ALPHABETICALLY
async function sortList() {
  await renderUsers();
  var list,
    i,
    switching,
    b,
    shouldSwitch,
    dir,
    switchcount = 0;
  list = $(".premium-members-container");
  switching = true;
  dir = "asc";
  while (switching) {
    switching = false;
    b = list[0].childNodes;
    for (i = 0; i < b.length - 1; i++) {
      shouldSwitch = false;
      if (dir == "asc") {
        if (b[i].innerHTML.toLowerCase() > b[i + 1].innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (b[i].innerHTML.toLowerCase() < b[i + 1].innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      b[i].parentNode.insertBefore(b[i + 1], b[i]);
      switching = true;
      switchcount++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

async function sortAnimation() {
  await sortList();
  $(".loader").css("display", "none");
  $(".premium-members-container").css("display", "flex");
  if ($(".premium-members-container")[0].childNodes.length === 0) {
    let noResultsMsg = `<p class="no-results-msg" style="text-align:center;">No results found. <a href="javascript:void(0)" onclick="history.back()">Please try again.</a></p>`;
    $("#premium-members-club").append(noResultsMsg);
    setTimeout(() => {
      $(".no-results-msg").trigger("click");
    }, 2000);
  }
}

setTimeout(function () {
  renderUsers();
  sortList();
  sortAnimation();
}, 1000);
