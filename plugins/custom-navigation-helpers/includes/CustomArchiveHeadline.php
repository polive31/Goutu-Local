<?php

/* Archive Headline customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomArchiveHeadline extends CustomArchive {

	public function __construct() {
		parent::__construct();
		// Title text
		add_filter( 'genesis_search_title_text', array($this,'custom_search_title_text') );
		add_filter( 'genesis_archive_title_text', array($this,'custom_archive_title') );
		// Intro text
		add_filter( 'genesis_term_intro_text_output', 'wpautop' );	
		add_filter( 'genesis_archive_description_text', array($this,'custom_archive_description') );
		// Shortcode
		add_shortcode('seo-friendly-title', array($this,'get_friendly_title')); 
	}

	public function custom_search_title_text() {	
		// $url = $_SERVER["REQUEST_URI"];
		// $WPURP_search = strpos($url, 'wpurp-search');
		// if ( $WPURP_search!==false )
		if ( isset( $_GET['wpurp-search'] ) )
			return __('Detailed Search Results', 'foodiepro');
		else 
			return sprintf( __('Search Results for:%s', 'foodiepro'), get_search_query());
	}

	public function get_friendly_title( $atts ) {
		$atts = shortcode_atts( array(
			'url' => 'true',
			), $atts );

		if ( is_archive() )
			$title = $this->custom_archive_title();
		elseif ( is_search() )
			$title = $this->custom_search_title_text();
		elseif ( is_singular() ) 
			$title = get_the_title();
		else
			$title = __('Visit Goutu.org', 'foodiepro');

		if ($atts['url']=='true') 
				$title = str_replace( ' ', '%20', $title);
		
		return $title;
	}

	public function custom_archive_title() {
		if ( is_author() ) {
			$term = $this->query->display_name;
			if ($this->initial_is_vowel($term)) 
				return $msg . sprintf(_x('All posts from %s','vowel','foodiepro'), $term);
			else 
				return $msg . sprintf(_x('All posts from %s','consonant','foodiepro'), $term);			
		}
		elseif ( is_tax() ) {
			$headline = get_term_meta( $this->query->term_id, 'headline', true );
			if ( !empty($headline) ) return $headline;

			$term = $this->query->display_name;

		    if ( is_tax('ingredient') ) {
				if ($this->initial_is_vowel($term))
					return $msg . sprintf(_x('All recipes containing %s','vowel','foodiepro'), $term);
				else 
					return $msg . sprintf(_x('All recipes containing %s','consonant','foodiepro'), $term);			
		    }
		    if ( is_tax('cuisine') ) {
				if ($this->is_plural($term)) 
					return $msg . sprintf(_x('All recipes from %s','plural','foodiepro'), $term);
				elseif ($this->initial_is_vowel($term)) 
					return $msg . sprintf(_x('All recipes from %s','vowel','foodiepro'), $term);
				else 
					return $msg . sprintf(_x('All recipes from %s','consonant','foodiepro'), $term);
		    }
			else 
				return $msg . single_term_title('', false);
		}
		else {	
			$headline = get_term_meta( $this->query->term_id, 'headline', true );
			if ( !empty($headline) ) return $headline;			
			return $msg . single_term_title('', false);
		};
	}

	public function custom_archive_description( $description ) {
		if ( !$this->is_tax ) return;
		$id = $this->query->term_id;
		$tax = $this->query->taxonomy;
		$description .= term_description( $id, $tax );
		if (empty($description)) 
			$description .= get_term_meta( $this->query->term_id, 'intro_text', true );		  
		return $description;
	}	
	


}











