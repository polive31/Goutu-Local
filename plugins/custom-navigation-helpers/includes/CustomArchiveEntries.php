<?php

/* Archive Entry Titles customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomArchiveEntries extends CustomNavigationHelpers {


	public function __construct() {
		parent::__construct();


		add_filter( 'genesis_post_title_output', array($this, 'archive_rating' ), 1 );		

		/* Customize archive pages */
		add_filter( 'post_class', array($this, 'set_grid_columns'));
		add_filter( 'genesis_before_entry', array($this, 'reorganize_entry_content') );
		
	}
	
	public function reorganize_entry_content() {
		if ( is_archive() || is_search() ) {
			remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
			add_action( 'genesis_entry_header', 'genesis_do_post_image', 5 );

			// Don't know why but 2 remove actions are needed to really remove the image from the entry content !
			remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
			remove_action( 'genesis_entry_content', 'genesis_do_post_image', 12 );
			
			remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
			add_action( 'genesis_before_entry_content', 'genesis_do_post_title' );

		}
	}

	public function set_grid_columns( $classes ) {
		if ( is_archive() || is_search() || is_tag() ) {
			$classes = foodie_pro_grid_one_half($classes); 
		} 
		return $classes;
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

	// Add overlay to RPWE widget
	public function rpwe_add_overlay($output, $args) {
		$disp_overlay = substr($args['cssID'],3,1);
		////foodiepro_log( array('WPRPE Output add rating'=>$output) );
		if ( $disp_overlay == '1') {
			$post_id = get_the_ID();
			$origin = $this->get_post_term( $post_id, 'cuisine', 'names');
			$output .= $this->output_tags( $origin, null, null, null);
		}
		return $output;
	}

	
	public function entry_tags() {
		
		$post_id = get_the_ID();
		$name = get_the_title( $post_id );

		$origin = wp_get_post_terms( $post_id, 'cuisine' );
		$diet = wp_get_post_terms( $post_id, 'diet' );
		$occasion = wp_get_post_terms( $post_id, 'occasion' );
		$season = wp_get_post_terms( $post_id, 'season' );
	
		if ( is_tax('cuisine') || is_author() ) {
			$tags_html = $this->output_tags( $origin, $diet, '', $season);
		}
		elseif ( is_tax('course') || is_search() || is_tax('difficult') ) {
			$tags_html = $this->output_tags( $origin, $diet, $occasion, $season);
		}	
		elseif ( is_tax('diet') ) {
			$tags_html = $this->output_tags( $origin, null, $occasion, $season);
		}	
		elseif ( is_tax('season') ) {
			$tags_html = $this->output_tags( null, $diet, $occasion, null);
		}
		elseif ( is_tax('occasion') ) {
			$tags_html = $this->output_tags( $origin, $diet, null, $season);
		}	
		elseif ( is_tax('ingredient') ) {
			$tags_html = $this->output_tags( $origin, $diet, $occasion, $season);
		}		
		elseif ( is_tag() ) {
			$tags_html = $this->output_tags( $origin, $diet, $occasion, $season);
		}
		else 
			$tags_html = '';		

		return $tags_html;
	}


	public function output_tags( $origin, $diet, $occasion, $season) {
		$tags = '';
		
		$left_id=0;
		$tr_id=0; // Top right
		$br_id=0; // Bottom Right
		
		
		if ( $this->get_season($season) == $this->current_season() ) {
			$season_msg = __('Seasonal', 'foodiepro');
			$tags .= '<div class="tag-overlay col-' . $this->get_season( $season ) . ' botright' . $br_id++ . '">' . $season_msg . '</div>';
		}
		
		if ( $this->is_veg($diet) ) {
			$veg_msg = '<i class="fa fa-leaf" aria-hidden="true"></i>';
			//$tags .= '<div class="overlay" id="veg">' . $veg_msg . '</div>';
			$tags .= '<div class="tag-overlay topright' . $tr_id++ . '" id="veg" title="' . __('Vegetarian','foodiepro') . '">' . $veg_msg . '</div>';
		}
		
		if ( $this->is_fest($occasion) ) {
			$fest_msg = __('Festive', 'foodiepro');
			$tags .= '<div class="tag-overlay left' . $left_id++ . '" id="fest">' . $fest_msg . '</div>';
		}			
		
		if ( $this->get_origin($origin) != false ) {
			$tags .= '<div class="tag-overlay left' . $left_id++ . '">' . $this->get_origin($origin) . '</div>';
			$left_id++;		
		}
		
		return $tags;
	}

	protected function get_origin( $terms ) {
		if ( empty($terms) || !$terms ) return false;
		$origin = $terms[0]->name;
		return empty($origin)?false:$origin;
	}	

	protected function get_season( $terms ) {
		if ( empty($terms) || !$terms ) return false;
		$season_id = $terms[0]->slug;
		return $season_id;
	}
	
	protected function current_season() {
		//get current month
		$currentMonth=DATE("m");
		//retrieve season
		if ($currentMonth>="03" && $currentMonth<="05")
		  $currentSeason = "printemps";
		elseif ($currentMonth>="06" && $currentMonth<="08")
		  $currentSeason = "ete";
		elseif ($currentMonth>="09" && $currentMonth<="11")
		  $currentSeason = "automne";
		else
		  $currentSeason = "hiver";	
		  
		return $currentSeason;
	}

	
	protected function is_fest( $terms ) {
		
		if ( empty($terms) || !$terms ) return false;
		
		$currentMonth=DATE("m");
		$slug = 'fetes';

		foreach ($terms as $term) {
			if ($term->slug == $slug) {
				if ( $currentMonth==11 || $currentMonth==12 ) {
					return true;
				}
			}
		}
	}
	
	protected function is_veg($terms) {
		if ( empty($terms) || !$terms ) return false;
		$isveg = $terms[0]->slug == 'vegetarien';
		return $isveg;
	}


}











