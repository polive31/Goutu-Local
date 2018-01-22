<?php

/* Archive Entry Titles customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomArchiveEntryTitle extends CustomArchive {


	public function __construct() {
		parent::__construct();
		add_filter( 'genesis_post_title_output', array($this,'archive_rating' ), 15 );		
		add_action( 'genesis_entry_header', array($this, 'do_post_title_before'), 1 );
		add_action( 'genesis_entry_header', array($this, 'do_post_title_after') );
	}
	
	
	// Add custom opening div for post title
	public function do_post_title_before() {
		if ( is_tax() || is_search() ) {
			echo '<div class="entry-header-overlay">';
			echo $this->entry_tags();
		}
	}

	// Add custom closing div for post title
	public function do_post_title_after() {
		if ( is_tax() || is_search() ) {
			echo '</div>';
		}
	}	
		
	/* Add rating to entry title
	-----------------------------------------------------------------------------*/
	public function archive_rating($title) {
		
		/* Display start rating below entry */
		if ( is_tax() || is_search() ) {
				$title .= do_shortcode('[display-star-rating category="global" display="minimal"]');
				//echo 'User rating global = ' . get_post_meta( get_the_ID(), 'user_rating_global', true );
		};	
		
		return $title;	
		
	}	

	/* Add tags to entry title 
	-----------------------------------------------------------------------------*/
	public function entry_tags( $title='' ) {

		if ( is_tax('cuisine') || is_author() ) {
			$post_id = get_the_ID();
			$origin = wp_get_post_terms( $post_id, 'cuisine', array("fields" => "names"));
			$diet = wp_get_post_terms( $post_id, 'diet');
			$season = wp_get_post_terms( $post_id, 'season');

			$title = $this->output_tags( $origin[0], $diet[0]->slug, '', $season[0]->slug) . $title;
		};
		
		if ( is_tax('course') || is_search() || is_tax('difficult') ) {
			$post_id = get_the_ID();
			
			$origin = wp_get_post_terms( $post_id, 'cuisine', array("fields" => "names"));			
			$occasion = wp_get_post_terms( $post_id, 'occasion');
			$season = wp_get_post_terms( $post_id, 'season');
			$diet = wp_get_post_terms( $post_id, 'diet');
			
			$title = $this->output_tags( $origin[0], $diet[0]->slug, $occasion, $season[0]->slug) . $title;
		};	

		if ( is_tax('diet') ) {
			$post_id = get_the_ID();

			$origin = wp_get_post_terms( $post_id, 'cuisine', array("fields" => "names"));			
			$season = wp_get_post_terms( $post_id, 'season');
			$occasion = wp_get_post_terms( $post_id, 'occasion');
			
			$title = $this->output_tags( $origin[0], '', $occasion, $season[0]->slug) . $title;
		};	
		
		if ( is_tax('season') ) {
			$post_id = get_the_ID();
			
			$diet = wp_get_post_terms( $post_id, 'diet');
			$occasion = wp_get_post_terms( $post_id, 'occasion');
			
			$title = $this->output_tags( '', $diet[0]->slug, $occasion, '') . $title;
		};
			

		if ( is_tax('occasion') ) {
			$post_id = get_the_ID();
			$origin = wp_get_post_terms( $post_id, 'cuisine', array("fields" => "names"));
			$diet = wp_get_post_terms( $post_id, 'diet');
			$season = wp_get_post_terms( $post_id, 'season');
			
			$title = $this->output_tags( $origin[0], $diet[0]->slug, '', $season[0]->slug) . $title;
		};	

		if ( is_tax('ingredient') ) {
			
			$post_id = get_the_ID();
			$origin = wp_get_post_terms( $post_id, 'cuisine', array("fields" => "names"));
			$diet = wp_get_post_terms( $post_id, 'diet');
			$season = wp_get_post_terms( $post_id, 'season');
			$occasion = wp_get_post_terms( $post_id, 'occasion');
			
			$title = $this->output_tags( $origin[0], $diet[0]->slug, $occasion, $season[0]->slug) . $title;
		};					

		return $title;
	}


	public function output_tags( $origin, $diet, $occasion, $season) {
		
		$season_msg = __('Seasonal', 'foodiepro');
		$fest_msg = __('Festive', 'foodiepro');
		//$veg_msg = __('Veggie', 'foodiepro');
		$veg_msg = '<i class="fa fa-leaf" aria-hidden="true"></i>';
		$tags = '';
		$tmp = '';
		
		$left_id=0;
		$tr_id=0;
		$br_id=0; // Bottom Right
		
		if ($this->is_season($season)) {
			$tags .= '<div class="overlay col-' . $season[0] . ' botright' . $br_id . '">' . $season_msg . '</div>';
			$br_id++;
		}

		if ( $this->is_veg($diet) ) {
			//$tags .= '<div class="overlay" id="veg">' . $veg_msg . '</div>';
			$tags .= '<div class="overlay topright' . $tr_id . '" id="veg">' . $veg_msg . '</div>';
			$tr_id++;
		}
		
		//print_r($occasion);
		if ($this->is_fest($occasion)) {
			$tags .= '<div class="overlay left' . $left_id . '" id="fest">' . $fest_msg . '</div>';
			$left_id++;
		}			
		
		if ( $origin!='' ) {
			$tags .= '<div class="overlay left' . $left_id . '">' . $origin . '</div>';
			$left_id++;		
		}
	
		

		
		return $tags;
	}



}











