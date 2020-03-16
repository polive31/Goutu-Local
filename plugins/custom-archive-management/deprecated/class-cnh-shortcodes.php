<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class CNH_Shortcodes {



	/* =================================================================*/
	/* = LIST TAXONOMY TERMS SHORTCODE
	/* =================================================================*/
	public function simple_list_taxonomy_terms($args) {
	    $args = shortcode_atts( array(
	        'taxonomy' => 'post_tag',
	        'orderby' => 'description',
	        'groupby' => ''
	    ), $args );

	    $terms = get_categories($args);

	    $output = '';

	    // Exit if there are no terms
	    if (! $terms) {
	        return $output;
	    }

	    // Start list
	    $output .= '<ul>';

	    // Add terms
	    foreach($terms as $term) {
	        $output .= '<li><a href="'. get_term_link($term) .'">'. esc_html($term->cat_name) .'</a></li>';
	    }

	    // End list
	    $output .= '</ul>';

	    return $output;
	}












}
