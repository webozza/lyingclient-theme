<?php

add_shortcode('lcms_category_premium_dropdown', 'lcms_category_premium_dropdown_shortcode');
function lcms_category_premium_dropdown_shortcode($atts)
{
    // Things that you want to do. 
    $a = shortcode_atts(array(
        'category' => 'uncategorized',
        //'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
    ), $atts);

    // *** COMMON INCLUDES FOR THE SHORTCODES ***
    require_once(plugin_dir_path(__FILE__) . 'includes' . DIRECTORY_SEPARATOR . 'shortcode-common-functions.php');


    // IF CATEGORY SUBMIT BUTTON WAS PRESSED, THEN GO TO RELEVANT CATEGORY PLACE PAGE
    // if ($_POST['submit_button_category'] && $_POST['place_categories']) {
    //     $redirect_url = esc_url(home_url('/premium-member-club-2/?category=' . sanitize_text_field($_POST['place_categories'])));
    //     wp_safe_redirect($redirect_url);
    //     exit;
    // }

    // Get the member categories
    $categories_results = lcms_get_categories();
    // echo '<pre>'; var_dump($categories_results);
    //exit();
    // var_dump(get_taxonomy( 'wpcode_location' ));


    // Build the html
    $html = '';    // clear html variable before building it

    if (!isset($_POST['place_categories'])) {
        $html .= '<div style="margin: 1rem 0 3rem 0;">';
        //if ($wpdb->num_rows > 0) {
        $html .= '<h5 style="margin-bottom:20px;">' . _e('OR select a Business Category', 'lcms-geodirectory-custom-functions') . '</h5>';
        //} else {
        //	$html.= '<h4 style="font-size:1.6rem;margin-bottom:20px;">'._e('Business Category','lcms-geodirectory-custom-functions').'</h4>';
        //	$html.= '<p>'._e('No place categories found.','lcms-geodirectory-custom-functions').'</p>';
        //}

        $html .= '<form method="post">';
        $html .= '		<select name="place_categories" id="place_categories">';
        $html .= '  		<option value="0" selected disabled>' . __('Select', 'lcms-geodirectory-custom-functions') . '</option>';
        foreach ($categories_results as $category_row) {
            $html .= '  		<option value="' . $category_row->term_id . '">' . __($category_row->name, 'lcms-geodirectory-custom-functions') . '</option>';
        }
        $html .= '		</select>';
        $html .= '		<button class="btn bsui btn-primary" name="submit_button_category" value="search">' . __('Go!', 'lcms-geodirectory-custom-functions') . '</button>';
        $html .= '</form>';



        $html .= '</div>';
    }



    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";

    return $html;
}

?>