<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


// Enqueue Bootstrap 5 Script and css
//wp_enqueue_style('bootstrap5-lcms', esc_url( plugin_dir_url( __FILE__ ) . '../../libraries/bootstrap/css/bootstrap.min.css' )); // Bootstrap 5 css - in header
//wp_enqueue_script('bootstrap5-lcms', esc_url( plugin_dir_url( __FILE__ ) . '../../libraries/bootstrap/js/bootstrap.min.js' ), array(), '', true);	// Bootstrap 5 js - in footer
/* *** DISABLED ABOVE - CSS WAS ENQUEUEING IN FOOTER - CODE PUT IN MAIN PLUGIN FILE NOW *** */


/*
 * SHORTCODE INCLUDE FUNCTIONS
 */

function pmpro_hasMembershipLevel() {
	//don't put any code up here
	
	//test for PMPro
	if ( ! defined( 'PMPRO_VERSION' ) ) {
	return;
	}
	
	//put your edits below here
	}

// ** Check if logged in, get current logged in user wordpress ID
function lcms_check_if_logged_in() {
    if ( !is_user_logged_in() ) {
        return array(
            'login_status' => false,
            'login_status_message' => '<div style="margin-top:30px;">'.
            '<p>'.__( 'You need to be logged in.','lcms-geodirectory-custom-functions').'</p>
            <p><button class="geodir_button btn btn-primary lcms-button" name="login_button"><a style="text-decoration: none;color:#fff;" href="'.get_site_url().'/login">' . __( 'Login','lcms-geodirectory-custom-functions' ) . '</a></button></p>'.
            '</div>'
        );
    } else {
        // Get user ID
        return array(
            'login_user_id' => get_current_user_id()
        );

        //$current_user_id = get_current_user_id();
        //echo '<br>Current user: '.$current_user_id.'<br>';	// DEBUG ONLY
        //echo site_url().'<br>';								// DEBUG ONLY
        //echo get_site_url();									// DEBUG ONLY
    }
}


// Check the currently logge din user's membership level
function lcms_check_membership_level( $message ) {
	// MEMBERSHIP CHECK
	// Resource: https://www.paidmembershipspro.com/documentation/content-controls/require-membership-function/#
	if( pmpro_hasMembershipLevel(array('3','Premium')) ) {
		$membership_level = 'premium';
	} elseif ( ( ! is_numeric( $membership_level ) ) && ( $found_level ) ) { $return = true; } 
	// elseif ( pmpro_hasMembershipLevel(array('2', 'Plus')) ) {
	// 	$membership_level = 'plus';
	// }
	 else {
		$membership_level = 'other';
	}

	//$link = '<a style="text-decoration:none;" href="'.get_site_url().'/membership-account/membership-checkout/?level=2">' . __( 'Upgrade to Plus or Premium for this feature', 'geodirectory' ) . '</a>';

	// Link to use to UPGRADE to PLUS or PREMIUM
	if ( $membership_level == 'other' ) {
		$link = '<a style="text-decoration:none;" href="'.get_site_url().'/membership/">' . __( 'Upgrade to Plus or Premium for this feature', 'geodirectory' ) . '</a>';
	}
	// Link to use to UPGRADE to PREMIUM
	if ( $membership_level == 'plus' ) {
		$link = '<a style="text-decoration:none;" href="'.get_site_url().'/membership-account/membership-checkout/?level=3">' . __( 'Upgrade to Premium for this feature', 'geodirectory' ) . '</a>';
	}

    // Message to return to UPGRADE membership
    $upgrade_message =  '<div style="margin: 2rem 0;">'.
							'<h2 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">'.__('Client Experience','lcms-geodirectory-custom-functions').'</h2>'.
							'<p style="font-size:0.8rem;margin-top:15px;">'.__( $message,'lcms-geodirectory-custom-functions').'</p>'.
							'<p class="geodir_message_upgrade">'.$link.'</p>'.
                        '</div>';

	// Conditional checks - return membership level or upgrade message
    if ( $membership_level != 'premium' && $membership_level != 'plus' ) {
		return array(
            $upgrade_message,
            $membership_level
        );
	} else {
        return $membership_level;
    }
}


// Check a specific user's membership level (supplying an user ID as argument)
function lcms_check_membership_level_with_user_id( $user_id ) {
	// MEMBERSHIP CHECK
	// Resource: https://www.paidmembershipspro.com/documentation/content-controls/require-membership-function/#
	if( pmpro_hasMembershipLevel( '3', $user_id ) ) {
		$membership_level = 'premium';
	} elseif ( pmpro_hasMembershipLevel( '2', $user_id ) ) {
		$membership_level = 'plus';
	} else {
		$membership_level = 'other';
	}

	// Link to use to UPGRADE to PREMIUM
	//$link = '<a href="'.get_site_url().'/membership-account/membership-checkout/?level=3">' . __( 'Upgrade to Premium for this feature', 'geodirectory' ) . '</a>';

    return $membership_level;
}

// Count user experience records
function lcms_count_user_experience_records( $user_id ) {

    global $wpdb;

	// COUNT ALL EXPERIENCE RECORDS FOR SPECIFIC USER
    $tableprefix = $wpdb->prefix;
    $sql = $wpdb->prepare(
        '
            SELECT '.$tableprefix.'geodir_gd_place_detail.post_id
            FROM '.$tableprefix.'geodir_gd_place_detail
            INNER JOIN '.$tableprefix.'posts ON '.$tableprefix.'geodir_gd_place_detail.post_id = '.$tableprefix.'posts.ID
            WHERE
				('.$tableprefix.'geodir_gd_place_detail.post_status = %s OR '.$tableprefix.'geodir_gd_place_detail.post_status = %s)
                AND '.$tableprefix.'posts.post_type = %s
                AND '.$tableprefix.'posts.post_author = %d
        ',
        'publish',
        'pending',
        'gd_place',
        $user_id
    );
    $search_results = $wpdb->get_results( $sql );
    //var_dump($search_results); //exit;

    $record_count = $wpdb->num_rows;

    return $record_count;
}


// Get all the business categories
function lcms_get_categories() {

    global $wpdb;

	// POPULATE DROPDOWN FIELD FROM "TERMS" AND "PLACE DETAIL" TABLES
	// DEV NOTE: Below 'and not' statements are temporary. I need to rather look at the 'post_type' value to filter out non-geodir category post types (such as categories in the below 'and not' statements)
	$tableprefix = $wpdb->prefix;
	$sql = $wpdb->prepare(
	    '
	        SELECT DISTINCT
			 '.$tableprefix.'terms.term_id,
	         '.$tableprefix.'terms.name,
	         '.$tableprefix.'terms.slug
	        FROM '.$tableprefix.'terms
	        LEFT JOIN '.$tableprefix.'geodir_gd_place_detail ON '.$tableprefix.'terms.term_id = '.$tableprefix.'geodir_gd_place_detail.default_category
			WHERE '.$tableprefix.'terms.term_id > 36
			AND NOT '.$tableprefix.'terms.slug = "astra"
			AND NOT '.$tableprefix.'terms.slug = "advertisement"
			AND NOT '.$tableprefix.'terms.slug = "alerta-de-estafas"
			AND NOT '.$tableprefix.'terms.slug = "analytics"
			AND NOT '.$tableprefix.'terms.slug = "functional"
			AND NOT '.$tableprefix.'terms.slug = "necesario"
			AND NOT '.$tableprefix.'terms.slug = "necessary"
			AND NOT '.$tableprefix.'terms.slug = "others"
			AND NOT '.$tableprefix.'terms.slug = "performance"
			AND NOT '.$tableprefix.'terms.slug = "scam-alert"
			AND NOT '.$tableprefix.'terms.term_id = "108"
			AND NOT '.$tableprefix.'terms.term_id = "113"
			AND NOT '.$tableprefix.'terms.term_id = "111"
			AND NOT '.$tableprefix.'terms.term_id = "112"
			AND NOT '.$tableprefix.'terms.term_id = "106"
			AND NOT '.$tableprefix.'terms.term_id = "110"
			AND NOT '.$tableprefix.'terms.term_id = "107"
			AND NOT '.$tableprefix.'terms.term_id = "109"
			AND NOT '.$tableprefix.'terms.term_id = "114"
			ORDER BY '.$tableprefix.'terms.name
	    '
		);
	$categories_results = $wpdb->get_results( $sql );

	$wpdb->flush();	// clear the results cache

    return $categories_results;
	// echo '<pre>'; var_dump($categories_results);
	//exit();
}


// Send email to admin on new experience record created by user
// returns true or false, depending on wp_mail send success
function lcms_send_email_admin_new_experience_approval(
	$user_experience_post_id,
	$user_name,
	$user_email,
	$user_user_id,
	$user_membership_level,
	$user_experience_text
	) {

	// This for debugging WP-mail errors. (de-activate this action when mail is working)
	// Using debugging in WP SMTP plugin instead
	//add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
	/*function log_mailer_errors( $wp_error ){
		$fn = ABSPATH . '/wp_mail_testing.log'; // say you've got a mail.log file in your server root
		$fp = fopen($fn, 'a');
		fputs($fp, "Mailer Error: " . $wp_error->get_error_message() ."\n");
		fclose($fp);
	}*/

/** TODO:
 * - get membership level for email body (perhaps not necessary)
 * - get link to the new experience post OR approval function (check how GD do it)
 */
	$message = nl2br('Hello Admin,
	
				A Lying Client website user has created a new client experience, awaiting your approval.

				User\'s name: '.$user_name.'
				User\'s email: '.$user_email.'
				User\'s membership level: '.$user_membership_level.'

				<strong>User\'s experience:</strong><br>'.

				nl2br($user_experience_text).'
				
				<a href="'.esc_url(get_home_url().'/approve-experience/?experience_post_id='.$user_experience_post_id).'">Approve this</a>

				(optional: you can reply to this email to respond to the user, if necessary)
	');

// **** NOTE: WORK WITH THIS FORMAT FOR POST APPROVAL LINK IN BODY OF EMAIL:
//<a href="'.esc_url(get_home_url()).'/approve-experience/?experience_slug='.$user_experience_slug.'">'.$search_row->post_title.'</a>

	//php mailer variables
// 	$to = get_option('admin_email');
	$to = 'lyingclient@gmail.com';	// FOR DEBUGGING ONLY
	$subject = 'Lying Client: New Client Experience added';

	$headers[] = 'From: Lying Client <contactus@lyingclient.com>';
	$headers[] = 'Reply-To: ' . $user_email;
	$headers[] = 'Bcc: mario@lcms.co.za';	// FOR DEBUGGING ONLY
	$headers[] = 'Content-type: text/html; charset=UTF-8';

	//Here put your Validation and send mail
	$sent = wp_mail($to, $subject, $message, $headers);
	
	if( $sent ) {
		return true;	//message sent!
	}
	else  {
		return false;	//message wasn't sent
	}

}
