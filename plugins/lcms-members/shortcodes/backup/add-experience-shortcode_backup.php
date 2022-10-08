<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


//CODE ADDED BY SHAKIL

function dbase_add_experience_page_scripts_styles()
{

	wp_enqueue_script('add_experience_page_script', plugin_dir_url(__FILE__) . 'assets/js/add_experience_page.js', array('jquery'), time());


	wp_enqueue_style('add_experience_page_style', plugin_dir_url(__FILE__) . 'assets/css/add_experience_page.css', array(), time());

}


add_action('wp_print_styles', 'dbase_add_experience_page_scripts_styles');
add_action('wp_print_scripts', 'dbase_add_experience_page_scripts_styles');


/*
 * ADD CLIENT EXPERIENCE - shortcode
 */

add_shortcode('dbase_add_experience_page', 'dbase_add_experience_page_shortcode');
function dbase_add_experience_page_shortcode($atts)
{
	wp_print_scripts();
	wp_print_styles();

	$a = shortcode_atts(array(
		//'category' => 'uncategorized',
		//'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	), $atts);

	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');


	/** Check if user logged in, otherwise return a message **/
	$login_status = dbase_check_if_logged_in();
	if (isset($login_status['login_status']) && $login_status['login_status'] == false) {
		return $login_status['login_status_message'];
	} else {
		$current_user_id = $login_status['login_user_id'];
	}


	/** Check MEMBERSHIP LEVEL **/
	$message = '<p>' . __('You do not have the correct membership to add a client experience.', 'd-base-geodirectory-custom-functions') . '</p>';
	$membership_level = dbase_check_membership_level($message);	// custom function in shortcode-header-include.php
	//var_dump($membership_level); //exit;
	//if ( $membership_level[1] == 'other' ) { return $membership_level[0]; }	// return message and exit shortcode



	global $wpdb, $post;


	/** Handling the DB CRUD **/
	//$wpdb->show_errors();	// enable only for debugging

	// UPDATE the details to DB
	if ($_POST['submit_button'] && $_POST['submit_button'] == 'save_experience') {
		// remove the submit field from array
		unset($_POST['submit_button']);


		// Add the 'posts' table record with SQL first - see this: https://developer.wordpress.org/reference/classes/wpdb/update/
		$my_post = array(
			'post_title'    => wp_strip_all_tags($_POST['exp_add_client_name']),
			'post_status'   => 'pending',
			'post_author'   => $current_user_id,
			'post_type'   => 'gd_place'
		);

		// Insert the post into the post table and get the new post ID or error
		$new_post_id = wp_insert_post($my_post, $wp_error);

		// Error checking on write to DB
		if (!is_wp_error($new_post_id)) {
			//the post is valid
		} else {
			//there was an error in the post insertion, 
			return $new_post_id->get_error_message();
		}

		$attachment_url = array();
		if (isset($_FILES['exp_add_client_attachement'])) {
			if (is_array($_FILES['exp_add_client_attachement']['tmp_name'])) {
				$exp_add_client_attachment_tmp_name = $_FILES['exp_add_client_attachement']['tmp_name'];

				foreach ($exp_add_client_attachment_tmp_name as $exp_add_client_attachment_tmp_name_data) {
					if (!empty($exp_add_client_attachment_tmp_name_data)) {
						//Image Upload Functions
						if (!function_exists('wp_generate_attachment_metadata')) {
							require_once(ABSPATH . '/wp-admin/includes/image.php');
							require_once(ABSPATH . '/wp-admin/includes/file.php');
							require_once(ABSPATH . '/wp-admin/includes/media.php');
						}

						$postId = $new_post_id;
						$image = $exp_add_client_attachment_tmp_name_data;
						$directory = "/" . date('Y') . "/" . date('m') . "/";
						$wp_upload_dir = wp_upload_dir();
						// $data = base64_decode($image);
						$filename = "IMG_" . time() . ".png";
						//$fileurl = $wp_upload_dir['url'] . '/' . basename( $filename );
						$fileurl = ABSPATH  . "/wp-content/uploads" . $directory . $filename;

						$filetype = wp_check_filetype(basename($fileurl), null);

						move_uploaded_file($image, $fileurl);

						$attachment = array(
							'guid' => $wp_upload_dir['url'] . '/' . basename($fileurl),
							'post_mime_type' => $filetype['type'],
							'post_title' => preg_replace('/\.[^.]+$/', '', basename($fileurl)),
							'post_content' => '',
							'post_status' => 'inherit'
						);

						//  print_r($attachment);
						//echo "<br>file name :  $fileurl";
						$attach_id = wp_insert_attachment($attachment, $fileurl, $postId);
						require_once(ABSPATH  . '/wp-admin/includes/image.php');

						// Generate the metadata for the attachment, and update the database record.
						$attach_data = wp_generate_attachment_metadata($attach_id, $fileurl);
						wp_update_attachment_metadata($attach_id, $attach_data);

						array_push($attachment_url, $wp_upload_dir['url'] . '/' . basename($fileurl));
					} else {
						$attachment_url = array(plugin_dir_url(__FILE__) . 'assets/no.png');
					}
				}
				// 
			} else {

				$attachment_url = array(plugin_dir_url(__FILE__) . 'assets/no.png');
			}
		} else {
			$attachment_url = array(plugin_dir_url(__FILE__) . 'assets/no.png');
		}
		// var_dump($attachment_url);

		// var_dump($_FILES['exp_add_client_attachement']['tmp_name']);



		// update the detail record with SQL - see this: https://developer.wordpress.org/reference/classes/wpdb/update/

		$sqlexecute = $wpdb->update(
			$wpdb->prefix . 'geodir_gd_place_detail',	// table name
			array(
				//'member_name'				=> 	stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_name'] ) ),
				//'business_name'			=> 	stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_business_name'] ) ),
				//'website'					=> 	stripslashes_deep(esc_url_raw( $_POST['exp_edit_member_website'] ) ),
				//'post_title'				=> 	stripslashes_deep(sanitize_text_field( $_POST['exp_edit_client_name'] ) ),
				'default_category'			=> 	stripslashes_deep(sanitize_text_field($_POST['exp_add_client_category'])),
				'clients_phone_number'		=> 	stripslashes_deep(sanitize_text_field($_POST['exp_add_client_phone_number'])),
				'clients_email'				=> 	stripslashes_deep(sanitize_email($_POST['exp_add_client_email'])),
				'clients_zip_code'			=> 	stripslashes_deep(sanitize_text_field($_POST['exp_add_client_zipcode'])),
				'clients_experience'		=>	stripslashes_deep(sanitize_textarea_field($_POST['exp_add_client_experience'])),
				'clients_experience_rating' =>	stripslashes_deep(sanitize_text_field($_POST['exp_add_client_rating'])),
				'attachment' =>	json_encode($attachment_url),
				'post_status' => 'pending'
			),
			array('post_id' => $new_post_id),			// where
			array('%s', '%s', '%s', '%s', '%s', '%s')	// data format
		);

		// Display a message to user on successful adding of record
		// Send an email to admin, notifying of a user adding a new record
		if ($sqlexecute) {
			// Set user display message
			$_SESSION['user_message'] = 'Your client experience record has been added. Pending admin approval.';

			// Send notification email to admin on creation of new experience record
			$experience_author_user_data = wp_get_current_user($current_user_id);	// get the post_author user object (users table)

			// set required variables for passing to below send mail function
			$user_name = $experience_author_user_data->display_name;
			$user_email = $experience_author_user_data->user_email;
			$user_user_id = $current_user_id;
			$user_membership_level = $membership_level;
			$user_experience_text = stripslashes_deep(sanitize_textarea_field($_POST['exp_add_client_experience']));
			$user_experience_link = 'none yet';

			// Run send mail function
			dbase_send_email_admin_new_experience_approval(
				$new_post_id,
				$user_name,
				$user_email,
				$user_user_id,
				$user_membership_level,
				$user_experience_text,
			);
			// **** NOTE: WORK WITH THIS FORMAT FOR POST APPROVAL LINK IN BODY OF EMAIL:
			//<a href="'.esc_url(get_home_url()).'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.'</a>
		}
	}

	// READ the details from DB
	if (isset($new_post_id)) {
		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
			'
					SELECT DISTINCT *
					FROM ' . $tableprefix . 'geodir_gd_place_detail
					WHERE ' . $tableprefix . 'geodir_gd_place_detail.post_status = %s AND ' . $tableprefix . 'geodir_gd_place_detail.post_id = %d
				',
			'publish',
			$new_post_id
		);
		$client_experience = $wpdb->get_results($sql);
		//$wpdb->print_error();	// enable only for debugging
		//var_dump($client_experience); exit;
	}

	// Get the member categories
	$categories_results = dbase_get_categories();
	//echo '<pre>'; var_dump($categories_results);
	//exit();

	/** End: Handling the DB CRUD **/


	// DISPLAY the form

	$html = '';	// clear html variable before building it

	if (
		$membership_level == 'premium' ||
		$membership_level == 'plus' ||
		($membership_level[1] == 'other' && dbase_count_user_experience_records($current_user_id) == 0) ||
		($membership_level[1] == 'other' && dbase_count_user_experience_records($current_user_id) > 1)
	) {
		// Display add form ONLY if current user has certain membership level (above if conditions)
		$html = '
					<div id="my-client-experience-container" style="margin: 2rem 0;">
						<h1 style="font-size:2rem;color: var(--ast-global-color-2); font-weight: 500;">' . __('List a Client', 'd-base-geodirectory-custom-functions') . '</h1>

						<p style="margin-bottom:30px;">' . __('Add the details of your client experience in two simple steps.', 'd-base-geodirectory-custom-functions') . '</p>

<div class="client-input-error" style=""><p style="">Something Went Wrong!</p></div>';

		// Show any temporary session message to the user (ie. like when a recoerd is added)
		//$_SESSION['user_message'] = 'This is test message';
		if (isset($_SESSION['user_message'])) {
			$html .= '
				<div class="col-sm-12" style="padding:10px 20px;margin-bottom:20px;background-color:#adedad;">'
				. $_SESSION['user_message'] .
				'</div>
							';
			unset($_SESSION['user_message']);	// clear the message to user after it's displayed
		}

		$html .= '<!-- jQuery script for below Nav Tabs, next/prev button click event actions -->
						<script>
						// jQuery(document).ready(function($) {

							//     function FieldsChecker(stepTwo){
							//         const client_input_error = $(".client-input-error");
							//         const exp_add_client_name = jQuery(\'input[name="exp_add_client_name"]\').val();
							//         const exp_add_client_phone_number = jQuery(\'input[name="exp_add_client_phone_number"]\').val();
							//         const exp_add_client_email = jQuery(\'input[name="exp_add_client_email"]\').val();
							//         const exp_add_client_zipcode = jQuery(\'input[name="exp_add_client_zipcode"]\').val();
							
							//         var re = /\S+@\S+\.\S+/;
									
							
							//         if(exp_add_client_name != "" && exp_add_client_email != "" && re.test(exp_add_client_email) && exp_add_client_zipcode != "" && !isNaN(exp_add_client_zipcode) ){
							//             const nextTabLinkEl = $(".nav-tabs .active").closest("li").next("li").find("a")[0];
							//             const nextTab = new bootstrap.Tab(nextTabLinkEl);
							//             nextTab.show();
										
							//             if(client_input_error.hasClass("active")){
							//                 client_input_error.removeClass("active");
							//             }
							
							//             if(stepTwo){
							//                 $("ul.nav-tabs .step-two-prevent a").attr("style", "pointer-events: auto")
							//             }
							//         }else{
							
							
							//             if(exp_add_client_name == ""){
							//                 client_input_error.html("<p>Client Name Field Is Missing! Try again</p>");
							//             }else if(exp_add_client_email == ""){
							//                     client_input_error.html("<p>Client Email Field Is Missing! Try again</p>");
							//             }else if(!re.test(exp_add_client_email)){
							//                 client_input_error.html("<p>Please Enter Valid E-mail! Try again</p>");
							//             }else if(exp_add_client_zipcode == ""){
							//                 client_input_error.html("<p>Client zip Code Field Is Missing! Try again</p>");
							//             }else if(isNaN(exp_add_client_zipcode)){
							//                 client_input_error.html("<p>Please Enter valid Client Zip Code! Try again</p>");
							//             }else{
							//                 client_input_error.html("<p>Something Went! Try again</p>");
							//             }
							//             client_input_error.addClass("active");
							//             client_input_error.focus();
							
							//             setTimeout(()=>{
							//                 client_input_error.removeClass("active");
							//             },10000)
								
							//         }
							//     }
							//     $(".btnNext").click(function() {
							//         FieldsChecker();
							//     });
							
							//     $("ul.nav-tabs .step-two-prevent").click((e)=>{
									
							//         $("ul.nav-tabs .step-two-prevent a").attr("style", "pointer-events: none");
							//         FieldsChecker(true);
							//     });
								
							//     $(".btnPrevious").click(function() {
							//         const prevTabLinkEl = $(".nav-tabs .active").closest("li").prev("li").find("a")[0];
							//         const prevTab = new bootstrap.Tab(prevTabLinkEl);
							//         prevTab.show();
							//     });
							// });
						</script>

						<!-- Nav tabs -->
						<ul class="nav nav-tabs">
						  <li class="nav-item">
							<a class="nav-link active" data-bs-toggle="tab" href="#client-details">
								Step 1
								<i class="fas fa-arrow-right ml-3" aria-hidden="true"></i>
							</a>
						  </li>
						  <li class="nav-item step-two-prevent">
							<a class="nav-link" data-bs-toggle="tab" href="#experience">
								Step 2
								<i class="fas fa-arrow-right ml-3" aria-hidden="true"></i>
							</a>
						  </li>
						  <!--<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#add tab pane id here">
								Step 3
							</a>
						  </li>-->
						</ul>
						
						<!-- Tab panes -->
						<form method="post" enctype="multipart/form-data">

						<div class="tab-content">

							<!--tab pane -->
							<div class="tab-pane container active" id="client-details">
								<h2 style="font-size:1.3rem;" class="mt-4">' . __('Your Client\'s Details', 'd-base-geodirectory-custom-functions') . '</h2>
									<!--<div class="form-group row mt-2 mb-2">
										<label for="exp_add_client_category" class="col-sm-3 col-form-label" >' . __('Client Category', 'd-base-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
										<select class="col-sm-4" name="exp_add_client_category" id="exp_add_client_category" required>
											<option value="0" selected disabled>' . __('Select', 'd-base-geodirectory-custom-functions') . '</option>';
		foreach ($categories_results as $category_row) {
			$html .= '<option value="' . $category_row->term_id . '">' . __($category_row->name, 'd-base-geodirectory-custom-functions') . '</option>';
		}
		$html .= '</select>
			</div>-->
									<div class="form-group row mt-2 mb-2">
										<label for="exp_add_client_name" class="col-sm-3 col-form-label" >' . __('Client\'s Name', 'd-base-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
										<input class="col-sm-4" type="text" name="exp_add_client_name" id="exp_add_client_name" class="form-control" required>
									</div>
									<div class="form-group row mt-2 mb-2">
										<label for="exp_add_client_phone_number" class="col-sm-3 col-form-label" >' . __('Client\'s Phone Number', 'd-base-geodirectory-custom-functions') . '</label>
											<input class="col-sm-4" type="text" name="exp_add_client_phone_number" id="exp_add_client_phone_number" class="form-control">
									</div>
									<div class="form-group row mt-2 mb-2">
										<label for="exp_add_client_email" class="col-sm-3 col-form-label" >' . __('Client\'s Email', 'd-base-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
										<input class="col-sm-4" type="email" name="exp_add_client_email" id="exp_add_client_email" class="form-control" required>
									</div>
									<div class="form-group row mt-2 mb-2">
										<label for="exp_add_client_zipcode" class="col-sm-3 col-form-label" >' . __('Client\'s Zip Code', 'd-base-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
										<input class="col-sm-4" type="text" name="exp_add_client_zipcode" id="exp_add_client_zipcode" class="form-control" required>
									</div>
									<a class="btn btn-primary btnNext mt-5">' . __('Next', 'd-base-geodirectory-custom-functions') . '</a>
							</div> <!--tab pane -->


							<!--tab pane -->
							<div class="tab-pane container fade" id="experience">
								<h2 style="font-size:1.3rem;" class="mt-4">' . __('Your Experience', 'd-base-geodirectory-custom-functions') . '</h2>
								<div class="form-group row mt-2 mb-2">
									<div class="col-md-8">
										<label for="exp_add_client_experience" class="col-form-label" >' . __('Describe your experience with the client', 'd-base-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
										<textarea class="" rows="8" name="exp_add_client_experience" maxlength="300" id="exp_add_client_experience" class="form-control" required></textarea>
									</div>
						
									<!-- the 5 star rating section  - ADD AND EDIT -->
									<!-- Note: see custom.css stylesheet for star review styling -->

									<div class="col-md-4">
										<h2 style="font-size:1.3rem;">Rate this experience</h2>
										<div class="star-wrapper">
										
											<input class="star-input" type="radio" id="exp_add_client_rating_1" name="exp_add_client_rating" value="5">
											<label class="star-label" for="exp_add_client_rating_1">&#9733;</label>

											<input class="star-input" type="radio" id="exp_add_client_rating_2" name="exp_add_client_rating" value="4">
											<label class="star-label" for="exp_add_client_rating_2">&#9733;</label>

											<input class="star-input" type="radio" id="exp_add_client_rating_3" name="exp_add_client_rating" value="3">
											<label class="star-label" for="exp_add_client_rating_3">&#9733;</label>

											<input class="star-input" type="radio" id="exp_add_client_rating_4" name="exp_add_client_rating" value="2">
											<label class="star-label" for="exp_add_client_rating_4">&#9733;</label>

											<input class="star-input" type="radio" id="exp_add_client_rating_5" name="exp_add_client_rating" value="1">
											<label class="star-label" for="exp_add_client_rating_5">&#9733;</label>

										</div>
									</div> <!-- end: the 5 star rating section -->';

		/* Add the media upload script */
		// function wk_enqueue_script()
		// {
		// 	//Enqueue media.
		// 	wp_enqueue_media();
		// 	// Enqueue custom js file.
		// 	wp_register_script('wk-admin-script', plugins_url(__FILE__), array('jquery'));
		// 	wp_enqueue_script('wk-admin-script');
		// }
		// add_action('admin_enqueue_scripts', 'wk_enqueue_script');
		// *** COMMON INCLUDES FOR THE SHORTCODES ***
		require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');


		// IF CATEGORY SUBMIT BUTTON WAS PRESSED, THEN GO TO RELEVANT CATEGORY PLACE PAGE
		// if ($_POST['submit_button_category'] && $_POST['place_categories']) {
		//     $redirect_url = esc_url(home_url('/premium-member-club-2/?category=' . sanitize_text_field($_POST['place_categories'])));
		//     wp_safe_redirect($redirect_url);
		//     exit;
		// }

		// Get the member categories
		$categories_results = dbase_get_categories();
		$html .= ' <!-- start: the categories section --><div class="col-md-12 my-4"><select name="exp_add_client_category" id="exp_add_client_category">';
		$html .= '  		<option value="0" selected disabled>' . __('Select', 'd-base-geodirectory-custom-functions') . '</option>';
		foreach ($categories_results as $category_row) {
			$html .= '  		<option value="' . $category_row->term_id . '">' . __($category_row->name, 'd-base-geodirectory-custom-functions') . '</option>';
		}
		$html .= '		</select></div> <!-- end: the categories section -->';


		$html .= '<!-- NEW FILE UPLOAD SECTION -->
		<div class="col-md-4 exp_add_client_attachement_div">
			<h2 style="font-size:1.3rem;" class="mt-4">' . __('Upload Attachment', 'd-base-geodirectory-custom-functions') . '</h2><p>(Optional)</p>
			
			<div class="exp_add_client_attachement_parent_div">
			 <img class="exp_add_client_attachement_parent_preview" height="250" width="250" style="border: 2px solid red; padding: 5px; margin: 5px"/>
				<input type="file" accept="image/png, image/jpeg" name="exp_add_client_attachement[]" id="exp_add_client_attachement" />
			</div>
		
		
		</div>

		<!-- New Upload File Button-->
		<a class="btn btn-primary exp_add_client_attachement_addon mt-5">' . __('Add New Attachment', 'd-base-geodirectory-custom-functions') . '</a>

								<!-- NEW FILE UPLOAD SECTION END-->
									
								</div>

								<a class="btn btn-primary btnPrevious mt-5">' . __('Back', 'd-base-geodirectory-custom-functions') . '</a>
								<p style="margin-top:30px;clear:left;"><button class="btn bsui btn-primary" name="submit_button" value="save_experience">' . __('Submit Client', 'd-base-geodirectory-custom-functions') . '</button></p>
							</div> <!--tab pane -->


						</div> <!-- tab content -->

						</form>

					</div>
					';
	} elseif ($membership_level[1] == 'other' && dbase_count_user_experience_records($current_user_id) == 1) {
		// If user is not a member (yet) and the free one-time client experience has already been entered by user
		$html = '
					<div style="margin: 2rem 0;">
						<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">' . __('List a Client', 'd-base-geodirectory-custom-functions') . '</h1>
						<p style="margin-top:15px;">' . __('Note: You have already used your one-time client experience.', 'd-base-geodirectory-custom-functions') . '</p>
					</div>
					';

		// Message to return to UPGRADE membership
		//$link = '<a href="'.get_site_url().'/membership-account/membership-checkout/?level=3">' . __( 'Upgrade to Plus or Premium for more features', 'geodirectory' ) . '</a>';	//Link to use to UPGRADE to PREMIUM
		$link = '<a href="' . esc_url(get_site_url() . '/membership') . '">' . __('Upgrade to Plus or Premium for more features!', 'geodirectory') . '</a>';	//Link to use to UPGRADE to PREMIUM
		$html .= '<p class="geodir_message_upgrade">' . $link . '</p>';
	} else {
		// Display a message if no experiences returned
		$html = '
					<div style="margin: 2rem 0;">
						<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">' . __('List a Client', 'd-base-geodirectory-custom-functions') . '</h1>
						<p style="margin-top:15px;">' . __('We have no result for this client experience entry. Contact the website administrator.', 'd-base-geodirectory-custom-functions') . '</p>
					</div>
					';
	}

	$wpdb->flush();	// clear the results cache

	//return "foo = {$a['foo']}";
	//return "<p>User count is {$user_count}</p>";
	return $html;
}
