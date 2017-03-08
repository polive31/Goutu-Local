<?php
/*
Description: Administrator shortcodes for Goutu
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =               BATCH MIGRATE POST RATINGS
/* =================================================================*/

function ajax_migrate_ratings() {
		
		
	// Shortcode parameters display	77
	
	PC::debug( array('In AJAX MIGRATE RATINGS'));
	
	$post_type = get_ajax_arg('post-type');
	$include = get_ajax_arg('include',__('Limit to posts'));
	
	if ( !(is_secure('MigrateRatings') ) ) exit;
			

	//PC:debug( array('$value after explode : '=>$value) );
		
	echo "<p>Batch Migrate Ratings script started...</p>";

	//$post_type_object = get_post_type_object($post_type);
	//$label = $post_type_object->label;


	$include = ($include=='all')?'':$include;

	$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

	foreach ($posts as $post) {

	  foreach (CustomStarRatings::ratingCats as $id=>$cat) {
	  	$cat_rating[$id]= get_post_meta($post->ID, 'user_rating_' . $id, True);
	  }
	  $cat_rating['global']= get_post_meta($post->ID, 'user_rating_global', True);
		PC:debug( array('$cat_ratings : %0'=> $cat_ratings) );
	  
	}
}
