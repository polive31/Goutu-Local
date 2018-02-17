<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Recipe_Templates {
	
	const RECIPES_PUBLISH_SLUG = 'publier-recettes';
	const RECIPE_NEW_SLUG = 'nouvelle-recette';
	const RECIPE_EDIT_SLUG = 'modifier-recette';
	protected static $_PluginPath;	
	protected $logged_in;
	
	public function __construct() {
	
		self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
		
		add_action( 'init', array($this, 'hydrate'));
		
		/* Load javascript styles */
		add_filter ( 'wpurp_assets_js', array($this,'enqueue_wpurp_js'), 15, 1 );
		
		/* Load stylesheets */
		add_filter ( 'wpurp_assets_css', array($this,'enqueue_wpurp_css'), 15, 1 );

		/* Customize User Submission shortcode */
		add_filter ( 'wpurp_user_submissions_current_user_edit_item', array($this, 'remove_recipe_list_on_edit_recipe'), 15, 2 );

		/* Custom menu template */
		//add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );

		/* Misc */
		//remove_action ( 'wp_enqueue_scripts', 'WPURP_Assets::enqueue');
		//wp_deregister_script('wpurp_script_minified');
		//wp_enqueue_script( 'wpurp_custom_script', get_stylesheet_directory_uri() . '/assets/js/wpurp_custom.js', array('jquery'), WPURP_VERSION, true );

		//add_action( 'genesis_before_content', array($this,'display_debug_info') );

	}

	/* Hydrate
	--------------------------------------------------------------*/	
	public function hydrate() {
		$this->logged_in = is_user_logged_in();	
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	public function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				//PC::debug(array( $msg => $var ) );
			}
	}

	public function display_debug_info() {
				
			//$this->dbg('In WPURP Custom Custom Templates Class', '');
			//$this->dbg('Plugin path', self::$_PluginPath);
	}	
	
	
/* Custom Menu Template */	
	public function wpurp_custom_menu_template( $form, $menu ) {
		return '';
	}
	
	
/* Custom Enqueue CSS */	
	public function enqueue_wpurp_css($css_enqueue) {
				
		//$this->dbg('In Enqueue WPURP CSS', '');
		//$this->dbg('Plugin path', self::$_PluginPath);
		
		if ( is_admin() ) return $css_enqueue;
			
		if ( is_singular('recipe') ) {
		  $css_enqueue=array(
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-recipe.css',
		              'public' => true,
		          ),
							/*array(
		              'url' => self::$_PluginPath . 'assets/css/tooltips.css',
		              'public' => true,
		          ),
							array(
		              'url' => '//fonts.googleapis.com/css?family=Oswald|Open+Sans',
		              'public' => true,
		          ),*/				                             
			);
		}
		elseif ( is_page( 'menus' ) ) { // Menu page
		  $css_enqueue=array(
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-menu.css',
		              'public' => true,
		          ),
			);		
		}
		
		elseif ( is_page( [self::RECIPES_PUBLISH_SLUG, self::RECIPE_NEW_SLUG, self::RECIPE_EDIT_SLUG] ) ) {
//		  $css_enqueue=null;
//		  $css_enqueue[]=
//							array(
//		              'name' => 'jquery-ui',
//		              'file' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		              'direct' => true,
//		              'public' => true,
//		              'url' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		              'dir' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		          ); 
//		  $css_enqueue[]=
//							array(
//		              'file' =>  self::$_PluginPath . 'vendor/select2/css/select2.min.css',
//		              'url' =>  self::$_PluginPath . 'vendor/select2/css/select2.min.css',
//		              'dir' =>  self::$_PluginPath . 'vendor/select2/css/select2.min.css',
//		              'public' => true,
//		              'direct' => true,
//		          ); 			
		  $css_enqueue[]=
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-recipe-submission.css',
		              'dir' => self::$_PluginPath . 'assets/css/custom-recipe-submission.css',
		              'public' => true,
		              'direct' => true,
		          ); 			
		}
		
		//echo '<pre style="margin-top:200px">' . print_r($css_enqueue,true) . '</pre>';
		return $css_enqueue;
	}

/* Custom Enqueue JS */
	public function enqueue_wpurp_js($js_enqueue) {
		
			if ( is_admin() ) return $js_enqueue;
		
			if ( is_singular('recipe') ) {
				
				$js_enqueue=array();		
				$min_js=self::$_PluginPath . 'assets/js/custom-recipe-tools.min.js';
				
				if ( file_exists( $min_js ) ) {
					$js_enqueue[] = array(
            'name' => 'custom-recipe-tools-minified',
            'url' => $min_js,
            'public' => true,
            'admin' => true,					
					);
				}
				else {
					
				$pause = '<i class="fa fa-pause" aria-hidden="true"></i>';
				$play = '<i class="fa fa-play" aria-hidden="true"></i>';
				$close = '<i class="fa fa-times" aria-hidden="true"></i>';

					
		    $js_enqueue=array(
		            array(
		                'name' => 'fraction',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/fraction-js/index.js',
		                'public' => true,
		                'admin' => true,
		            ),

		            array(
		                'name' => 'print_button',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/js/print_button.js',
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_print',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_print' ),
		                    'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
		                    'coreUrl' => WPUltimateRecipe::get()->coreUrl,
		                    'premiumUrl' => WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
		                    'title' => __('Print this Recipe','foodiepro'),
		                    'permalinks' => get_option('permalink_structure'),
		                ),
		            ),
		    	      array(
		                'name' => 'adjustable-servings',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js',
		                'public' => true,
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
		                /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/favorite-recipes/js/favorite-recipes.js',*/
		                'url' => self::$_PluginPath . 'assets/js/custom_favorite_recipe.js',
		               	'premium' => true,
		                'public' => true,
		                'setting' => array( 'favorite_recipes_enabled', '1' ),
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_favorite_recipe',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_favorite_recipe' ),
		                )
		            ),	  
		            array(
		                /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/js/add-to-shopping-list.js',*/
		                'name' => 'custom-shopping-list',
		                'url' => self::$_PluginPath . 'assets/js/custom_shopping_list.js',
		                'premium' => true,
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_add_to_shopping_list',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
		                )
		            ),
		            array(
		                'name' => 'wpurp-timer',
		                'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/timer/js/timer.js',
		                'premium' => true,
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_timer',
		                    'icons' => array(
		                        'pause' => $pause,
		                        'play' => $play,
		                        'close' => $close,
		                    ),
		                )
		            ),		            
		    );	
				}
			}
			
			elseif ( is_page( [self::RECIPES_PUBLISH_SLUG, self::RECIPE_NEW_SLUG, self::RECIPE_EDIT_SLUG] ) ) {
			 $js_enqueue = $js_enqueue;
			}
			
			else {
				$js_enqueue=array();
			}
			
		return $js_enqueue;
	}

	
/* Custom Recipe Submission Shortcode */
	public function remove_recipe_list_on_edit_recipe( $item, $recipe ) {
		if ( isset( $_GET['wpurp-edit-recipe'] ) )
			$html = '';
		else {
			$url = get_permalink() . self::RECIPE_EDIT_SLUG;	
			//$url = 'http://www.goutu.main/accueil/publier/publier-recettes/recipe-edit/';	
			$html = '<li>';
      $html .= '<a href="' . $url . '?wpurp-edit-recipe=' . $recipe->ID() . '">' . $recipe->title() . '</a>';
      $html .= '</li>';
		}
		return $html;
	}
	
	
	public static function output_tooltip($content,$position) {
		$path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
	
		$html ='<div class="tooltip-content">';
		$html.= '<div class="wrap">';
		$html.=$content;
		$html.='<img class="callout" data-no-lazy="1" src="' . $path . '">';
		$html.='</div>';
		$html.='</div>';
		
		return $html;
	}

	
}


