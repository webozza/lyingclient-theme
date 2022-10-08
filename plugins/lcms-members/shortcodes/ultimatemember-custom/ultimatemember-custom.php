<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * ULTIMATE MEMBER - CUSTOM FUNCTIONS INCLUDE
 */


/* Add a new Profile tab
* @param array $tabs
* @return array
*/
// Resource: https://wordpress.org/support/topic/how-to-retrieve-user-data-from-ultimate-member-plugin/


//This code is added by shakil for password show/hide


add_filter("um_confirm_user_password_form_edit_field","um_user_password_form_edit_field", 10, 2 );
add_filter("um_user_password_form_edit_field","um_user_password_form_edit_field", 10, 2 );
function um_user_password_form_edit_field( $output, $set_mode ){
    
    ob_start();
     ?>
    <div id='um-field-show-passwords-<?php echo $set_mode;?>' style='text-align:right;display:block;'>
    	<i class='um-faicon-eye-slash'></i>
    	<a href='#'><?php _e("Show password","ultimate-member"); ?></a>
    </div>
    <script type='text/javascript'>
	    jQuery('div[id="um-field-show-passwords-<?php echo $set_mode;?>"] a').click(function(){ 
		 
            var $parent = jQuery(this).parent("div"); 
            var $form = jQuery(".um-<?php echo $set_mode;?> .um-form");

		    $parent.find("i").toggleClass(function() {
		    	if ( jQuery( this ).hasClass( "um-faicon-eye-slash" ) ) {
	                $parent.find("a").text('<?php _e("Hide password","ultimate-member"); ?>');
		    		jQuery( this ).removeClass( "um-faicon-eye-slash" )
		    		$form.find(".um-field-password").find("input[type=password]").attr("type","text");
		    	   return "um-faicon-eye";
			    }
				 
				jQuery( this ).removeClass( "um-faicon-eye" );
				$parent.find("a").text('<?php _e("Show password","ultimate-member"); ?>');
			    $form.find(".um-field-password").find("input[type=text]").attr("type","password");
			  
                return "um-faicon-eye-slash";
			});

		    return false; 

		});
	</script>
    <?php 
	return $output.ob_get_clean();

}
// This code ended here

// **** NOTE: BELOW IS A SAMPLE FROM UM - FOR REFERENCE ***
add_action( 'um_profile_content_mycustomtab_default', 'um_profile_content_mycustomtab_default' );
function um_myclientexperiencestab_add_tab( $tabs ) {

/**
* You could set the default privacy for custom tab.
* There are values for ‘default_privacy’ atribute:
* 0 – Anyone,
* 1 – Guests only,
* 2 – Members only,
* 3 – Only the owner
*/
$tabs[ 'mycustomtab' ] = array(
'name' => 'My Client Experiences',
'icon' => 'um-faicon-list-ul',
'default_privacy' => 2,
);

UM()->options()->options[ 'profile_tab_' . 'mycustomtab' ] = true;

return $tabs;
}
add_filter( 'um_profile_tabs', 'um_myclientexperiencestab_add_tab', 1000 );

/**
* Render tab content
* @param array $args
*/
function um_profile_content_mycustomtab_default( $args ) {

	// display the content of dbase shortcode
	echo do_shortcode( '[dbase_list_my_client_experiences]' );

}