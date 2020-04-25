<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Define all hooks to be used by the different classes */

class Custom_Comment_Management {

	public function __construct() {

		/* Hooks for CCM_Assets class (static)
		-----------------------------------------------------------------*/
        $Assets = new CCM_Assets();
		add_action( 'wp_enqueue_scripts',                       'CCM_Assets::enqueue_ccm_assets' );
		// add_action( 'wp_loaded', 								'CCM_Assets::remove_comment_reply_script');


		/* Hooks for CCM_Comments_List class
		-----------------------------------------------------------------*/
		$CommentList = new CCM_Comments_List();

		/* Comments structure is handled in \themes\genesis\lib\structure\comments.php */

		add_filter( 'edit_comment_link',                   		array( $CommentList, 'remove_comment_link') );

		/* Add anchor to comments section title	*/
		add_filter( 'genesis_title_comments',                   array( $CommentList, 'add_comments_title_markup'), 15, 1 );

		/* Add custom "reply to" text to each comment's reply link, to be displayed when "reply to" button is pressed */
		add_action( 'comment_reply_link',                 		array( $CommentList, 'add_reply_to'), 10, 4 );

		/* Customize comment item */
		add_filter( 'comment_author_says_text', 				array( $CommentList, 'custom_comment_author_says'));
		add_filter( 'genesis_show_comment_date', 				array( $CommentList, 'custom_comment_date'));
		add_filter( 'get_comment_author', 						array( $CommentList, 'add_comment_author_link'), 10, 3);

		/* Move comment form on top of the comments list */
		add_action( 'genesis_before_comments',                 	array( $CommentList, 'move_comments_form'), 0 );

		/* Customize comment section title */
		// add_filter( 'genesis_title_comments',                   array( $CommentList,'custom_comment_text') );

		/* Customize navigation links */
		add_filter( 'genesis_prev_comments_link_text',          array( $CommentList, 'custom_comments_prev_link_text') );
		add_filter( 'genesis_next_comments_link_text',          array( $CommentList, 'custom_comments_next_link_text') );


		/* Hooks for CSR_Form class
		-----------------------------------------------------------------*/
		$CommentForm = new CCM_Form();
		/* Force comment form display on all posts types */
		add_filter('comments_open', 							array($CommentForm, 'force_comment_form_display'), 20, 2);

		/* Remove url input box from comment box, add author and email */
		add_filter( 'comment_form_default_fields',				array( $CommentForm, 'customize_comment_form') );

		/* Format the comment form for "new/reply to" title toggle in JS */
		add_filter( 'comment_form_defaults',                    array( $CommentForm, 'change_comment_form_defaults') );

		// adds the captcha to the WordPress form
		add_filter( 'comment_form_submit_button',				array( $CommentForm, 'comment_form_add_recaptcha'), 15, 2 );

		// Server side recaptcha verification
		add_filter( 'preprocess_comment', 						array( $CommentForm, 'verify_comment_recaptcha'), 1, 1);

	}

}
