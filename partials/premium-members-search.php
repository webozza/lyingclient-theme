<?php 
	/* Template Name: Premium Member Club */
	get_header();
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div id="premium-members-club">
		<h1>Premium Member Club</h1>
			
			
<?php echo do_shortcode("[ihc-list-users num_of_entries ='10' entries_per_page ='100' order_by ='user_registered' order_type ='desc' filter_by_level ='1' levels_in ='3' user_fields ='city,businessnameump,businesscategoryump,zip,user_url' theme ='ihc-theme_8' color_scheme ='ee3733' columns ='5' align_center ='1' show_search ='1' search_by ='businesscategoryump,businessnameump,phone,zip,city,user_url' show_search_filter ='1' search_filter_items ='businesscategoryump' general_pagination_theme ='ihc-listing-users-pagination-1' pagination_pos ='top' ]");?>
			
			
			

		</div>
	</main>
</div>

<script src=''. get_stylesheet_template_uri() . '/js/' .''></script>
<?php get_footer(); ?>