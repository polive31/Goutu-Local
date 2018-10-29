<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_WPURP_Templates {
	
	const RECIPES_LIST_SLUG = 'publier-recettes';
	const RECIPE_NEW_SLUG = 'nouvelle-recette';
	const RECIPE_EDIT_SLUG = 'modifier-recette';
	const CUSTOM_WPURP_TEMPLATES_VERSION = '0.0.1';
	protected static $_PluginPath;	
	protected static $_PluginUri;	
	protected $logged_in;
	protected $custom_enqueued_scripts = array();
	protected $custom_enqueued_styles = array();
	
	public function __construct() {
	
		self::$_PluginUri = plugin_dir_url( dirname( __FILE__ ) );
		self::$_PluginPath = plugin_dir_path( dirname( __FILE__ ) );
		
		add_action( 'init', array($this, 'hydrate'));
		
		/* WPURP enqueue list */
		add_filter ( 'wpurp_assets_js', array($this,'enqueue_wpurp_js'), 15, 1 );
		add_filter ( 'wpurp_assets_css', array($this,'enqueue_wpurp_css'), 15, 1 );
		
		/* Custom enqueue list */
		add_action( 'wp_enqueue_scripts', array($this, 'custom_wpurp_scripts_styles_enqueue') );

		/* Ajax Callbacks for Autocomplete jquery plugin  */
		add_action('wp_ajax_nopriv_get_tax_terms', array($this, 'ajax_custom_get_tax_terms'));
		add_action('wp_ajax_get_tax_terms', array($this, 'ajax_custom_get_tax_terms'));
		
        // Ajax callbacks for ingredient preview 
        add_action( 'wp_ajax_ingredient_preview', array( $this, 'ajax_ingredient_preview'));
        add_action( 'wp_ajax_nopriv_ingredient_preview', array( $this, 'ajax_ingredient_preview'));

		/* Customize User Submission shortcode */
		// add_filter ( 'wpurp_user_submissions_current_user_edit_item', array($this, 'remove_recipe_list_on_edit_recipe'), 15, 2 );

		/* Custom menu template */
		//add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );

		/* Misc */
		//remove_action ( 'wp_enqueue_scripts', 'WPURP_Assets::enqueue');
		//wp_deregister_script('wpurp_script_minified');

	}

	/* Hydrate
	--------------------------------------------------------------*/	
	public function hydrate() {
		$this->logged_in = is_user_logged_in();	
	}
	
	
/* Custom Menu Template */	
	public function wpurp_custom_menu_template( $form, $menu ) {
		return '';
	}

	
/* Custom Enqueue CSS */	
	public function enqueue_wpurp_css($css_enqueue) {
		
		if ( is_admin() ) return $css_enqueue;
			
		if ( is_singular('recipe') ) {
		  	$css_enqueue=array();
		  	$this->custom_enqueued_styles=array(
				array(
					'url' => self::$_PluginUri . 'assets/css/custom-recipe.css',
					'path' => self::$_PluginPath . 'assets/css/custom-recipe.css',
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
					'url' => self::$_PluginUri . 'assets/css/custom-menu.css',
					'path' => self::$_PluginPath . 'assets/css/custom-menu.css',
					'public' => true,
		        ),
			);		
		}
		
		elseif ( is_page( [self::RECIPE_NEW_SLUG, self::RECIPE_EDIT_SLUG] ) ) {
//		  $css_enqueue=null;
//		  $css_enqueue[]=array(
//		              'name' => 'jquery-ui',
//		              'file' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		              'direct' => true,
//		              'public' => true,
//		              'url' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		              'dir' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css',
//		          ); 
		  		$css_enqueue[]=array(
					'url' =>  self::$_PluginUri . 'vendor/select2/css/select2.min.css',
					'dir' =>  self::$_PluginPath . 'vendor/select2/css/select2.min.css',
					'file' => 'select2.min.css',
					'public' => true,
					'direct' => true,
				); 			
			$this->custom_enqueued_styles=array(
				array(
					'url' => '//fonts.googleapis.com/css?family=Cabin',
					'public' => true,
	          	),
	  			array(
					'url' => self::$_PluginUri . 'vendor/autocomplete/jquery.auto-complete.css',
					'path' => self::$_PluginPath . 'vendor/autocomplete/jquery.auto-complete.css',
					'public' => true,
					'direct' => true,
	          	),		          	
	          	// The style below is needed for recipe preview
	  			array(
					'url' => self::$_PluginUri . 'assets/css/custom-recipe.css',
					'path' => self::$_PluginPath . 'assets/css/custom-recipe.css',
					'public' => true,
					'direct' => true,
	          	),
	  			array(
					'url' => self::$_PluginUri . 'assets/css/custom-recipe-submission.css',
					'path' => self::$_PluginPath . 'assets/css/custom-recipe-submission.css',
					'public' => true,
					'direct' => true,
	          	),        		          	
			);	          		
		}
		else 
		  $css_enqueue=array();
			
		//echo '<pre style="margin-top:200px">' . print_r($css_enqueue,true) . '</pre>';
		return $css_enqueue;
	}

/* Custom Enqueue JS */
	public function enqueue_wpurp_js($js_enqueue) {
		
			if ( is_admin() ) return $js_enqueue;
		
			if ( is_singular('recipe') ) {
				
				$js_enqueue=array();		
					
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
		                // 'url' => WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js',
		                'url' => self::$_PluginUri . 'assets/js/custom_adjustable_servings.js',
		                'path' => self::$_PluginPath . 'assets/js/custom_adjustable_servings.js',
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

				$this->custom_enqueued_scripts = array (
					array(
		                'name' => 'custom-favorite-recipe',
		                /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/favorite-recipes/js/favorite-recipes.js',*/
		                'url' => self::$_PluginUri . 'assets/js/custom_favorite_recipe.js',
		                'path' => self::$_PluginPath . 'assets/js/custom_favorite_recipe.js',
		               	// 'premium' => true,
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
		                'url' => self::$_PluginUri . 'assets/js/custom_shopping_list.js',
		                'path' => self::$_PluginPath . 'assets/js/custom_shopping_list.js',
		                // 'premium' => true,
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
					// array(
					//     'name' => 'select2wpurp',
					//     'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/select2/',
					//     'file' => 'select2.min.js',
					//     'deps' => array(
					//         'jquery',
					//     ),
					// ),
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
					// array(
					// 	'name' => 'suggest',
					//     'deps' => array(
					//         'jquery',
					//     ),						
					// ),					
		            array(
		                'name' => 'custom-user-submissions',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'custom_user_submission.js',
		                'deps' => array(
		                    'jquery',
		                    'jquery-ui-sortable',
		                    'select2',
		                    // 'select2wpurp',
		                ),
		                'footer' => true,
		                'data' => array(
		                    'name' => 'custom_user_submissions',
							'ajaxurl' => admin_url( 'admin-ajax.php' ),
							'nonce' => wp_create_nonce('preview_ingredient'),
		                	'placeholder' => WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png',
		                )
		           	)					
            	);            	
			}
			
			else {
				$js_enqueue=array();
			}
			
		return $js_enqueue;
	}

	public function remove_entry( $array, $field, $value) {
		if (!isset( $array )) return false;
		foreach ($array as $key=>$item) {
			if ( isset( $item[ $field ] ) )
				if ( $item[ $field ] == $value) 
					break;
		}
		if ( isset($key) ) 
			unset($array[ $key ]);

		return $array;
	}

	public function custom_wpurp_scripts_styles_enqueue() {

		// Enqueue Styles
		if ( array() == $this->custom_enqueued_styles ) return;
		foreach ( $this->custom_enqueued_styles as $key=>$style) {
			$handler = isset($style['name'])?$style['name']:'custom_wpurp_style_' . $key;
			// $handler = (null !== $script(['name']))?$script(['name']):'custom_wpurp_' . $key;
			$url = isset($style['url'])?$style['url']:'';
			$path = isset($style['path'])?$style['path']:'';
			$file = isset($style['file'])?$style['file']:'';
			$deps = isset($style['deps'])?$style['deps']:'';
			// $version = self::CUSTOM_WPURP_TEMPLATES_VERSION;
			$version = CHILD_THEME_VERSION;
			$this->custom_enqueue_style( $handler, $url, $path, $file, $deps, $version );
  		}

		// Enqueue Scripts
		if ( array() == $this->custom_enqueued_scripts ) return;
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
			$this->custom_enqueue_script( $handler, $url, $path, $file, $deps, $version, $footer );

			if (isset($script['data'])) {	
				$data_name = $script['data']['name'];
				unset( $script['data']['name'] );
				wp_localize_script( $handler, $data_name, $script['data'] );
			}
  		}
	}


	public function custom_enqueue_style( $handler, $url='', $path='', $file='', $deps='', $version='' ) {	
		if ( !strpos($file, '.min.css') ) {
			$minfile = str_replace( '.css', '.min.css', $file );
			if (file_exists( $path . $minfile) && WP_MINIFY ) {	
				$file=$minfile;
			}
		}
		//echo '<pre>' . "minpath = {$minpath}" . '</pre>';
		//echo '<pre>' . "path = {$path}" . '</pre>';
	  	//if ((url_exists($minpath)) && (WP_DEBUG==false)) {
	    wp_enqueue_style( $handler, $url . $file, $deps, $version );
	}


	public function custom_enqueue_script( $handler, $url='', $path='', $file='', $deps='', $version='', $footer=false ) {	
		if ( !strpos($file, '.min.js') ) {
			$minfile = str_replace( '.js', '.min.js', $file );
			if (file_exists( $path . $minfile) && WP_MINIFY ) {	
				$file=$minfile;
			}
		}
		//echo '<pre>' . "minpath = {$minpath}" . '</pre>';
		//echo '<pre>' . "path = {$path}" . '</pre>';
	  	//if ((url_exists($minpath)) && (WP_DEBUG==false)) {
	    wp_enqueue_script( $handler, $url . $file, $deps, $version, $footer );
	}


	public function ajax_custom_get_tax_terms() {
		// global $wpdb; //get access to the WordPress database object variable

		if ( !is_user_logged_in() ) die();
		if ( ! isset( $_GET['tax'] ) ) die();
		if ( ! isset( $_GET['keys'] ) ) die();
		
		$taxonomy = $_GET['tax'];
		$keys = $_GET['keys'];

		$terms = get_terms( array(
		    'taxonomy' => $taxonomy,
		    'name__like' => $keys,
		    'hide_empty' => false,
		) );

		//copy the business titles to a simple array
		$suggestions = array();
		foreach( $terms as $term )
			$suggestions[] = addslashes($term->name);
			
		echo json_encode($suggestions); //encode into JSON format and output
	 
		die(); //stop "0" from being output		
	}

	
/* Custom Recipe Submission Shortcode */
	// public function remove_recipe_list_on_edit_recipe( $item, $recipe ) {
	// 	if ( isset( $_GET['wpurp-edit-recipe'] ) )
	// 		$html = '';
	// 	else {
	// 		$url = get_permalink() . self::RECIPE_EDIT_SLUG;	
	// 		//$url = 'http://www.goutu.main/accueil/publier/publier-recettes/recipe-edit/';	
	// 		$html = '<li>';
	// 		$html .= '<a href="' . $url . '?wpurp-edit-recipe=' . $recipe->ID() . '">' . $recipe->title() . '</a>';
	// 		$html .= '</li>';
	// 	}
	// 	return $html;
	// }
	
	
	// public static function output_tooltip($content,$position) {
	// 	// $path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
	// 	$uri = self::$_PluginUri . 'assets/img/callout_'. $position . '.png';
	
	// 	$html ='<div class="tooltip-content">';
	// 	$html.= '<div class="wrap">';
	// 	$html.=$content;
	// 	$html.='<img class="callout" data-no-lazy="1" src="' . $uri . '">';
	// 	$html.='</div>';
	// 	$html.='</div>';
		
	// 	return $html;
	// }

    public function ajax_ingredient_preview() {

        if( ! check_ajax_referer( 'preview_ingredient', 'security', false ) ) {
            wp_send_json_error( array('msg' => 'Nonce not recognized'));
            die();
        }

        if (! is_user_logged_in()) {
            wp_send_json_error( array('msg' => 'User not logged-in'));
            die();
        }

        if( isset($_POST['target_ingredient_id'] ) ) {
            $id= $_POST['target_ingredient_id'][0];
            // echo $id;
        }
        else {
            wp_send_json_error( array('msg' => 'No ingredient id provided'));
            die();
        }

        if ( empty($_POST['ingredient']) ) {
             wp_send_json_error( array('msg' => 'No ingredient name provided'));
            die();       	
        }

        $args=array(
            'amount' => '',
            'unit'  => '',
            'ingredient' => '',
            'notes' => ''
        );

        foreach ($args as $key => $value ) {
            if( isset( $_POST[$key] ) )            
                $args[$key] = $_POST[$key];
        }

        $args['links']='no';

        $ingredient_preview =  Custom_WPURP_Ingredient::display( $args );

        wp_send_json_success( array('msg' => $ingredient_preview) );

        die();
          
    }


	
}


