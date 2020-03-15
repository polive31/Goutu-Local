<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Custom_Admin_Helpers {

	public function __construct() {

        $Columns = new CAM_Tax_Columns();
        add_filter( 'manage_edit-cuisine_columns',              array($Columns, 'add_archive_headline_column'));
        add_action( 'manage_cuisine_custom_column',             array($Columns, 'populate_archive_headline_column'), 10, 3);

        $Helpers = new CAM_Assets();
        add_filter( 'page_attributes_dropdown_pages_args',      array($Helpers, 'my_slug_show_all_parents' ));
        add_filter( 'quick_edit_dropdown_pages_args',           array($Helpers, 'my_slug_show_all_parents' ));
        add_action( 'wp_admin_enqueue_scripts',                 array($Helpers, 'load_admin_stylesheet' ));
        add_action( 'after_setup_theme',                        array($Helpers, 'remove_admin_bar'));
        add_action( 'init',                                     array($Helpers, 'blockusers_init' ));
        // add_action( 'admin_init',                               array($Helpers, 'prevent_plugin_update_conflicts'));

        /* Hooks for Custom_Admin_Post_Filter
        ------------------------------------------------------------- */
        $Filter = new CAM_Post_Filter();
        add_action( 'restrict_manage_posts',    array($Filter, 'restrict_manage_posts')   );
        add_filter( 'parse_query',              array($Filter, 'add_posts_filter' )       );
        add_action( 'admin_bar_menu',           array($Filter, 'add_toolbar_items'), 999  );

    }

    // public static function get_instance() {
    //     if (NULL===self::$instance) {
    //         self::$instance = new self;
    //     }
    //     return self::$instance;
    // }



}
