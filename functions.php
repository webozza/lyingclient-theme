<?php
/**
 * CUSTOM CHILD THEME
 * Coded by @webozza - https://freelancer.com/u/webozza
 */  

function astra_child_style() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
	wp_enqueue_script( 'premium-members-club', get_stylesheet_directory_uri() . '/js/premium-members-club.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'astra_child_style' );