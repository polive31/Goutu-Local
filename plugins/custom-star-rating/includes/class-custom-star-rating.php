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

		/* Hooks for CSR_Assets class
		-----------------------------------------------------------------*/		
		add_action( 'wp',                                       'CSR_Assets::hydrate' );
		add_filter( 'wpurp_register_ratings_taxonomy',          'CSR_Assets::translate_ratings_taxonomy' );
        add_action( 'wp_enqueue_scripts',                       'CSR_Assets::register_star_rating_style' );	
                

        /* Hooks for CSR_Meta class
        -----------------------------------------------------------------*/		
        $Meta = new CSR_Meta();
        add_action( 'comment_post',                             array( $Meta, 'update_comment_post_meta'), 10, 3 );
		add_action( 'edit_comment',                             array( $Meta, 'update_comment_post_meta'), 10, 3 );
		add_action( 'transition_comment_status',                array( $Meta, 'comment_status_change_callback'), 10, 3 );	
		add_action( 'save_post',                                array( $Meta, 'add_default_rating' ) );
        
		
		/* Hooks for CSR_Comments_List class
		-----------------------------------------------------------------*/		
        $CommentList = new CSR_Comments_List();
        add_action( 'genesis_before_content',                   array( $CommentList,'custom_genesis_list_comments') );
		/* Add anchor to comments section title	*/ 
		add_filter('genesis_title_comments',                    array( $CommentList,'add_comments_title_markup'), 15, 1 );
		/* Remove comment form unless it's a comment reply page */ 
		add_action( 'genesis_comment_form',                     array( $CommentList,'remove_recipe_comments_form'), 0 );
		/* Disable logged in / logged out link */ 
		add_filter( 'comment_form_defaults',                    array( $CommentList,'change_comment_form_defaults') );
		/* Customize comment section title */ 
		add_filter('genesis_title_comments',                    array( $CommentList,'custom_comment_text') );
		/* Customize navigation links */ 
		add_filter('genesis_prev_comments_link_text',           array( $CommentList,'custom_comments_prev_link_text') );
		add_filter('genesis_next_comments_link_text',           array( $CommentList,'custom_comments_next_link_text') );
		/* Disable url input box in comment form unlogged users */ 
        add_filter('comment_form_default_fields',               array( $CommentList,'customize_comment_form') );
        
        
		
		/* Hooks for CSR_Shortcodes class
		-----------------------------------------------------------------*/		
        $Shortcode = new CSR_Shortcodes();
		add_shortcode( 'comment-rating-form',                   array( $Shortcode, 'display_comment_form_with_rating_shortcode') );
        add_shortcode( 'json-ld-rating', 			            array( $Shortcode, 'display_json_ld_rating_shortcode') );
		add_shortcode( 'display-star-rating',                   array( $Shortcode, 'display_star_rating_shortcode') );



	}

}