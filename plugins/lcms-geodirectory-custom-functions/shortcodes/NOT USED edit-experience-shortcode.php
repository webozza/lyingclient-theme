<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * EDIT CLIENT EXPERIENCE - shortcode
 */

add_shortcode('lcms_edit_experience_page', 'lcms_edit_experience_page_shortcode'); 
function lcms_edit_experience_page_shortcode( $atts ) {
	$a = shortcode_atts( array(
	        //'category' => 'uncategorized',
	        //'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	    ), $atts );


	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once( plugin_dir_path(__FILE__) .'includes'. DIRECTORY_SEPARATOR .'shortcode-common-functions.php');


	/** Check if user logged in, otherwise return a message **/
	$login_status = lcms_check_if_logged_in();
	if ( isset($login_status['login_status']) && $login_status['login_status'] == false ) {
		return $login_status['login_status_message'];
	} else {
		$current_user_id = $login_status['login_user_id'];
	}


	//$_GET['experience_slug'] = 'lolita-g';						// DEBUG ONLY: this is to assigne value temporarily during dev
	
	$experience_slug = sanitize_key( $_GET['experience_slug'] );	// Get CPT value of 'gd_place' passed from display page
	//echo 'Slug value from GET: '.$experience_slug.'<br>';			//DEBUG ONLY

	if ( !isset($_GET['experience_slug']) ) {
		return
		'<div style="margin: 2rem 0;">'.
		'<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">'.__('Client Experience','lcms-geodirectory-custom-functions').'</h1>'.
		'<p style="font-size:0.8rem;margin-top:15px;">'.__( 'No client experience was selected.','lcms-geodirectory-custom-functions').'</p>'.
		'</div>';
	}

	global $wpdb, $post;

	/** GET the gd_place (CPT), post ID from the passed url slug for the specific client expierence record **/
		$theClientExperiencePost = get_posts( array(
			'post_type'   		=> 'gd_place',
			'name'  			=> $experience_slug,
			'numberposts' 		=> 1,
			//'posts_per_page'	=> 3,
			//'category_name'	=> $subject_slug,
			//'orderby'			=> 'title',
			//'order'			=> 'asc'
		));
		//echo 'This is the GD place detail post_id to use for DB search: '.$theClientExperiencePost[0]->ID;	//DEBUG ONLY
		$experience_post_id = (int) $theClientExperiencePost[0]->ID;
		//var_dump($theClientExperiencePost);


	/** Check if current user owns this experience record otherwise exit with message **/
		// READ the details from DB
		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
			'
				SELECT DISTINCT '.$tableprefix.'geodir_gd_place_detail.post_id, '.$tableprefix.'posts.ID, '.$tableprefix.'posts.post_author
				FROM '.$tableprefix.'geodir_gd_place_detail
				INNER JOIN '.$tableprefix.'posts ON '.$tableprefix.'geodir_gd_place_detail.post_id = '.$tableprefix.'posts.ID
				WHERE '.$tableprefix.'geodir_gd_place_detail.post_status = %s AND '.$tableprefix.'geodir_gd_place_detail.post_id = %d
			',
				'publish',
				$experience_post_id
		);
		$client_experience_check = $wpdb->get_results( $sql );
		//$wpdb->print_error();	// enable only for debugging
		//var_dump($client_experience_check);

		// exit if user does not own this record
		if ( $current_user_id != $client_experience_check[0]->post_author ) {
			return
				'<div style="margin: 2rem 0;">'.
				'<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">'.__('Client Experience','lcms-geodirectory-custom-functions').'</h1>'.
				'<p style="font-size:0.8rem;margin-top:15px;">'.__( 'You do not have permission to edit this client experience.','lcms-geodirectory-custom-functions').'</p>'.
				'</div>';
		}

	/** Check MEMBERSHIP LEVEL **/
		$message = '<p>'.__('You do not have the correct membership to edit this client experience.','lcms-geodirectory-custom-functions').'</p>';
		$membership_level = lcms_check_membership_level( $message );	// custom function in shortcode-header-include.php
		//var_dump($membership_level);
		if ( $membership_level[1] == 'other' ) { return $membership_level[0]; }	// return message and exit shortcode

	/** Handling the DB CRUD **/
		//$wpdb->show_errors();	// enable only for debugging

		// UPDATE the details from DB
		if ( $_POST['submit_button'] && $_POST['submit_button'] == 'save_experience' ) {
			// remove the submit field from array
			unset( $_POST['submit_button'] );

			// update the record with SQL - see this: https://developer.wordpress.org/reference/classes/wpdb/update/
			$sqlexecute = $wpdb->update(
				$wpdb->prefix . 'geodir_gd_place_detail',	// table name
				array(
					//'member_name' => 			stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_name'] ) ),
					//'business_name' => 			stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_business_name'] ) ),
					//'website' => 				stripslashes_deep(esc_url_raw( $_POST['exp_edit_member_website'] ) ),
					'post_title' => 			stripslashes_deep(sanitize_text_field( $_POST['exp_edit_client_name'] ) ),
					'clients_phone_number' => 	stripslashes_deep(sanitize_text_field( $_POST['exp_edit_client_phone_number'] ) ),
					'clients_email' => 			stripslashes_deep(sanitize_email( $_POST['exp_edit_client_email'] ) ),
					'clients_zip_code' => 		stripslashes_deep(sanitize_text_field( $_POST['exp_edit_client_zipcode'] ) ),
					'clients_experience' =>		stripslashes_deep(sanitize_textarea_field( $_POST['exp_edit_client_experience'] ) ),
					'clients_experience_rating' =>		stripslashes_deep(sanitize_text_field( $_POST['exp_edit_client_experience_rating'] ) )
				),
				array('post_id' => $experience_post_id),			// where
				array( '%s','%s','%s','%s','%s','%s','%s','%s','%s' )	// data format
			);
			//$wpdb->print_error();	// enable only for debugging
			//var_dump($sqlexecute);
		
		}

		// READ the details from DB
			if ( $experience_post_id ) {
				$tableprefix = $wpdb->prefix;
				$sql = $wpdb->prepare(
					'
						SELECT DISTINCT *
						FROM '.$tableprefix.'geodir_gd_place_detail
						WHERE '.$tableprefix.'geodir_gd_place_detail.post_status = %s AND '.$tableprefix.'geodir_gd_place_detail.post_id = %d
					',
						'publish',
						$experience_post_id
				);
				$client_experience = $wpdb->get_results( $sql );
				//$wpdb->print_error();	// enable only for debugging
				//var_dump($client_experience);
			}


	/** End: Handling the DB CRUD **/

	// DISPLAY the form
		$html = '';	// clear html variable before building it

		if ( $membership_level == 'premium' || $membership_level == 'plus' ) {
			$html = '
					<div style="margin: 2rem 0;">
						<h1 style="font-size:2rem;color: var(--ast-global-color-2); font-weight: 500;">'.__('Edit My Client Experience','lcms-geodirectory-custom-functions').'</h1>
						<p style="margin-bottom:30px;">'.__('Edit the details of your client experience.','lcms-geodirectory-custom-functions').'</p>
						<form method="post">';

							/*<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_member_name" class="col-sm-3 col-form-label" >Member Name<span class="text-danger">*</span></label>
									<input class="col-sm-4" value="'.__($client_experience[0]->member_name,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_member_name" id="exp_edit_member_name" class="form-control" required>
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_member_business_name" class="col-sm-3 col-form-label" >Member\'s Business Name<span class="text-danger">*</span></label>
								<input class="col-sm-4" value="'.__($client_experience[0]->business_name,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_member_business_name" id="exp_edit_member_business_name" class="form-control" required>

							</div>';
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_member_website" class="col-sm-3 col-form-label" >Member\'s Website</label>
								<input
									// check membership level for website field
									if($membership_level == 'other' && !current_user_can( 'manage_options' ) ) {
										$html .= 'disabled';
									}
									$html .= ' class="col-sm-4" value="'.__($client_experience[0]->website,'lcms-geodirectory-custom-functions').'" type="url" name="exp_edit_member_website" id="exp_edit_member_website" class="form-control">';

									if($membership_level == 'other' && !current_user_can( 'manage_options' ) ) {
										$html .= '<span class="geodir_message_upgrade">'.$link.'</span>';
									}

							$html .= '</div>
							*/
							$html .= '<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_name" class="col-sm-3 col-form-label" >Client\'s name<span class="text-danger">*</span></label>
								<input class="col-sm-4" value="'.__($client_experience[0]->post_title,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_client_name" id="exp_edit_client_name" class="form-control" required>
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_phone_number" class="col-sm-3 col-form-label" >Client\'s Phone Number</label>
									<input class="col-sm-4" value="'.__($client_experience[0]->clients_phone_number,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_client_phone_number" id="exp_edit_client_phone_number" class="form-control">
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_email" class="col-sm-3 col-form-label" >Client\'s Email<span class="text-danger">*</span></label>
								<input class="col-sm-4" value="'.__($client_experience[0]->clients_email,'lcms-geodirectory-custom-functions').'" type="email" name="exp_edit_client_email" id="exp_edit_client_email" class="form-control" required>
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_zipcode" class="col-sm-3 col-form-label" >Client\'s Zip Code<span class="text-danger">*</span></label>
								<input class="col-sm-4" value="'.__($client_experience[0]->clients_zip_code,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_client_zipcode" id="exp_edit_client_zipcode" class="form-control" required>
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_experience" class="col-sm-3 col-form-label" >Experience with Client<span class="text-danger">*</span></label>
								<textarea class="col-sm-12" rows="8" name="exp_edit_client_experience" id="exp_edit_client_experience" class="form-control" required>'.__($client_experience[0]->clients_experience,'lcms-geodirectory-custom-functions').'</textarea>
							</div>
							<div class="form-group row mt-2 mb-2">
								<label for="exp_edit_client_experience_rating" class="col-sm-3 col-form-label" >Client\'s Experience Rating<span class="text-danger">*</span></label>
								<input class="col-sm-4" value="'.__($client_experience[0]->clients_experience_rating,'lcms-geodirectory-custom-functions').'" type="text" name="exp_edit_client_experience_rating" id="exp_edit_client_experience_rating" class="form-control" required>
							</div>

							<p style="margin-top:30px;clear:left;"><button class="btn bsui btn-primary" name="submit_button" value="save_experience">'.__('Save','lcms-geodirectory-custom-functions').'</button></p>
							
						</form>
					</div>
					';
		} else {
			$html = '
					<div style="margin: 2rem 0;">
						<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">'.__('Edit My Client Experience','lcms-geodirectory-custom-functions').'</h1>
						<p style="font-size:0.8rem;margin-top:15px;">'.__('We have no result for this client experience entry. Contact the website administrator.','lcms-geodirectory-custom-functions').'</p>
					</div>
					';
		}

	$wpdb->flush();	// clear the results cache
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}