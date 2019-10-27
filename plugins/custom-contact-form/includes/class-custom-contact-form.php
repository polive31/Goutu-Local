<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Contact_Form {

	public function __construct() {
		$Admin=new CCF_Admin();
		add_action('init', 					array($Admin, 'create_contact_post_type'), 10);
		add_action('admin_menu', 			array($Admin, 'add_ccf_options'));

		$Public=new CCF_Public();
		add_shortcode('custom-contact-form', array($Public, 'ccf_shortcode'));
	}

}
