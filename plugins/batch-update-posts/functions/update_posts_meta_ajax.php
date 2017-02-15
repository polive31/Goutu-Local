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


// Shortcode parameters display	
if ( isset($_POST['key']) ) {
	$key = $_POST['key'];
	echo sprintf("<b>Key</b> = %s",$key);
	echo "<br>";
}
	
if ( isset($_POST['new-key']) ) {
	$new_key = $_POST['new-key'];
	echo sprintf("<b>New Key</b> = %s",new_$key);
	echo "<br>";
}

if ( isset($_POST['post-type']) ) {
	$post_type = $_POST['post-type'];
	echo sprintf("<b>Post type</b> = %s",$post_type);
	echo "<br>";
}
	
if ( isset($_POST['include']) ) {
	$include = $_POST['include'];
	echo sprintf("<b>Limited to posts</b> = %s",$include);
	echo "<br>";
}

if ( isset($_POST['value']) ) {
	$value = $_POST['value'];
	echo sprintf("<b>Value</b> = %s",$value);
	echo "<br>";
}

if ( isset($_POST['cmd']) ) {
	$value = $_POST['cmd'];
	echo sprintf("<b>Command</b> = %s",$cmd);
	echo "<br>";
}
		
if ( is_array($value) )
	$value = extractKeyValuePairs( $value );
else
	$value = $a['value'];

//PC:debug( array('$value after explode : '=>$value) );
	
echo "<p>Batch Update Meta script started...</p>";

$post_type_object = get_post_type_object($post_type);
$label = $post_type_object->label;

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
  		if ( ! empty($meta_value) ) delete_post_meta($post->ID, $key);
  		echo sprintf("%s key deleted in %s",$key,$post->post_title) . "<br>"; //Prints updated after ran.
  		break;
  }
}


function extractKeyValuePairs($string, $delimiter = ' ') {
    $params = explode($delimiter, $string);
    $pairs = [];
    for ($i = 0; $i < count($params); $i++) {
        $pairs[$params[$i]] = $params[++$i];
    }

    return $pairs;
}