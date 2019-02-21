<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Assets {
	
	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	private static $PLUGIN_URI;	
	private static $PLUGIN_PATH;	


	public function __construct() {
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
	}


/********************************************************************************
****                DISABLE WPURP ENQUEUE                        **********
********************************************************************************/
	public function enqueue_wpurp_css( $css_enqueue ) {
		if ( is_admin() ) 
		return $css_enqueue;
		else 
		return array();
	}
	
	public function enqueue_wpurp_js( $js_enqueue ) {
		if ( is_admin() ) 
		return $js_enqueue;
		else 
		return array();
	}	
	
	/********************************************************************************
	****               ADD RECIPE POST TYPE FIELDS TO CPM_Assets                 ****
	********************************************************************************/		
	public function setup_CPM_recipe_page_slugs( $slugs ) {
		$slugs['recipe_list'] = 'publier-recettes';
		$slugs['recipe_form'] = 'saisie-recette';
		return $slugs;
	}        
	
	public function setup_CPM_required( $required ) {
		$required['recipe']= array(
			'recipe_title'			=> __('Recipe Title','foodiepro'),
			'recipe_course' 		=> __('Recipe Course', 'foodiepro'),
			'recipe_difficult' 		=> __('Recipe Difficulty', 'foodiepro'),
			'recipe_ingredients' 	=> __('Ingredients', 'foodiepro'),
			'recipe_servings' 		=> __('Number of Servings', 'foodiepro'),
			'recipe_prep_time' 		=> __('Preparation Time', 'foodiepro'),
		);
		return $required;
	}	
	
	public function setup_CPM_taxonomies( $taxonomies ) {
		$taxonomies['recipe'] = array(    
			'post_tag' => array(
				'multiselect' => true,
				'orderby' 	=> 'name',
				'required'	=> false,
				'labels'	=>array(
					'singular_name'=>__( 'Keywords', 'foodiepro' ),
				),
			),
			'course' => array(
				'multiselect' => false,
				'orderby' 	=> 'description',
				'required'	=> true,
				'labels'	=> array(
					'singular_name'=>__( 'Course', 'foodiepro' ),
				),
			),
			'cuisine' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'name',
				'hierarchical'	=> true,
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Cuisine', 'foodiepro' ),
				),
			),
			'season' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Season', 'foodiepro' ),
				),
			),		
			'occasion' => array(
				'multiselect' 	=> true,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Occasion', 'foodiepro' ),
				),
			),	
			'diet' => array(
				'multiselect' 	=> true,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Diet', 'foodiepro' ),
				),
			),	
			'difficult' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'description',
				'required'		=> true,
				'labels'	=> array(
					'singular_name'=>__( 'Difficulty', 'foodiepro' ),
				),
			),										
		);

		return $taxonomies;
	}	
	
	public function setup_CPM_recipe_labels( $labels ) {
		$labels['recipe'] = array(
			'title'			=> _x( 'Post Title', 'recipe', 'foodiepro' ),
			'edit_button'	=> _x( 'Edit Post', 'recipe', 'foodiepro' ),
			'new_button'	=> _x( 'New Post', 'recipe', 'foodiepro' ),
			'new1'			=> _x( 'Write your new post on this page.', 'recipe', 'foodiepro' ),
			'new2'			=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it will be visible to others according to your visibility preferences.', 'recipe', 'foodiepro' ),
			'edit1'			=> _x( 'Edit your existing post on this page.', 'recipe', 'foodiepro' ),
			'edit2' 		=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it wil be visible to others according to your visibility preferences.', 'recipe', 'foodiepro' ),
			'draft1'		=> _x( 'Post saved as a draft.','recipe', 'foodiepro'),
			'draft2'		=> _x( 'It will not be visible on the site, but you can edit it at any time and submit it later.','recipe','foodiepro'),
			'back'			=> _x( 'Back to <a href="%s">my posts</a>.', 'recipe', 'foodiepro'),
			'publish-admin'	=> _x( 'Dear administrator, this post is now <a href="%s">published</a>.', 'recipe', 'foodiepro' ),
			'publish-user'	=> _x( 'Post submitted! Thank you, your post is now awaiting moderation.', 'recipe', 'foodiepro' ),
			'required'		=> _x( 'In order for your post to be published, please fill-in those required fields:', 'recipe', 'foodiepro' ),
		);
		return $labels;
	}
	
	/********************************************************************************
	****                ADD ENQUEUED SCRIPTS & STYLES TO CPM_Assets                    **********
	********************************************************************************/		
	public function setup_CPM_recipe_styles( $styles ) {
		// Enables custom gallery shortcode & stylesheet loading
		new Custom_Gallery_Shortcode();
		new Tooltip();
		
		// Reuse some default post styles
		$styles['cpm-list']['location'][]			='recipe_list';
		$styles['cpm-select2']['location'][]		='recipe_form';
		$styles['cpm-submission-form']['location'][]='recipe_form';
		$styles['post-font']['location'][]			='recipe';
		
		// Enqueue specific recipe styles
		$styles['crm-recipe'] = array(
			'file' 		=> 'assets/css/custom-recipe.css',
			'uri' 		=> self::$PLUGIN_URI,
			'dir' 		=> self::$PLUGIN_PATH,
			'location' 	=> array('recipe'),
		);

		$styles['crm-autocomplete'] = array(
			'file' 		=> 'jquery.auto-complete.css',
			'uri' 		=> self::$PLUGIN_URI . 'vendor/autocomplete/',
			'dir' 		=> self::$PLUGIN_PATH . 'vendor/autocomplete/',
			'location' 	=> array('recipe_form'),
		);
				
		$styles['crm-form'] = array(
			'file' 		=> 'custom-recipe-submission.css',
			'uri' 		=> self::$PLUGIN_URI . 'assets/css/',
			'dir' 		=> self::$PLUGIN_PATH . 'assets/css/',
			'location' 	=> array('recipe_form'),
		);		  
				
		return $styles;
	}
			
			
	public function setup_CPM_recipe_scripts( $scripts ) {	
			
		$pause = '<i class="fa fa-pause" aria-hidden="true"></i>';
		$play = '<i class="fa fa-play" aria-hidden="true"></i>';
		$close = '<i class="fa fa-times" aria-hidden="true"></i>';
		$stop = '<i class="fa fa-stop" aria-hidden="true"></i>';
		$repeat = '<i class="fa fa-repeat" aria-hidden="true"></i>';
		$prev = '<i class="fa fa-step-backward" aria-hidden="true"></i>';
		$next = '<i class="fa fa-step-forward" aria-hidden="true"></i>';


		// Reuse some default post scripts
		$scripts['cpm-list']['data']['confirm_message'] = _x( 'Are you sure you want to delete this post :', 'recipe', 'foodiepro' );
		$scripts['cpm-list']['location'][]='recipe_list';

		$scripts['cpm-select2-taxonomies']['location'][]='recipe_form';
		$scripts['cpm-select2']['location'][]='recipe_form';
		$scripts['cpm-select2-fr']['location'][]='recipe_form';
		$scripts['cpm-tinymce']['location'][]='recipe_form';
		$scripts['jquery-touch-punch']['location'][]='recipe_form';


		$scripts['crm-fraction'] = array (
			'file' 		=> 'fraction.js',
			'uri' 		=> self::$PLUGIN_URI . 'vendor/fraction/',
			'dir' 		=> self::$PLUGIN_PATH . 'vendor/fraction/',
			'footer' 	=> true,
			'location' 	=> array('recipe'),
		);
		
		$scripts['crm-print_button'] = array (
			'file' 		=> 'print_button.js',
			'uri'		=> self::$PLUGIN_URI . 'assets/js/',
			'dir' 		=> self::$PLUGIN_PATH . 'assets/js/',
			'deps' 		=> array(
				'jquery',
			),
			'footer' 	=> true,
			'data' 		=> array(
				'name' 		=> 'wpurp_print',
				'ajaxurl' 	=> WPUltimateRecipe::get()->helper('ajax')->url(),
				'nonce' 	=> wp_create_nonce( 'wpurp_print' ),
				// 'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
				'custom_print_css_url' => self::$PLUGIN_URI . '/assets/css/custom-recipe-print.css',
				'coreUrl' 	=> WPUltimateRecipe::get()->coreUrl,
				'premiumUrl'=> WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
				'title' 	=> __('Print this Recipe','foodiepro'),
				'permalinks'=> get_option('permalink_structure'),
			),
			'location' 	=> array('recipe'),
		);
		
		$scripts['crm-voice'] = array (
			'file' 			=> 'custom_text_to_speech.js',
			'uri' 			=> self::$PLUGIN_URI . '/assets/js/',
			'dir' 			=> self::$PLUGIN_PATH . '/assets/js/',
			'footer' => true,
			'deps' => array(
				'jquery',
				'crm-responsive-voice',
			),
			'data' => array(
				'name' => 'recipeRead',
				'voice' => 'French Female',
				'icon' => array(
					'prev' 	=> $prev,
					'next' 	=> $next,
					'repeat' => $repeat,
					'play' 	=> $play,
					'pause' => $pause,
					'stop' 	=> $stop,
				),
				'title' => array(
					'prev' 	=> __('Read previous step','foodiepro'),
					'next' 	=> __('Read next step','foodiepro'),
					'repeat' => __('Read this step again','foodiepro'),
					'pause' => __('Pause reading','foodiepro'),
					'play' 	=> __('Continue reading','foodiepro'),
					'stop' 	=> __('Stop reading and close player','foodiepro'),
				),							
			),
			'location' 		=> array('recipe'),
		);
			
		$scripts['crm-responsive-voice'] = array (
			'uri' 		=> 'https://code.responsivevoice.org/responsivevoice.js',
			'footer' 	=> true,
			'location' 	=> array('recipe'),					
		);								

		$scripts['crm-timer'] = array (
			'file' 		=> 'timer.js',
			'uri' 		=> self::$PLUGIN_URI . '/assets/js/',
			'dir' 		=> self::$PLUGIN_PATH . '/assets/js/',
			'footer' 	=> true,
			'deps' 		=> array(
				'jquery',
			),
			'data' 		=> array(
				'name' => 'wpurp_timer',
				'icons' => array(
					'play' => $play,
					'pause' => $pause,
					'close' => $close,
				),
			),
			'location' 	=> array('recipe'),
		);
			
		$scripts['crm-adjustable-servings'] = array (
			'file' 		=> 'custom_adjustable_servings.js',
			'uri' 		=> self::$PLUGIN_URI . 'assets/js/',
			'dir' 		=> self::$PLUGIN_PATH . 'assets/js/',
			'footer' 	=> true,
			'deps' 		=> array(
				'jquery',
				'crm-fraction',
				'crm-print_button',
			),
			'data' 		=> array(
				'name' 				=> 'wpurp_servings',
				'precision' 		=> 1,
				'decimal_character' => ',',
			),
			'location' 	=> array('recipe'),
		);
		
		$scripts['crm-favorite-recipe'] = array (
			'file' 		=> 'custom_favorite_recipe.js',
			'uri' 		=> self::$PLUGIN_URI . 'assets/js/',
			'path' 		=> self::$PLUGIN_PATH . 'assets/js/',
			'footer' 	=> true,
			'setting' 	=> array( 'favorite_recipes_enabled', '1' ),
			'deps' 		=> array(
				'jquery',
			),
			'data' 	=> array(
				'name' 		=> 'custom_favorite_recipe',
				'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
				'nonce' 	=> wp_create_nonce( 'custom_favorite_recipe' ),
			),
			'location' 	=> array('recipe'),
		);	  
			
		$scripts['crm-autocomplete'] = array (
			'file' 		=> 'jquery.auto-complete.js',
			'uri' 		=> self::$PLUGIN_URI . 'vendor/autocomplete/',
			'dir' 		=> self::$PLUGIN_PATH . 'vendor/autocomplete/',
			'deps' 		=> array(
				'jquery',
			),
			'location' 	=> array('recipe_form'),
		);
			
		$scripts['crm-unit-suggestion'] = array (
			'file' => 'ingredient_unit_suggestion.js',
			'uri' => self::$PLUGIN_URI . 'assets/js/',
			'dir' => self::$PLUGIN_PATH . 'assets/js/',
			'deps' => array(
				'jquery',
				'crm-autocomplete',
			),
			'footer' => true,
			'location' 	=> array('recipe_form'),
		);
			
		$scripts['crm-ingredient-preview'] = array (
			'file' => 'ingredient_preview.js',
			'uri' => self::$PLUGIN_URI . 'assets/js/',
			'dir' => self::$PLUGIN_PATH . 'assets/js/',
			'deps' => array(
				'jquery',
				'crm-autocomplete',
			),
			'footer' => true,
			'data' => array(
				'name' 		=> 'ingredient_preview',
				'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
				'nonce' 	=> wp_create_nonce('preview_ingredient'),
			),		                
			'location' 	=> array('recipe_form'),
		);	
		
		$scripts['crm-submission'] = array (
			'file' => 'custom_recipe_submission.js',
			'uri' => self::$PLUGIN_URI . 'assets/js/',
			'path' => self::$PLUGIN_PATH . 'assets/js/',
			'deps' => array(
				'cpm-tinymce',
				'jquery',
				'jquery-ui-sortable',
			),
			'footer' => true,
			'data' => array(
				'name' => 'custom_recipe_submission_form',
				'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
				'nonce' 	=> wp_create_nonce('custom_recipe_submission_form'),
				'placeholder' => WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png',
				'fileTypes' => self::ATTACHMENT_FORMATS,
				'wrongFileType' => sprintf(__('Authorized file formats are : %s','foodiepro'),implode(', ',self::ATTACHMENT_FORMATS)),
				'maxFileSize' => self::MAX_ATTACHMENT_SIZE_KB,
				'fileTooBig' => sprintf(__('The maximum file size is %s kB','foodiepro'), self::MAX_ATTACHMENT_SIZE_KB),
				'deleteImage' => __('Do you really want to delete this image ?','foodiepro'),
			),					
			'location' 	=> array('recipe_form'),
		);            	
		
		return $scripts;
	}

}