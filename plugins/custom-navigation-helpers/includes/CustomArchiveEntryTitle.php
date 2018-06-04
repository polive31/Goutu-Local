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
		add_filter( 'rpwe_in_thumbnail', array($this, 'rpwe_add_overlay'), 10, 2 );
	}


	//* Customize the entry meta in the entry header (requires HTML5 theme support)
	function custom_post_info_filter($post_info) {
		if (is_single()) {
			$post_info = sprintf(__('Published on %s by <span id="username">%s</span>', 'foodiepro'), '[post_date]', '[bp-author]');
			global $post, $current_user;
			get_currentuserinfo();
			if ($post->post_author == $current_user->ID) { 
				$edit_page_url = do_shortcode( '[permalink slug="' . Custom_WPURP_Shortcodes::RECIPE_EDIT_SLUG . '"]' );
				$edit_url = 'href="' . $edit_page_url . '?wpurp-edit-recipe=' . $post->ID . '" ';   
            	$edit_title = 'title="' . __('Edit recipe', 'foodiepro') . '" ';

	   			$post_info .= ' <a ' . $edit_url . $edit_title . '><i class="fa fa-pencil"></i></a>';    
			}
		}
		else {
			//$post_info = sprintf(__('By <span id="username">%s</span>', 'foodiepro'), '[bp-author]');
			$post_info = '';
		}
		return $post_info;
	}
	
	
	// Add custom opening div for post title
	public function do_post_title_before() {
		if ( is_tax() || is_search() || is_tag() ) {
			echo '<div class="entry-header-overlay">';
			echo $this->entry_tags();
		}
	}

	// Add custom closing div for post title
	public function do_post_title_after() {
		if ( is_tax() || is_search() || is_tag() ) {
			echo '</div>';
		}
	}	
		
	/* Add rating to entry title
	-----------------------------------------------------------------------------*/
	public function archive_rating($title) {
		/* Display start rating below entry */
		if ( is_archive() || is_search() || is_tag() ) {
				// Rating BEFORE entry title
				// $title = do_shortcode('[display-star-rating category="global" display="minimal"]') . $title;
				// Rating AFTER entry title
				$title .= '<span class="entry-rating">';
				$title .= do_shortcode('[display-star-rating category="global" display="minimal" markup="span"]');
				$title .= do_shortcode('[like-count]');
				$title .= '</span>';
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

	// Add overlay to RPWE widget
	public function rpwe_add_overlay($output, $args) {
		$disp_overlay = substr($args['cssID'],3,1);
		////foodiepro_log( array('WPRPE Output add rating'=>$output) );
		if ( $disp_overlay == '1') {
			$post_id = get_the_ID();
			$origin = $this->get_post_term( $post_id, 'cuisine', 'names');
			$output .= $this->output_tags( $origin, null, null, null);
			// $overlay = "TOTO";
		}
		return $output;
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
			$title = $this->output_tags( $origin, null, $occasion, $season->slug) . $title;
		};	
		
		if ( is_tax('season') ) {
			$title = $this->output_tags( null, $diet->slug, $occasion, null) . $title;
		};
			
		if ( is_tax('occasion') ) {
			$title = $this->output_tags( $origin, $diet->slug, null, $season->slug) . $title;
		};	

		if ( is_tax('ingredient') ) {
			$title = $this->output_tags( $origin, $diet->slug, $occasion, $season->slug) . $title;
		};		

		if ( is_tag() ) {
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
			$tags .= '<div class="overlay topright' . $tr_id . '" id="veg" title="' . __('Vegetarian','foodiepro') . '">' . $veg_msg . '</div>';
			$tr_id++;
		}
		
		//print_r($occasion);
		if ($this->is_fest($occasion)) {
			$tags .= '<div class="overlay left' . $left_id . '" id="fest">' . $fest_msg . '</div>';
			$left_id++;
		}			
		
		if ( !is_null($origin) ) {
			$tags .= '<div class="overlay left' . $left_id . '">' . $origin . '</div>';
			$left_id++;		
		}
		
		return $tags;
	}



}











