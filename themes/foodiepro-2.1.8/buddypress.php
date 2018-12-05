<?php


/* =================================================================*/
/* =                 HEADLINE 
/* =================================================================*/

// Provide the page headline on some buddypress pages
add_action( 'genesis_entry_header', 'buddypress_page_title' );
function buddypress_page_title() {
	if ( bp_is_members_directory() ) {
		echo '<h1>' . get_the_title() . '</h1>';
	}
}

/* =================================================================*/
/* =                 CONTENT 
/* =================================================================*/

// Provide the page content on some buddypress pages
// add_action( 'genesis_before_content', 'buddypress_page_content' );
// add_action( 'bp_before_directory_members_page', 'buddypress_page_content' );
// add_action( 'bp_before_member_home_content', 'buddypress_page_content' );
// add_action( 'bp_before_register_page', 'buddypress_page_content' );
// add_action( 'bp_before_activation_page', 'buddypress_page_content' );
function buddypress_page_content( $post_id = 0, $more_link_text = null, $stripteaser = false ) {
	global $post;
	$result = bp_current_component();

	$page_array = get_option( 'bp-pages' );

	$post_id = $page_array[ $result ];

	$post = get_post( $post_id );

	setup_postdata( $post, $more_link_text, $stripteaser );
	the_content();
	wp_reset_postdata( $post );
}



/* =================================================================*/
/* =                 SIDEBARS
/* =================================================================*/

// Social before content (Cover Image) widgeted area
add_action( 'genesis_before_content_sidebar_wrap', 'add_before_content_area');
function add_before_content_area() {
	if ( bp_is_my_profile() || bp_is_user_profile() ) {
		genesis_widget_area( 'social-before-content', array(
		   'before' => '<div class="top before-content widget-area" id="buddypress">',
		   'after'  => '</div>',
		));  
	}
}

// Specific sidebar logic on Buddypress pages
add_action('get_header','buddypress_sidebar');
function buddypress_sidebar() {
	// This function removes the default genesis primary sidebar
	// and ensures that there is no sidebar at all if registration page
	// then enables the social sidebar callback
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
	if (bp_is_register_page()) return;
	add_action( 'genesis_sidebar', 'foodiepro_do_social_sidebar' );
}

// Display the social sidebar
function foodiepro_do_social_sidebar() {
	if ( bp_is_my_profile() || bp_is_user_profile() ) {
		//get_sidebar( 'social' );
		dynamic_sidebar( 'social-sidebar' );
	}
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
