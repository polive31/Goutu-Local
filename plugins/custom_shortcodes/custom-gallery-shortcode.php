<?php
/*
Plugin Name: Custom Gallery Shortcode
Plugin URI: http://goutu.org
Description: Provides customized gallery shortcode
Version: 1.0
Author: Pascal Olive 
Author URI: http://goutu.org
License: GPL
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	

/* Suppression de la feuille de style de la gallerie Wordpress */
add_filter( 'use_default_gallery_style', '__return_false' );
add_shortcode('custom-gallery', 'custom_gallery_shortcode');
add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'custom-gallery-stylesheet', 'custom-gallery-stylesheet.css', array(), CHILD_THEME_VERSION );		
} );


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


