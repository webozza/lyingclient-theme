<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * LIST MEMBERS WEBSITES (PLUS/PREMIUM PLANS ONLY) - shortcode
 */


add_shortcode('lcms_list_members_websites_marketing', 'lcms_list_members_websites_marketing_shortcode'); 
function lcms_list_members_websites_marketing_shortcode( $atts ) {
	/** Shortcode attributes **/
		$a = shortcode_atts( array(
				'category' => 'uncategorized',
				'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
			), $atts );


	/** COMMON INCLUDES FOR THE SHORTCODES **/
		require_once( plugin_dir_path(__FILE__) .'includes'. DIRECTORY_SEPARATOR .'shortcode-common-functions.php');


	/** Check if user logged in, otherwise return a message **/
		/*$login_status = lcms_check_if_logged_in();
		if ( isset($login_status['login_status']) && $login_status['login_status'] == false ) {
			return $login_status['login_status_message'];
		} else {
			$current_user_id = $login_status['login_user_id'];
		}*/


	/** Check MEMBERSHIP LEVEL **/
		/*$message = '<p>'.__('You do not have the correct membership to display this client experience.','lcms-geodirectory-custom-functions').'</p>';
		$membership_level = lcms_check_membership_level( $message );	// custom function in shortcode-header-include.php
		//var_dump($membership_level);
		if ( $membership_level[1] == 'other' ) { return $membership_level[0]; }	// return message and exit shortcode
		*/

	// IF CATEGORY SUBMIT BUTTON WAS PRESSED, THEN GO TO RELEVANT CATEGORY PLACE PAGE
	/*if ( $_POST['submit_button_category'] && $_POST['member_categories'] ){
		$redirect_url = esc_url(home_url('/search-customers/?category='.sanitize_text_field( $_POST['member_categories'])) );
		wp_safe_redirect( $redirect_url );
		exit;
	}*/

	/** AFTER ALL THE USER VERIFICATION DONE, QUERY DB AND CREATE HTML **/

	global $wpdb;


	/** FIND ALL RECORDS FOR PLUS/PREMIUM MEMBERS WITH WEBSITES **/
	$tableprefix = $wpdb->prefix;
	$sql = $wpdb->prepare(
		'
			SELECT DISTINCT
			'.$tableprefix.'users.ID as user_ID,
			'.$tableprefix.'users.display_name,
			'.$tableprefix.'users.user_url
			FROM '.$tableprefix.'users
			ORDER BY '.$tableprefix.'users.display_name ASC
		',
			'member_business_category'
	);
	$search_results = $wpdb->get_results( $sql );
	//var_dump($search_results); //exit;


	// Populate category array with premium member categories only, if the premium member has a category
	$search_results_count = 0;	// initiliase counter for categories array items
	$user_array_count = 0;	// initiliase counter for categories array items
	$categories_array = array();	// Initiliase the new dropdown array
	$users_updated_array = array();	// initiliase new user array to hold processed search_results from DB
	foreach ( $search_results as $user_details ) {

		$user_category_id = get_user_meta( $user_details->user_ID, 'member_business_category', true );

		// Create a category select dropdown array
		if ( $user_category_id ) {
			$category_details = get_term( $user_category_id );	// get categrory details for this user
			//echo 'User ID: '.$user_details->user_ID.'. Category ID is: '.$user_category_id.' - '.$category_details->name.' - '.$category_details->slug.'<br>';

			if ( !array_key_exists($user_category_id, $categories_array) ) {
				// add category to array for dropdown (only on first occurence)
				$categories_array[$search_results_count]['category_id'] = $user_category_id;
				$categories_array[$search_results_count]['category_name'] = $category_details->name;
				$categories_array[$search_results_count]['category_slug'] = $category_details->slug;

				$search_results_count ++;
			}

			if ( !array_key_exists('uncategorized', $categories_array) ) {	// no category id found
				// add 'uncategorized' category to array for dropdown (only on first occurence)
				$categories_array[$search_results_count]['category_id'] = 'uncategorized';
				$categories_array[$search_results_count]['category_name'] = 'Uncategorized';
				$categories_array[$search_results_count]['category_slug'] = 'uncategorized';

				$search_results_count ++;
			}
		}

		// Create updated array with users (from search_results SQL result data)
		if ( $user_category_id ) {
		//echo $categories_array[$search_results_count]['category_slug'];
			// user does have a category - place as category
			$users_updated_array[$user_array_count] = $user_details;
			$users_updated_array[$user_array_count]->user_category_slug = $category_details->slug;

			$user_array_count ++;	// increment count

		} elseif ( !$user_category_id )  {
			// user does not have a category - place as 'uncategorized'
			$users_updated_array[$user_array_count] = $user_details;
			$users_updated_array[$user_array_count]->user_category_slug = 'uncategorized';

			$user_array_count ++;	// increment count
		}



	}
	//var_dump($categories_array); //exit;

	// remove the users in $users_updated_array, that are not in dropdown selection $categories_array above
	if ( $_POST['submit_button_category'] && !in_array(sanitize_text_field($_POST['member_categories']), $categories_array) ) {
		$unset_count = 0;
		foreach ( $users_updated_array as $user_detail ) {
			//var_dump($user_detail->user_category_slug);
			if ( $user_detail->user_category_slug != sanitize_text_field($_POST['member_categories']) ) {
				unset($users_updated_array[$unset_count]);
			}
			$unset_count ++;
		}
		$users_updated_array = array_values($users_updated_array);	// reset array index to start index from 0
	} else {
		$users_updated_array = array();
	}


	//var_dump($users_updated_array); //exit;
	//echo count($users_updated_array);
	//usage: echo $users_updated_array[0]->display_name;



	





/*	 BACKUP CODE
		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
		    '
		        SELECT
				'.$tableprefix.'users.ID as user_ID,
				'.$tableprefix.'users.display_name,
				'.$tableprefix.'users.user_url
		        FROM '.$tableprefix.'users
				ORDER BY '.$tableprefix.'users.display_name ASC
		    '
		);
		$search_results = $wpdb->get_results( $sql );
		var_dump($search_results); exit;
*/

	/** START THE HTML CREATION **/
		$html = '';	// clear html variable before building html

		$html .= '<div style="margin: 2rem 0;">
					<h2 style="font-size:1.75rem;color: var(--ast-global-color-2); font-weight: 500;">'.__('Premium Member Club','lcms-geodirectory-custom-functions').'</h2>
					<p style="">'.__('A directory of our Premium members business websites. Please contact them directly for any service request.','lcms-geodirectory-custom-functions').'</p>
				</div>';
         

				if ($wpdb->num_rows > 0) {

					// **** FINISH THIS *****
					// note: might have to get user meta for each record, to extract the member_business_category


					// Get the premium member categories for dropdown
					//$categories_results = lcms_get_categories();
					//echo '<pre>'; var_dump($categories_results);
					//exit();

					$html .= '<div style="margin: 1rem 0 3rem 0;">';


									$html .= '<form method="post">';
									$html .= '		<select name="member_categories" id="member_categories">';
									$html .= '  		<option value="0" selected disabled>'.__('select category','lcms-geodirectory-custom-functions').'</option>';

									$count = 0;	// initiliase category counter
									//var_dump($categories_array);
									//echo 'this: '.$categories_array[0]['category_id'];

									for ($count = 0; $count < count($categories_array); $count++) {
									//foreach ( $categories_array as $category_row ) {
										//echo 'this: '.$category_row[$count]->category_id;
										$html .= '  		<option value="'.$categories_array[$count]['category_slug'].'">'.__($categories_array[$count]['category_name'],'lcms-geodirectory-custom-functions').'</option>';
										//$count ++;
									}

									$html .= '		</select>';
									$html .= '		<button class="btn bsui btn-primary" name="submit_button_category" value="search">'.__('Go!','lcms-geodirectory-custom-functions').'</button>';
									$html .= '</form>';

					$html .= '</div>';

					// Get the category name (instead of using the slug)
					foreach ( $categories_array as $member_category ) {
						if ( $member_category['category_slug'] == sanitize_text_field($_POST['member_categories']) ) {
							$category_name = $member_category['category_name'];
						}
					}

					if ( $_POST['submit_button_category'] && sanitize_text_field($_POST['member_categories']) ) {
						$html .= '<p>'.__('Business category','lcms-geodirectory-custom-functions').' <strong>"'.$category_name.'"</strong>:</p>';
					}

					$html .= '<ul>';
						var_dump($users_updated_array);
						foreach ( $users_updated_array as $search_row ) {

							$member_user_data = get_user_by( 'id', $search_row->user_ID );	// get the user meta object (usermeta table)

							// check membership level for each member and whether a website address is available in user profile

							// Get membership level for business website display user (to display premium meberships only below)
							$membership_level_for_user_id = lcms_check_membership_level_with_user_id( $search_row->user_ID );

							if ( $member_user_data->user_url && $membership_level_for_user_id == 'premium' ) {

								$html.= '<li>';

								// Remove the http protocol from web address - for display purpose only
									$client_clean_website_address = esc_url($member_user_data->user_url);
									$client_clean_website_address = trim( str_replace( array( 'http://', 'https://' ), '', $client_clean_website_address ), '/' );

									$html .= '
										<a href="'.esc_url($member_user_data->user_url).'" target="_blank">'.$member_user_data->nickname.' ('.$client_clean_website_address.')</a>';

								$html.= '</li>';

							}

						}
					
					$html .= '</ul>';

				} else {
					$html.= '<p>'.__('No Premium member listings found.<br>'.$current_user_id,'lcms-geodirectory-custom-functions').'</p>';
				}


	$wpdb->flush();	// clear the results cache
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
};