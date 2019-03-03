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
		-----------------------------------------------------------------*/
		$Post_Template = new Custom_Post_Template();
		
		/* POST_TYPE-DEPENDENT HOOKS (to be instanciated for each new post type)		
		-----------------------------------------------------------------------------------------*/
		add_action( 'cpm_post_toolbar', 			array($Post_Template, 'add_post_toolbar') );			

		/* POST_TYPE-INDEPENDENT HOOKS (must be called only once here) 
		-----------------------------------------------------------------------------------------*/
		// Create a dynamic hook for toolbar
		add_action( 'genesis_before_entry_content', array($Post_Template, 'add_post_type_toolbar_action'), 15);		

		// Filters the post meta information, including post edit under the headline
		add_filter( 'genesis_post_info', 'Custom_Post_Template::custom_post_meta', 20 );

		// Filters post thumbnail output in order to let lightbox plugin format them accordingly
		add_filter( 'the_content', 					array($Post_Template, 'add_lightbox_link') );

		/* Remove private/protected title mention */
		add_filter( 'private_title_format', 		array($Post_Template, 'title_format') );
		add_filter( 'protected_title_format',		array($Post_Template, 'title_format') );

		/* Modified read more link */
		add_filter( 'excerpt_more', 				array($Post_Template, 'foodie_pro_read_more_link' ) );
		add_filter( 'get_the_content_more_link', 	array($Post_Template, 'foodie_pro_read_more_link' ) );
		add_filter( 'the_content_more_link', 		array($Post_Template, 'foodie_pro_read_more_link' ) );	

				
		
		/* Hooks for CPM_Assets class
		-----------------------------------------------------------------*/		
		// $CPM_Assets = new CPM_Assets();
		// add_action( 'wp', 							array($CPM_Assets,'hydrate'));
		// add_action( 'wp_enqueue_scripts', 			array($CPM_Assets, 'scripts_styles_enqueue' ) );
		add_action( 'wp', 						'CPM_Assets::hydrate' );
		add_action( 'wp_enqueue_scripts', 		'CPM_Assets::scripts_styles_enqueue' );


		
		/* Hooks for CPM_Submission class
		-----------------------------------------------------------------*/		
		$CPM_Post_Submission = new CPM_Submission('post');
		/* POST_TYPE-DEPENDENT HOOKS (to be instanciated for each new post type)		
		-----------------------------------------------------------------------------------------*/
		add_filter( 'cpm_post_section', 						array( $CPM_Post_Submission, 'add_post_specific_section'), 15, 3 );	
		
		// Ajax callbacks for Custom Submission Form
        add_action( 'wp_ajax_cpm_remove_featured_image', 		array( $CPM_Post_Submission, 'ajax_remove_featured_image') );
		add_action( 'wp_ajax_nopriv_cpm_remove_featured_image', array( $CPM_Post_Submission, 'ajax_remove_featured_image') );

		add_action( 'wp_ajax_cpm_tinymce_upload_image', 		array( $CPM_Post_Submission, 'ajax_tinymce_upload_image') );
		add_action( 'wp_ajax_nopriv_cpm_tinymce_upload_image', 	array( $CPM_Post_Submission, 'ajax_tinymce_upload_image') );

		// Support of french language on dropdowns
		add_filter( 'wp_dropdown_cats', 						array($CPM_Post_Submission, 'add_lang_to_select'));
		

		
		/* Hooks for CPM_List class
		-----------------------------------------------------------------*/			
		$CPM_Post_List = new CPM_List('post');
		// Ajax callbacks for custom Post List
        add_action( 'wp_ajax_cpm_delete_post', 					array( $CPM_Post_List, 'ajax_user_delete_post') );
		add_action( 'wp_ajax_nopriv_cpm_delete_post', 			array( $CPM_Post_List, 'ajax_user_delete_post') );
		

		
		/* Hooks for Shortcodes
		-----------------------------------------------------------------*/
		$CPM_List_Shortcodes = new CPM_List_Shortcodes();
        add_shortcode( 'cpm-list', array( $CPM_List_Shortcodes, 'custom_submission_form_list_shortcode' ) );
		add_shortcode( 'cpm-button', array( $CPM_List_Shortcodes, 'new_post_button' ) );
		

		$CPM_Submission_Shortcodes = new CPM_Submission_Shortcodes();
        add_shortcode( 'cpm-form', array( $CPM_Submission_Shortcodes, 'custom_submission_form_shortcode' ) );
	}

}