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
	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	protected static $_PluginPath;	
	protected static $_PluginUri;	
	// public static $logged_in;

	protected $custom_enqueued_scripts = array();
	protected $custom_enqueued_styles = array();
	private $post_ID;

	public function __construct() {
	
		self::$_PluginUri = plugin_dir_url( dirname( __FILE__ ) );
		self::$_PluginPath = plugin_dir_path( dirname( __FILE__ ) );
		
		add_action( 'init', array($this, 'hydrate'));
		
		/* WPURP enqueue list */
		add_filter ( 'wpurp_assets_js', array($this,'enqueue_wpurp_js'), 15, 1 );
		add_filter ( 'wpurp_assets_css', array($this,'enqueue_wpurp_css'), 15, 1 );
		
		/* Custom enqueue list */
		add_action( 'wp_enqueue_scripts', array($this, 'custom_wpurp_scripts_styles_enqueue') );

        /* Customize Recipe Screen output */
        add_filter('wpurp_output_recipe', array($this,'display_recipe'), 10, 2 ); 

        /* Customize Recipe Print output */
        add_filter( 'wpurp_output_recipe_print', array($this,'print_recipe'), 10, 2 );

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
		// self::$logged_in = is_user_logged_in();	
		// self::$id=0;
	}
	
/* Custom Menu Template */	
	// public function wpurp_custom_menu_template( $form, $menu ) {
	// 	return '';
	// }

    public function print_recipe( $content, $recipe ) {
        $post_ID = get_the_ID();

        ob_start();
        include( self::$_PluginPath . 'templates/custom-recipe-template.php' );
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    public function display_recipe( $content, $recipe ) {

        if ( isset($recipe->ID ) )
            $recipe = new Custom_WPURP_Recipe( $recipe->ID );
        else
            $recipe = new Custom_WPURP_Recipe( $recipe->ID() );


        $imgID = $recipe->featured_image();
        $imgAlt = get_post_meta($imgID,'_wp_attachment_image_alt', true);
        if (empty($imgAlt))
            // $imgAlt=sprintf(__('Recipe of %s', 'foodiepro'), $recipe->title());
            $imgAlt=$recipe->title();

        ob_start();
        
        // Output JSON+LD metadata & rich snippets
        echo $this->json_ld_meta_output($recipe,'');

        include( self::$_PluginPath . 'templates/custom-recipe-template.php' );

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    public function json_ld_meta_output( $recipe, $args ) {
        $Custom_Metadata = new Custom_Recipe_Metadata();
        // $metadata = in_array( WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ), array( 'json', 'json-inline' ) ) ? $Custom_Metadata->get_metadata( $recipe ) : '';
        $metadata = $Custom_Metadata->get_metadata( $recipe );

        ob_start();
        echo $metadata;
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function custom_ingredients_list( $recipe, $args ) {
        $out = '';
        $previous_group = '';       
        $first_group = true;
        //$out .= '<ul class="wpurp-recipe-ingredients">';
        
        foreach( $recipe->ingredients() as $ingredient ) {

            if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
                $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
                if ( $term !== null && !is_wp_error( $term ) ) {
                    $ingredient['ingredient'] = $term->name;
                }
            }

            if( $ingredient['group'] != $previous_group || $first_group ) { //removed isset($ingredient['group'] ) && 
                $out .= $first_group ? '' : '</ul>';
                $out .= '<ul class="wpurp-recipe-ingredients">';
                $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
                $previous_group = $ingredient['group'];
                $first_group = false;
            }

            $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeIngredient"' : '';

            $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';
            $out .= '<input type="checkbox" name="ingredient-check">&nbsp;</input>';

            $ingredient['links'] = 'yes';
            $out .= Custom_WPURP_Ingredient::display( $ingredient );

            $out .= '</li>';
        }
        //$out .= '</ul>';

        return $out;
    }
            
    public function custom_instructions_list( $recipe, $args ) {
        $out = '';
        $previous_group = '';
        $instructions = $recipe->instructions();
        
        $out .= '<ol class="wpurp-recipe-instruction-container">';
        $first_group = true;
        
        for( $i = 0; $i < count($instructions); $i++ ) {
                    
            $instruction = $instructions[$i];
                    $first_inst = false;
                    
                    if( $instruction['group'] != $previous_group ) { /* Entering new instruction group */
                            $first_inst = true;
                $out .= $first_group ? '' : '</ol>';
                $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
                $out .= '<ol class="wpurp-recipe-instructions">';
                $previous_group = $instruction['group'];
                        $first_group = false;
            }

            $style = $first_inst ? ' li-first' : '';
            $style .= !isset( $instructions[$i+1] ) || $instruction['group'] != $instructions[$i+1]['group'] ? ' li-last' : '';

            $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeInstructions"' : '';

            $out .= '<li class="wpurp-recipe-instruction ' . $style . '">';
            //$out .= '<div' . $meta . '>'.$instruction['description'].'</div>';
            $out .= '<span>' . $instruction['description'] . '</span>';

            if( !empty($instruction['image']) ) {
                $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
                $thumb_url = $thumb['0'];

                $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
                $full_img_url = $full_img['0'];

                $title_tag = WPUltimateRecipe::option( 'recipe_instruction_images_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $instruction['image'] ) ) : esc_attr( $instruction['description'] );
                $alt_tag = WPUltimateRecipe::option( 'recipe_instruction_images_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $instruction['image'], '_wp_attachment_image_alt', true ) ) : esc_attr( $instruction['description'] );

                if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) {
                    $out .= '<div class="instruction-step-image"><a href="' . $full_img_url . '" rel="lightbox" title="' . $title_tag . '">';
                    $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
                    $out .= '</a></div>';
                } else {
                    $out .= '<div class="instruction-step-image"><img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/></div>';
                }
            }

            $out .= '</li>';
        }
            $out .= '</ol>';

        return $out;
    }
	








/********************************************************************************
****                   CUSTOM ENQUEUE STYLES                           **********
********************************************************************************/








	
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
			// $css_enqueue=array();
			
			// Remove public_base.css stylesheet from enqueued styles
			foreach ($css_enqueue as $key=>$style) {
				if ( strpos('public_base.css',$style['file']) ) {
					break 1;
				}
			}
			unset($css_enqueue[$key]);

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
					'url' => self::$_PluginUri . 'assets/css/custom-user-submission.css',
					'path' => self::$_PluginPath . 'assets/css/custom-user-submission.css',
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
		                'name' => 'custom-user-submissions',
		                'url' => self::$_PluginUri . 'assets/js/',
		                'path' => self::$_PluginPath . 'assets/js/',
		                'file' => 'custom_user_submission.js',
		                'deps' => array(
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
				// $version = self::CUSTOM_WPURP_TEMPLATES_VERSION;
				$version = CHILD_THEME_VERSION;
				$this->custom_enqueue_style( $handler, $url, $path, $file, $deps, $version );
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
				$this->custom_enqueue_script( $handler, $url, $path, $file, $deps, $version, $footer );

				if (isset($script['data'])) {	
					$data_name = $script['data']['name'];
					unset( $script['data']['name'] );
					wp_localize_script( $handler, $data_name, $script['data'] );
				}
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


	
}


