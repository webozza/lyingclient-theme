<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * LIST MY CLIENT EXPERIENCES - shortcode
 */


// LIST ALL CURRENT LOGGED IN USER'S CLIENT EXPERIENCES: function that runs when shortcode is called
add_shortcode('lcms_list_my_client_experiences', 'lcms_list_my_client_experiences_shortcode'); 
function lcms_list_my_client_experiences_shortcode( $atts ) {
    // Things that you want to do. 
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


	/** Check MEMBERSHIP LEVEL **/
		//$message = '<p>'.__('You do not have the correct membership to display client experiences.','lcms-geodirectory-custom-functions').'</p>';
		//$membership_level = lcms_check_membership_level( $message );	// custom function in shortcode-header-include.php
		//var_dump($membership_level);
		//if ( $membership_level[1] == 'other' ) { return $membership_level[0]; }	// return message and exit shortcode

		// $message = '<p>'.__('You do not have the correct membership to display client experiences.','lcms-geodirectory-custom-functions').'</p>';
		// $membership_level = lcms_check_membership_level( $message );
		// if ( $membership_level[1] == 'other' ) { return $membership_level[0]; }



	global $wpdb, $post;

	//var_dump($_POST);
	
	$html = '';	// clear html variable before building it

	// FIND ALL RECORDS FOR MY EXPERIENCES
		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
		    '
		        SELECT
				'.$tableprefix.'geodir_gd_place_detail.post_id,
				'.$tableprefix.'geodir_gd_place_detail.post_title,
				'.$tableprefix.'geodir_gd_place_detail._search_title,
				'.$tableprefix.'geodir_gd_place_detail.member_name,
				'.$tableprefix.'geodir_gd_place_detail.business_name,
				'.$tableprefix.'geodir_gd_place_detail.website,
				'.$tableprefix.'geodir_gd_place_detail.clients_phone_number,
				'.$tableprefix.'geodir_gd_place_detail.clients_email,
				'.$tableprefix.'geodir_gd_place_detail.clients_zip_code,
				'.$tableprefix.'geodir_gd_place_detail.clients_experience
		        FROM '.$tableprefix.'geodir_gd_place_detail
				INNER JOIN '.$tableprefix.'posts ON '.$tableprefix.'geodir_gd_place_detail.post_id = '.$tableprefix.'posts.ID
		        WHERE '.$tableprefix.'geodir_gd_place_detail.post_status = %s AND '.$tableprefix.'posts.post_author = %d
				ORDER BY '.$tableprefix.'geodir_gd_place_detail.post_title ASC
		    ',
	        	'publish',
	        	$current_user_id
		);
		$search_results = $wpdb->get_results( $sql );


		//var_dump($search_results);

		$html .= '
				<div style="margin: 2rem 0;">
					<h2 style="font-size:1.75rem;color: var(--ast-global-color-2); font-weight: 500;">'.__('My Client Experiences','lcms-geodirectory-custom-functions').'</h2>
					<p style="">'.__('My listed experiences','lcms-geodirectory-custom-functions').'</p>
				</div>

				<div style="margin: 1rem 0;">
					<ul>';
						if ($wpdb->num_rows > 0) {
							//$html .= '<p>'.__('My experiences','lcms-geodirectory-custom-functions').':</p>';
							foreach ( $search_results as $search_row ) {
								//$html.= '<li><a href="'.esc_url(get_home_url()).'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.' - '.__('Complaint by:','lcms-geodirectory-custom-functions').' '.$search_row->business_name.'</a></li>';
								$html.= '<li><a href="'.esc_url(get_home_url()).'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.'</a></li>';
							}
						} else {
							$html.= '<p>'.__('No client experience listings found for your account.','lcms-geodirectory-custom-functions').'</p>';
						}
		$html .= '	</ul>
				</div>';


	// $wpdb->flush();	// clear the results cache
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}