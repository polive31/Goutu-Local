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
		add_filter( 'wpurp_register_ratings_taxonomy',          'CSR_Assets::translate_ratings_taxonomy' );
        add_action( 'wp_enqueue_scripts',                       'CSR_Assets::register_star_rating_assets' );
        add_action( 'wp_enqueue_scripts',                       'CSR_Assets::enqueue_comment_reply_script' );

        /* Hooks for CSR_Rating class
        -----------------------------------------------------------------*/
        $Rating = new CSR_Rating();
        add_shortcode( 'json-ld-rating', 			            array( $Rating, 'display_json_ld_rating_shortcode') );
		add_shortcode( 'display-star-rating',                  	array( $Rating, 'display_star_rating_shortcode') );
        add_action( 'comment_post',                             array( $Rating, 'update_comment_post_meta'), 10, 3 );
		add_action( 'transition_comment_status',                array( $Rating, 'comment_status_change_callback'), 10, 3 );
		add_action( 'save_post',                                array( $Rating, 'add_default_rating' ) );
		/* Support order by rating archives */
		add_action( 'pre_get_posts',                            array( $Rating, 'sort_entries_by_rating' ) );


		/* Hooks for CSR_Comments_List class
		-----------------------------------------------------------------*/
        $CommentList = new CSR_Comments_List();

		/* Customize comment list	*/
		add_action( 'genesis_before_content',                   array( $CommentList,'custom_genesis_list_comments') );
		/* Add anchor to comments section title	*/
		add_filter( 'genesis_title_comments',                   array( $CommentList,'add_comments_title_markup'), 15, 1 );
		/* Remove comment form unless it's a comment reply page */
		// add_action( 'genesis_comment_form',                     array( $CommentList,'remove_recipe_comments_form'), 0 );
		/* Customize comment section title */
		add_filter( 'genesis_title_comments',                   array( $CommentList,'custom_comment_text') );
		/* Customize navigation links */
		add_filter( 'genesis_prev_comments_link_text',          array( $CommentList,'custom_comments_prev_link_text') );
		add_filter( 'genesis_next_comments_link_text',          array( $CommentList,'custom_comments_next_link_text') );



		/* Hooks for CSR_Form class
		-----------------------------------------------------------------*/
        $CommentForm = new CSR_Form();
		add_shortcode( 	'comment-rating-form',                 	array( $CommentForm, 'display_comment_form_with_rating_shortcode') );
		/* Disable logged in / logged out link */
		add_filter( 'comment_form_defaults',                    array( $CommentForm, 'change_comment_form_defaults') );
		/* Disable url input box in comment form unlogged users */
		add_filter( 'comment_form_default_fields',				array( $CommentForm, 'customize_comment_form') );

		add_filter( 'comment_form_submit_button',				array( $CommentForm, 'add_comment_recaptcha'), 15, 2 );



	}

}
