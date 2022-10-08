<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * DISPLAY CLIENT EXPERIENCE - shortcode
 */

add_shortcode('lcms_admin_approve_experience_page', 'lcms_admin_approve_experience_page_shortcode'); 
function lcms_admin_approve_experience_page_shortcode( $atts ) {
	$a = shortcode_atts( array(
	        //'category' => 'uncategorized',
	        //'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	    ), $atts );


	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once( plugin_dir_path(__FILE__) .'includes'. DIRECTORY_SEPARATOR .'shortcode-common-functions.php');

	// If no experience post ID supplied in url parameter, then exit shortcode
	if ( !isset($_GET['experience_post_id']) ) {
		return '<p>Incomplete url. Contact admin.</p>';
	}

	// Get the experience post ID
	$experience_post_id = sanitize_key( $_GET['experience_post_id'] );	// Get slug from URL


	/** START THE WEB PAGE BUILD PROCESS */

	global $wpdb, $post;

	/** Get the experience author's user data for this experience (from wp users table) and use in the html form below **/
		// resource: https://docs.ultimatemember.com/article/153-umfetchuser

		//$experience_author_user_data = wp_get_current_user( //$client_experience_check[0]->post_author );	// get the post_author user object (users table)
		// Usage: $experience_author_user_data->user_url;


	/** Handling the DB CRUD **/
		//$wpdb->show_errors();	// enable only for debugging

		// UPDATE the details to DB

			// Update the status to 'publish' in POSTS table - see this: https://developer.wordpress.org/reference/classes/wpdb/update/
			$sql_update_post = wp_update_post(
				array(
					'ID' =>				$experience_post_id,
					'post_status' =>	'publish'
				)
			);

			// Update the status to 'publish' in GD details table - see this: https://developer.wordpress.org/reference/classes/wpdb/update/
			$sql_update_gd_detail = $wpdb->update(
				$wpdb->prefix . 'geodir_gd_place_detail',	// table name
				array(
					//'member_name' => 				stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_name'] ) ),
					//'business_name' => 			stripslashes_deep(sanitize_text_field( $_POST['exp_edit_member_business_name'] ) ),
					//'website' => 					stripslashes_deep(esc_url_raw( $_POST['exp_edit_member_website'] ) ),
					'post_status' => 				'publish'
				),
				array('post_id' => $experience_post_id),			// where
				array( '%s' )	// data format
			);

			// Display a message to user on success
			if ( $sql_update_gd_detail !== FALSE ) {
				$_SESSION['user_message'] = 'You have approved the user\'s experience.';
			} else {
				$_SESSION['user_message'] = 'There was an error approving this client experience. Please contact admin.';
			}


		// READ the experience details from Geodirectory table
			// Note: Members details that were entered on or prior to 21-3-2022 are used in the 'Client Experience' display page. After this date, the members details are read from WP user table (because GeoDirectiry plugin was used to record member details at the time) - discussed with Tamera.
			// So, reading the post date from posts table, for any conditional code below.
			/*if ( $experience_post_id ) {
				$tableprefix = $wpdb->prefix;
				$sql = $wpdb->prepare(
					'
						SELECT DISTINCT
							'.$tableprefix.'posts.post_date,
							'.$tableprefix.'geodir_gd_place_detail.*,
							'.$tableprefix.'terms.name AS client_category_name
						FROM '.$tableprefix.'geodir_gd_place_detail
						INNER JOIN '.$tableprefix.'posts ON '.$tableprefix.'posts.ID = '.$tableprefix.'geodir_gd_place_detail.post_id
						LEFT JOIN '.$tableprefix.'terms ON '.$tableprefix.'terms.term_id = '.$tableprefix.'geodir_gd_place_detail.default_category
						WHERE '.$tableprefix.'geodir_gd_place_detail.post_status = %s AND '.$tableprefix.'geodir_gd_place_detail.post_id = %d
					',
						'publish',
						$experience_post_id
				);
				$client_experience = $wpdb->get_results( $sql );
			}*/
			//var_dump($client_experience); //exit;

	/** End: Handling the DB CRUD **/

	
	// DISPLAY the form

		$html = '';	// clear html variable before building it

		$html = '
				<div style="margin: 2rem 0;">
					<h1 style="font-size:2rem;color: var(--ast-global-color-2); font-weight: 500;">'.__('Client Experience Approval','lcms-geodirectory-custom-functions').'</h1>
					<!--<p style="margin-bottom:30px;">'.__('The details of the client experience.','lcms-geodirectory-custom-functions').'</p>-->';

					// Show any temporary session message to the user (ie. like when a recoerd is added)
					//$_SESSION['user_message'] = 'This is test message';
					if ( isset($_SESSION['user_message']) ) {
						$html .= '
							<div class="col-sm-12" style="padding:10px 20px;margin-bottom:20px;background-color:#adedad;">'
								.$_SESSION['user_message'].
							'</div>
						';
						unset($_SESSION['user_message']);	// clear the message to user after it's displayed
					}

		$html .= '
				</div>';


	$wpdb->flush();	// clear the results cache
	$_GET = array();	// reset the post array (to prevent looping on this shortcode)
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}