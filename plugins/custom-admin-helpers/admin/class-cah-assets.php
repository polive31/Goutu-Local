<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class CAH_Assets {
    /**
     * Show all parents, regardless of post status.
     *
     * @param   array  $args  Original get_pages() $args.
     *
     * @return  array  $args  Args set to also include posts with pending, draft, and private status.
     */


    private static $options;

    public function __construct() {
        self::$options = get_option('foodiepro');
    }

    public static function get_option( $index='' ) {
        if (empty($index))
            return self::$options;
        elseif (isset(self::$options[$index]))
            return self::$options[$index];
        else {
            return false;
        }
    }

    public static function set_option( $index, $value ) {
        self::$options[$index]=$value;
    }

    // public function my_slug_show_all_parents( $args ) {
    //     $args['post_status'] = array( 'publish', 'pending', 'draft', 'private' );
    //     return $args;
    // }

    /* Chargement des feuilles de style admin */

    public function load_admin_stylesheet() {
        wp_enqueue_style( 'admin-css', CHILD_THEME_URL . '/assets/css/admin.css', array(), CHILD_THEME_VERSION );
    }


}
