<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class Custom_Batch_Manage_Posts
{

	public function __construct()
	{

		// $Admin = new CBMP_Admin();
		// add_action('admin_menu',				array( $Admin, 'add_cbmp_options'));

		$Public = new CBMP_Public();
		add_action('wp_enqueue_scripts',		array( $Public, 'init_scripts'));


		$Post_Meta = new CBMP_Post_Meta();
		add_action("wp_ajax_ManageMeta", 				array($Post_Meta, "ajax_batch_manage_meta"));
		add_action("wp_ajax_nopriv_ManageMeta", 		array($Post_Meta, "ajax_batch_manage_meta"));
		add_shortcode('batch-manage-post-meta', 		array($Post_Meta, 'batch_manage_meta_shortcode'));

		$Tax_Meta = new CBMP_Taxonomy_Meta();
		add_action("wp_ajax_ManageTaxMeta", 			array($Tax_Meta, "ajax_batch_manage_tax_meta"));
		add_action("wp_ajax_nopriv_ManageTaxMeta", 		array($Tax_Meta, "ajax_batch_manage_tax_meta"));
		add_shortcode('batch-manage-tax-meta', 			array($Tax_Meta, 'batch_manage_taxonomy_meta_shortcode'));

		// $Comments = new CBMP_Comments();
		// add_action("wp_ajax_DeleteComment", 		array($Comments, "ajax_batch_delete_comments"));
		// add_action("wp_ajax_nopriv_DeleteComment", 	array($Comments, "ajax_batch_delete_comments"));
		// add_shortcode('batch-delete-comments', 		array($Comments, 'batch_delete_comments_shortcode'));

		// $Post_Ratings = new CBMP_Post_Ratings();
		// add_action("wp_ajax_MigrateRatings", 		array($Post_Ratings, "ajax_migrate_ratings"));
		// add_action("wp_ajax_nopriv_MigrateRatings", array($Post_Ratings, "ajax_migrate_ratings"));
		// add_shortcode('batch-migrate-ratings', 		array($Post_Ratings, 'batch_migrate_ratings_shortcode'));

	}

}
