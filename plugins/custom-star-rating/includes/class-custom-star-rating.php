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
		add_action( 'genesis_before_comments',                 	array( $CommentList,'move_comments_form'), 0 );
		/* wrap the standard comment form into an id for comment_reply.js script to use this one instead of the rating one */
		// add_action( 'genesis_before_comment_form',              array( $CommentList,'comment_form_wrap_begin') );
		// add_action( 'genesis_after_comment_form',              	array( $CommentList,'comment_form_wrap_end') );

		/* Customize comment section title */
		add_filter( 'genesis_title_comments',                   array( $CommentList,'custom_comment_text') );
		/* Customize navigation links */
		add_filter( 'genesis_prev_comments_link_text',          array( $CommentList,'custom_comments_prev_link_text') );
		add_filter( 'genesis_next_comments_link_text',          array( $CommentList,'custom_comments_next_link_text') );



		/* Hooks for CSR_Form class
		-----------------------------------------------------------------*/
        $CommentForm = new CSR_Form();
		add_shortcode('comment-rating-form',                 	array( $CommentForm, 'display_comment_form_with_rating_shortcode') );
		/* Disable logged in / logged out link */
		add_filter( 'comment_form_defaults',                    array( $CommentForm, 'change_comment_form_defaults') );
		/* Disable url input box in comment form unlogged users */
		add_filter( 'comment_form_default_fields',				array( $CommentForm, 'customize_comment_form') );
		// adds the captcha to the WordPress form
		add_filter( 'rating_form_submit_button',				array( $CommentForm, 'rating_form_add_recaptcha'), 15, 2 );
		add_filter( 'comment_form_submit_button',				array( $CommentForm, 'comment_form_add_recaptcha'), 15, 2 );
		// Server side recaptcha verification
		add_filter( 'preprocess_comment', 						array($CommentForm, 'verify_comment_recaptcha'), 1, 1);

		/* DEPRECATED */
		// displays issue message if recaptcha failed
		// add_action('comment_form_top', 						array( $CommentForm, 'display_recaptcha_error') );
		// delete comment that fail the captcha challenge
		// add_action( 'wp_head', 									array( $CommentForm, 'delete_failed_captcha_comment'));
		// authenticate the captcha answer
		// add_filter( 'preprocess_comment', 						array( $CommentForm, 'validate_captcha_field'));
		// redirect location for comment
		// add_filter( 'comment_post_redirect', 					array( $CommentForm, 'redirect_fail_captcha_comment'), 10, 2);
	}



}
