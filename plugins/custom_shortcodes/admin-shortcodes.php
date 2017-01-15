<?php
/*
Plugin Name: Admin Shortcodes
Plugin URI: http://goutu.org/
Description: Administrator shortcodes for Goutu
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =               BATCH UPDATE USER RATINGS
/* =================================================================*/


/* Batch update user_ratings_ratings custom field */
function batch_update_meta() {
	
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'key' => 'recipe_user_ratings_rating', 
	), $atts );
	
	$key = $a['key'];
  $post_type = $a['post-type'];
		
	echo '<div class="clearfix">';
	echo "Batch Update Meta script started..." . "\n";
	
  $value = 0;
  
  $post_type_object = get_post_type_object($post_type);
  $label = $post_type_object->label;
  echo  "Updating all " . $label . "\n";
  $posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

  foreach ($posts as $post) {
    $meta_value = get_post_meta($post->ID, $key, True);
    if (empty($meta_value)){
    //$meta_value2 = media_process($meta_value1, $post->ID); //Returns a string after it finishes process.
    update_post_meta($post->ID, $key, $value);
    echo $post->post_title." UPDATED" . "\n"; //Prints updated after ran.
    }
  }
  echo '/div';

}

add_shortcode('batch-update-meta', 'batch_update_meta');


?>