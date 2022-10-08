<?php
/**
 * CUSTOM CHILD THEME
 * Coded by @webozza - https://freelancer.com/u/webozza
 */  

function astra_child_style() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
	wp_enqueue_script( 'fetch-premium-member', get_stylesheet_directory_uri() . '/js/fetch-premium-member.js', array( 'jquery' ) );
	wp_enqueue_script( 'client-exp-search', get_stylesheet_directory_uri() . '/js/client-exp-search.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'astra_child_style' );