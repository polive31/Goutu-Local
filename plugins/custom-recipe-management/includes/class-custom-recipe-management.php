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


        /* Hooks for Recipe output
        ------------------------------------------------------------- */
        $Output = new CRM_Output();
        add_filter( 'wpurp_output_recipe_print',array($Output, 'print_recipe'), 10, 2 );

        // add_filter( 'wpurp_recipe_content_loop_check',  array($Recipe_Display, 'disable_wpurp_rendering'));
        add_filter( 'the_content',              array($Output, 'display_recipe_from_scratch'), 10, 2 );
        /* Filter gallery shortcode to remove instructions images */
        add_action('fu_after_upload',           array($Output, 'tag_uploaded_images'), 10, 3);
        add_filter('cgs_media',                 array($Output, 'fetch_gallery_images'), 10, 2);


        /* Hooks for CRM_Favorite
        ------------------------------------------------------------- */
        $Favorite = new CRM_Favorite();
        add_action( 'wp_ajax_custom_favorite_recipe',           array( $Favorite, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe',    array( $Favorite, 'ajax_favorite_recipe' ) );
        add_filter( 'query_vars',                               array( $Favorite, 'add_query_vars_filter') );
        add_shortcode( 'crm-favorites-list',                    array( $Favorite, 'favorite_recipes_shortcode' ) );

        /* Hooks for CRM_Print
        ------------------------------------------------------------- */
        $Print = new CRM_Print();
        add_action('init',                                      array($Print, 'endpoint'));
        add_action('init',                                      array($Print, 'print_page'));
        add_action('template_redirect',                         array($Print, 'redirect'));

        /* Hooks for CRM_Submission
        ------------------------------------------------------------- */
        // Create specific hooks for recipe submission
        $Recipe_Submission_Hooks = new CPM_Submission( 'recipe' );
        $Recipe_Submission = new CRM_Submission();

        // Specific recipe section in Custom Submission Form
        // Specific recipe submission actions
		add_filter( 'cpm_recipe_section', 				    array( $Recipe_Submission, 'add_recipe_specific_section'), 15, 3 );
        add_action( 'cpm_recipe_submission_main', 	        array( $Recipe_Submission, 'recipe_submission_main'     ), 15, 3 );

        // Ajax callbacks for thumbnails removal
        add_action( 'wp_ajax_crm_remove_recipe_image',      array( $Recipe_Submission, 'ajax_remove_instruction_image' ));
        add_action( 'wp_ajax_crm_remove_recipe_image',      array( $Recipe_Submission, 'ajax_remove_instruction_image' ));

        // Ajax callbacks for ingredient preview
        add_action( 'wp_ajax_ingredient_preview',           array( $Recipe_Submission, 'ajax_ingredient_preview'    ));
        add_action( 'wp_ajax_nopriv_ingredient_preview',    array( $Recipe_Submission, 'ajax_ingredient_preview'    ));

        // Ajax Callbacks for Autocomplete jquery plugin
        add_action('wp_ajax_nopriv_get_tax_terms',          array( $Recipe_Submission, 'ajax_custom_get_tax_terms'  ));
        add_action('wp_ajax_get_tax_terms',                 array( $Recipe_Submission, 'ajax_custom_get_tax_terms'  ));

        /* Hooks for CRM_Recipe_Save
        ------------------------------------------------------------- */
        $Save = new CRM_Recipe_Save();
        add_filter('wp_insert_post_empty_content',          array($Save, 'check_empty'), 10, 2);
        add_action('save_post',                             array($Save, 'save'), 10, 2);

        /* Hooks for CRM_Ingredient
        ------------------------------------------------------------- */
        $Ingredient = new CRM_Ingredient();
        add_shortcode( 'ingredient',                array( $Ingredient, 'display_ingredient_shortcode'         ), 10, 2 );
        add_shortcode( 'ingredient-months',         array( $Ingredient, 'display_ingredient_months_shortcode'  ), 10, 2 );
		add_filter('ctlw_meta_query_args',          array( $Ingredient, 'query_current_month_ingredients'), 15, 3 );



        /* Hooks for CRM Shortcodes
        ------------------------------------------------------------- */
        $Shortcodes = new CRM_Recipe_Shortcodes();
        add_shortcode('recipe',                     array($Shortcodes, 'recipe_shortcode'));
        add_shortcode('recipe-timer',               array($Shortcodes, 'timer_shortcode'));

        /* Hooks for CRM Widgets
        ------------------------------------------------------------- */
        add_action( 'widgets_init', 'crm_lists_dropdown_widget_init' );
        add_action( 'widgets_init', 'crm_nutrition_label_widget_init' );


    }



}
