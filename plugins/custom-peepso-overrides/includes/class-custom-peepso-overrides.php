<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Peepso_Overrides {
	
	public function __construct() {	

		$Customizations = new CPO_Customizations();
		// Adds author avatar and link to profile, to post meta information under the post headline
		add_filter( 'peepso_postbox_message', 			array($Customizations, 'custom_postbox_message') );		
		add_filter( 'peepso_blogposts_post_types', 		array($Customizations, 'blogposts_custom_post_types') );

		/* Integrate recipe post type to Peepso activity stream (blogposts module)  */
		add_action( 'publish_recipe', 					array($Customizations, 'blogposts_publish_recipe'), 1, 2 );		
        add_filter( 'peepso_activity_stream_action', 	array($Customizations, 'publish_recipe_activity_stream_action'), PHP_INT_MAX, 2);

		

		$Shortcodes = new CPO_Shortcodes();
		add_shortcode('peepso-user-avatar', 			array($Shortcodes,'get_user_avatar_shortcode'));
		// add_shortcode('peepso-user-field', 				array($Shortcodes,'get_user_field_shortcode'));

	}

}
