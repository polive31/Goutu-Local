<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Custom_Admin_Helpers {
    protected static $instance = NULL;

    public static function get_instance()
    {
        if (NULL === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

	public function __construct() {

        $Columns = new CAH_Tax_Columns();
        add_filter( 'manage_edit-cuisine_columns',              array($Columns, 'add_archive_headline_column'));
        add_action( 'manage_cuisine_custom_column',             array($Columns, 'populate_archive_headline_column'), 10, 3);

        // $Metabox = new CAH_Post_Metabox();
        // add_filter('add_meta_boxes_recipe',                     array($Metabox, 'post_excerpt_meta_box'));
        // add_filter('add_meta_boxes_post',                       array($Metabox, 'post_excerpt_meta_box'));

        $Assets = new CAH_Assets();
        // add_filter( 'page_attributes_dropdown_pages_args',      array($Assets, 'my_slug_show_all_parents' ));
        // add_filter( 'quick_edit_dropdown_pages_args',           array($Assets, 'my_slug_show_all_parents' ));
        add_action( 'wp_admin_enqueue_scripts',                 array($Assets, 'load_admin_stylesheet' ));
        // add_action( 'admin_init',                               array($Helpers, 'prevent_plugin_update_conflicts'));

        $Filter = new CAH_Post_Filter();
        add_action( 'restrict_manage_posts',                    array($Filter, 'restrict_manage_posts')   );
        add_filter( 'parse_query',                              array($Filter, 'add_posts_filter' )       );

        $Options = new CAH_Options();
        add_action('admin_menu',                                array($Options, 'add_foodiepro_options_page'));
        add_action('admin_init',                                array($Options, 'hydrate'), 0);
        add_action('admin_init',                                array($Options, 'register_color_theme_settings'));
        add_action('admin_init',                                array($Options, 'register_adminbar_settings'));

        add_action('admin_init',                                array($Options, 'maybe_display_admin_notice'));

        $Adminbar = new CAH_Adminbar();
        // add_action( 'after_setup_theme',    array($Adminbar, 'admin_bar_visibility'));
        add_action( 'show_admin_bar',                           array($Adminbar, 'filter_admin_bar_visibility'));
        add_filter( 'casm_styles_blacklist',                    array($Adminbar, 'remove_dashicons'));

        add_action( 'init',                                     array($Adminbar, 'blockusers_init' ));
        add_action( 'admin_bar_menu',                           array($Adminbar, 'add_toolbar_items'), 999  );

    }

}
