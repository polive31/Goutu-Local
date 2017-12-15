<?php


/* =================================================================*/
/* =                 LAYOUT
/* =================================================================*/

// Apply Full Width Content layout
add_action( 'get_header', 'bp_set_full_layout' );
function bp_set_full_layout() {
	// Force full width content
	add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
}

// Add widgeted areas
add_action( 'bp_after_member_home_content', 'get_social_sidebar' );
function get_social_sidebar() {
  get_sidebar( 'social' );
}

add_action( 'genesis_after_loop', 'add_social_bottom_area');
function add_social_bottom_area() {
  if ( bp_is_user() && (bp_current_action()=='public') ) {
  	//echo bp_current_action();
	  genesis_widget_area( 'social-bottom', array(
	      'before' => '<div class="social-bottom widget-area">',
	      'after'  => '</div>',
		));
	}
}

/* Workaround for shortcodes in rpwe "after" html not executing 
in social-bottom widgeted area  */
add_filter( 'rpwe_markup', 'add_more_from_author_link');
function add_more_from_author_link($html) {
	$user_id=bp_displayed_user_id();
	$name=bp_core_get_username($user_id);	
  if ( bp_is_user() && (bp_current_action()=='public') ) {
		$html.='<p class="more-from-category">'; 
		$html.='<a href="/author/' . $name . '">Toutes les recettes de ' . $name . '→</a>';
		$html.='</p>';
	}
	return $html;
}


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
