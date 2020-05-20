<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_SEO {

	public function __construct() {
		$Admin = new CSEO_Admin();
		add_filter('wp_insert_post_data', 				array($Admin, 'set_post_excerpt'), 15, 2);

		$Public = new CSEO_Public();
		add_action('genesis_after_header', 				array($Public, 'foodiepro_do_breadcrumbs'));
		add_filter('wpseo_sitemap_exclude_taxonomy', 	array($Public, 'sitemap_exclude_taxonomy'), 10, 2);
		add_filter('wpseo_title', 						array($Public, 'wpseo_uppercase_title'));
	// add_filter('wpseo_metadesc', 'foodiepro_populate_metadesc', 100, 1);
	// add_action('genesis_meta','add_pinterest_meta'); /* Already done in YOAST SEO */
    // add_filter('wpseo_breadcrumb_single_link', 'foodiepro_edit_breadcrumbs', 10, 2);

	}


}
