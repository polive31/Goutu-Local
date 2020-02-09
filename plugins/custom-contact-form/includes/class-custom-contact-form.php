<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Contact_Form {

	public function __construct() {
		$Admin=new CCF_Admin();
		add_action('init', 									array($Admin, 'create_contact_post_type'), 10);
		add_action('updated_postmeta', 						array($Admin, 'hydrate'));
		add_action('admin_enqueue_scripts', 				array($Admin, 'enqueue_ccf_admin_js'));

		add_action('admin_menu', 							array($Admin, 'add_ccf_options'));
		add_action('admin_menu', 							array($Admin, 'add_ccf_submenus'));
		add_filter('parse_query', 							array($Admin, 'customize_ccf_post_query'));
		add_filter('query_vars', 							array($Admin, 'add_ccf_queryvars'));

		add_filter('manage_contact_posts_columns', 			array($Admin, 'set_contact_forms_columns'));
		add_action('manage_contact_posts_custom_column', 	array($Admin, 'contact_define_columns'), 10, 2);

		// add_filter('manage_edit-book_sortable_columns', 	array($Admin, 'contact_sortby'));
		// add_filter('request', 								array($Admin, 'contact_orderby'));
		// add_filter('pre_get_posts', 						array($Admin, 'restrict_events_by_meta'));
		// add_action('restrict_manage_posts', 				array($Admin, 'contacts_filter_restrict_posts'));


		add_action('add_meta_boxes_contact',				array($Admin, 'requester_meta_box'));
		add_action('add_meta_boxes_contact',				array($Admin, 'send_mail_meta_box'));
		add_action('add_meta_boxes_contact',				array($Admin, 'tokens_legend_meta_box'));
		add_action('add_meta_boxes_contact',				array($Admin, 'mail_history_meta_box'));
		add_action('wp_ajax_send_contact_as_mail',					array($Admin, 'ajax_send_contact_as_mail_cb'));
		add_action('wp_ajax_wp_ajax_nopriv_send_contact_as_mail',	array($Admin, 'ajax_send_contact_as_mail_cb'));


		$Public=new CCF_Public();
		add_shortcode('custom-contact-form', array($Public, 'ccf_shortcode'));
	}

}
