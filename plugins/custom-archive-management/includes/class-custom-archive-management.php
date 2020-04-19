<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_Archive_Management {

	public function __construct() {

		$Assets = new CNH_Assets();
		add_action( 'wp_enqueue_scripts', 				array($Assets, 'enqueue_cnh_scripts'));

		/* Archive Metadata rendering */
		$Provide_Meta = new CNH_Archive_Meta();
		add_action('genesis_entry_header', 				array($Provide_Meta, 	'add_entry_to_items'));
		add_filter('csd_enqueue_archive_meta',       	array($Provide_Meta,	'enqueue_archive_meta'));
		$Archive_Meta = CSD_Meta::get_instance('archive');
		add_action('wp_footer',   						array($Archive_Meta, 	'render'));


		$Entries = new CNH_Archive_Entries();
		add_filter( 'genesis_post_title_output', 		array($Entries, 'archive_rating' ), 1 );
		/* Customize archive pages */
		add_filter( 'post_class', 						array($Entries, 'set_grid_columns'));
		add_filter( 'genesis_before_entry', 			array($Entries, 'reorganize_entry_content') );


		$Tags = new CNH_Tags_Overlay();
		add_action( 'genesis_entry_header', 			array($Tags, 'do_post_title_before'), 1 );
		add_action( 'genesis_entry_header', 			array($Tags, 'do_post_title_after') );


		$Headline = new CNH_Archive_Headline();
		add_filter( 'init', 							array($Headline, 'hydrate') );
		// Headline text
		add_filter( 'genesis_archive_title_text', 		array($Headline, 'custom_archive_title') );
		add_filter( 'genesis_search_title_text', 		array($Headline, 'custom_search_title_text') );
		// Intro text
		add_filter( 'genesis_term_intro_text_output', 	'wpautop' );
		add_filter( 'genesis_archive_description_text', array($Headline, 'custom_archive_description') );
		// add_filter( 'genesis_term_intro_text_output', 'wpautop' );
		//Customize explorer tab text
		add_filter('document_title_parts', 				array($Headline, 'get_seo_friendly_page_title'), 99, 1);
		add_shortcode('wpseo_title', 					array($Headline, 'get_seo_friendly_page_title'));


		// $Shortcodes = new CNH_Shortcodes();
		// add_shortcode('taxonomy-terms', 				array($Shortcodes, 'simple_list_taxonomy_terms'));

	}

}
