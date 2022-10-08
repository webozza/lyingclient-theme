<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

//CODE ADDED BY SHAKIL

function lcms_display_experience_page_scripts_styles()
{

	wp_enqueue_script('display_experience_page_script', plugin_dir_url(__FILE__) . 'assets/js/display_experience_page.js', array('jquery'), time());


	wp_enqueue_style('display_experience_page_style', plugin_dir_url(__FILE__) . 'assets/css/display_experience_page.css', array(), time());
}


add_action('wp_print_styles', 'lcms_display_experience_page_scripts_styles');
add_action('wp_print_scripts', 'lcms_display_experience_page_scripts_styles');

/*
 * DISPLAY CLIENT EXPERIENCE - shortcode
 */

add_shortcode('lcms_display_experience_page', 'lcms_display_experience_page_shortcode');
function lcms_display_experience_page_shortcode($atts)
{
	wp_print_styles();
	wp_print_scripts();

	$a = shortcode_atts(array(
		// 'category' => 'uncategorized',
		//'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	), $atts);


	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');


	/** Check if user logged in, otherwise return a message **/
	$login_status = lcms_check_if_logged_in();
	if (isset($login_status['login_status']) && $login_status['login_status'] == false) {
		return $login_status['login_status_message'];
	} else {
		$current_user_id = $login_status['login_user_id'];
	}


	/** Check MEMBERSHIP LEVEL **/
	// $message = '<p>' . __('You do not have the correct membership to display this client experience.', 'lcms-geodirectory-custom-functions') . '</p>';
	// $membership_level = lcms_check_membership_level($message);	// custom function in shortcode-header-include.php
	// var_dump($membership_level);
	// if ($membership_level[1] == 'other') {
		// return $membership_level[0];
	//}	// return message and exit shortcode


	/** If the URL parameter (post slug) is not passed, then return with message */
	if (!isset($_GET['experience_slug'])) {
		return
			'<div style="margin: 2rem 0;">' .
			'<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">' . __('Client Experience', 'lcms-geodirectory-custom-functions') . '</h1>' .
			'<p style="margin-top:15px;">' . __('No client experience was selected.', 'lcms-geodirectory-custom-functions') . '</p>' .
			'</div>';
	}

	$experience_slug = sanitize_key($_GET['experience_slug']);	// Get CPT value of 'gd_place' passed from display page


	/** START THE WEB PAGE BUILD PROCESS */

	global $wpdb, $post;

	/** GET the experience post ID from the passed url slug for the specific client experience record **/
	$theClientExperiencePost = get_posts(array(
		'post_type'   		=> 'gd_place',
		'name'  			=> $experience_slug,
		'numberposts' 		=> 1,
		//'posts_per_page'	=> 3,
		//'category_name'	=> $subject_slug,
		//'orderby'			=> 'title',
		//'order'			=> 'asc'
	));
	$experience_post_id = (int) $theClientExperiencePost[0]->ID;
	//echo 'This is the GD place detail post_id to use for DB search: '.$theClientExperiencePost[0]->ID;	//DEBUG ONLY
	// var_dump($theClientExperiencePost);


	/** Check if current user owns this experience record otherwise exit with message **/
	// READ the details from DB
	$tableprefix = $wpdb->prefix;
	$sql = $wpdb->prepare(
		'
				SELECT DISTINCT ' . $tableprefix . 'geodir_gd_place_detail.post_id, ' . $tableprefix . 'posts.ID, ' . $tableprefix . 'posts.post_author
				FROM ' . $tableprefix . 'geodir_gd_place_detail
				INNER JOIN ' . $tableprefix . 'posts ON ' . $tableprefix . 'geodir_gd_place_detail.post_id = ' . $tableprefix . 'posts.ID
				WHERE ' . $tableprefix . 'geodir_gd_place_detail.post_status = %s AND ' . $tableprefix . 'geodir_gd_place_detail.post_id = %d
			',
		'publish',
		$experience_post_id
	);
	$client_experience_check = $wpdb->get_results($sql);
	//var_dump($client_experience_check); exit;



	/** Handling the DB CRUD **/
	//$wpdb->show_errors();	// enable only for debugging



	// UPDATE the details to DB
	if ($_POST['submit_button'] && $_POST['submit_button'] == 'save_experience') {


		// $attachment_url = '';
		// if (isset($_FILES['exp_edit_client_attachement'])) {
		// 	if (!empty($_FILES['exp_edit_client_attachement']['tmp_name'])) {
		// 		//Image Upload Functions

		// 		if (!function_exists('wp_generate_attachment_metadata')) {
		// 			require_once(ABSPATH . '/wp-admin/includes/image.php');
		// 			require_once(ABSPATH . '/wp-admin/includes/file.php');
		// 			require_once(ABSPATH . '/wp-admin/includes/media.php');
		// 		}

		// 		$postId = $experience_post_id;
		// 		$image = $_FILES['exp_edit_client_attachement']['tmp_name'];
		// 		$directory = "/" . date('Y') . "/" . date('m') . "/";
		// 		$wp_upload_dir = wp_upload_dir();
		// 		// $data = base64_decode($image);
		// 		$filename = "IMG_" . time() . ".png";
		// 		//$fileurl = $wp_upload_dir['url'] . '/' . basename( $filename );
		// 		$fileurl = ABSPATH  . "/wp-content/uploads" . $directory . $filename;

		// 		$filetype = wp_check_filetype(basename($fileurl), null);

		// 		move_uploaded_file($image, $fileurl);

		// 		$attachment = array(
		// 			'guid' => $wp_upload_dir['url'] . '/' . basename($fileurl),
		// 			'post_mime_type' => $filetype['type'],
		// 			'post_title' => preg_replace('/\.[^.]+$/', '', basename($fileurl)),
		// 			'post_content' => '',
		// 			'post_status' => 'inherit'
		// 		);
		// 		//  print_r($attachment);
		// 		//echo "<br>file name :  $fileurl";
		// 		$attach_id = wp_insert_attachment($attachment, $fileurl, $postId);
		// 		require_once(ABSPATH  . '/wp-admin/includes/image.php');

		// 		// Generate the metadata for the attachment, and update the database record.
		// 		$attach_data = wp_generate_attachment_metadata($attach_id, $fileurl);
		// 		wp_update_attachment_metadata($attach_id, $attach_data);

		// 		$attachment_url = $wp_upload_dir['url'] . '/' . basename($fileurl);
		// 	}
		// }

		$attachment_url = '';

		if (isset($_POST['exp_edit_client_attachement_input'])) {
			$attachment_url = $_POST['exp_edit_client_attachement_input'];
		}


		// var_dump($attachment_url);
		// remove the submit field from array
		unset($_POST['submit_button']);


		$update_value_arr = array(
			//'member_name' => 				stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_name'] ) ),
			//'business_name' => 			stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_business_name'] ) ),
			//'website' => 					stripslashes_deep(esc_url_raw( $_POST['exp_edit_member_website'] ) ),
			'post_title' => 				stripslashes_deep(sanitize_text_field($_POST['exp_edit_client_name'])),
			'clients_phone_number' => 		stripslashes_deep(sanitize_text_field($_POST['exp_edit_client_phone_number'])),
			'clients_email' => 				stripslashes_deep(sanitize_email($_POST['exp_edit_client_email'])),
			'clients_zip_code' => 			stripslashes_deep(sanitize_text_field($_POST['exp_edit_client_zipcode'])),
			'clients_experience' =>			stripslashes_deep(sanitize_textarea_field($_POST['exp_edit_client_experience'])),
			'clients_experience_rating' =>	stripslashes_deep(sanitize_text_field($_POST['exp_edit_client_experience_rating'])),
			'attachment' =>	json_encode($attachment_url),
			'default_category' =>	stripslashes_deep(sanitize_text_field($_POST['exp_edit_client_category']))
		);

		// Update the record with SQL - see this: https://developer.wordpress.org/reference/classes/wpdb/update/
		$sqlexecute = $wpdb->update(
			$wpdb->prefix . 'geodir_gd_place_detail',	// table name
			$update_value_arr,
			array('post_id' => $experience_post_id),			// where
			array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')	// data format
		);

		// Display a message to user on success
		if ($sqlexecute !== FALSE) {
			$_SESSION['user_message'] = 'Your experience record has been updated';
		} else {
			$_SESSION['user_message'] = 'There was an error updating this client experience. Please contact admin.';
		}
	}

	// READ the experience details from Geodirectory table
	// Note: Members details that were entered on or prior to 21-3-2022 are used in the 'Client Experience' display page. After this date, the members details are read from WP user table (because GeoDirectiry plugin was used to record member details at the time) - discussed with Tamera.
	// So, reading the post date from posts table, for any conditional code below.
	if ($experience_post_id) {
		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
			'
						SELECT DISTINCT
							' . $tableprefix . 'posts.post_date,
							' . $tableprefix . 'geodir_gd_place_detail.*,
							' . $tableprefix . 'terms.name AS client_category_name
						FROM ' . $tableprefix . 'geodir_gd_place_detail
						INNER JOIN ' . $tableprefix . 'posts ON ' . $tableprefix . 'posts.ID = ' . $tableprefix . 'geodir_gd_place_detail.post_id
						LEFT JOIN ' . $tableprefix . 'terms ON ' . $tableprefix . 'terms.term_id = ' . $tableprefix . 'geodir_gd_place_detail.default_category
						WHERE ' . $tableprefix . 'geodir_gd_place_detail.post_status = %s AND ' . $tableprefix . 'geodir_gd_place_detail.post_id = %d
					',
			'publish',
			$experience_post_id
		);
		$client_experience = $wpdb->get_results($sql);
	}
	//var_dump($client_experience); //exit;



	/** Get the experience author's user data for this experience (from wp users table) and use in the html form below **/
	// resource: https://docs.ultimatemember.com/article/153-umfetchuser
	//$experience_author_user_data = wp_get_current_user( $client_experience_check[0]->post_author );	// get the post_author user object (users table)
	$experience_author_user_data = get_userdata($client_experience_check[0]->post_author);	// get the post_author user object (users table)
	// Usage: $experience_author_user_data->user_url;
	//var_dump($experience_author_user_data);

	//var_dump(get_userdata($client_experience_check[0]->post_author));

	// Only for old client experiences that are displayed (the dummy reecords): Check if it's an old record created with the Geodirectory plugin (prior to 30-3-2022) - then get member details from the older GD details table
	$postdate = date('Y-m-d', strtotime($client_experience[0]->post_date));	// the experience post's date
	$checkdate = date('Y-m-d', strtotime('2022-03-30'));					// date to check against
	if ($postdate < $checkdate) {
		$member_name_datechecked = $client_experience[0]->member_name;			// Member name
		$member_business_datechecked = $client_experience[0]->business_name;	// Member business name
		$member_website_datechecked = $client_experience[0]->website;			// Member website
	} else {
		$member_ID_datechecked = $experience_author_user_data->ID;				// member ID
		$member_name_datechecked = $experience_author_user_data->display_name;	// member name
		$member_business_datechecked = $experience_author_user_data->nickname;	// Member business name
		$member_Business_category_id_datechecked = get_user_meta($member_ID_datechecked, 'member_business_category', true);	// Member business category
		$member_website_datechecked = $experience_author_user_data->user_url;	// Member website
	}

	// echo var_dump($client_experience);
	/** End: Handling the DB CRUD **/

	// DISPLAY the form

	$html = '';	// clear html variable before building it

	// if ($membership_level == 'premium' || $membership_level == 'plus') {
	if (!empty($client_experience)) {
		$html = '
					<div style="margin: 2rem 0;">
						<h1 style="font-size:2rem;color: var(--ast-global-color-2); font-weight: 500;">' . __('Client Experience', 'lcms-geodirectory-custom-functions') . '</h1>
						<p style="margin-bottom:30px;">' . __('The details of the client experience.', 'lcms-geodirectory-custom-functions') . '</p>';

		// Show any temporary session message to the user (ie. like when a recoerd is added)
		//$_SESSION['user_message'] = 'This is test message';
		if (isset($_SESSION['user_message'])) {
			$html .= '<div class="col-sm-12" style="padding:10px 20px;margin-bottom:20px;background-color:#adedad;">'
				. $_SESSION['user_message'] .
				'</div>
							';
			unset($_SESSION['user_message']);	// clear the message to user after it's displayed
		}

		$html .= '<!-- jQuery script for below Nav Tabs, next/prev button click event actions -->
						<script>
						
						</script>
		
						<style>
							
						</style>
		
						<!-- Nav tabs -->

						<ul class="nav nav-tabs">
							<li class="nav-item">
							<a  class="nav-link active" data-bs-toggle="tab" href="#client-experience-details">
								<i class="fa fa-info-circle ml-3" aria-hidden="true"></i>
								Client Experience Details
							</a>
							</li>';

		// check if current user owns this experience record - otherwise don't show the edit tab
		if ($current_user_id == $client_experience_check[0]->post_author && ($membership_level == 'premium' || $membership_level == 'plus')) {
			$html .= '<li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#edit-client-experience">
								<i class="fas fa-edit ml-3" aria-hidden="true"></i>
								Edit Client Experience
							</a>
						</li>';
		}

		$html .= '</ul>
						
						<!-- Tab panes -->
		
						<div class="tab-content" style="background-color:#ffffff;padding:20px 30px;">


						<!-- VIEW CLIENT EXPERIENCE -->
							<!--tab pane -->
							<div class="tab-pane container active" id="client-experience-details">

								<div class="row">
									<div class="col-md-6 col-sm-12">

										<h2 style="font-size:1.3rem;margin-top:30px;margin-bottom:20px;"><strong>' . __('Member Details', 'lcms-geodirectory-custom-functions') . '</strong></h2>
										<div class="row">
											<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Member\'s Name:', 'lcms-geodirectory-custom-functions') . '</p>
											<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_member_name" id="exp_display_member_name">' . $member_name_datechecked . '</p>
										</div>
										<div class="row">
											<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Member\'s Business:', 'lcms-geodirectory-custom-functions') . '</p>
											<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_member_business" id="exp_display_member_business">' . $member_business_datechecked . '</p>
										</div>';

		// Get the member categories
		$categories_results = lcms_get_categories();	// get all categories
		//$categories_member_category = get_user_meta( $member_ID_datechecked, 'member_business_category', true );	// get the cat ID stored for th euser in the custom created user meta field (created in UM plugin register page)

		$html .= '
										<div class="row">
											<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Member\'s Category:', 'lcms-geodirectory-custom-functions') . '</p>
											<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_member_category" id="exp_display_member_category">';

		foreach ($categories_results as $member_category_row) {
			if ($member_category_row->term_id == $member_Business_category_id_datechecked) {
				$exists_member_category_name = TRUE;
			}
		}
		if ($exists_member_category_name === TRUE) {
			$html .= __($member_category_row->name, 'lcms-geodirectory-custom-functions');	// there is a member category
		} else {
			$html .= __('none', 'lcms-geodirectory-custom-functions');	// there is no member category
		}


		$html .= '</p>
										</div>';

		if ($member_website_datechecked) {	// ONLY PREMIUM MEMBERS WEBSITE SHOWS
			// check membership level for website field
			if ($membership_level == 'premium') {

				// Remove the http protocol from web address - for display purpose only

				$client_clean_website_address = esc_url($member_website_datechecked);

				$client_clean_website_address = trim(str_replace(array('http://', 'https://'), '', $client_clean_website_address), '/');

				$client_clean_website_address = '<a href="' . esc_url($member_website_datechecked) . '" target="_blank">' . $client_clean_website_address . '</a>';
			}

			$html .= '
										<div class="row">
											<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Member\'s Website:', 'lcms-geodirectory-custom-functions') . '</p>
											<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_member_website" id="exp_display_member_website">' . $client_clean_website_address . '</p>
										</div>';
		}

		$html .= '</div>

									<div class="col-md-6 col-sm-12">
										<h2 style="font-size:1.3rem;margin-top:30px;margin-bottom:20px;"><strong>' . __('Client Details', 'lcms-geodirectory-custom-functions') . '</strong></h2>
											<div class="row">
												<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Client\'s Name', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_client_name" id="exp_display_client_name">' . $client_experience[0]->post_title . '</p>
											</div>
											<div class="row">
												<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Client\'s Phone Number', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_client_phone_number" id="exp_display_client_phone_number">' . $client_experience[0]->clients_phone_number . '</p>
											</div>
											<div class="row">
												<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Client\'s Email', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_client_email" id="exp_display_client_email">' . $client_experience[0]->clients_email . '</p>
											</div>
											<div class="row">
												<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Client\'s Zip Code', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_client_zipcode" id="exp_display_client_zipcode">' . $client_experience[0]->clients_zip_code . '</p>
											</div>
											<div class="row">
												<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Category', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7" name="exp_display_client_category_name" id="exp_display_client_category_name">' . $client_experience[0]->client_category_name . '</p>
											</div>
											<div class="row mt-3">
												<!--<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" >' . __('Rating by member', 'lcms-geodirectory-custom-functions') . ':</p>
												<p style="margin-bottom:0;" class="col-sm-7">' . $client_experience[0]->clients_experience_rating . '-->
												
												
													<!-- the 5 star rating section - DISPLAY -->
													<!-- Note: see custom.css stylesheet for star review styling -->

													<p style="margin-bottom:0;font-weight:600;" class="col-sm-5" name="exp_display_client_experience_rating" id="exp_display_client_experience_rating">' . __('Rating by Member', 'lcms-geodirectory-custom-functions') . ':</p>
														<div class="star-wrapper col-sm-7">';

		if ($client_experience[0]->clients_experience_rating == 0) {
			$html .= '
															<p class="star-label" style="float:left;" name="exp_display_client_rating_0" id="exp_display_client_rating_0"><em>none</em></p>';
		}
		if ($client_experience[0]->clients_experience_rating > 0) {
			$html .= '
															<div class="star-label" name="exp_display_client_rating_1" id="exp_display_client_rating_1">&#9733;</div>';
		}
		if ($client_experience[0]->clients_experience_rating > 1) {
			$html .= '
															<div class="star-label" name="exp_display_client_rating_2" id="exp_display_client_rating_2">&#9733;</div>';
		}
		if ($client_experience[0]->clients_experience_rating > 2) {
			$html .= '
															<div class="star-label" name="exp_display_client_rating_3" id="exp_display_client_rating_3">&#9733;</div>';
		}

		if ($client_experience[0]->clients_experience_rating > 3) {
			$html .= '
															<div class="star-label" name="exp_display_client_rating_4" id="exp_display_client_rating_4">&#9733;</div>';
		}
		if ($client_experience[0]->clients_experience_rating > 4) {
			$html .= '
															<div class="star-label" name="exp_display_client_rating_5" id="exp_display_client_rating_5">&#9733;</div>';
		}

		$html .= '

													</div> <!-- end: the 5 star rating section -->	
												
												
												</p>
											</div>
									</div>
								</div>

								<div class="row mt-4">
								<h2 style="font-size:1.3rem;margin-top:30px;margin-bottom:20px;" class="col-sm-12" ><strong>' . __('Experience with Client', 'lcms-geodirectory-custom-functions') . '</strong></h2>
								</div>
								<div class="row">';
		$modified_client_experience = str_replace(
			'Client review:',
			'<strong>' . __('Client review', 'lcms-geodirectory-custom-functions') . ':</strong>',
			$client_experience[0]->clients_experience
		);
		$modified_client_experience = str_replace(
			'Business response:',
			'<strong>' . __('Business response', 'lcms-geodirectory-custom-functions') . ':</strong>',
			$modified_client_experience
		);

		$html .= '<p class="col-sm-12 mb-5" name="exp_display_client_experience" id="exp_display_client_experience">' . __(nl2br($modified_client_experience), 'lcms-geodirectory-custom-functions') . '</p>
								</div>';

		//attachement added by devshakil 
		$attachment_show_url = plugin_dir_url(__FILE__) . 'assets/no.png';
		
		//DISPLAY TAB ATTACHMENT
		$attachment_div_html = '<div class="attachment_popup_img">
										<div class="attachment_popup_div_close">x</div>
										<img class="img-fluid" src="' . $attachment_show_url . '" />
									</div>';

		//EDIT TAB ATTACHMENT					
		$attachement_div_edit_html = '<div class="exp_edit_client_attachement_div">
										<div class="exp_edit_client_attachement_parent_div">
											<img src="' . $attachment_show_url . '" class="exp_edit_client_attachement_image_preview" height="250" width="250" style="border: 2px solid red; padding: 5px; margin: 5px"/>
											<input type="file" accept="image/png, image/jpeg" name="exp_edit_client_attachement[]" id="exp_edit_client_attachement" class="form-control" />
											<input type="hidden" name="exp_edit_client_attachement_input[]" id="exp_edit_client_attachement_input" class="form-control" value="' . $attachment_show_url . '"/>
										</div>
									</div>';

		if (!empty($client_experience[0]->attachment)) {
			$attachment_show_url = json_decode($client_experience[0]->attachment);
			if (is_array($attachment_show_url)) {

				$attachment_div_html = '';
				$attachement_div_edit_html = '';

				foreach ($attachment_show_url as $attachment_show_url_data) {
					//DISPLAY TAB ATTACHMENT
					$attachment_div_html .= '<div class="attachment_popup_img">
												<div class="attachment_popup_div_close">x</div>
												<img class="img-fluid" src="' . $attachment_show_url_data . '" />
											</div>';


					//EDIT TAB ATTACHMENT
					$attachement_div_edit_html .= '<div class="exp_edit_client_attachement_div">
											<div class="exp_edit_client_attachement_parent_div">
												<img src="' . $attachment_show_url_data . '" class="exp_edit_client_attachement_image_preview active" height="250" width="250" style="border: 2px solid red; padding: 5px; margin: 5px"/>
												<input type="file" accept="image/png, image/jpeg" name="exp_edit_client_attachement[]" id="exp_edit_client_attachement" class="form-control" />
												<input type="hidden" name="exp_edit_client_attachement_input[]" id="exp_edit_client_attachement_input" class="form-control" value="' . $attachment_show_url_data . '"/>
											</div>
										</div>';
				}
			} else {
				$attachment_show_url = plugin_dir_url(__FILE__) . 'assets/no.png';
			}
		}
		$placeholder_img = 'assets/no.png';
		if (!$attachment_show_url = '/wp-content/plugins/lcms-geodirectory-custom-functions/shortcodes/assets/no.png') {
			$html .= '<hr>
								<div class="attachment_div">
									<button class="view_attachment">View Attachment</button>

									<div class="attachment_popup_div">
										' . $attachment_div_html . '
									</div>
								</div>';
		}

		/*if ( $current_user_id == $client_experience_check[0]->post_author && ( $membership_level == 'premium' || $membership_level == 'plus' ) ) {
									$html .= '<a class="btn btn-primary btnNext">'.__('Next','lcms-geodirectory-custom-functions').'</a>';
								}*/

		$html .= '</div> <!--tab pane -->


						<!-- EDIT CLIENT EXPERIENCE -->';
		if ($current_user_id == $client_experience_check[0]->post_author && ($membership_level == 'premium' || $membership_level == 'plus')) {

			$html .= '<!--tab pane -->
								<div class="tab-pane container fade" id="edit-client-experience">
									<h2 style="font-size:1.3rem;margin-top:30px;"><strong>' . __('Edit Client Experience', 'lcms-geodirectory-custom-functions') . '</strong></h2>
									<div class="form-group row mt-2 mb-2">
										<p style="margin-bottom:30px;">' . __('Edit the details of your client experience.', 'lcms-geodirectory-custom-functions') . '</p>
										<form method="post" enctype="multipart/form-data">
										<input type="hidden" name="experience_post_id" value="' . $experience_post_id . '" />
										<input type="hidden" name="admin_ajax_url_lcms" value="' . admin_url('admin-ajax.php') . '" />
											<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_name" class="col-sm-3 col-form-label" >' . __('Client\'s name', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<input class="col-sm-4" value="' . $client_experience[0]->post_title . '" type="text" name="exp_edit_client_name" id="exp_edit_client_name" class="form-control" required>
											</div>
											<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_phone_number" class="col-sm-3 col-form-label" >' . __('Client\'s Phone Number', 'lcms-geodirectory-custom-functions') . '</label>
													<input class="col-sm-4" value="' . $client_experience[0]->clients_phone_number . '" type="text" name="exp_edit_client_phone_number" id="exp_edit_client_phone_number" class="form-control">
											</div>
											<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_email" class="col-sm-3 col-form-label" >' . __('Client\'s Email', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<input class="col-sm-4" value="' . $client_experience[0]->clients_email . '" type="email" name="exp_edit_client_email" id="exp_edit_client_email" class="form-control" required>
											</div>
											<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_zipcode" class="col-sm-3 col-form-label" >' . __('Client\'s Zip Code', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<input class="col-sm-4" value="' . $client_experience[0]->clients_zip_code . '" type="text" name="exp_edit_client_zipcode" id="exp_edit_client_zipcode" class="form-control" required>
											</div>';

			// Get the member categories
			$categories_results = lcms_get_categories();
			$html .= '<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_category" class="col-sm-3 col-form-label" >' . __('Client Category', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<select class="col-sm-4" name="exp_edit_client_category" id="exp_edit_client_category" required value="' . $client_experience[0]->default_category . '">

													<option value="' . $client_experience[0]->default_category . '" selected>' . __($client_experience[0]->client_category_name, 'lcms-geodirectory-custom-functions') . '</option>';

			foreach ($categories_results as $category_row) {
				$html .= '<option value="' . $category_row->term_id . '">' . __($category_row->name, 'lcms-geodirectory-custom-functions') . '</option>';
			}

			$html .= '</select>
											</div>
											<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_experience" class="col-sm-3 col-form-label" >' . __('My experience with Client', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<textarea class="col-sm-12" rows="8" name="exp_edit_client_experience" id="exp_edit_client_experience" class="form-control" required>' . __($client_experience[0]->clients_experience, 'lcms-geodirectory-custom-functions') . '</textarea>
											</div>

											<div class="form-group row mt-4 mb-2">
												<label for="exp_edit_client_attachement" class="col-sm-3 col-form-label" >' . __('Attachment', 'lcms-geodirectory-custom-functions') . '</label>

												' . $attachement_div_edit_html . '
												
												<a class="btn btn-primary exp_edit_client_attachement_addon mt-5">' . __('Add New Attachment', 'lcms-geodirectory-custom-functions') . '</a>

											</div>

											<!--<div class="form-group row mt-2 mb-2">
												<label for="exp_edit_client_experience_rating" class="col-sm-3 col-form-label" >' . __('Client\'s Experience Rating', 'lcms-geodirectory-custom-functions') . '<span class="text-danger">*</span></label>
												<input class="col-sm-4" value="' . $client_experience[0]->clients_experience_rating . '" type="text" name="exp_edit_client_experience_rating" id="exp_edit_client_experience_rating" class="form-control" required>-->


												<!-- the 5 star rating section  - ADD AND EDIT -->
												<!-- Note: see custom.css stylesheet for star review styling -->
												
												<!--<div class="col-md-4">
												<h2 style="font-size:1.3rem;">' . __('Rate this experience', 'lcms-geodirectory-custom-functions') . '</h2>
													<div class="star-wrapper">
													
														<input class="star-input" type="radio" id="exp_add_client_rating_1" name="exp_edit_client_experience_rating" value="5">
														<label class="star-label" for="exp_add_client_rating_1">&#9733;</label>
			
														<input class="star-input" type="radio" id="exp_add_client_rating_2" name="exp_edit_client_experience_rating" value="4">
														<label class="star-label" for="exp_add_client_rating_2">&#9733;</label>
			
														<input class="star-input" type="radio" id="exp_add_client_rating_3" name="exp_edit_client_experience_rating" value="3">
														<label class="star-label" for="exp_add_client_rating_3">&#9733;</label>
			
														<input class="star-input" type="radio" id="exp_add_client_rating_4" name="exp_edit_client_experience_rating" value="2">
														<label class="star-label" for="exp_add_client_rating_4">&#9733;</label>
			
														<input class="star-input" type="radio" id="exp_add_client_rating_5" name="exp_edit_client_experience_rating" value="1">
														<label class="star-label" for="exp_add_client_rating_5">&#9733;</label>
			
													</div>
												</div>--> <!-- end: the 5 star rating section -->
											<!--</div>-->
						
											<p style="margin-top:30px;clear:left;"><button class="btn bsui btn-primary" name="submit_button" value="save_experience">' . __('Save changes', 'lcms-geodirectory-custom-functions') . '</button></p>
											
										</form>
						
									</div>
									<!--<a class="btn btn-primary btnPrevious">' . __('Back', 'lcms-geodirectory-custom-functions') . '</a>-->
						
								</div> <!--tab pane -->';
		}


		// $html .= '</div> <!-- tab content -->


		// 				<div class="col-sm-12 mt-5">
		// 					<div id="disqus_thread"></div>
		// 					<script>
		// 						/**
		// 						*  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
		// 						*  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
		// 						/*
		// 						var disqus_config = function () {
		// 						this.page.url = ' . home_url($_SERVER['REQUEST_URI']) . ';  // Replace PAGE_URL with your page\'s canonical URL variable
		// 						this.page.identifier = ' . $_SERVER['REQUEST_URI'] . '; // Replace PAGE_IDENTIFIER with your page\'s unique identifier variable
		// 						};
		// 						*/
		// 						(function() { // DON\'T EDIT BELOW THIS LINE
		// 						var d = document, s = d.createElement("script");
		// 						s.src = "https://lyingclient-com.disqus.com/embed.js";
		// 						s.setAttribute("data-timestamp", +new Date());
		// 						(d.head || d.body).appendChild(s);
		// 						})();
		// 					</script>
		// 					<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
		// 				</div>


		// 			</div>';

		//lysqus plugin shortcode
		$html .= '</div><div class="col-sm-12 mt-5">' . do_shortcode('[lysqus_comment_experience]') . '</div>';
	} else {

		$html = '<div style="margin: 2rem 0;">
						<h1 style="font-size:2rem;color: var(--ast-global-color-2); font-weight: 500;">' . __('Client Experience', 'lcms-geodirectory-custom-functions') . '</h1>
						<p style="margin-top:15px;">' . __('We have no result for this client experience entry. Contact the website administrator.', 'lcms-geodirectory-custom-functions') . '</p>
					</div>
					';
	}

	$wpdb->flush();	// clear the results cache


	//return "foo = {$a['foo']}";
	//return "<p>User count is {$user_count}</p>";
	return $html;
}
