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


/* Share Title Output
--------------------------------------------- */
	
function display_share_title() {
	if (is_singular('post')) $msg=__('Share this post','foodiepro');
	elseif (is_singular('recipe')) $msg=__('Share this recipe','foodiepro');
	$html = '<h3 class="share-title">' . $msg . '</h3>';
	return $html;
}

add_shortcode('share-title', 'display_share_title'); 


/* Index Links generation
--------------------------------------------- */

function add_index_link($atts) {
	 //Inside the function we extract parameter of our shortcode
	extract( shortcode_atts( array(
		'back' => 'false',
	), $atts ) );
	

	if ($back!='true'):
		
		$obj = get_queried_object();
		$tax_id = $obj -> taxonomy;
		$parent = $obj -> parent;
		$current = $obj -> term_id;

		switch ($tax_id) {
	    case 'course':
				$url = "/recettes/plats";
				//$msg = "De l'ap�ritif au dessert";
				$msg = __('Courses', 'foodiepro');
				break;
	    case 'season':
				$url = "/recettes/saisons";
				//$msg = "Cuisine de saisons";
				$msg = __('Seasons', 'foodiepro');
				break;
	    case 'occasion':
				$url = "/recettes/occasions";
				//$msg = "En toutes occasions";
				$msg = __('Occasions', 'foodiepro');
				break;
	    case 'diet':
				$url = "/recettes/regimes";
				//$msg = "R�gimes et di�t�tique";
				$msg = __('Diets', 'foodiepro');
				break;
	    case 'cuisine':
	    	$url="Parent" . $parent;
	    	if ($parent == 9996 || $current == 9996) {
	    		$url = "/recettes/regions";
					//$msg = "Cuisines de r�gions";
					$msg = __('France', 'foodiepro');}
	    	else {
	    		$url = "/recettes/monde";
					//$msg = "Cuisines du monde";
					$msg = __('World', 'foodiepro');}
	    	break;
	    case 'category':
				$url = "/blogs";
				$msg = __('All blogs', 'foodiepro');
				break;	
		}
		
	else:
			$url .= 'javascript:history.back()';
			$msg = __('Previous page','foodiepro');
	endif;
	
	$output = '<ul class="menu"> <li> <a class="back-link" href="' . $url . '">' . $msg . '</a> </li> </menu>';
	return $output;
}
add_shortcode('index-link', 'add_index_link'); 


/**
 * Builds the Gallery shortcode output.
 *
 * This implements the functionality of the Gallery Shortcode for displaying
 * WordPress images on a post.
 *
 * @since 2.5.0
 *
 * @staticvar int $instance
 *
 * @param array $attr {
 *     Attributes of the gallery shortcode.
 *
 *     @type string       $order      Order of the images in the gallery. Default 'ASC'. Accepts 'ASC', 'DESC'.
 *     @type string       $orderby    The field to use when ordering the images. Default 'menu_order ID'.
 *                                    Accepts any valid SQL ORDERBY statement.
 *     @type int          $id         Post ID.
 *     @type string|array $size       Size of the images to display. Accepts any valid image size, or an array of width
 *                                    and height values in pixels (in that order). Default 'thumbnail'.
 *     @type string       $ids        A comma-separated list of IDs of attachments to display. Default empty.
 *     @type string       $include    A comma-separated list of IDs of attachments to include. Default empty.
 *     @type string       $exclude    A comma-separated list of IDs of attachments to exclude. Default empty.
 *     @type string       $link       What to link each image to. Default empty (links to the attachment page).
 *                                    Accepts 'file', 'none'.
 * }
 * @return string HTML content to display gallery.
 */
function custom_gallery_shortcode( $attr ) {
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}

	/**
	 * Filters the default gallery shortcode output.
	 *
	 * If the filtered output isn't empty, it will be used instead of generating
	 * the default gallery template.
	 *
	 * @since 2.5.0
	 * @since 4.2.0 The `$instance` parameter was added.
	 *
	 * @see gallery_shortcode()
	 *
	 * @param string $output   The gallery output. Default empty.
	 * @param array  $attr     Attributes of the gallery shortcode.
	 * @param int    $instance Unique numeric ID of this gallery shortcode instance.
	 */
	$output = apply_filters( 'post_gallery', '', $attr, $instance );
	if ( $output != '' ) {
		return $output;
	}

	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'gallery-id' => '',
		'id'         => $post ? $post->ID : 0,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );
	$gallery_id = $atts['gallery-id'];
	
	/* CSS style output */
	$selector = $gallery_id;
	/* $selector = "gallery-{$instance}"; original */
	
	$output ='';
	$gallery_style = '';

	/**
	 * Filters whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 */
	$size_class = sanitize_html_class( $atts['size'] );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-size-{$size_class}'>";

	/**
	 * Filters the default gallery shortcode CSS styles.
	 *
	 * @since 2.5.0
	 *
	 * @param string $gallery_style Default CSS styles and opening HTML div container
	 *                              for the gallery shortcode output.
	 */
	$output .= apply_filters( 'gallery_style', $gallery_style . $gallery_div );
	
	/* Add picture button markup output */
	$output .='<div class="add-picture-button">';
	$button_id = is_user_logged_in() ? 'upload_picture' : 'join_us';
	$output.='<button id="' . $button_id . '">' . __('Add a picture','foodiepro') . '</button>';
	$output.='</div>';
	
  /* Gallery content output */
   
	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	} else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
	}

	if ( empty( $attachments ) ) {
		$output .= "
			</div>\n";
		return $output;
	}

  /* Gallery picture loop */ 

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {

		$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
		if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
			$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
		} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
			$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
		} else {
			$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
		}
		$image_meta  = wp_get_attachment_metadata( $id );

		$output .= "<div class='gallery-item'>";
		$output .= "
			<div class='gallery-icon'>
				$image_output
			</div>";/*gallery-icon*/
		$output .= "</div>"; /* gallery-item */
	}	
	
	$output .= "
		</div>\n";/* gallery container */
	$output .= '<br style="clear:both" />'; /* clearfix */

	return $output;
}

add_shortcode('custom-gallery', 'custom_gallery_shortcode');



?>