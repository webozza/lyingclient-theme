<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * PAID MEMBERSHIP PRO - CUSTOM FUNCTIONS INCLUDE
 */



/**
 * Add custom fields to Paid Memberships Pro checkout page.
 * Must have PMPro & Register Helper Add On installed and activated to work.
 * Add this code to a PMPro Customizations Plugin or Code Snippets plugin.
 */
// RESOURCE: https://www.paidmembershipspro.com/register-helper-a-step-by-step-guide-on-creating-custom-fields/

// *** disabled this because Ultimate member plugin was implemented by Sakib ***
//add_action( 'init', 'dbase_pmpro_add_fields_to_checkout' );
function dbase_pmpro_add_fields_to_checkout(){
	//don't break if Register Helper is not loaded
	if(!function_exists( 'pmprorh_add_registration_field' )) {
		return false;
	}
	
	$fields = array();

	$fields[] = new PMProRH_Field(
		'member_name',					// input name, will also be used as meta key
		'text',						// type of field
		array(
			'label'		=> 'Your Name',		// custom field label
			'profile'	=> true,		// show in user profile
			'required'	=> true,		// make this field required
		)
	);
	$fields[] = new PMProRH_Field(
		'member_business_name',					// input name, will also be used as meta key
		'text',						// type of field
		array(
			'label'		=> 'Your Business Name',		// custom field label
			'profile'	=> true,		// show in user profile
			'required'	=> true,		// make this field required
		)
	);
	$fields[] = new PMProRH_Field(
		'member_website',					// input name, will also be used as meta key
		'text',						// type of field
		array(
			'label'		=> 'Your Business Website',		// custom field label
			'profile'	=> true,		// show in user profile
			'required'	=> true,		// make this field required
		)
	);
	$fields[] = new PMProRH_Field(
		'member_telephone',					// input name, will also be used as meta key
		'text',						// type of field
		array(
			'label'		=> 'Your Contact Number',	// custom field label
			'required'	=> false,	// make this field required
			'profile'	=> true,	// show in user profile
		)
	);

	
	//add the fields to default forms
	foreach($fields as $field){
		pmprorh_add_registration_field(
			'checkout_boxes', // location on checkout page
			$field	// PMProRH_Field object
		);
	}
}