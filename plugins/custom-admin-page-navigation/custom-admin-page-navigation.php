<?php
/*
Plugin Name: Custom Admin Page Navigation
Description: Quick Access to any page from admin bar at frontend / backend.
Author: sandesh055 modified polive
Version: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( "capn_Quick_Page_Navigation" ) ) {
	class capn_Quick_Page_Navigation {

		/*
		* Constructor function that initializes required actions and hooks
		* @Since 1.0
		*/
		function __construct() {

			// Activation & Deactivation
			register_deactivation_hook( __FILE__, array( $this, 'capn_plugin_deactivate' ) );
			register_activation_hook( __FILE__, array( $this, 'capn_plugin_activate' ) );

			// Check User and Add Menu
			add_action( 'init', array( $this, 'capn_check_user' ) );
		}

		// Plugin Deactivation
		function capn_plugin_deactivate() {
			if ( ! current_user_can( 'activate_plugins' ) )
				return;
		}

		// Plugin Activation
		function capn_plugin_activate() {
			if ( ! current_user_can( 'activate_plugins' ) )
				return;
		}

		function capn_check_user() {
			$user = wp_get_current_user();
			$allowed_roles = array('editor', 'administrator', 'author');

			if( is_user_logged_in() && !array_intersect( $allowed_roles, $user->roles ) ) {
				return;
			}

			if( !current_user_can( 'edit_others_pages' ) ) {
				return;
			}

			// Admin Bar Menu
			add_action( 'admin_bar_menu', array( $this, 'capn_add_admin_bar_wp_menu' ), 555 );
    		add_action('admin_enqueue_scripts', array( $this, 'capn_common_scripts' ), 555 );
    		add_action('wp_enqueue_scripts', array( $this, 'capn_common_scripts' ), 555 );
		}

		function capn_common_scripts() {
			wp_enqueue_style('sqpn-common', plugins_url('/assets/css/sqpn-common.css', __FILE__), array() );
			wp_enqueue_script('sqpn-common', plugins_url('/assets/js/sqpn-common.js', __FILE__), array('jquery'), '', true);
		}

		function capn_add_admin_bar_wp_menu( $wp_admin_bar ) {

			$wp_admin_bar->add_node(
				array(
		    		'id' => 'capn_wp_pages', // an unique id (required)
		    		'title' => 'WP Pages', // title/menu text to display
		    		'href' => admin_url( '/edit.php?post_type=page'), // target url of this menu item
		    		'meta' => array(
		    		    'class' => 'sqpn-wp-pages-menu',
		    		)
		    	)
		    );

		    $wp_admin_bar->add_node(
				array(
		    		'parent'	=> 'capn_wp_pages',
		    		'id' 		=> 'search_capn_wp_sub_pages', // an unique id (required)
		    		'title' 	=> '<input type="text" class="sqpn-search-input sqpn-wp-search-page" placeholder="Search Page" data-type="wp"/>',
		    		/*'href' 		=> '#', // target url of this menu item*/
		    		'meta' 		=> array(
		    		    'class' => 'sqpn-search-input-group sqpn-wp-pages-search',
		    		)
		    	)
		    );

			$this->capn_add_pages_to_menu($wp_admin_bar, 3, 0);
		}


		function capn_add_pages_to_menu($wp_admin_bar, $depth, $parent) {

			$options = array(
				'sort_column' => 'menu_order',
				'parent' => $parent,
				'post_type' => 'page',
				'post_status' => array('draft','publish', 'private', 'pending'),
			);
			$pages = get_pages($options);


			foreach ($pages as $page) {
				$id 	= $page->ID;
				$title 	= $page->post_title;
				$status 	= $page->post_status;
				$url  	= get_page_link( $id );

//				echo "<pre>";
//				var_dump( $id );
//				var_dump( $title );
//				var_dump( $url );
//				echo "</pre>";

				if ($parent==0)
					$parent_node = 'capn_wp_pages';
				else
					$parent_node = $parent . '_capn_wp_sub_pages';

				$wp_admin_bar->add_node(
					array(
			    		'parent'	=> $parent_node,
			    		'id' 		=> $id.'_capn_wp_sub_pages', // an unique id (required)
			    		'title' 	=> $title, // title/menu text to display
			    		'href' 		=> admin_url( '/post.php?post=' . $id . '&action=edit'),
			    		'meta' 		=> array(
			    		    'class' => 'sqpn-bb-pages-sub-menu' . ' status-' . $status,
			    		)
			    	)
			    );

				if ($depth>0) $this->capn_add_pages_to_menu($wp_admin_bar, $depth-1, $id);

			}
		}

	}
	new capn_Quick_Page_Navigation();
}
