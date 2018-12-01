<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LazyLoad extends CustomScriptsStylesEnqueue {

	public function __construct() {
		parent::__construct();

		add_action( 'wp_enqueue_scripts', array($this, 'register_lazyload_scripts' ) );
		// add_filter( 'post_thumbnail_html', array($this, 'format_img_for_lazyload'), PHP_INT_MAX, 5 );
		add_filter( 'post_thumbnail_html', array($this, 'lazy_load_responsive_images_filter_post_thumbnail_html'), 10, 5);
		add_filter( 'wp_get_attachment_image_attributes', array($this, 'lazy_load_responsive_images_modify_post_thumbnail_attr'), 20, 3);
		add_filter( 'the_content', array($this, 'lazy_load_responsive_images'), 20 );
	}

	public function register_lazyload_scripts() {
		$js_uri = self::$PLUGIN_URI . 'vendor/';
		$js_path = self::$PLUGIN_PATH . 'vendor/';

		custom_enqueue_script( 'lazysizes', $js_uri, $js_path, 'lazysizes.min.js', array( 'jquery' ), CHILD_THEME_VERSION, true);

	}

	// public function format_img_for_lazyload( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	// 	// $content = str_replace('<img','<img data-qazy="true"', $content);
	// 	$alt = get_the_title($post_id);
	// 	$class="lazyload ";
	// 	$class .= isset($attr['class'])?$attr['class']:''; 
	// 	$id = isset($attr['id'])?$attr['id']:''; 
	// 	$img_attr = wp_get_attachment_image_src($post_thumbnail_id, $size);
	// 	// https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
	// 	// The returned array contains four values: the URL of the attachment image src, the width of the image file, the height of the image file, and a boolean representing whether the returned array describes an intermediate (generated) image size or the original, full-sized upload.
	// 	$html = '<img data-src="' . $img_attr[0] . '" alt="' . $alt . '" class="' . $class . '" id="' . $id . '" />';
	// 	$html .= '<p>In format_img_for_lazyload</p>';

	// 	return $html;
	// }
	public function lazy_load_responsive_images ( $content ) {
	   if ( empty( $content ) ) {
	      return $content;
	   }
	   $dom = new DOMDocument();
	   libxml_use_internal_errors( true );
	   $dom->loadHTML( $content );
	   libxml_clear_errors();
	   foreach ( $dom->getElementsByTagName( 'img' ) as $img ) {
	      if ( $img->hasAttribute( 'sizes' ) && $img->hasAttribute( 'srcset' ) ) {
	         $sizes_attr = $img->getAttribute( 'sizes' );
	         $srcset     = $img->getAttribute( 'srcset' );
	         $img->setAttribute( 'data-sizes', $sizes_attr );
	         $img->setAttribute( 'data-srcset', $srcset );
	         $img->removeAttribute( 'sizes' );
	         $img->removeAttribute( 'srcset' );
	         $src = $img->getAttribute( 'src' );
	         if ( ! $src ) {
	            $src = $img->getAttribute( 'data-noscript' );
	         }
	      } else {
	         $src = $img->getAttribute( 'src' );
	         if ( ! $src ) {
	            $src = $img->getAttribute( 'data-noscript' );
	         }
	         $img->setAttribute( 'data-src', $src );
	      }
	      $classes = $img->getAttribute( 'class' );
	      $classes .= " lazyload";
	      $img->setAttribute( 'class', $classes );
	      $img->removeAttribute( 'src' );
	      $noscript      = $dom->createElement( 'noscript' );
	      $noscript_node = $img->parentNode->insertBefore( $noscript, $img );
	      $noscript_img  = $dom->createElement( 'IMG' );
	      $noscript_img->setAttribute( 'class', $classes );
	      $new_img = $noscript_node->appendChild( $noscript_img );
	      $new_img->setAttribute( 'src', $src );
	      $content = $dom->saveHTML();
	   }

	   return $content;
	}

	public function lazy_load_responsive_images_modify_post_thumbnail_attr( $attr, $attachment, $size ) {
	   if ( isset( $attr['sizes'] ) ) {
	      $data_sizes = $attr['sizes'];
	      unset( $attr['sizes'] );
	      $attr['data-sizes'] = $data_sizes;
	   }

	   if ( isset( $attr['srcset'] ) ) {
	      $data_srcset = $attr['srcset'];
	      unset( $attr['srcset'] );
	      $attr['data-srcset']   = $data_srcset;
	      $attr['data-noscript'] = $attr['src'];
	      unset( $attr['src'] );
	   }

	   $attr['class'] .= ' lazyload';

	   return $attr;
	}

	public function lazy_load_responsive_images_filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	   $dom = new DOMDocument();
	   libxml_use_internal_errors( true );
	   $dom->loadHTML( $html );
	   libxml_clear_errors();
	   foreach ( $dom->getElementsByTagName( 'img' ) as $img ) {
	      $src     = $img->getAttribute( 'data-noscript' );
	      $classes = $img->getAttribute( 'class' );
	   }
	   $noscript_element = "<noscript><img src='" . $src . "' class='" . $classes . "'></noscript>";
	   $html .= $noscript_element;

	   return $html;
	}


}
