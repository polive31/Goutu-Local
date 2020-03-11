<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomSiteNotifications {

	public function __construct() {

		$Enqueue = new CSN_Public();
        add_action( 'wp_enqueue_scripts', 		array( $Enqueue, 'popups_styles_register' ) );

		$Mails = new CustomSiteMails();
		/* Event hooks */
		add_action( 'pending_to_publish',  			array( $Mails, 'published_post_notification_callback'), 10, 1 );
		add_action( 'transition_comment_status', 	array( $Mails, 'transition_comment_callback'), 10, 3 );
		add_action( 'comment_post', 				array( $Mails, 'insert_comment_callback'), 10, 3 );

		/* Mail Customizations */
		add_filter ( 'wp_mail_content_type', 	array( $Mails, 'html_mail_content_type'));
		add_filter ( 'wp_mail_from', 			array( $Mails, 'contact_address'));
		add_filter ( 'wp_mail_from_name', 		array( $Mails, 'site_name'));
		// add_filter( 'bp_core_signup_send_validation_email_message', array($this, 'custom_activation_link'), 10, 3 );

		$Popups = new CustomSitePopups();
		// The following action allows to instantiate the popups selectively depending on the post type & page
		add_action( 'wp', 						array( $Popups, 'create_popup_actions') );

		$Like = new CSN_Like();
		// Default value on save post (allows for sorting by like count)
		add_action('save_post',                 array('CSN_Like', 'add_default_like_count'));
		// Assets
		add_action('wp_enqueue_scripts', 		array($Like, 'enqueue_scripts'));
		// Sorting by like count
		add_action('pre_get_posts',             array($Like, 'sort_entries_by_like_count'));
		// Ajax
		add_action('wp_ajax_like_post', 		array($Like, 'ajax_like_post'));
		add_action('wp_ajax_nopriv_like_post', 	array($Like, 'ajax_like_post'));
		// Shortcodes
		add_shortcode('like-count', 			array($Like, 'like_count_shortcode'));

	}


}
