<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CNH_Archive_Entries {


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
				$title .= (class_exists('CSR_Rating')) ? CSR_Rating::render( 'entry', false, 'span' ) : '';
				$title .= (class_exists('CPM_Like')) ? CPM_Like::get_like_count() : '';
				$title .= '</span>';
				//echo 'User rating global = ' . get_post_meta( get_the_ID(), 'user_rating_global', true );
		};
		return $title;
	}


}
