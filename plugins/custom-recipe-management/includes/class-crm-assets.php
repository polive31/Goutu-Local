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
	 ****                SETUP TAXONOMIES LIST                        ***
	 ********************************************************************************/

	public static function get_taxonomies() {
		$taxonomies = array(
			'ingredient'	=> array( __('Ingredients', 'crm'), __('Ingredient', 'crm')),
            'course'    	=> array( __('Courses', 'crm'),  	__('Course', 'crm')),
            'occasion'  	=> array( __('Occasions', 'crm'),  	__('Occasion', 'crm')),
            'cuisine'   	=> array( __('Cuisines', 'crm'),  	__('Cuisine', 'crm')),
            'diet'      	=> array( __('Diets', 'crm'),  		__('Diet', 'crm')),
            'season'    	=> array( __('Seasons', 'crm'),  	__('Season', 'crm')),
            'difficult'   	=> array( __('Levels', 'crm'),  	__('Level', 'crm')),
		);
		return $taxonomies;
	}

	/********************************************************************************
	****               ADD RECIPE POST TYPE FIELDS TO CPM_Assets                 ****
	********************************************************************************/
	public function setup_CPM_recipe_page_slugs( $slugs ) {
		$slugs['recipe_list'] = 'publier-recettes';
		$slugs['recipe_favorites'] = 'favoris-recettes';
		$slugs['recipe_form'] = 'saisie-recette';
		$slugs['recipe_print'] = __('print', 'crm');
		return $slugs;
	}

	public function setup_CPM_required( $required ) {
		$required['recipe']= array(
			'recipe_title'			=> __('Recipe Title','crm'),
			'recipe_content'		=> __('Recipe Description','crm'),
			'recipe_course' 		=> __('Recipe Course', 'crm'),
			'recipe_difficult' 		=> __('Recipe Difficulty', 'crm'),
			'recipe_ingredients' 	=> __('Ingredients', 'crm'),
			'recipe_servings' 		=> __('Number of Servings', 'crm'),
			'recipe_prep_time' 		=> __('Preparation Time', 'crm'),
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
					'singular_name'=>__( 'Keywords', 'crm' ),
				),
			),
			'course' => array(
				'multiselect' => false,
				'orderby' 	=> 'description',
				'required'	=> true,
				'labels'	=> array(
					'singular_name'=>__( 'Course', 'crm' ),
				),
			),
			'cuisine' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'name',
				'hierarchical'	=> true,
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Cuisine', 'crm' ),
				),
			),
			'season' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Season', 'crm' ),
				),
			),
			'occasion' => array(
				'multiselect' 	=> true,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Occasion', 'crm' ),
				),
			),
			'diet' => array(
				'multiselect' 	=> true,
				'orderby' 		=> 'description',
				'required'		=> false,
				'labels'	=> array(
					'singular_name'=>__( 'Diet', 'crm' ),
				),
			),
			'difficult' => array(
				'multiselect' 	=> false,
				'orderby' 		=> 'description',
				'required'		=> true,
				'labels'	=> array(
					'singular_name'=>__( 'Difficulty', 'crm' ),
				),
			),
		);
		return $taxonomies;
	}

	public function setup_CPM_recipe_labels( $labels ) {
		$labels['recipe'] = array(
			'title'						=> _x( 'Post Title', 'recipe', 'crm' ),
			'edit_button'				=> _x( 'Edit Post', 'recipe', 'crm' ),
			'delete_button'				=> _x( 'Delete Post', 'recipe', 'crm' ),
			'new_button'				=> _x( 'New Post', 'recipe', 'crm' ),
			'new1'						=> _x( 'Write your new post on this page.', 'recipe', 'crm' ),
			'new2'						=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it will be visible to others according to your visibility preferences.', 'recipe', 'crm' ),
			'edit1'						=> _x( 'Edit your existing post on this page.', 'recipe', 'crm' ),
			'edit2' 					=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it wil be visible to others according to your visibility preferences.', 'recipe', 'crm' ),
			'draft1'					=> _x( 'Post saved as <a href="%s">a draft</a>.','recipe', 'crm'),
			'draft2'					=> _x( 'It will not be visible on the site, but you can edit it at any time and submit it later.','recipe','crm'),
			'back'						=> _x( 'Back to <a href="%s">my posts</a>.', 'recipe', 'crm'),
			'publish-admin'				=> _x( 'Dear administrator, this post is now <a href="%s">published</a>.', 'recipe', 'crm' ),
			'publish-user'				=> _x( 'Post submitted! Thank you, your post is now awaiting moderation.', 'recipe', 'crm' ),
			'required'					=> _x( 'In order for your post to be published, please fill-in those required fields:', 'recipe', 'crm' ),
			'noposts'					=> _x( 'You have no posts yet.', 'recipe', 'crm'),
			'post_publish_title'		=> _x( 'Your post just got published !', 'recipe', 'crm'),
			'post_publish_content'		=> _x( 'Greetings, your post <a href="%s">%s</a> just got published !', 'recipe', 'crm'),
			'post_publish_content1' 	=> _x( 'It is visible on the website, and appears on <a href="%s">your blog</a>.', 'recipe','crm'),
			'comment_publish_title'		=> _x( '%s commented one of your posts', 'recipe', 'crm'),
			'comment_publish_content'	=> _x( '%s added a comment to your post <a href="%s">%s</a> :', 'recipe', 'crm'),
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
		$styles['cpm-list']['location'][]				= 'recipe_list';
		$styles['cpm-list']['location'][]				= 'recipe_favorites';
		$styles['cpm-select2']['location'][]			= 'recipe_form';
		$styles['cpm-submission-form']['location'][]	= 'recipe_form';
		$styles['post-font']['location'][]				= 'recipe';

		// Enqueue specific recipe styles
		$styles['crm-recipe-print'] = array(
			'file' 		=> 'assets/css/custom-recipe-print.css',
			'uri' 		=> self::$PLUGIN_URI,
			'dir' 		=> self::$PLUGIN_PATH,
			'location' 	=> array('recipe_print'),
		);


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

		$pause = '<i class="fas fa-pause" aria-hidden="true"></i>';
		$play = '<i class="fas fa-play" aria-hidden="true"></i>';
		$close = '<i class="fas fa-times" aria-hidden="true"></i>';
		$stop = '<i class="fas fa-stop" aria-hidden="true"></i>';
		$repeat = '<i class="fas fa-redo-alt" aria-hidden="true"></i>';
		$prev = '<i class="fas fa-step-backward" aria-hidden="true"></i>';
		$next = '<i class="fas fa-step-forward" aria-hidden="true"></i>';


		// Reuse some default post scripts
		$scripts['cpm-list']['data']['confirm_message'] = _x( 'Are you sure you want to delete this post :', 'recipe', 'crm' );
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

		// $scripts['crm-print_button'] = array (
		// 	'file' 		=> 'print_button.js',
		// 	'uri'		=> self::$PLUGIN_URI . 'assets/js/',
		// 	'dir' 		=> self::$PLUGIN_PATH . 'assets/js/',
		// 	'deps' 		=> array(
		// 		'jquery',
		// 	),
		// 	'footer' 	=> true,
		// 	'data' 		=> array(
		// 		'name' 		=> 'wpurp_print',
		// 		// 'ajaxurl' 	=> WPUltimateRecipe::get()->helper('ajax')->url(),
		// 		'nonce' 	=> wp_create_nonce( 'wpurp_print' ),
		// 		// 'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
		// 		'custom_print_css_url' => self::$PLUGIN_URI . '/assets/css/custom-recipe-print.css',
		// 		// 'coreUrl' 	=> WPUltimateRecipe::get()->coreUrl,
		// 		// 'premiumUrl'=> WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
		// 		'title' 	=> __('Print this Recipe','crm'),
		// 		'permalinks'=> get_option('permalink_structure'),
		// 	),
		// 	'location' 	=> array('recipe'),
		// );

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
					'prev' 	=> __('Read previous step','crm'),
					'next' 	=> __('Read next step','crm'),
					'repeat' => __('Read this step again','crm'),
					'pause' => __('Pause reading','crm'),
					'play' 	=> __('Continue reading','crm'),
					'stop' 	=> __('Stop reading and close player','crm'),
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
				// 'crm-print_button',
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
				'placeholder' => self::$PLUGIN_URI . 'img/camera.png',
				'fileTypes' => self::ATTACHMENT_FORMATS,
				'wrongFileType' => sprintf(__('Authorized file formats are : %s','crm'),implode(', ',self::ATTACHMENT_FORMATS)),
				'maxFileSize' => self::MAX_ATTACHMENT_SIZE_KB,
				'fileTooBig' => sprintf(__('The maximum file size is %s kB','crm'), self::MAX_ATTACHMENT_SIZE_KB),
				'deleteImage' => __('Do you really want to delete this image ?','crm'),
				'deleteIngredient' => __('Do you really want to delete this ingredient ?','crm'),
				'deleteInstruction' => __('Do you really want to delete this instruction ?','crm'),
				'deleteInstructionGroup' => __('Do you really want to delete this group ?\n(Instructions below will be preserved)','crm'),
				'deleteIngredientGroup' => __('Do you really want to delete this group ?\n(Ingredients below will be preserved)','crm'),
			),
			'location' 	=> array('recipe_form'),
		);

		return $scripts;
	}

}
