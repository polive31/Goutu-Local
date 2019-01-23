<?php

/* Archive Entry Titles customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Add tags as an overlay to each entry in an archive page
-----------------------------------------------------------------------------*/


class CustomArchiveEntryTags extends CustomArchiveEntries {


	public function __construct() {
		parent::__construct();	
		add_filter( 'rpwe_in_thumbnail', array($this, 'rpwe_add_overlay'), 10, 2 );

		add_action( 'genesis_entry_header', array($this, 'do_post_title_before'), 1 );
		add_action( 'genesis_entry_header', array($this, 'do_post_title_after') );
	}


	/* RPWE TAG FORMATTING
	----------------------------------------------------------*/
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


	/* ENTRY TAG FORMATTING
	----------------------------------------------------------*/

	// Add custom opening div for post thumbnail title
	public function do_post_title_before() {
		// if ( is_tax() || is_search() || is_tag() || is_author() ) {
		if ( is_archive() || is_search() ) {
			echo '<div class="entry-header-overlay">';
			echo $this->entry_tags();
		}
	}

	// Add custom closing div for post thumbnail title
	public function do_post_title_after() {
		if ( is_tax() || is_search() || is_tag() ) {
			echo '</div>';
		}
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
		}
		else 
			$tags_html = $this->output_tags( $origin, $diet, $occasion, $season);
			
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











