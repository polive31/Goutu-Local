<?php
/*
Description: Batch Delete Comments
Author: Pascal Olive
Author URI: http://goutu.org
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =               BATCH DELETE COMMENTS
/* =================================================================*/

if ( isset($_POST['post-type']) ) {
	$post_type = $_POST['post-type'];
	echo sprintf("<b>Post type</b> = %s",$post_type);
	echo "<br>";
}
	
if ( isset($_POST['include']) ) {
	$include = $_POST['include'];
	echo sprintf("<b>Limit to Posts</b> = %s",$include);
	echo "<br>";
}

echo "<p>Batch Delete Comments script started...</p>";
echo sprintf("Limit to posts = %s", $include);

//$post_type_object = get_post_type_object($post_type);
//$label = $post_type_object->label;

//if ( ! empty($include) || ( $include == 'all') ) {
// 	$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
//  foreach ($posts as $post) {
//		$comments = get_comments( array('post_id'=>$post->ID ) );
//		echo sprintf(__('Post %s contains %d comments'), $post->post_title, count($comments) );
//		echo "<br>";
//		
//	}
//	
//}
//
//else {
//	echo "Please provide post IDs or 'all' for deletion to take place";
//} 


?>


