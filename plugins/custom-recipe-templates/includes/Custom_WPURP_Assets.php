<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_WPURP_Assets {
	
	const RECIPES_LIST_SLUG = 'publier-recettes';
	const RECIPE_NEW_SLUG = 'nouvelle-recette';
	const RECIPE_EDIT_SLUG = 'modifier-recette';
	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	protected static $_PluginPath;	
	protected static $_PluginUri;	
	// public static $logged_in;

	protected $custom_enqueued_scripts = array();
	protected $custom_enqueued_styles = array();
	protected $custom_registered_scripts = array();
	protected $custom_registered_styles = array();

	public function __construct() {
	
		self::$_PluginUri = plugin_dir_url( dirname( __FILE__ ) );
		self::$_PluginPath = plugin_dir_path( dirname( __FILE__ ) );
		
		/* WPURP enqueue list */
		add_filter ( 'wpurp_assets_js', array($this,'enqueue_wpurp_js'), 15, 1 );
		add_filter ( 'wpurp_assets_css', array($this,'enqueue_wpurp_css'), 15, 1 );

		/* Custom enqueue list */
		add_action( 'wp_enqueue_scripts', array($this, 'custom_wpurp_scripts_styles_enqueue') );		
		add_action( 'wp_enqueue_scripts', array($this, 'custom_wpurp_scripts_styles_register') );		
	}

/********************************************************************************
****                   CUSTOM ENQUEUE STYLES                           **********
********************************************************************************/
	
	public function enqueue_wpurp_css($css_enqueue) {
		
		if ( is_admin() ) return $css_enqueue;
			
		if ( is_singular('recipe') ) {
		  	$css_enqueue=array();

			// Enables custom gallery shortcode & stylesheet loading
        	new Custom_Gallery_Shortcode();
        	new Tooltip();
		  	
		  	$this->custom_enqueued_styles=array(
				array(
					'url' => self::$_PluginUri . 'assets/css/',
					'path' => self::$_PluginPath . 'assets/css/',
					'file' => 'custom-recipe.css',
					'public' => true,
				),
				array(
					'url' => '//fonts.googleapis.com/css?family=Cabin',
					'public' => true,
	          	),         				          			                             
			);
		}
		elseif ( is_page( 'menus' ) ) { // Menu page
		  	$css_enqueue=array();

		  	$this->custom_enqueued_styles=array(
				array(
					'name' => 'custom-menu-styles',		
					'url' => self::$_PluginUri . 'assets/css/',
					'path' => self::$_PluginPath . 'assets/css/',
					'file' => 'custom-menu.css',
					'public' => true,
		        ),
			);		
		}
		elseif ( is_page( [self::RECIPE_NEW_SLUG, self::RECIPE_EDIT_SLUG] ) ) {
			$css_enqueue=array();
						
			$this->custom_enqueued_styles=array(
				array(
					'url' =>  self::$_PluginUri . 'vendor/select2/css/',
					'path' =>  self::$_PluginPath . 'vendor/select2/css/',
					'file' => 'select2.min.css',
					'public' => true,
					'direct' => true,
				),		
				array(
					'url' => '//fonts.googleapis.com/css?family=Cabin',
					'public' => true,
	          	),
	  			array(
					'url' => self::$_PluginUri . 'vendor/autocomplete/',
					'path' => self::$_PluginPath . 'vendor/autocomplete/',
					'file' => 'jquery.auto-complete.css',
					'public' => true,
					'direct' => true,
	          	),		          	
	  			array(
					'url' => self::$_PluginUri . 'assets/css/',
					'path' => self::$_PluginPath . 'assets/css/',
					'file' => 'custom-user-submission.css',
					'public' => true,
					'direct' => true,
	          	),              	   		          	
	          	// The style below is needed for recipe preview
	  			array(
					'url' => self::$_PluginUri . 'assets/css/',
					'path' => self::$_PluginPath . 'assets/css/',
					'file' => 'custom-recipe.css',
					'public' => true,
					'direct' => true,
	          	),
			);
		}	          		
		elseif ( is_page( [self::RECIPES_LIST_SLUG] ) ) {
			$css_enqueue=array();
						
			// $this->custom_registered_styles=array(
			// 	array(
			// 		'url' =>  self::$_PluginUri . 'assets/css/',
			// 		'path' =>  self::$_PluginPath . 'assets/css/',
			// 		'file' => 'custom-post-list.css',
			// 		'public' => true,
			// 		'direct' => true,
			// 	),
			// );						
		}
		else 
		  $css_enqueue=array();

		// Styles to be registered, and enqueued later in dedicated shortcodes
		$this->custom_registered_styles=array(
			array(
				'name' => 'custom-post-list',
				'url' =>  self::$_PluginUri . 'assets/css/',
				'path' =>  self::$_PluginPath . 'assets/css/',
				'file' => 'custom-post-list.css',
				'public' => true,
				'direct' => true,
			),
		);			  
			
		//echo '<pre style="margin-top:200px">' . print_r($css_enqueue,true) . '</pre>';
		return $css_enqueue;
	}




/********************************************************************************
****                   CUSTOM ENQUEUE SCRIPTS                           **********
********************************************************************************/

	public function enqueue_wpurp_js($js_enqueue) {
		
			if ( is_admin() ) return $js_enqueue;
		
			if ( is_singular('recipe') ) {
				
				$js_enqueue=array();		
					
				$pause = '<i class="fa fa-pause" aria-hidden="true"></i>';
				$play = '<i class="fa fa-play" aria-hidden="true"></i>';
				$close = '<i class="fa fa-times" aria-hidden="true"></i>';
				$stop = '<i class="fa fa-stop" aria-hidden="true"></i>';
				$repeat = '<i class="fa fa-repeat" aria-hidden="true"></i>';
				$prev = '<i class="fa fa-step-backward" aria-hidden="true"></i>';
				$next = '<i class="fa fa-step-forward" aria-hidden="true"></i>';

				$this->custom_enqueued_scripts = array (
		            array(
		                'name' => 'fraction',
		                'url' => self::$_PluginUri . 'vendor/fraction/',
		                'path' => self::$_PluginPath . 'vendor/fraction/',
		                'file' => 'fraction.js',
		                'public' => true,
		                'admin' => true,
		                'footer' => true,
		            ),
		            array(
						'name' => 'print_button',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'print_button.js',
		                'public' => true,
		                'deps' => array(
							'jquery',
		                ),
						'footer' => true,
		                'data' => array(
							'name' => 'wpurp_print',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_print' ),
		                    // 'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
		                    'custom_print_css_url' => $_PluginUri . '/assets/css/custom-recipe-print.css',
		                    'coreUrl' => WPUltimateRecipe::get()->coreUrl,
		                    'premiumUrl' => WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
		                    'title' => __('Print this Recipe','foodiepro'),
		                    'permalinks' => get_option('permalink_structure'),
		                ),
					),
		            array(
						'name' => 'recipe-read',
		                'url' => self::$_PluginUri . '/assets/js/',
		                'path' => self::$_PluginPath . '/assets/js/',
		                'file' => 'custom_text_to_speech.js',
		                'public' => true,
						'footer' => true,
		                'deps' => array(
		                    'jquery',
		                    'responsive-voice',
		                ),
		                'data' => array(
							'name' => 'recipeRead',
							'voice' => 'French Female',
		                    'icon' => array(
		                        'prev' => $prev,
		                        'next' => $next,
		                        'repeat' => $repeat,
		                        'play' => $play,
								'pause' => $pause,
		                        'stop' => $stop,
							),
		                    'title' => array(
		                        'prev' => __('Read previous step','foodiepro'),
		                        'next' => __('Read next step','foodiepro'),
		                        'repeat' => __('Read this step again','foodiepro'),
		                        'pause' => __('Pause reading','foodiepro'),
		                        'play' => __('Continue reading','foodiepro'),
		                        'stop' => __('Stop reading and close player','foodiepro'),
		                    ),							
		                )
					),
		            array(
		                'name' => 'responsive-voice',
		                // 'url' => 'https://code.responsivevoice.org/1.5.10/responsivevoice.js?source=wp-plugin',
		                'url' => 'https://code.responsivevoice.org/responsivevoice.js',
						'footer' => true,
		                'public' => true,
		                'admin' => false,						
					),										
		            array(
						'name' => 'wpurp-timer',
		                'url' => self::$_PluginUri . '/assets/js/',
		                'path' => self::$_PluginPath . '/assets/js/',
		                'file' => 'timer.js',
		                'premium' => true,
		                'public' => true,
						'footer' => true,
		                'deps' => array(
							'jquery',
		                ),
		                'data' => array(
							'name' => 'wpurp_timer',
		                    'icons' => array(
								'play' => $play,
								'pause' => $pause,
		                        'close' => $close,
		                    ),
							)
					),						
					array(
						'name' => 'custom-adjustable-servings',
						// 'url' => WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js',
						'url' => self::$_PluginUri . 'assets/js/',
						'path' => self::$_PluginPath . 'assets/js/',
						'file' => 'custom_adjustable_servings.js',
						'public' => true,
						'footer' => true,
						'deps' => array(
							'jquery',
		                    'fraction',
		                	'print_button',
		                ),
		                'data' => array(
							'name' => 'wpurp_servings',
		                    'precision' => 1,
		                    'decimal_character' => ',',
		                ),
		            ),					
					array(
						'name' => 'custom-favorite-recipe',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'custom_favorite_recipe.js',
		                'public' => true,
						'footer' => true,
		                'setting' => array( 'favorite_recipes_enabled', '1' ),
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'custom_favorite_recipe',
		                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
		                    'nonce' => wp_create_nonce( 'custom_favorite_recipe' ),
		                )
		            ),	  
		            // array(
		            //     /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/js/add-to-shopping-list.js',*/
		            //     'name' => 'custom-shopping-list',
		            //     'url' => self::$_PluginUri . 'assets/js/',
		            //     'path' => self::$_PluginPath . 'assets/js/',
		            //     'file' => 'custom_shopping_list.js',
		            //     // 'premium' => true,
		            //     'public' => true,
		            //     'deps' => array(
		            //         'jquery',
		            //     ),
		            //     'data' => array(
		            //         'name' => 'wpurp_add_to_shopping_list',
		            //         'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		            //         'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
		            //     )
		            // ),
		        );
				// }
			}
			
			elseif ( is_page( [self::RECIPES_LIST_SLUG] ) ) {
				$js_enqueue = array();

				$this->custom_enqueued_scripts = array (
					array(
		                'name' => 'custom-user-submissions-list',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'custom_user_submissions_list.js',
		                'deps' => array(
					        'jquery',
		                ),
		                'footer' => true,
		                'data' => array(
		                    'name' => 'custom_user_submissions_list',
		                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
		                    'nonce' => wp_create_nonce( 'custom_user_submissions_list' ),
		                    'confirm_message' => __( 'Are you sure you want to delete this recipe:', 'foodiepro' ),
		                )
		            ),
		        );

			}	

			elseif ( is_page( [self::RECIPE_NEW_SLUG, self::RECIPE_EDIT_SLUG] ) ) {
				$js_enqueue = array();

				$this->custom_enqueued_scripts = array(
					array(
					    'name' => 'autocomplete',
					    'url' => self::$_PluginUri . 'vendor/autocomplete/',
					    'path' => self::$_PluginPath . 'vendor/autocomplete/',
					    'file' => 'jquery.auto-complete.js',
					    'deps' => array(
					        'jquery',
					    ),
					),					
					array(
					    'name' => 'select2',
					    'url' => self::$_PluginUri . 'vendor/select2/js/',
					    'path' => self::$_PluginPath . 'vendor/select2/js/',
					    'file' => 'select2.js',
					    'deps' => array(
					        'jquery',
					    ),
					),
					array(
					    'name' => 'select2-fr',
					    'url' => self::$_PluginUri . 'vendor/select2/js/i18n/',
					    'path' => self::$_PluginPath . 'vendor/select2/js/i18n/',
					    'file' => 'fr.js',
					    'deps' => array(
					        'select2',
					    ),
					),											
					array(
						'name' => 'jquery-touch-punch',
					    'deps' => array(
					        'jquery',
					        'jquery-ui-core',
					    ),						
					),
		            array(
		                'name' => 'select2-taxonomies',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'select2_taxonomies.js',
		                'deps' => array(
		                    'jquery',
		                    'select2',
		                ),
		                'footer' => false,
		           	),
		            array(
		                'name' => 'ingredient-unit-suggestion',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'ingredient_unit_suggestion.js',
		                'deps' => array(
		                    'jquery',
		                    'autocomplete',
		                ),
		                'footer' => true,
		           	),
		            array(
		                'name' => 'ingredient-preview',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'ingredient_preview.js',
		                'deps' => array(
		                    'jquery',
		                ),
		                'footer' => true,
		                'data' => array(
		                    'name' => 'ingredient_preview',
							'ajaxurl' => admin_url( 'admin-ajax.php' ),
							'nonce' => wp_create_nonce('preview_ingredient'),
		                )		                
		           	),	
		            array(
		                'name' => 'tinymce',
		                'url' => self::$_PluginUri . 'vendor/tinymce/',
		                'path' => self::$_PluginPath . 'vendor/tinymce/',
		                'file' => 'tinymce.min.js',
		                'deps' => array(
		                    'jquery',
		                ),
		                'footer' => true,	                
		           	),			           			           			           											
		            array(
		                'name' => 'custom-user-submissions',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'custom_user_submission.js',
		                'deps' => array(
		                	'tinymce',
		                    'jquery',
		                    'jquery-ui-sortable',
		                ),
		                'footer' => true,
		                'data' => array(
		                    'name' => 'custom_user_submissions',
		                	'placeholder' => WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png',
		                	'fileTypes' => self::ATTACHMENT_FORMATS,
		                	'wrongFileType' => sprintf(__('Authorized file formats are : %s','foodiepro'),implode(', ',self::ATTACHMENT_FORMATS)),
		                	'maxFileSize' => self::MAX_ATTACHMENT_SIZE_KB,
		                	'fileTooBig' => sprintf(__('The maximum file size is %s kB','foodiepro'), self::MAX_ATTACHMENT_SIZE_KB),
		                )
		           	)					
            	);            	
			}
			
			else {
				$js_enqueue=array();
			}
			
		return $js_enqueue;
	}


/********************************************************************************
****                GENERAL REGISTER FUNCTIONS                          *********
********************************************************************************/

	public function custom_wpurp_scripts_styles_register() {

		// Register Styles
		if ( $this->custom_registered_styles != array() ) {
			foreach ( $this->custom_registered_styles as $key=>$style) {
				$handler = isset($style['name'])?$style['name']:'custom_wpurp_style_' . $key;
				// $handler = (null !== $script(['name']))?$script(['name']):'custom_wpurp_' . $key;
				$url = isset($style['url'])?$style['url']:'';
				$path = isset($style['path'])?$style['path']:'';
				$file = isset($style['file'])?$style['file']:'';
				$deps = isset($style['deps'])?$style['deps']:'';
				$media = isset($style['media'])?$style['media']:'all';
				// $version = self::CUSTOM_WPURP_TEMPLATES_VERSION;
				$version = CHILD_THEME_VERSION;
				custom_register_style( $handler, $url, $path, $file, $deps, $version, $media );
	  		}
		}		
	}


/********************************************************************************
****                GENERAL ENQUEUE FUNCTIONS                          **********
********************************************************************************/

	public function custom_wpurp_scripts_styles_enqueue() {

		// Enqueue Styles
		if ( $this->custom_enqueued_styles != array() ) {
			foreach ( $this->custom_enqueued_styles as $key=>$style) {
				$handler = isset($style['name'])?$style['name']:'custom_wpurp_style_' . $key;
				// $handler = (null !== $script(['name']))?$script(['name']):'custom_wpurp_' . $key;
				$url = isset($style['url'])?$style['url']:'';
				$path = isset($style['path'])?$style['path']:'';
				$file = isset($style['file'])?$style['file']:'';
				$deps = isset($style['deps'])?$style['deps']:'';
				$media = isset($style['media'])?$style['media']:'all';
				// $version = self::CUSTOM_WPURP_TEMPLATES_VERSION;
				$version = CHILD_THEME_VERSION;
				custom_enqueue_style( $handler, $url, $path, $file, $deps, $version, $media );
	  		}
		}

		// Enqueue Scripts
		if ( $this->custom_enqueued_scripts != array() ) {
			foreach ( $this->custom_enqueued_scripts as $key=>$script) {
				$handler = isset($script['name'])?$script['name']:'custom_wpurp_script_' . $key;
				// $handler = (null !== $script(['name']))?$script(['name']):'custom_wpurp_' . $key;
				$url = isset($script['url'])?$script['url']:'';
				$path = isset($script['path'])?$script['path']:'';
				$file = isset($script['file'])?$script['file']:'';
				$deps = isset($script['deps'])?$script['deps']:'';
				// $version = self::CUSTOM_WPURP_TEMPLATES_VERSION;
				$version = CHILD_THEME_VERSION;
				$footer = isset($script['footer'])?$script['footer']:false;
				custom_enqueue_script( $handler, $url, $path, $file, $deps, $version, $footer );

				if (isset($script['data'])) {	
					$data_name = $script['data']['name'];
					unset( $script['data']['name'] );
					wp_localize_script( $handler, $data_name, $script['data'] );
				}
			}
		}
	}
}

new Custom_WPURP_Assets();