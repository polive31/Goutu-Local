<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Define all hooks to be used by the different classes */

class Custom_Star_Rating {

	public function __construct() {

		/* Hooks for CSR_Assets class (static)
		-----------------------------------------------------------------*/
		add_action( 'wp',                                       'CSR_Assets::hydrate' );
		// add_filter( 'wpurp_register_ratings_taxonomy',          'CSR_Assets::translate_ratings_taxonomy' );
        add_action( 'wp_enqueue_scripts',                       'CSR_Assets::register_csr_assets' );

        /* Hooks for CSR_Rating class
        -----------------------------------------------------------------*/
        $Rating = new CSR_Rating();
		add_shortcode( 'display-star-rating',                  	array( $Rating, 'display_star_rating_shortcode') );
        add_action( 'comment_post',                             array( $Rating, 'update_comment_post_meta'), 10, 3 );
		add_action( 'transition_comment_status',                array( $Rating, 'comment_status_change_callback'), 10, 3 );
		add_action( 'save_post',                                array( $Rating, 'add_default_rating' ) );
		/* Support order by rating archives */
		add_action( 'pre_get_posts',                            array( $Rating, 'sort_entries_by_rating' ) );
		/* Display rating in comment lists of matching post types */
		add_filter( 'genesis_show_comment_date',                array( $Rating, 'display_rating_in_comments_list'), 10, 2 );


		/* Hooks for CSR_Form class
		-----------------------------------------------------------------*/
        $CommentForm = new CSR_Form();
		add_shortcode('comment-rating-form',                 	array( $CommentForm, 'get_comment_form_with_rating_shortcode') );


	}



}
