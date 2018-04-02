<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class ContactFormPostType {

	public function __construct() {	
		add_action('init', array($this, 'ccf_create_contact_post_type'), 10);
	}

	public function ccf_create_contact_post_type() {

		/* Property */
		$labels = array(
			'name'                => _x('Contact Requests', 'Post Type General Name', 'textdomain'),
			'singular_name'       => _x('Contact Request', 'Post Type Singular Name', 'textdomain'),
			'menu_name'           => __('Contacts', 'textdomain'),
			'name_admin_bar'      => __('Contacts', 'textdomain'),
			'parent_item_colon'   => __('Parent Item:', 'textdomain'),
			'all_items'           => __('All Items', 'textdomain'),
			'add_new_item'        => __('Add New Item', 'textdomain'),
			'add_new'             => __('Add New', 'textdomain'),
			'new_item'            => __('New Item', 'textdomain' ),
			'edit_item'           => __('Edit Item', 'textdomain'),
			'update_item'         => __('Update Item', 'textdomain'),
			'view_item'           => __('View Item', 'textdomain'),
			'search_items'        => __('Search Item', 'textdomain'),
			'not_found'           => __('Not found', 'textdomain'),
			'not_found_in_trash'  => __('Not found in Trash', 'textdomain'),
		);
		$rewrite = array(
			'slug'                => _x('contact', 'contact', 'textdomain'),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => false,
		);
		$args = array(
			'label'               => __('contact', 'textdomain'),
			'description'         => __('Contacts', 'textdomain'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
			'taxonomies'          => array(''),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-testimonial',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => 'contact',
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type('contact', $args);	
	}

}

