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
	}


}
