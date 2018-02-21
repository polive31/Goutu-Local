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
		add_filter( 'genesis_post_title_output', array($this,'archive_rating' ), 1 );		
		add_action( 'genesis_entry_header', array($this, 'do_post_title_before'), 1 );
		add_action( 'genesis_entry_header', array($this, 'do_post_title_after') );
		add_action( 'genesis_post_info', array($this, 'custom_post_info_filter') );
	}


	//* Customize the entry meta in the entry header (requires HTML5 theme support)
	function custom_post_info_filter($post_info) {
		if (is_single()) 
			$post_info = sprintf(__('Published on %s by <span id="username">%s</span>', 'foodiepro'), '[post_date]', '[bp-author]');
		else {
			//$post_info = sprintf(__('By <span id="username">%s</span>', 'foodiepro'), '[bp-author]');
			$post_info = '';
		}
		return $post_info;
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
				$title = do_shortcode('[display-star-rating category="global" display="minimal"]') . $title;
				//echo 'User rating global = ' . get_post_meta( get_the_ID(), 'user_rating_global', true );
		};	
		
		return $title;	
		
	}	

	/* Add tags to entry title 
	-----------------------------------------------------------------------------*/
	public function get_post_term($post_id, $tax, $field=null) {
		if (is_null($field))
 			$terms=wp_get_post_terms($post_id, $tax);
 		else
 			$terms=wp_get_post_terms($post_id, $tax, array("fields" => $field));
 		if (is_wp_error($terms)) 
 			return '';
 		else
 			return $terms[0];
	}
	
	public function entry_tags( $title='' ) {
		
		$post_id = get_the_ID();
		$origin = $this->get_post_term( $post_id, 'cuisine', 'names');
		//echo '<pre>' . print_r($origin) . '</pre>';
		$diet = $this->get_post_term( $post_id, 'diet');
		//echo '<pre>' . print_r($diet->slug) . '</pre>';
		$occasion = $this->get_post_term( $post_id, 'occasion');
		//echo '<pre>' . print_r($occasion) . '</pre>';
		$season = $this->get_post_term( $post_id, 'season');
		//echo '<pre>' . print_r($season->slug) . '</pre>';
	
		if ( is_tax('cuisine') || is_author() ) {
			$title = $this->output_tags( $origin, $diet->slug, '', $season->slug) . $title;
		};
		
		if ( is_tax('course') || is_search() || is_tax('difficult') ) {
			$title = $this->output_tags( $origin, $diet->slug, $occasion, $season->slug) . $title;
		};	

		if ( is_tax('diet') ) {
			$title = $this->output_tags( $origin, '', $occasion, $season->slug) . $title;
		};	
		
		if ( is_tax('season') ) {
			$title = $this->output_tags( '', $diet->slug, $occasion, '') . $title;
		};
			
		if ( is_tax('occasion') ) {
			$title = $this->output_tags( $origin, $diet->slug, '', $season->slug) . $title;
		};	

		if ( is_tax('ingredient') ) {
			$title = $this->output_tags( $origin, $diet->slug, $occasion, $season->slug) . $title;
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











