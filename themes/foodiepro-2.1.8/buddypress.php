<?php


/* =================================================================*/
/* =                 LAYOUT
/* =================================================================*/

// Apply Full Width Content to registration page
// add_action( 'bp_loaded', 'bp_register_set_full_layout' );
// function bp_register_set_full_layout() {
// 	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
// }

// Social before content (Cover Image) widgeted area
add_action( 'genesis_before_content_sidebar_wrap', 'add_before_content_area');
function add_before_content_area() {
	if (bp_is_register_page()) return;
	genesis_widget_area( 'social-before-content', array(
	   'before' => '<div class="top before-content widget-area" id="buddypress">',
	   'after'  => '</div>',
	));  
}

// Replace primary sidebar with social sidebar
add_action('get_header','social_sidebar');

function social_sidebar() {
	// remove the default genesis primary sidebar
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
	// no sidebar at all on registration page
	if (bp_is_register_page()) return;
	// add an action hook to call the function for your custom sidebar
	add_action( 'genesis_sidebar', 'foodiepro_do_social_sidebar' );
}

// Display the social sidebar
function foodiepro_do_social_sidebar() {
	//get_sidebar( 'social' );
	dynamic_sidebar( 'social-sidebar' );
}

// Social Bottom sidebar
add_action( 'genesis_after_content', 'add_social_bottom_area');
function add_social_bottom_area() {
  if ( bp_is_user() && (bp_current_action()=='public') ) {
  	//echo bp_current_action();
	  genesis_widget_area( 'social-bottom', array(
	      'before' => '<div class="bottom after-content social-bottom widget-area">',
	      'after'  => '</div>',
		));
	}
}


/* =================================================================*/
/* =                 BREADCRUMBS
/* =================================================================*/


/* Remove the breacrumbs
/**
 * I used genesis_before, but you can also use get_header or other hooks as long
 * as you call the check function prior to the breadcrumbs being called.
 */
add_action( 'genesis_before', 'wps_remove_genesis_breadcrumbs' );
function wps_remove_genesis_breadcrumbs() {
    remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
}

//* Remove the entry title (requires HTML5 theme support)
add_action( 'genesis_before', 'wps_remove_genesis_entry_header' );
function wps_remove_genesis_entry_header() {
	remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
}




genesis();
