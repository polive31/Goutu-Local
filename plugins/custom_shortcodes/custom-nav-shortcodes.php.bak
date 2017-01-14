<?php
/*
Plugin Name: Custom Navigation Shortcodes
Plugin URI: http://goutu.org/
Description: Taxonomy and navigation custom shortcodes
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


/* =================================================================*/
/* =                    TAXONOMY LIST SHORTCODE     
/* =================================================================*/

// First we create a function
function list_terms_taxonomy( $atts ) {
	static $dropdown_cnt;
	$html = '';

 //Inside the function we extract custom taxonomy parameter of our shortcode
	extract( shortcode_atts( array(
		'dropdown' => 'false',
		'taxonomy' => 'category',
		'label' => '',
		'select_msg' => __( 'Select...', 'foodiepro' ),
		'all_msg' => '',
		'depth' => 1,
		'child_of' => 0,
		'exclude' => '',
		'index_title' => '',
		'index_path' => ''
	), $atts ) );


// Extraction of taxonomy from current url
	if ($taxonomy == 'url') {
		$obj = get_queried_object();
		$taxonomy = $obj -> taxonomy;
		if ($taxonomy == 'cuisine') {
			// extract term of depth = 1
			$parent = $obj -> parent;
			$current = $obj -> term_id;
			if ($parent==0) {
				$child_of = $current;}
			else {
				$child_of = $parent;
				$parent_meta = get_term_by('id', $parent, 'cuisine');
				if ($parent_meta != false) $all_msg = $parent_meta->name;
				$all_url = add_query_arg( 'cuisine', $parent_meta->slug, home_url() );
			}
		}
	}

 //arguments for function wp_list_categories
	$args = array( 
		'taxonomy' => $taxonomy,
		'child_of' => $child_of,
		'depth' => $depth,
		'exclude' => $exclude,
		'orderby'  => 'slug',
		//'title_li' => '',
		'echo' => false
	);
	
	if ($dropdown=='true') {	
		$dropdown_id = $taxonomy . ++$dropdown_cnt;
		
		$html = '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $label . '</label>';

		$args['show_option_none'] = $select_msg;
		$args['show_option_all'] = $all_msg;
		$args['option_none_value'] = 'none';
		$args['selected'] = 'none';
		$args['id'] = $dropdown_id;
		$args['name'] = $dropdown_id;
		$args['value_field'] = 'slug';
		
		$html .= wp_dropdown_categories( $args );
		
		// Get taxonomy slug from taxonomy ID
		$tax_meta = get_taxonomy( $taxonomy );
		if ($tax_meta != false) 
			$tax_slug = $tax_meta->rewrite['slug'];
		
		$html .= "<script type='text/javascript'>\n";
		$html .= '/* <![CDATA[ */' . "\n";
		$html .= '(function() {' . "\n";
		$html .= ' var '. $dropdown_id . '_dropdown = document.getElementById( "' . esc_js( $dropdown_id ) . '" );' . "\n";
		$html .= ' function on' . $dropdown_id . 'Change() {' . "\n";
		//$html .= 'alert("On Change detected");';
		$html .= '  var choice = '. $dropdown_id . '_dropdown.options[ '. $dropdown_id . '_dropdown.selectedIndex ].value;' . "\n";
		$html .= '	if ( choice !="none" ) {' . "\n";
		$html .= '		  location.href = "' . home_url() . '/' . $tax_slug . '/" + choice;' . "\n";
		$html .= '	}' . "\n";
		$html .= '	if ( choice =="0" ) {' . "\n";
		$html .= '		  location.href = "' . $all_url . '";' . "\n";
		$html .= '	}' . "\n";
		$html .= ' }' . "\n";
		$html .= '	'. $dropdown_id . '_dropdown.onchange = on' . $dropdown_id . 'Change;' . "\n";
		$html .= '})();' . "\n";
		$html .= '/* ]]> */' . "\n";
		$html .= '</script>' . "\n";

	}
	
	else {

	 	$html = '<ul class="menu">';
	 	// wrap it in unordered list 

		$html .= wp_list_categories($args);	

		if ($index_title!='')
			$html .= '<li class="ct-index-url"> <a class="back-link" href="' . site_url($index_path) . '">' . $index_title . '</a></li>';
	 
	 	$html .= '</ul>';
		
	}

 // Return the output
 	return $html;
 
}

// Add a shortcode that executes our function
add_shortcode('ct_terms', 'list_terms_taxonomy');


?>