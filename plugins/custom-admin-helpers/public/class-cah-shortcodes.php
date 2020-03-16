<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class CAH_Shortcodes {

	public function get_post_count( $atts ) {
		//Let's not loose time if user doesn't have the rights
		if( !current_user_can('editor') && !current_user_can('administrator') ) return;

	    $atts = shortcode_atts( array(
	        'status' => 'pending', //draft, publish, auto-draft, private, separated by " "
	        'type' => 'post', //recipe
	        'category_name' => '', //recipe
		), $atts );

		$status = explode(' ', $atts['status']);

		$args = array(
			// 'author' => 1, // user ID here
			'posts_per_page' => -1, // retrieve all
			'post_type' => $atts['type'],
			'category_name' => $atts['category_name'],// list of category IDs to be included, separate by commas
			// 'post_status' => 'any' // any status
			'post_status' => $status
		);

		$posts = get_posts( $args );
		$html = (count($posts)>0)?'<span class="post-count-indicator">('.count($posts).')</span>':'';

		return $html;
    }
}
