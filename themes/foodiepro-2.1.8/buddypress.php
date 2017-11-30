<?php


/* =================================================================*/
/* =                 WIDGETED AREAS
/* =================================================================*/

// Apply Full Width Content layout
add_action( 'get_header', 'bp_set_full_layout' );
function bp_set_full_layout() {
	// Force full width content
	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
}


// Add a "social" sidebar besides the profile page 
add_action( 'bp_after_member_home_content', 'get_social_sidebar' );
function get_social_sidebar() {
  get_sidebar( 'social' );
}


//* Remove the breacrumbs
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
