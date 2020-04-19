<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Custom_Recent_Posts_Widget {

	public function __construct() {
		// Load the admin style.
		add_action( 'admin_enqueue_scripts', 			array( &$this, 'admin_style' ) );
		// Register widget.
		add_action( 'widgets_init', 					array( &$this, 'register_widget' ) );
		// Register new image size.
		add_action( 'init', 							array( &$this, 'register_image_size' ) );


		$RPWE = new RPWE_Customizations();
		// Customize taxonomies list in the "display overlay" form option list
		add_filter('rpwe_overlay_tax_list', 			array($RPWE, 'rpwe_custom_overlay_tax_list'));
		// Add overlay over rpwe thumbnails
		add_filter('rpwe_in_thumbnail', 				array($RPWE, 'rpwe_add_overlay'), 10, 2);
		// Add post author to RPWE  widget
		add_filter('rpwe_post_title_meta', 				array($RPWE, 'rpwe_add_author'), 10, 2);
		/* Modify WP Recent Posts extended output, depending on the css ID field value */
		add_filter('rpwe_after_thumbnail', 				array($RPWE, 'wprpe_add_avatar'), 20, 2);
		/* Modify WPRPE output, displaying posts from current logged-in user */
		add_filter('rpwe_default_query_arguments', 		array($RPWE, 'wprpe_query_user_posts'));
		/* Workaround for shortcodes in rpwe "after" html not executing */
		// add_filter( 'rpwe_markup', 					array($this, 'add_more_from_author_link'),15, 2 );
		/* Prevent redundant posts when several rpwe instances are called on the same page */
		add_action('rpwe_loop', 						array($RPWE, 'rpwe_get_queried_posts'));
		/* ??? */
		add_filter('rpwe_default_query_arguments', 		array($RPWE, 'rpwe_exclude_posts'));
		/* Add user rating to RPWE widget */
		add_filter('rpwe_post_title', 					array($RPWE, 'rpwe_add_rating'), 10, 2);
		/* Modify WP Recent Posts ordering, depending on the orderby field value */
		add_filter('rpwe_default_query_arguments', 	array($RPWE, 'wprpe_orderby_rating'));

	}


	/**
	 * Register custom style for the widget settings.
	 *
	 * @since  0.8
	 */
	public function admin_style() {
		// Loads the widget style.
		wp_enqueue_style( 'rpwe-admin-style', trailingslashit( RPWE_ASSETS ) . 'css/rpwe-admin.css', null, null );
	}

	/**
	 * Register the widget.
	 *
	 * @since  0.9.1
	 */
	public function register_widget() {
		// require_once( RPWE_CLASS . 'widget.php' );
		register_widget( 'Recent_Posts_Widget_Extended' );
	}

	/**
	 * Register new image size.
	 *
	 * @since  0.9.4
	 */
	function register_image_size() {
		add_image_size( 'rpwe-thumbnail', 45, 45, true );
	}

}
