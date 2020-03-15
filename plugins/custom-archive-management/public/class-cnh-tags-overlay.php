<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Add tags as an overlay to each entry in an archive page
-----------------------------------------------------------------------------*/

class CNH_Tags_Overlay {

	/* ENTRY TAG FORMATTING
	----------------------------------------------------------*/

	// Add custom opening div for post thumbnail title
	public function do_post_title_before() {
		// if ( is_tax() || is_search() || is_tag() || is_author() ) {
		if ( is_archive() || is_search() || is_tag() ) {
			echo '<div class="entry-header-overlay">';
			echo self::entry_tags();
		}
	}

	// Add custom closing div for post thumbnail title
	public function do_post_title_after() {
		if ( is_archive() || is_search() || is_tag() ) {
			echo '</div><!-- end of entry-header-overlay -->';
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
			$tags_html = self::output_tags( $origin, $diet, '', $season);
		}
		elseif ( is_tax('course') || is_search() || is_tax('difficult') ) {
			$tags_html = self::output_tags( $origin, $diet, $occasion, $season);
		}
		elseif ( is_tax('diet') ) {
			$tags_html = self::output_tags( $origin, null, $occasion, $season);
		}
		elseif ( is_tax('season') ) {
			$tags_html = self::output_tags( null, $diet, $occasion, null);
		}
		elseif ( is_tax('occasion') ) {
			$tags_html = self::output_tags( $origin, $diet, null, $season);
		}
		elseif ( is_tax('ingredient') ) {
			$tags_html = self::output_tags( $origin, $diet, $occasion, $season);
		}
		else
			$tags_html = self::output_tags( $origin, $diet, $occasion, $season);

		return $tags_html;
	}


	public static function output_tags( $origin, $diet, $occasion, $season) {
		$tags = '';

		$left_id=0;
		$tr_id=0; // Top right
		$br_id=0; // Bottom Right


		if ( self::get_season($season) == self::current_season() ) {
			$season_msg = __('Seasonal', 'foodiepro');
			$tags .= '<div class="tag-overlay col-' . self::get_season( $season ) . ' botright' . $br_id++ . '">' . $season_msg . '</div>';
		}

		if ( self::is_veg($diet) ) {
			// $veg_msg = foodiepro_get_icon('leaf', '', '', __('Vegetarian', 'foodiepro') );
			// $veg_msg = foodiepro_get_icon('carrot', '', '', __('Vegetarian', 'foodiepro') );
			$veg_msg = foodiepro_get_icon('pepper-hot', '', '', __('Vegetarian', 'foodiepro') );
			//$tags .= '<div class="overlay" id="veg">' . $veg_msg . '</div>';
			$tags .= '<div class="tag-overlay topright' . $tr_id++ . '" id="veg" title="' . __('Vegetarian','foodiepro') . '">' . $veg_msg . '</div>';
		}

		if ( self::is_fest($occasion) ) {
			$fest_msg = __('Festive', 'foodiepro');
			$tags .= '<div class="tag-overlay left' . $left_id++ . '" id="fest">' . $fest_msg . '</div>';
		}

		if ( self::get_origin($origin) != false ) {
			$tags .= '<div class="tag-overlay left' . $left_id++ . '">' . self::get_origin($origin) . '</div>';
			$left_id++;
		}

		return $tags;
	}

	public static function get_origin( $terms ) {
		if ( empty($terms) || !$terms ) return false;
		$origin = $terms[0]->name;
		return empty($origin)?false:$origin;
	}

	public static function get_season( $terms ) {
		if ( empty($terms) || !$terms ) return false;
		$season_id = $terms[0]->slug;
		return $season_id;
	}

	public static function current_season() {
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


	public static function is_fest( $terms ) {
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
		return false;
	}

	public static function is_veg($terms) {
		if ( !is_array($terms) || empty($terms) || !$terms ) return false;
		foreach ($terms as $term) {
			if ($term->slug == 'vegetarien') return true;
		}
		return false;
	}


}
