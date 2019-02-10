<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Custom_Recipe_Management {
    
    protected static $instance;
    
	public function __construct() {
        /* Hooks for CRM_Assets
        ------------------------------------------------------------- */        
        $Assets = new CRM_Assets();
        /* Disable WPURP scripts & style enqueue */
        add_filter ( 'wpurp_assets_js',     array($Assets,'enqueue_wpurp_js'), 15, 1 );
        add_filter ( 'wpurp_assets_css',    array($Assets,'enqueue_wpurp_css'), 15, 1 );

        
        /* recipe setup filters for CPM  */
        add_filter( 'cpm_page_slugs',       array( $Assets, 'setup_CPM_recipe_page_slugs') );
        add_filter( 'cpm_labels',           array( $Assets, 'setup_CPM_recipe_labels'    ) );
        add_filter( 'cpm_enqueued_styles',  array( $Assets, 'setup_CPM_recipe_styles'    ) );
        add_filter( 'cpm_enqueued_scripts', array( $Assets, 'setup_CPM_recipe_scripts'   ) );
        add_filter( 'cpm_taxonomies',       array( $Assets, 'setup_CPM_taxonomies'       ) );
        add_filter( 'cpm_required',         array( $Assets, 'setup_CPM_required'         ) );
        

        /* Hooks for Recipe output
        ------------------------------------------------------------- */
        $Recipe_Template = new CRM_Recipe_Template();
        /* Override WPURP output (includes toolbar, therefore no need for cpm_recipe_toolbar action) */
        add_filter( 'wpurp_output_recipe',          array($Recipe_Template,'display_recipe'), 10, 2 ); 
        add_filter( 'wpurp_output_recipe_print',    array($Recipe_Template,'print_recipe'), 10, 2 );
        
       
        // Recipe headline filter
        // add_filter( 'genesis_post_info',            array($Recipe_Template, 'add_recipe_edit_button'), 20); // Important : priority must be above 15 since post meta is customized with priority 15 (see custom post templates)

        /* Hooks for CRM_Favorite
        ------------------------------------------------------------- */
        $Favorite = new CRM_Favorite();
        add_action( 'wp_ajax_custom_favorite_recipe',           array( $Favorite, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe',    array( $Favorite, 'ajax_favorite_recipe' ) );

        add_filter( 'query_vars',                               array( $Favorite, 'add_query_vars_filter') );   



        /* Hooks for CRM_Submission
        ------------------------------------------------------------- */
        // Create specific hooks for recipe submission
        $Recipe_Submission_Hooks = new CPM_Submission( 'recipe' );
        $Recipe_Submission = new CRM_Submission();

        // Specific recipe section in Custom Submission Form
        // Specific recipe submission actions
		add_filter( 'cpm_recipe_section', 				    array( $Recipe_Submission, 'add_recipe_specific_section'), 15, 3 );	
		add_action( 'cpm_recipe_submission_main', 	        array( $Recipe_Submission, 'recipe_submission_main'     ), 15, 3 );	
        
        // Ajax callbacks for ingredient preview 
        add_action( 'wp_ajax_ingredient_preview',           array( $Recipe_Submission, 'ajax_ingredient_preview'    ));
        add_action( 'wp_ajax_nopriv_ingredient_preview',    array( $Recipe_Submission, 'ajax_ingredient_preview'    ));  

        // Ajax Callbacks for Autocomplete jquery plugin 
        add_action('wp_ajax_nopriv_get_tax_terms',          array( $Recipe_Submission, 'ajax_custom_get_tax_terms'  ));
        add_action('wp_ajax_get_tax_terms',                 array( $Recipe_Submission, 'ajax_custom_get_tax_terms'  ));    


        
        /* Hooks for Custom_Ingredient_Meta
        ------------------------------------------------------------- */
        $Ingredient_Meta = new Custom_Ingredient_Meta();
		add_action( 'admin_init',                   array( $Ingredient_Meta, 'hydrate'                          ));
		add_action( 'ingredient_add_form_fields',   array( $Ingredient_Meta, 'callback_admin_add_months_field'  ), 10, 2 );
		add_action( 'ingredient_edit_form_fields',  array( $Ingredient_Meta, 'callback_ingredient_edit_fields'  ), 10, 2 );
		add_action( 'edited_ingredient',            array( $Ingredient_Meta, 'callback_admin_save_meta'         ), 10, 2 );  
		add_action( 'create_ingredient',            array( $Ingredient_Meta, 'callback_admin_save_meta'         ), 10, 2 );

                
        
        /* Hooks for CRM_List
        ------------------------------------------------------------- */        
        // $List = new CRM_List();        
        // // Ajax callbacks
        // add_action( 'wp_ajax_custom_user_submissions_delete_recipe', array( $Recipe_Submission, 'ajax_user_delete_recipe') );
        // add_action( 'wp_ajax_nopriv_custom_user_submissions_delete_recipe', array( $Recipe_Submission, 'ajax_user_delete_recipe') );

        /* Hooks for CRM_List_Shortcodes
        ------------------------------------------------------------- */        
        // $List_Shortcodes = new CRM_List_Shortcodes();        
        // Created & Favorite recipes lists shortcodes
        // add_shortcode( 'custom-recipe-submissions-current-user-list',   array( $List_Shortcodes, 'submissions_current_user_list_shortcode' ) );
        // add_shortcode( 'custom-wpurp-favorites',                        array( $List_Shortcodes, 'favorite_recipes_shortcode' ) );  
        
        
        /* Hooks for CRM_Recipe_Shortcodes
        ------------------------------------------------------------- */        
        // $Recipe_Shortcodes = new CRM_Recipe_Shortcodes();
        // add_shortcode( 'display-recipe', array( $Recipe_Shortcodes, 'recipe_shortcode' ) );
        // // Customize recipe timer shortcode
        // remove_shortcode('recipe-timer');
        // add_shortcode('recipe-timer', array($Recipe_Shortcodes,'custom_recipe_timer')); 
        
        // add_shortcode('tts', array($Recipe_Shortcodes,'add_tts_button')); 


        /* Hooks for CRM_Submission_Shortcodes
        ------------------------------------------------------------- */        
        // $Submission_Shortcodes = new CRM_Submission_Shortcodes();
        // // Submission pages shortcodes
        // add_shortcode( 'custom-wpurp-submissions-new-recipe', array( $Submission_Shortcodes, 'new_submission_shortcode' ) );
        // add_shortcode( 'custom-wpurp-submissions-current-user-edit', array( $Submission_Shortcodes, 'submissions_current_user_edit_shortcode' ) );
        
    }

    public static function get_instance() {
        if (NULL===self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
	
}

