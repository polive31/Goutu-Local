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


//* Hook category widget area after profile content
//add_action( 'bp_public_profile_content', 'add_social_widgeted_area' );
function add_social_widgeted_area() {
	//if (!bp_is_register_page())
    genesis_widget_area( 'social-bottom', array(
        'before' => '<div class="social-bottom widget-area">',
        'after'  => '</div>',
  	));
}

// Add a "social" sidebar besides the profile page 
add_action( 'bp_after_member_home_content', 'get_social_sidebar' );
function get_social_sidebar() {
  get_sidebar( 'social' );
}



genesis();
