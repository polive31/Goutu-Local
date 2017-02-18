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
/* =               BATCH UPDATE POST META
/* =================================================================*/

function ajax_batch_manage_meta() {
		
		
	// Shortcode parameters display	
	
	$key = get_ajax_arg('key');
	$new_key = get_ajax_arg('new-key');
	$post_type = get_ajax_arg('post-type');
	$include = get_ajax_arg('include',__('Limit to posts'));
	$value = get_ajax_arg('value');
	$cmd = get_ajax_arg('cmd');
	
	$nonce_check = check_ajax_referer( 'ManageMeta' . $cmd, false, false );
	if ( ! $nonce_check ) {
		echo 'Security check failed, script stopped';
		exit;
	}
			
	if ( is_array($value) )
		$value = extractKeyValuePairs( $value );
	else
		$value = $a['value'];

	//PC:debug( array('$value after explode : '=>$value) );
		
	echo "<p>Batch Manage Meta script started...</p>";

	//$post_type_object = get_post_type_object($post_type);
	//$label = $post_type_object->label;

	if ( $cmd=='delete' && empty($include) ) {
		echo "Please provide post IDs or 'all' for deletion to take place";
		return '';
	}
	$include = ($include=='all')?'':$include;

	$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

	foreach ($posts as $post) {

	  $meta_value = get_post_meta($post->ID, $key, True);
	  switch ($cmd) {
	  	case 'add':
	  		if ( empty($meta_value) ) add_post_meta($post->ID, $key, $value);
	  		echo sprintf("%s=%s added to %s",$key,$value,$post->post_title) . "<br>"; //Prints updated after ran.
	  		break;
	  	case 'rename':
	  		if ( ! empty($meta_value) ) {
	  			update_post_meta($post->ID, $new_key, $meta_value);
	  			delete_post_meta($post->ID, $key);
	  			echo sprintf("%s renamed to %s in %s",$key,$new_key,$post->post_title) . "<br>"; //Prints updated after ran.
	  		}
	  		else {
	  			update_post_meta($post->ID, $new_key, '0');
	  			echo sprintf("Key %s not found in %s. Updated %s to '0'.",$key, $post->post_title, $newkey);
	  		}
				$new_value = get_post_meta($post->ID, $new_key, True);
				PC::debug(array('Value for renamed key' => $new_value));
	  		break;
	  	case 'replace':
	  		update_post_meta($post->ID, $key, $value);
	  		echo sprintf("%s updated to %s in %s",$key,$value,$post->post_title) . "<br>"; //Prints updated after ran.
	  		break;
	  	case 'delete':
  			echo sprintf("%s value for post %s : ",$key,$post->post_title); //Prints updated after ran.
  			print_r($meta_value);
  			echo "<br>";
	  		if ( ! empty($meta_value) ) {
		  		delete_post_meta($post->ID, $key);
		  		$meta_value = get_post_meta($post->ID, $key, True);
		  		echo sprintf("%s key value after deletion : %s",$key,$meta_value) . "<br>"; //Prints updated after ran.
	  		}
	  		break;
	  	case 'read':
  			echo sprintf("%s value for post %s :",$key,$post->post_title); //Prints updated after ran.
  			print_r($meta_value);
  			echo "<br>";
	  		break;
	  }
	}
}
