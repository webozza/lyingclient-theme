<?php
/**
 * Plugin Name:       LCMS
 * Plugin URI:        https://lyingclient.com
 * Description:       Custom functions to extend GeoDirectory and Paid Membership Pro plugin functionality
 * Version:           2.0.8
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            Tamera McKnight Preacher
 * Author URI:        https://lyingclient.com
 * Developer:         Mario
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lcms-geodirectory-custom-functions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// bails if PHP version is lower than required
if (version_compare(PHP_VERSION, '7.0.0', '<')) {
    // add admin notice here
    __e('lcms Solutions plugin: This website\'s PHP version is too low. Minimum PHP version 7.0 required');
    return;
}


// *** INITIALISE SESSIONS ***
//resource: https://www.ironistic.com/insights/using-php-sessions-in-wordpress/
// START SESSION
if ( session_status() == PHP_SESSION_NONE ) {
    session_start();

	// FOR DEBUG ONLY
	//$_SESSION['user_message'] = 'This is test message';
	//if ( isset($_SESSION['user_message']) ) { echo $_SESSION['user_message']; }
	//var_dump($_SESSION);

	/*
    //add_action( 'show_session_data', 'lcms_show_session_data' );
    function lcms_show_session_data() {
        // Set session variable to use for personalisation on any pages
        if ( is_user_logged_in() ) {
            if (wp_get_current_user()->first_name) {
                $_SESSION['myname'] = ' '.wp_get_current_user()->first_name;
                echo 'first name: '.$_SESSION['myname'];
            } elseif (wp_get_current_user()->display_name) {
                $_SESSION['myname'] = ' '.wp_get_current_user()->display_name;
            echo 'display name: '.$_SESSION['myname'];
            } elseif (wp_get_current_user()->user_nicename) {
                $_SESSION['myname'] = ' '.wp_get_current_user()->user_nicename;
            }
        } else {
            echo 'User not logged in.';
        }
    }*/

}


// INCLUDE THE lcms FUNCTIONS TOOLBOX
//include 'lcms-toolbox.php';

/*Sources:
 * https://pagely.com/blog/creating-custom-shortcodes/
 * https://www.smashingmagazine.com/2011/09/interacting-with-the-wordpress-database/
 * https://www.sitepoint.com/working-with-databases-in-wordpress/
 * https://wordpress.stackexchange.com/questions/214049/wpdb-read-and-write-value-from-to-database
 * https://wordpress.stackexchange.com/questions/90836/insert-data-in-database-using-form
 * https://wordpress.stackexchange.com/questions/251390/get-data-from-dropdown-and-update-page
 */

// ===================================================

// ENQUEUE SCRIPTS - IN HEADER / FOOTER
// Inserting a script in the WordPress head and footer (before closing body tag)
add_action ('wp_enqueue_scripts', 'lcms_enqueue_custom_head_footer_scripts_styles');
function lcms_enqueue_custom_head_footer_scripts_styles() {
	// Source: https://squareinternet.co/inserting-scripts-in-the-wordpress-head-or-body-using-wp_enqueue_script/
	// Source: https://developer.wordpress.org/reference/functions/wp_enqueue_script/

	// HEAD SCRIPTS HERE:

		wp_enqueue_style('custom-lcms', plugin_dir_url(__FILE__) . 'css/'. 'custom.css');    // lcms custom css
		//wp_enqueue_style('animate-lcms', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css');    // animate elements you select on web page
		//wp_enqueue_style('fancybox-lcms', esc_url( get_stylesheet_directory_uri() . '/3rdparty/fancybox/jquery.fancybox.min.css' )); // lightbox/popup library
		//wp_enqueue_style('aos-lcms', esc_url( get_stylesheet_directory_uri() . '/3rdpartylibraries/aos/dist/aos.css' )); // aos - animate on scroll library
		
		//if ( is_page( array('edit-client-experience-post') ) ) { wp_enqueue_style('bootstrap5-lcms', esc_url( plugin_dir_url( __FILE__ ) . 'libraries/bootstrap/css/bootstrap.min.css' )); } // Bootstrap 5 - ONLY SLECIFIC PAGE(S)

		/*
		echo '<meta name="facebook-domain-verification" content="u7cgjfkofjxcjbpnvw2d7i3tc3pt4r" />';
		echo '
			<!-- Facebook Pixel Code -->
			<script>
			!function(f,b,e,v,n,t,s)
			{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window, document,"script",
			"https://connect.facebook.net/en_US/fbevents.js");
			fbq("init", "389203872839810");
			fbq("track", "PageView");
			</script>
			<noscript><img height="1" width="1" style="display:none"
			src="https://www.facebook.com/tr?id=389203872839810ev=PageView&noscript=1"
			/></noscript>
			<!-- End Facebook Pixel Code -->
		';
		*/

	//FOOTER SCRIPTS HERE:

		// footer scripts distinguished by addditional parameters, example as follows:
		// USAGE: wp_enqueue_script( string $handle, string $src = '', string[] $deps = array(), string|bool|null $ver = false, bool $in_footer = false )

		// FontAwesome library
		//wp_enqueue_script('fontawesome-lcms', 'https://kit.fontawesome.com/cd5b551998.js', '', '', true);    // icons library

		// Fancybox library and my custom script
		//wp_enqueue_script('fancybox-lcms', esc_url( get_stylesheet_directory_uri() . '/3rdparty/fancybox/jquery.fancybox.min.js' ), array(), '', true);  // lightbox/popup library
		//wp_enqueue_script('custom-fancybox-lcms', esc_url( get_stylesheet_directory_uri() . '/3rdparty/_my-custom-scripts/fancybox/custom_fancybox.js' ), array('fancybox-lcms', 'jquery'), '', true);  // fancybox my custom script

		// AOS library and my custom script
		//wp_enqueue_script('aos-lcms', esc_url( get_stylesheet_directory_uri() . '/3rdpartylibraries/aos/dist/aos.js' ), array('jquery'), '', true);  // aos - animate on scroll library
		//wp_enqueue_script('custom-aos-lcms', esc_url( get_stylesheet_directory_uri() . '/js/lcms-custom.js' ), array('aos-lcms'), '', true);   // fancybox my custom script

		// Particles JS script
		//wp_enqueue_script('particles-lcms', esc_url( get_stylesheet_directory_uri() . '/js/particles/particles.min.js' ), array(), '', true);
		//wp_enqueue_script('particles-app-lcms', esc_url( get_stylesheet_directory_uri() . '/js/particles/app.js' ), array(), '', true);

		// Bootstrap JS script
		//if ( is_page( array('edit-client-experience-post') ) ) { wp_enqueue_script('bootstrap5-lcms', esc_url( plugin_dir_url( __FILE__ ) . 'libraries/bootstrap/js/bootstrap.min.js' ), array(), '', true); }

}




// *** INCLUDE THE CODE PAGES ***
// this plugin's files
// NOTE USED - require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'edit-experience-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'add-experience-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'display-experience-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'search-experience-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'search-experience-shortcode-by-premium.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'list-my-experiences-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'list-premium-members-websites-marketing-page-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'admin-approve-experience-shortcode.php');
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'fun-facts-shortcode.php');

// 3rd party plugin - lcms customization files
//require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'pmpro-custom'. DIRECTORY_SEPARATOR .'pmpro-custom.php');	// Add custom pmpro fields function
require_once( plugin_dir_path(__FILE__) . 'shortcodes'. DIRECTORY_SEPARATOR .'ultimatemember-custom'. DIRECTORY_SEPARATOR .'ultimatemember-custom.php');	// Add custom pmpro fields function


// *** THIS WAS TEMPORARY SHORTCODE CODE TO REMOVE THE 30000 DUPLICATED 'APPROVE EXPERIENCE' PAGES CREATED - BUG IN CODE - SEEMS OK NOW
//add_shortcode('lcms_delete_duplicate_pages', 'lcms_delete_duplicate_pages'); 
/*function lcms_delete_duplicate_pages( $atts ) {

	global $wpdb;

	//$readquery = 0;
	$tableprefix = $wpdb->prefix;
	$sql = $wpdb->prepare(
		'
			SELECT *
			FROM '.$tableprefix.'posts
			WHERE post_title = %s
		',
			'Approve Experience'
	);
	$readquery = $wpdb->get_results( $sql );
	$html = 'Read: '.$wpdb->num_rows.'<br>';

	//$wpdb->flush();	// clear the results cache


	//$deletequery = 0;
	$tableprefix = $wpdb->prefix;
	$deletesql = $wpdb->prepare(
		'
			DELETE
			FROM '.$tableprefix.'posts
			WHERE post_title = %s
		',
			'Approve Experience'
	);
	//$deletequery = $wpdb->query( $deletesql );
	//$html .= 'Delete: '.$deletequery.'<br>';

	$wpdb->flush();	// clear the results cache

	return $html;
}*/


// ** Check if logged in, get current logged in user wordpress ID
function lcms_return_business_categories_to_um_plugin() {

    global $wpdb;

    // POPULATE DROPDOWN FIELD FROM "TERMS" AND "PLACE DETAIL" TABLES
    // DEV NOTE: Below 'and not' statements are temporary. I need to rather look at the 'post_type' value to filter out non-geodir category post types (such as categories in the below 'and not' statements)
    $tableprefix = $wpdb->prefix;
    $sql = $wpdb->prepare(
        '
            SELECT DISTINCT
             '.$tableprefix.'terms.term_id,
             '.$tableprefix.'terms.name
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

    $wpdb->flush(); // clear the results cache

	// Format the array to specification required by UM plugin (custom dropdown field 'member_business_category'), using the term_id/name as key/value (no array index permitted in array for UM plugin)
	foreach ( $categories_results as $category ) {
		$categories_formatted[$category->term_id] = $category->name;
	}

    return $categories_formatted;
}

/**
 * Proper way to enqueue scripts and styles
 */
add_action( 'wp_enqueue_scripts', 'wpdocs_theme_name_scripts' );
function wpdocs_theme_name_scripts() {
    wp_enqueue_style('bootstrap5-lcms', plugin_dir_url( __FILE__ ) . 'libraries/bootstrap/css/bootstrap.min.css' ); // Bootstrap 5 css - in header
    wp_enqueue_script('bootstrap5-lcms', esc_url( plugin_dir_url( __FILE__ ) . 'libraries/bootstrap/js/bootstrap.min.js' ), array(), '', true);	// Bootstrap 5 js - in footer
}



// Load language translation capability
add_action( 'init', 'lcms_wpdocs_load_textdomain' );
function lcms_wpdocs_load_textdomain() {
    load_plugin_textdomain( 'lcms-geodirectory-custom-functions', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

// Display custom hamburger menu icon if menu item created is named "hamburgermenu"
add_filter( 'wp_nav_menu_objects' , 'lcms_wp_nav_menu_objects' , 10, 2 );
function lcms_wp_nav_menu_objects( $navitems, $args ) {

	foreach ( $navitems as $val ) {
		if ( $val->post_name == 'hamburgermenu' )	{
			$val->title = __('
			<style>
				li.menu-item-4636 span.sub-arrow {display: none;}
			</style>
			<i class="fa fa-bars custom-hamburger-menu" aria-hidden="true"></i>', 'lcms-geodirectory-custom-functions');
		}
	}
    return $navitems;
}




// ************************
// ADMIN MAINTENANCE FUNCTION ONLY: find all records with search term
// ************************
//add_shortcode('lcms_admin_find_keyword', 'lcms_admin_find_keyword'); 
function lcms_admin_find_keyword( $atts ) {

    // Things that you want to do. 
	$a = shortcode_atts( array(
	        //'category' => 'uncategorized',
	        'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
	    ), $atts );

	
	//var_dump($_POST);

	// delete posts with ids from $postids array below
	if ( $_POST && isset($_POST['delete_submit']) ) {
		echo 'delete button was pressed';
		array_pop($_POST);	// REMOVE SUBMIT BUTTON FROM POST ARRAY (last array element)

		// get only post id's into a new array for use in delete function
		$postids = array();
		foreach ( $_POST as $key => $val ) {
			array_push($postids, $val);		// add only the post id value to array
		}
		echo '<pre>'; var_dump($postids); echo '</pre>';

		/*$tableprefix = $wpdb->prefix;
		wpdb::delete(
		    '.$tableprefix.'geodir_gd_place_detail', // table to delete from
		    array(
		        'post_id' => 123 // value in column to target for deletion
		    ),
		    array(
		        '%d' // format of value being targeted for deletion
		    )
		);*/
	}

	global $wpdb;

	$tableprefix = $wpdb->prefix;
	$sql = $wpdb->prepare(
	    "
	        SELECT
	         post_id,
	         clients_experience
	        FROM '.$tableprefix.'geodir_gd_place_detail
	        WHERE post_status = %s AND (clients_experience LIKE %s OR clients_experience LIKE %s OR clients_experience LIKE %s)
	      	ORDER BY post_id
	    ",
	    	'publish',
        	'%'.$wpdb->esc_like('sorry').'%',
        	'%'.$wpdb->esc_like('apologize').'%',
        	'%'.$wpdb->esc_like('apologies').'%'
	);
	$search_results = $wpdb->get_results( $sql );

	echo $wpdb->num_rows;

	?>

  <form action="" method="POST">

	<?php foreach ( $search_results as $result ) { ?>
		<table>
			<tr>
				<td style="padding:20px;">
    			<input style="width: 25px; height: 25px;" type="checkbox" name="form_post_id-<?= $result->post_id ?>" value="<?= $result->post_id ?>">
    			</td>
				<td>
    			<p><?= nl2br($result->clients_experience); ?></p>
    			</td>
    		</tr>
    	</table>
	<?php } ?>

    <input type="submit" name="delete_submit" value="Delete">

  </form>

	<?php
	//echo '<pre>'; var_dump($search_results); echo '</pre>';

	$wpdb->flush();	// clear the results cache
	
    //return "foo = {$a['foo']}";
    //return "<p>User count is {$user_count}</p>";
    //return $html;
}

// *** ADD CODE TO HEADER ***
add_action( 'wp_head', function(){
	?>
	<style>
		.geodir-search-form-wrapper .gd-search-field-near{
			display:none;
		}
		.geodir-search-form-wrapper  .geodir-search .flex-grow-1 {
			display:none;
		}
		#geodir-add-listing-submit .geodir_preview_button {
			display:none;
		}
		.geodir-post-rating {
			display:none;
		}
		.client-experience-field {
			margin-top: 20px;
		}
		.geodir_post_meta_title {
			font-weight:bold;
		}
		.geodir-field-clients_experience .geodir_post_meta_title {
			font-size: 1.3rem;
		}
		.bsui .btn-primary, button.bsui.btn-primary {
			background-color:#ed3439;
			border-color:#ed3439;
		}
		.bsui .btn-primary:hover, button.bsui.btn-primary:hover {
			background-color:#ffffffff;
			color: #ed3439;
			border: 1px solid #ed3439;	
		}
		#geodirectory-add-post [data-argument="default_category"] {
			display: none;
		}

		/* lcms plugin */
		button.lcms-button {background-color:#ed3439;}

	</style>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
		$('div.geodir-field-clients_experience p').each(function() {
		    var me = $(this);
		    me.html( me.text().replace('Client review:','<strong>Client review:</strong><br>').replace('Business response:','<strong>Business response:</strong><br>') );
		});
	});
	</script>

	<!--<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>-->

	<?php 
});

// *** ADD CODE TO FOOTER ***
/*add_action( 'wp_footer', function(){
	?>
		<!--<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>-->
	<?php 
});*/

// THIS IS TO CLEAR BUFFER FOR REDIRECT IN CATEGORY DROPDOWN, BECAUSE HEADERS ALREADY SENT
// Resource: https://tommcfarlin.com/wp_redirect-headers-already-sent/
function app_output_buffer() {
	ob_start();
} // soi_output_buffer
add_action('init', 'app_output_buffer');



// *** 3RD PARTY PLUGIN CUSTOM CODE BELOW ***


// *** FILTER HOOK FOR ADD LISTING (CUSTOMER) PAGE AND MODIFY A SPECIFIC CUSTOM FIELD'S HTML ***
// Page where hook is: geodirectory/includes/custom-fields/functions.php
function gd_custom_field_input__type_fieldname( $html, $cf ) {
	// resource: https://wpgeodirectory.com/support/topic/edit-add-new-listing-form/

	$html_var = $cf['htmlvar_name'];
	//echo '<pre>'; var_dump($cf); echo '</pre>';

	// ADD LINK TO UPGRADE TO PREMIUM HERE
	$link = '<a href="'.get_site_url().'/membership-account/membership-checkout/?level=3">' . __( 'Upgrade to Premium for this feature', 'geodirectory' ) . '</a>';
	
	// MEMBERSHIP CHECK
	// Resource: https://www.paidmembershipspro.com/documentation/content-controls/require-membership-function/#
	if( pmpro_hasMembershipLevel(array('3','Premium')) ) {
		$membership_level = 'premium';
	} else {
		$membership_level = 'other';
	}

	ob_start();
	?>
	<div data-argument="<?php echo $cf['name'];?>" class="form-group row" data-rule-key="<?php echo $cf['name'];?>" data-rule-type="<?php echo $cf['type'];?>">
		<label for="<?php echo $cf['name'];?>" class="  col-sm-2 col-form-label"><?php echo $cf['frontend_title'];?></label>
		<div class="col-sm-10">
			<input <?php if( $membership_level == 'other' && !current_user_can( 'manage_options' ) ) {echo 'disabled';} ?> type="<?php echo $cf['type'];?>" name="<?php echo $cf['htmlvar_name'];?>" id="<?php echo $cf['htmlvar_name'];?>" class="form-control " field_type="<?php echo $cf['type'];?>">
			<?php if( $membership_level == 'other' && !current_user_can( 'manage_options' ) ) { ?>
				<span class="geodir_message_upgrade"><?php echo $link; ?></span>
			<?php } ?>
			<span class="geodir_message_note"><?php _e($cf['desc'], 'geodirectory');?></span>
			<?php if ($cf['is_required']) { ?>
				<span class="geodir_message_error"><?php _e($cf['required_msg'], 'geodirectory'); ?></span>
			<?php } ?>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
add_filter( 'geodir_custom_field_input_url_website', 'gd_custom_field_input__type_fieldname', 10, 2 );



// *** FILTER HOOK TO CHANGE THE 'SUBMIT LISTING' BUTTON TEXT - ADD CLIENT PAGE ***
// Page where hook is: geodirectory/includes/class-geodir-post-data.php
function geodir_change_submit_btn_text() {
	$submittext = __('Submit Customer', 'geodirectory');
	return $submittext;
}
add_filter( 'geodir_add_listing_btn_text', 'geodir_change_submit_btn_text' );


// *** FILTER HOOK TO CHANGE THE 'PREVIEW LISTING' BUTTON TEXT - ADD CLIENT PAGE ***
// Page where hook is: geodirectory/includes/class-geodir-post-data.php
function geodir_change_preview_btn_text( $listing_type, $post, $package ) {
	$package = 'Preview Client';
	//var_dump($listing_type);
	return $package;
}
//add_filter( 'geodir_add_listing_form_end', 'geodir_change_preview_btn_text', 10, 3 );
// ### MARIO: THERE IS NO FILTER HOOK FOR PREVIEW BUTTON - NEED ANOTHER SOLUTION ###


// === TEST FUNCTIONS ===

//add_shortcode('say_hello', 'say_hello_func');
function say_hello_func() {
    // display an informational message
    return '<p style="margin-left:20px;margin-right:20px;color:orange;font-size:15px !important;line-height:1.3rem;">THIS IS A CUSTOM FUNCTION TEST</p>';
    //return site_url();
    //return get_site_url();
}

// function that runs when shortcode is called
//add_shortcode('greeting', 'wpb_demo_shortcode'); 
function wpb_demo_shortcode( $atts ) { 
    // Things that you want to do. 
$a = shortcode_atts( array(
        'foo' => 'something',
        'bar' => 'something else',
    ), $atts );

    return "foo = {$a['foo']}";
}


// Method: Filter content in main query to randomize post order
//add_filter( 'pre_get_posts', 'thefunction', 10);
function thefunction($query) {
	// Resources:
	// https://pippinsplugins.com/playing-nice-with-the-content-filter/
	// https://wordpress.stackexchange.com/questions/402521/how-do-i-display-main-query-posts-in-random-order-using-add-filter

	//if ( is_singular() && is_main_query() ) {	// only do for main content area - not sidebars, footers etc.
	//	$content = '<h3>hello</h3>'.$content;
	//}

	//var_dump($content);

/*
// FIRST METHOD - NEEDS WORKING ON
    $args = array(
        'post_type' => 'post',
        'orderby' => 'rand'
    );

	$mario_query = new  WP_Query( $args );
	query_posts('post_type=post');

	if ( have_posts() ) {

		while ( have_posts() ) {

			the_post();

		}

	} else { echo '<div style="margin: 15px 20px 0px 20px;">error: no posts retrieved.</div>'; }

	wp_reset_postdata();
*/

// SECOND METHOD - WHICH WORKS!
	/*
    //if ( ! is_admin() && $query->is_main_query() ) {
    if ( ! is_admin()  ) {
        // Not a query for an admin page.
        // It's the main query for a front end page of your site.
 
            // Let's change the query using arguments.
            $query->set( 'orderby', 'rand' );

    }

	return $query;
	*/
}


// RANDOMIZE POST LOOP: function that runs when shortcode is called - THIS HAS NO FORMATTING, IN AN ELEMENTOR PAGE THOUGH
//add_shortcode('lcms_random_posts', 'lcms_random_posts_order'); 
function lcms_random_posts_order( $atts ) {
	    // organise passed shortcode atrributes array. 
		$a = shortcode_atts( array(
		        //'category' => 'uncategorized',
		        'category'    => isset($_GET['category']) ? sanitize_key($_GET['category']) : 'uncategorized',
		), $atts );


		// HOW TO DO A CUSTOMIZED POST LOOP TO SUITE ANY OCCASION (ie. where a post/archive page template is not used )
		// RESOURCES:
		// https://developer.wordpress.org/reference/classes/wp_query/
		// https://developer.wordpress.org/reference/functions/the_post/
		// FOR A LIST OF WP_QUERY ARGUMENTS: https://www.billerickson.net/code/wp_query-arguments/



// **** CHECK THIS RESOURCE FOR RANDOMIZING:
		// https://stackoverflow.com/questions/19570119/select-a-random-post-without-repeat
		// https://stackoverflow.com/questions/3641871/randomly-order-posts-on-wordpress-loop


// >>>>>> THIS SOLUTION!!! https://wordpress.stackexchange.com/questions/84864/filter-the-content-custom-post-type-and-wp-query


// **** SEE IF ORDERBY => 'RAND' CAN BE USED IN THE NORMAL THE_CONTENT FILTER

		// set the parameters for the class instance below to filter 'post' records you want returned
	    $args = array(
	        'post_type' => 'post',
	        'orderby' => 'rand'
	    );

		$mario_query = new  WP_Query( $args );

		if ( $mario_query->have_posts() ) {

			while ( $mario_query->have_posts() ) {


				$mario_query->the_post();

				/*if( has_post_thumbnail() ) {
					echo '<a class="entry-image-link" href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a>';
				}*/

				?><div style="margin: 15px 20px 0px 20px;"><?php the_title(); echo ' - '; the_id(); ?></div> <?php

			    ?><div style="padding: 5px 10px; margin: 0px 20px; border: solid 1px;"><?php the_content(); ?></div> <?php

			}

		} else { echo '<div style="margin: 15px 20px 0px 20px;">error: no posts retrieved.</div>'; }

		wp_reset_postdata();

}





//NEW CODE ADDED BY SHAKIL

add_action('wp_ajax_ajax_function_for_image_uploading', 'ajax_function_for_image_uploading_function');
add_action('wp_ajax_nopriv_ajax_function_for_image_uploading', 'ajax_function_for_image_uploading_function');


function ajax_function_for_image_uploading_function()
{
    $attachment_url = '';
    if (isset($_FILES['exp_edit_client_attachement']) && isset($_POST['experience_post_id'])) {
        if (!empty($_FILES['exp_edit_client_attachement']['tmp_name'])) {
            //Image Upload Functions

            if (!function_exists('wp_generate_attachment_metadata')) {
                require_once(ABSPATH . '/wp-admin/includes/image.php');
                require_once(ABSPATH . '/wp-admin/includes/file.php');
                require_once(ABSPATH . '/wp-admin/includes/media.php');
            }

            $postId = $_POST['experience_post_id'];
            $image = $_FILES['exp_edit_client_attachement']['tmp_name'];
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

            $attachment_url = $wp_upload_dir['url'] . '/' . basename($fileurl);
        }
    }

    echo $attachment_url;
    wp_die();
}


//EDITABLE FIELDS ON UM PAGE

add_action('um_after_account_general', 'showUMExtraFields', 100);

global $ultimatemember;

function showUMExtraFields() {

	$id = um_user('ID');
	$output = '';
	$names = array('Zip_Code', 'user_url', 'cityState', 'member_business_category');
  
	$fields = array(); 
	foreach( $names as $name )
	  $fields[ $name ] = UM()->builtin()->get_specific_field( $name );
	$fields = apply_filters('um_account_secure_fields', $fields, $id);
	$output = '<style>
				/* Chrome, Safari, Edge, Opera */
			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
			}

			/* Firefox */
			input[type=number] {
			-moz-appearance: textfield;
			}

			#Zip_Code{
				padding: 0 12px !important;
				width: 100%;
				display: block !important;
				-moz-border-radius: 2px;
				-webkit-border-radius: 2px;
				border-radius: 2px;
				outline: none !important;
				cursor: text !important;
				font-size: 15px !important;
				height: 40px !important;
				box-sizing: border-box !important;
				box-shadow: none !important;
				margin: 0 !important;
				position: static;
				outline: none !important;
			}
		</style>
		<script>
		jQuery(document).ready(()=>{jQuery(`input[id="Zip_Code"]`).attr("maxlength","5"); jQuery(`input[id="Zip_Code"]`).attr("type","number"); jQuery(`input[id="Zip_Code"]`).attr("onKeyPress", "if(this.value.length==5) return false;")})
		</script>';
	foreach( $fields as $key => $data )
	  $output .= UM()->fields()->edit_field( $key, $data );
  
	echo $output;
}

add_action('um_account_pre_update_profile', 'getUMFormData', 100);

function getUMFormData(){
  $id = um_user('ID');
    $names = array('Zip_Code', 'user_url', 'cityState', 'member_business_category');

  foreach( $names as $name )
    update_user_meta( $id, $name, $_POST[$name] );
}



// === END: TEST FUNCTIONS ===
