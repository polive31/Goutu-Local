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
	
	PC::debug( array('In AJAX MIGRATE RATINGS'));
	echo "<p>Batch Migrate Ratings script started...</p>";
	
	$post_type = get_ajax_arg('post-type');
	$include = get_ajax_arg('include',__('Limit to posts'));
	
	if ( !(is_secure('MigrateRatings' . 'migrate') ) ) exit;
			

	//PC:debug( array('$value after explode : '=>$value) );
		

	//$post_type_object = get_post_type_object($post_type);
	//$label = $post_type_object->label;

	$customRatings = new CustomStarRatings;

	$include = ($include=='all')?'':$include;

	$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

	foreach ($posts as $post) {

	  $user_ratings = get_post_meta($post->ID, 'recipe_user_ratings', false);
	  
	  $cats = $customRatings->ratingCats;
	  $cats[] = 'global';
	  
	  foreach ( $cats as $id=>$cat) {
	  	$cat_rating[$id]= get_post_meta($post->ID, 'user_rating_' . $id, True);
	  }

		PC::debug( array('$cat_ratings : %0'=> $cat_ratings) );
		PC::debug( array('$recipe user ratings: %0'=> $user_ratings) );
	  
	}
}
