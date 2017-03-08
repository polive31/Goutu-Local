<?php
/*
Plugin Name: Misc Shortcodes
Plugin URI: http://goutu.org
Description: Provides misc shortcodes  
Version: 1.0
Author: Pascal Olive 
Author URI: http://goutu.org
License: GPL
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	

/* Language switcher
--------------------------------------------- */
	
//function display_language_switcher() {
//	if( !function_exists('pll_the_languages')
//			return 'Polylang not installed';		
//		$lang = pll_the_languages( array( 'echo' => 0,'raw' => 1, 'hide_current' => 1 ));
//	foreach ($lang as $thislang) {
//		if (!$thislang->current_lang)
//			$html .= '<a href="' . $lang->url .'"> ' . $lang->name . '</a>';
//	}
//	return $html;
//}
//
//add_shortcode('language', 'display_language_switcher'); 

/* Add Comment Form 
-----------------------------------------------*/
function add_comment_form_shortcode() {
//		$comments_args = array( 
//			'title_reply' => __( '', 'genesis' ), 
//      'comment_field'=>'<p class="comment-form-comment"></p>', 
//		);
		$comment_args='';
    ob_start();
    comment_form($comment_args);
    $cform = ob_get_contents();
    ob_end_clean();
    return $cform;
 }
add_shortcode( 'add-comment-form', 'add_comment_form_shortcode' );


/* Share Title Output
--------------------------------------------- */
	
function display_share_title() {
	if (is_singular()) {
		if (is_singular('recipe')) 
			$msg=__('Share this recipe','foodiepro');
		else
			$msg=__('Share this post','foodiepro');
		$html = '<h3 class="share-title">' . $msg . '</h3>';
	}
	return $html;
}

add_shortcode('share-title', 'display_share_title'); 



?>