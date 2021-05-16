<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Assets {

	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	// Used both on recipe display and also to output the step URL as metadata
	const RECIPE_STEP_ID_ROOT = 'wpurp_recipe_instruction';
	const RECIPE_THUMB_INPUT = 'recipe_thumbnail';
	// const RECIPE_VIDEO_INPUT = 'recipe_video_thumbnail';

	private static $PLUGIN_URI;
	private static $PLUGIN_PATH;


	public function __construct() {
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
	}

	/* PRINT URL KEYWORD
	---------------------------------------------------------------------------*/
	public static function keyword() {
		$keyword = urlencode(__('print', 'crm'));
		return $keyword;
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
	public function setup_CPM_recipe_fallback_image( $fallbacks )
	{
		$fallbacks['recipe']=trailingslashit(self::$PLUGIN_URI).'assets/img/fallback.jpg';
		return $fallbacks;
	}


	public function setup_CPM_recipe_page_slugs( $slugs ) {
		$slugs['recipe'] = array(
			'recipe_list' 		=> 'publier-recettes',
			'recipe_favorites' 	=> 'favoris-recettes',
			'recipe_form' 		=> 'saisie-recette',
			'recipe_print' 		=> __('print', 'crm'),
		);
		return $slugs;
	}

	public function setup_CPM_post_status( $statuses ) {
		$statuses['recipe'] = array(
			'all'		=> array(
				'label'			=> _x('All my posts', 'recipe', 'crm'),
				'description'	=> '',
			),
			'restored'	=> array(
				'label'			=> _x('Restored', 'recipe', 'crm'),
				'description'	=> _x('Those posts have been automatically kept but not saved as drafts. You can delete them if you don\'t need them.', 'recipe', 'crm'),
			),
			'pending'	=> array(
				'label'			=> _x('Pending', 'recipe', 'crm'),
				'description'	=> _x('Those posts have been submitted and pending administrator\'s approval.', 'recipe', 'crm'),
			),
			'draft'		=> array(
				'label'			=> _x('Draft', 'recipe', 'crm'),
				'description'	=> _x('Those posts are in preparation, and not yet submitted.', 'recipe', 'crm'),
			),
			'publish'	=> array(
				'label'			=> _x('Public', 'recipe', 'crm'),
				'description'	=> _x('Those posts have been approved by the administrator. They are visible on the website, according to your visibility preferences.', 'recipe', 'crm'),
			),
		);
		return $statuses;
	}

	public function setup_CPM_required( $required ) {
		$required['recipe']= array(
			'post_title'				=> __('Recipe Title','crm'),
			'post_content'				=> __('Recipe Description','crm'),
			'recipe_image_attachment'	=> __('Recipe Featured Image','crm'),
			'recipe_course' 			=> __('Recipe Course', 'crm'),
			'recipe_difficult' 			=> __('Recipe Difficulty', 'crm'),
			'recipe_ingredients' 		=> __('Ingredients', 'crm'),
			'recipe_servings' 			=> __('Number of Servings', 'crm'),
			'recipe_prep_time' 			=> __('Preparation Time', 'crm'),
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
			'cook_time'					=> __( 'Cook Time', 'crm' ),
			'prep_time'					=> __( 'Prep Time', 'crm' ),
			'passive_time'				=> __( 'Passive Time', 'crm' ),
			'edit_button'				=> _x( 'Edit Post', 'recipe', 'crm' ),
			'delete_button'				=> _x( 'Delete Post', 'recipe', 'crm' ),
			'new_button'				=> _x( 'New Post', 'recipe', 'crm' ),
			'featured_image'			=> _x('Add here your best picture for this post !', 'recipe', 'crm'),
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
			'not_like'					=> _x('liked your post %s', 'recipe', 'crm'),
			'not_comment'				=> _x('commented your post %s', 'recipe', 'crm'),
			'not_comment_respond'		=> _x('answered your comment on post %s', 'recipe', 'crm'),
			'comment_form_headline'		=> _x('Leave a comment on this post', 'recipe', 'crm'),
			'error404_draft' 			=> _x('The post you are trying to read is not yet approved by administrators.', 'recipe', 'crm'),
			'error404_pending' 			=> _x('The post you are trying to read is not yet approved by administrators.', 'recipe', 'crm'),
			'error404_private' 			=> _x('The post you are trying to read is reserved to members.', 				'recipe', 'crm'),
			'error404_friends' 			=> _x('The post you are trying to read is private.', 							'recipe', 'crm'),
			'error404_groups' 			=> _x('The post you are trying to read is private.', 							'recipe', 'crm'),
			'tooltip_like'				=> __('I cooked and liked this recipe', 'crm'),
			'tooltip_dislike' 			=> __('Do not like this recipe anymore', 'crm'),
            'like0'						=> __('I cooked it', 'crm'),
            'like1'						=> _n('%s cooked', '%s cooked', 1, 'crm'),
            'liken'						=> _n('%s cooked', '%s cooked', 2, 'crm'),
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
		// $styles['post-font']['location'][]				= 'recipe';

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
		$scripts['cpm-like']['location'][]='recipe';
		$scripts['cpm-like']['data']['post_type']='recipe';

		$scripts['cpm-list']['data']['confirm_message'] = _x( 'Are you sure you want to delete this post :', 'recipe', 'crm' );
		$scripts['cpm-list']['location'][]='recipe_list';

		$scripts['cpm-submission']['location'][] = 'recipe_form';

		$scripts['crm-autosave']= $scripts['cpm-autosave'];
		$scripts['crm-autosave']['data']['post_type']='recipe';
		$scripts['crm-autosave']['data']['nonce']=wp_create_nonce('custom_recipe_autosave');
		$scripts['crm-autosave']['location']=array('recipe_form');

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

		$scripts['crm-responsive-voice-apikey'] = array(
			'uri'		=> 'https://code.responsivevoice.org/responsivevoice.js?key=6uuS1yl4',
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

		$scripts['crm-recipe-helpers'] = array (
			'file' 		=> 'recipe_helpers.js',
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
			'data' => array(
				'name' 		=> 'ingredient_autocomplete',
				'ajaxurl' 	=> admin_url( 'admin-ajax.php' ),
				'nonce' 	=> wp_create_nonce('ingredient_name_suggestion'),
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
				'name' 						=> 'custom_recipe_submission_form',
				'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
				'nonce' 					=> wp_create_nonce('custom_recipe_submission_form'),
				'deleteIngredient' 			=> __('Do you really want to delete this ingredient ?','crm'),
				'deleteInstruction' 		=> __('Do you really want to delete this instruction ?','crm'),
				'deleteInstructionGroup' 	=> __('Do you really want to delete this group ?\n(Instructions below will be preserved)','crm'),
				'deleteIngredientGroup' 	=> __('Do you really want to delete this group ?\n(Ingredients below will be preserved)','crm'),
				'deleteImageFirst' 			=> __('Please remove the image prior to removing this instruction.','crm'),
			),
			'location' 	=> array('recipe_form'),
		);

		return $scripts;
	}


	/* TEMPLATES
	---------------------------------------------------------------------- */
	public static function echo_template_part( $slug, $name=false, $args=array() ) {
		extract($args);

		$templates_path = trailingslashit(self::$PLUGIN_PATH) . 'templates/';
		$template = 'template-' . $slug;
		$template .= $name ? '-' . $name : '';
		$template .= '.php';
		include( $templates_path . $template );
	}

	public static function get_template_part($slug, $name = false, $args = array())
	{
		ob_start();
		self::echo_template_part( $slug, $name, $args );
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

}
