<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * SEARCH FOR A CLIENT EXPERIENCE - shortcode
 */


// SEARCH: function that runs when shortcode is called
add_shortcode('lcms_search', 'lcms_search_shortcode'); 
function lcms_search_shortcode( $atts ) {
    // Things that you want to do. 
	$a = shortcode_atts( array(
	        //'category' => 'uncategorized',
	        'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	    ), $atts );

	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once( plugin_dir_path(__FILE__) .'includes'. DIRECTORY_SEPARATOR .'shortcode-common-functions.php');

	global $wpdb;

	//var_dump($_POST);
	
	$html = '';	// clear html variable before building it

	if ( $a['category'] == 'uncategorized' ) {
		$html = '
				<div style="margin: 2rem 0;">
						<h1 style="font-size:1.75rem;margin-bottom:20px;color: var(--ast-global-color-2); font-weight: 500;">'.__('Client Experience','lcms-geodirectory-custom-functions').'</h1>

						<form method="post">
							<div class="form-group row mt-2 mb-2">
								<div class="col-sm-4">
									<input type="text" name="search_term" placeholder="'.__('Enter Search','lcms-geodirectory-custom-functions').'" class="form-control">
								</div>
								<div class="col-sm-8">
									<button class="btn bsui btn-primary mt-1" name="submit_button" value="search">'.__('Search','lcms-geodirectory-custom-functions').'</button>
								</div>
							</div>
						</form>

						<p style="font-size:0.8rem;">'.__('You can Search by using a client\'s name, zip code, email address, website address, and phone number.','lcms-geodirectory-custom-functions').'</p>

				</div>
				';
	}


	// IF SEARCH FORM IS SUBMITTED
	if ($_POST && $_POST['submit_button'] && $_POST['search_term']){
		$search_term = sanitize_text_field( $_POST['search_term'] );

		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
		    '
		        SELECT
		         post_id,
		         post_title,
		         _search_title,
		         member_name,
		         business_name,
		         website,
		         clients_phone_number,
		         clients_email,
		         clients_zip_code,
		         clients_experience
		        FROM '.$tableprefix.'geodir_gd_place_detail
		        WHERE post_status = %s AND (
		         post_title LIKE %s
		         OR _search_title LIKE %s
		         OR member_name LIKE %s
		         OR business_name LIKE %s
		         OR website LIKE %s
		         OR clients_phone_number LIKE %s
		         OR clients_email LIKE %s
		         OR clients_zip_code LIKE %s
		         OR clients_experience LIKE %s
		         )
		      	ORDER BY post_title, business_name, member_name
		    ',
	        	'publish',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%',
	        	'%'.$wpdb->esc_like($search_term).'%'
		);
		$search_results = $wpdb->get_results( $sql );
		
		// var_dump($search_results);
		
		$html .= '<div style="margin: 1rem 0;">';
					if ($wpdb->num_rows > 0) {
						$html .= '<p>'.__('Your search results for keyword','lcms-geodirectory-custom-functions').' <strong>"'.$search_term.'"</strong>:</p>';
						foreach ( $search_results as $search_row ) {
							//$html.= '<li><a href="'.get_home_url().'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.' - '.__('Complaint by:','lcms-geodirectory-custom-functions').' '.$search_row->business_name.'</a></li>';
							$html.= '<li><a href="'.get_home_url().'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.'</a></li>';
						}
					} else {
						$html.= '<p>'.__('No results found. Please try again.','lcms-geodirectory-custom-functions').'</p>';
					}
		$html .= '</div>';

	}

	// IF CATEGORY IS SPECIFIED THROUGH SHORTCODE FROM A URL (USED IN SHORTCODE'S ATTRIBUTE)
	if ( isset($a['category']) && $a['category'] != 'uncategorized' && $a['category'] != '' ){
		$search_term = $a['category'];

		$tableprefix = $wpdb->prefix;
		$sql = $wpdb->prepare(
		    '
		        SELECT
		         post_id,
		         post_title,
		         _search_title,
		         '.$tableprefix.'terms.name,
		         member_name,
		         business_name,
		         website,
		         clients_phone_number,
		         clients_email,
		         clients_zip_code,
		         clients_experience
		        FROM '.$tableprefix.'geodir_gd_place_detail
		        INNER JOIN '.$tableprefix.'terms ON '.$tableprefix.'terms.term_id = '.$tableprefix.'geodir_gd_place_detail.default_category
		        WHERE post_status = %s AND (
					'.$tableprefix.'terms.slug = %s
		         OR '.$tableprefix.'terms.name = %s
		         )
		      	ORDER BY post_title, business_name, member_name
		    ',
	        	'publish',
	        	$search_term,
	        	$search_term
		);
		$search_results = $wpdb->get_results( $sql );
		
		//var_dump($search_results);

		$html .= '<div style="margin: 1rem 0;">';
					if ($wpdb->num_rows > 0) {
						$html .= '<h1 style="font-size:1.6rem;margin-bottom:20px;">'.__('Business Category','lcms-geodirectory-custom-functions').': <strong>"'.__(ucwords(str_replace('-', ' ', $search_term)),'lcms-geodirectory-custom-functions').'"</strong></h1>';
						foreach ( $search_results as $search_row ) {
							//$html.= '<li><a href="'.get_home_url().'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.' - '.__('Complaint by:','lcms-geodirectory-custom-functions').' '.$search_row->business_name.'</a></li>';
							$html.= '<li><a href="'.get_home_url().'/client-experience/?experience_slug='.str_replace(' ', '-', $search_row->_search_title).'">'.$search_row->post_title.'</a></li>';
						}
					} else {
						$html.= '<h1 style="font-size:1.6rem;margin-bottom:20px;">Business Category</h1>';
						//$html.= '<p>No results found for the category <strong>"'.ucwords($search_term).$search_row->thisname.'"</strong>. Please <a href="'.home_url('/search-customers').'">try again</a>.</p>';
						$html.= '<p>'.__('No results were found for this category. Please ','lcms-geodirectory-custom-functions').'<a href="'.home_url('/search-customers').'">'._e('try again','lcms-geodirectory-custom-functions').'</a></p>';
					}
		$html .= '</div>';

	}


	$wpdb->flush();	// clear the results cache
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}

// DROPDOWN CATEGORY: - function that runs when shortcode is called
add_shortcode('lcms_category_dropdown', 'lcms_category_dropdown_shortcode'); 
function lcms_category_dropdown_shortcode( $atts ) {
    // Things that you want to do. 
	$a = shortcode_atts( array(
	        'category' => 'uncategorized',
	        //'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	    ), $atts );

	// *** COMMON INCLUDES FOR THE SHORTCODES ***
	require_once( plugin_dir_path(__FILE__) .'includes'. DIRECTORY_SEPARATOR .'shortcode-common-functions.php');


	// IF CATEGORY SUBMIT BUTTON WAS PRESSED, THEN GO TO RELEVANT CATEGORY PLACE PAGE
	if ( $_POST['submit_button_category'] && $_POST['place_categories'] ){
		$redirect_url = esc_url(home_url('/search-customers/?category='.sanitize_text_field( $_POST['place_categories'])) );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	// Get the member categories
	$categories_results = lcms_get_categories();
	//echo '<pre>'; var_dump($categories_results);
	//exit();


	// Build the html
	$html = '';	// clear html variable before building it

	$html .= '<div style="margin: 1rem 0 3rem 0;">';
				//if ($wpdb->num_rows > 0) {
					$html .= '<h5 style="margin-bottom:20px;">'._e('OR select a Business Category','lcms-geodirectory-custom-functions').'</h5>';
				//} else {
				//	$html.= '<h4 style="font-size:1.6rem;margin-bottom:20px;">'._e('Business Category','lcms-geodirectory-custom-functions').'</h4>';
				//	$html.= '<p>'._e('No place categories found.','lcms-geodirectory-custom-functions').'</p>';
				//}

					$html .= '<form method="post">';
					$html .= '		<select name="place_categories" id="place_categories">';
					$html .= '  		<option value="0" selected disabled>'.__('Select','lcms-geodirectory-custom-functions').'</option>';
					foreach ( $categories_results as $category_row ) {
						$html .= '  		<option value="'.$category_row->slug.'">'.__($category_row->name,'lcms-geodirectory-custom-functions').'</option>';
					}
					$html .= '		</select>';
					$html .= '		<button class="btn bsui btn-primary" name="submit_button_category" value="search">'.__('Go!','lcms-geodirectory-custom-functions').'</button>';
					$html .= '</form>';



	$html .= '</div>';
	
	

    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    return $html;
}
