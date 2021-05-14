<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Custom_Recipe_Management {

    protected static $instance = NULL;

    public static function get_instance()
    {
        if (NULL === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

	public function __construct() {
        /* Hooks for CRM_Assets
        ------------------------------------------------------------- */
        $Assets = new CRM_Assets();
        /* Disable WPURP scripts & style enqueue */
        add_filter ( 'wpurp_assets_js',     array( $Assets,'enqueue_wpurp_js'), 20, 1 );
        add_filter ( 'wpurp_assets_css',    array( $Assets,'enqueue_wpurp_css'), 20, 1 );
        /* recipe setup filters for CPM  */
        add_filter( 'cpm_page_slugs',       array( $Assets, 'setup_CPM_recipe_page_slugs') );
        add_filter( 'cpm_labels',           array( $Assets, 'setup_CPM_recipe_labels'    ) );
        add_filter( 'cpm_enqueued_styles',  array( $Assets, 'setup_CPM_recipe_styles'    ) );
        add_filter( 'cpm_enqueued_scripts', array( $Assets, 'setup_CPM_recipe_scripts'   ) );
        add_filter( 'cpm_taxonomies',       array( $Assets, 'setup_CPM_taxonomies'       ) );
        add_filter( 'cpm_required',         array( $Assets, 'setup_CPM_required'         ) );
        add_filter( 'cpm_post_status',      array( $Assets, 'setup_CPM_post_status'      ) );
        add_filter( 'cpm_fallback_image',    array( $Assets, 'setup_CPM_recipe_fallback_image'  ) );

        /* Hooks for CRM_Recipe_Post_Type
        ------------------------------------------------------------- */
        $Recipe_CPT = new CRM_Recipe_Post_Type();
        add_action('init',                  array($Recipe_CPT, 'register_recipe_post_type'), 2);
        add_filter('post_class',            array($Recipe_CPT, 'recipe_post_class'));
        // add_filter('post_type_link',        array($Recipe_CPT, 'remove_recipe_slug'), 10, 3);
        // add_action('pre_get_posts',         array($Recipe_CPT, 'remove_recipe_slug_in_parse_request'));

        /* Hooks for CRM_Taxonomies */
        // In order for the slug setting to operate, the taxonomies must be registered before the custom post type
        $Recipe_Taxonomies = new CRM_Taxonomies();
        add_action('init',                  array($Recipe_Taxonomies, 'register'), 1);


        /* Hooks for CRM notices
        ------------------------------------------------------------- */
        $Notices = new CRM_Notices();
        add_action('admin_init',            array($Notices, 'wpurp_hide_notice'));
        add_action('admin_notices',         array($Notices, 'wpurp_admin_notices'));

        /* Hooks for recipe taxonomies Metadata
        ------------------------------------------------------------- */
        $Ingredient_MetaData = new CRM_Ingredient_Metadata();

        $Ingredient_Meta = new CRM_Ingredient_Month();
        add_action('admin_init',                   array($Ingredient_Meta, 'hydrate'));
        add_action('wp',                           array($Ingredient_Meta, 'hydrate'));
        add_action('ingredient_add_form_fields',   array($Ingredient_Meta, 'callback_admin_add_months_field'), 10, 2);
        add_action('ingredient_edit_form_fields',  array($Ingredient_Meta, 'callback_ingredient_edit_fields'), 10, 2);
        add_action('edited_ingredient',            array($Ingredient_Meta, 'callback_admin_save_meta'), 10, 2);
        add_action('create_ingredient',            array($Ingredient_Meta, 'callback_admin_save_meta'), 10, 2);

        /* Hooks for Recipe output
        ------------------------------------------------------------- */
        $Output = new CRM_Output();

        add_action('wp',                            array($Output, 'do_recipe_content'), 10, 3);

        /* Filter gallery shortcode to remove instructions images */
        add_action('fu_after_upload',               array($Output, 'tag_uploaded_images'), 10, 3);
        add_filter('cgs_media',                     array($Output, 'fetch_gallery_images'), 10, 2);

        /* Print hooks */
        add_action('init',                          array($Output, 'endpoint'));
        add_action('template_redirect',             array($Output, 'redirect'));
        // add_action('init',                                      array($Print, 'print_page'));

        /* Shortcodes */
        add_shortcode('recipe',                     array($Output, 'recipe_shortcode'));
        add_shortcode('recipe-timer',               array($Output, 'timer_shortcode'));
        add_shortcode('timer',                      array($Output, 'timer_shortcode'));

        /* Hooks for Recipe Metadata output
        ------------------------------------------------------------- */
        if (class_exists('CSD_Meta')) {
            $Provide_Meta = new CRM_Recipe_Meta();
            add_filter('csd_enqueue_recipe_meta',       array($Provide_Meta,    'enqueue_recipe_meta'));
            $Recipe_Meta = CSD_Meta::get_instance('recipe');
            add_action('wp_footer',                     array($Recipe_Meta,     'render'));
        }

        /* Hooks for CRM_Favorite
        ------------------------------------------------------------- */
        $Favorite = new CRM_Favorite();
        add_action( 'wp_ajax_custom_favorite_recipe',           array( $Favorite, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe',    array( $Favorite, 'ajax_favorite_recipe' ) );
        add_filter( 'query_vars',                               array( $Favorite, 'add_list_query_var') );
        add_filter('cpm_list_dropdown_widget_args',             array( $Favorite, 'cpm_list_dropdown_widget_args_cb'), 10, 2 );
        add_shortcode( 'crm-favorites-list',                    array( $Favorite, 'favorite_recipes_shortcode' ) );


        /* Hooks for CRM_Submission
        ------------------------------------------------------------- */
        // Create specific hooks for recipe submission
        $CPM_Submission_Instance = new CPM_Submission( 'recipe' );
        // Ajax callbacks for recipe autosave
        // TODO MAYBE OPTIMIZE => set action to "post_autosave" in JS
        add_action( 'wp_ajax_recipe_autosave',                   array($CPM_Submission_Instance, 'ajax_post_autosave_cb'));

        // Specific recipe section in Custom Submission Form
        $CRM_Submission_Instance = new CRM_Submission();
        // Add recipe form fields
        add_filter( 'cpm_recipe_section', 				        array($CRM_Submission_Instance, 'cpm_recipe_section_cb'), 15, 2 );

        // Save ingredient terms if recipe gets published
        add_action( 'publish_recipe', 	                        array($CRM_Submission_Instance, 'insert_ingredient_terms'), 15, 3 );

        // Specific recipe submission actions
        add_action( 'cpm_recipe_submission_main', 	            array($CRM_Submission_Instance, 'save_recipe_meta'     ), 15, 2 );

        // Ajax callbacks for thumbnails removal
        add_action( 'wp_ajax_cpm_remove_recipe_image',          array($CRM_Submission_Instance, 'ajax_remove_instruction_image' ));

        // Ajax Callbacks for Autocomplete jquery plugin
        add_action('wp_ajax_get_tax_terms',                     array($CRM_Submission_Instance, 'ajax_custom_get_tax_terms'  ));

        // Ajax callbacks for recipe image upload
        add_action('wp_ajax_cpm_upload_recipe_image',           array($CRM_Submission_Instance, 'ajax_upload_recipe_image'));


        /* Hooks for CRM_Ingredient
        ------------------------------------------------------------- */
        $Ingredient = new CRM_Ingredient();
        add_shortcode( 'ingredient',                        array( $Ingredient, 'display_ingredient_shortcode'         ), 10, 2 );
        add_shortcode( 'ingredient-months',                 array( $Ingredient, 'display_ingredient_months_shortcode'  ), 10, 2 );
        add_filter( 'ctlw_meta_query_args',                 array( $Ingredient, 'query_current_month_ingredients'), 15, 3 );

        // Ajax callbacks for ingredient preview
        add_action( 'wp_ajax_ingredient_preview',           array( $Ingredient, 'ajax_ingredient_preview'));


        /* Hooks for CRM Shortcodes
        ------------------------------------------------------------- */
        // $Shortcodes = new CRM_Recipe_Shortcodes();

        /* Hooks for CRM Widgets
        ------------------------------------------------------------- */
        add_action( 'widgets_init',                 'crm_nutrition_label_widget_init' );


    }



}
