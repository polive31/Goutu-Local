<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_Site_Notifications {

	public function __construct() {

		$Enqueue = new CSN_Public();
        add_action( 'wp_enqueue_scripts', 		array( $Enqueue, 'popups_styles_register' ) );

		$Mails = new CSN_Mails();
		/* Post publish hooks */
		add_action( 'pending_to_publish',  			array( $Mails, 'published_post_notification_callback'), 10, 1 );
		add_action( 'draft_to_pending',  			array( $Mails, 'pending_post_notification_callback'), 10, 1 );

		/* Comments publish hooks */
		add_action( 'transition_comment_status', 	array( $Mails, 'transition_comment_callback'), 10, 3 );
		add_action( 'comment_post', 				array( $Mails, 'insert_comment_callback'), 10, 3 );

		/* Mail Customizations */
		add_filter ( 'wp_mail_content_type', 		array( $Mails, 'html_mail_content_type'));
		add_filter ( 'wp_mail_from', 				array( $Mails, 'contact_address'));
		add_filter ( 'wp_mail_from_name', 			array( $Mails, 'site_name'));

		$Popups = new CSN_Popups();
		// The following action allows to instantiate the popups selectively depending on the post type & page
		add_action( 'wp', 							array( $Popups, 'create_popup_actions') );

		$Notifications = new CSN_Notifications();
		// add_action('wp_loaded',						array( $Notifications, 'setup_notifications'));
		add_action('foodiepro_send_notification',	array( $Notifications, 'send_notification_on_event'), 10, 4);
	}


}
