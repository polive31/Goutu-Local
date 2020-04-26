<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Define all hooks to be used by the different classes */

class Custom_Post_Management {

	public function __construct() {
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		// add_action( 'wp', array($this,'hydrate'));

		/* Hooks for Generic_Template class => global hooks, available for all post types, post type as argument
		/* Hooks for CPM_Assets class
		-----------------------------------------------------------------*/
		// CPM Assets
		add_action( 'init', 									'CPM_Assets::hydrate', 1 );
		add_action( 'wp_enqueue_scripts', 						'CPM_Assets::scripts_styles_enqueue' );

		// CPM Private
		add_filter('query_vars', 								'CPM_Private::custom_404_error_queryvar');
		add_action('template_redirect', 						'CPM_Private::redirect_private_content', 9);
		add_shortcode('custom-404', 							'CPM_Private::custom_404_page');

		$Status = new CPM_Post_Status();
		add_action('init', 										array($Status, 'hydrate'), 1);
		add_action('init', 										array($Status, 'register_restored_post_status'));
		add_action('admin_footer', 								array($Status, 'display_status_option_in_post_edit'));
		add_action('admin_footer-edit.php', 					array($Status, 'display_status_option_in_post_quick_edit'));
		add_filter('display_post_states', 						array($Status, 'display_status_in_post_grid'));
		add_filter('query_vars', 								array($Status, 'add_post_status_queryvar' ));
		add_filter('pre_get_posts',             				'CPM_Post_Status::remove_restored_from_archives');


		$Like = new CPM_Like();
		// Default value on save post (allows for sorting by like count)
		add_action('save_post',                 				array('CPM_Like', 'add_default_like_count'));
		// Assets
		add_action('wp_enqueue_scripts', 						array($Like, 'enqueue_scripts'));
		// Sorting by like count
		add_action('pre_get_posts',             				array($Like, 'sort_entries_by_like_count'));
		// Ajax
		add_action('wp_ajax_like_post', 						array($Like, 'ajax_like_post'));
		add_action('wp_ajax_nopriv_like_post', 					array($Like, 'ajax_like_post'));
		// Shortcodes
		add_shortcode('like-count', 							array($Like, 'like_count_shortcode'));

		$Post_Template = new CPM_Output();
		/* POST_TYPE-DEPENDENT HOOKS (to be instanciated for each new post type)
		-----------------------------------------------------------------------------------------*/
		add_action( 'cpm_post_toolbar', 						array($Post_Template, 'add_post_toolbar') );

		/* POST_TYPE-INDEPENDENT HOOKS (must be called only once here)
		-----------------------------------------------------------------------------------------*/
		// Create a dynamic hook for toolbar
		add_action( 'genesis_before_entry_content', 			array($Post_Template, 'add_post_type_toolbar_action'), 15);

		// Filters the post meta information, including post edit under the headline
		add_filter( 'genesis_post_info', 						'CPM_Output::custom_post_meta', 20 );

		// Add featured image to the post
		add_filter( 'the_content', 								array($Post_Template, 'add_featured_image'), 1);

		// Filters post thumbnail output in order to let lightbox plugin format them accordingly
		add_filter( 'the_content', 								array($Post_Template, 'add_lightbox_link'), 15 );

		/* Remove private/protected title mention */
		add_filter( 'genesis_post_title_text', 					array($Post_Template, 'escape_and_cleanup_title') );
		add_filter( 'private_title_format', 					array($Post_Template, 'remove_status_prefix_from_title') );
		add_filter( 'protected_title_format',					array($Post_Template, 'remove_status_prefix_from_title') );

		/* Modified read more link */
		add_filter( 'excerpt_more', 							array($Post_Template, 'foodie_pro_read_more_link' ) );
		add_filter( 'get_the_content_more_link', 				array($Post_Template, 'foodie_pro_read_more_link' ) );
		add_filter( 'the_content_more_link', 					array($Post_Template, 'foodie_pro_read_more_link' ) );

		/* Post Metadata rendering */
		if (class_exists('CSD_Meta')) {
			$Post_Meta = CSD_Meta::get_instance('post');
			/* We want this information to be populated first, in order to be superseded by others (e.g. recipe)
				therefore priority is set to 1 for posts */
			add_filter('csd_enqueue_post_meta',       				array($Post_Template, 	'enqueue_post_meta'), 1);
			add_action('wp_footer',   								array($Post_Meta, 		'render'));
		}


		/* Hooks for Submission Shortcodes
		IMPORTANT : must remain as separate static class
		-----------------------------------------------------------------*/
		$Shortcode = new CPM_Submission_Shortcode();
		add_shortcode( 'cpm-form', 								array($Shortcode, 'custom_submission_form_shortcode' ) );

		/* Hooks for CPM_Submission class
		-----------------------------------------------------------------*/
		$Post_Submission = new CPM_Submission('post');
		add_filter('wp_insert_post_empty_content',             'CPM_Submission::allow_empty_submission_form', 10, 2);

		/* POST_TYPE-DEPENDENT HOOKS (to be instanciated for each new post type)
		-----------------------------------------------------------------------------------------*/
		// Ajax callbacks for Custom Submission Form
        // add_action( 'admin_post_cpm_submit_post', 				array($Post_Submission, 'submit') );
        // add_action( 'admin_post', 								array($Post_Submission, 'submit') );
		// add_action( 'admin_post_nopriv_cpm_submit_post', 		array($Post_Submission, 'submit') );

		add_action( 'wp_ajax_cpm_remove_post_image', 			array($Post_Submission, 'ajax_remove_featured_image') );
		add_filter( 'cpm_post_section', 						array($Post_Submission, 'add_post_specific_section'), 15, 2 );
		add_action( 'wp_ajax_cpm_tinymce_upload_image', 		array($Post_Submission, 'ajax_tinymce_upload_image') );

		add_action( 'wp_ajax_cpm_upload_post_image', 			array($Post_Submission, 'ajax_upload_post_image') );

		// add_action( 'wp_ajax_nopriv_cpm_remove_featured_image', array($Post_Submission, 'ajax_remove_featured_image') );
		// add_action( 'wp_ajax_nopriv_cpm_tinymce_upload_image', 	array($Post_Submission, 'ajax_tinymce_upload_image') );

		// Support of french language on dropdowns
		add_filter( 'wp_dropdown_cats', 						array($Post_Submission, 'add_lang_to_select'));

		// Ajax callbacks for post autosave
		add_action('wp_ajax_post_autosave',                  	array($Post_Submission, 'ajax_post_autosave_cb'));

		/* Hooks for CPM_List class
		-----------------------------------------------------------------*/
		$CPM_Post_List = new CPM_List('post');
		// Ajax callbacks for custom Post List
        add_action( 'wp_ajax_cpm_delete_post', 					array( $CPM_Post_List, 'ajax_user_delete_post') );
		add_action( 'widgets_init', 							'cpm_list_dropdown_widget_init');

		/* Hooks for CPM Output shortcodes
		IMPORTANT : Always treat shortcodes as a separate class, independent from post type
		SO DO NOT MERGE WITH CPM_List or other post_type-dependant class !!!
		---------------------------------------------------------------------------------------*/
		$CPM_Output_Shortcodes = new CPM_Output_Shortcodes();
        add_shortcode( 'cpm-list', 								array($CPM_Output_Shortcodes, 'custom_post_list_shortcode' ) );
		add_shortcode( 'cpm-button', 							array($CPM_Output_Shortcodes, 'new_post_button' ) );

	}

}
