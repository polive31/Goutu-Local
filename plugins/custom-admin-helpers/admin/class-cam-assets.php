<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class CAM_Assets {
    /**
     * Show all parents, regardless of post status.
     *
     * @param   array  $args  Original get_pages() $args.
     *
     * @return  array  $args  Args set to also include posts with pending, draft, and private status.
     */

    public function my_slug_show_all_parents( $args ) {
        $args['post_status'] = array( 'publish', 'pending', 'draft', 'private' );
        return $args;
    }

    /* Chargement des feuilles de style admin */

    public function load_admin_stylesheet() {
        wp_enqueue_style( 'admin-css', CHILD_THEME_URL . '/assets/css/admin.css', array(), CHILD_THEME_VERSION );
    }


    /* Disable admin bar for all users except admin */
    public function remove_admin_bar() {
        if ( !(current_user_can('administrator') || is_admin()) ) {
            show_admin_bar(false);
        }
    }

    public function show_admin_bar() {
        if (WP_ALWAYS_SHOW_ADMIN_BAR===true)
            return true;
    }

    /* Disable dashboard for non admin */
    public function blockusers_init() {
        if ( is_admin() && !current_user_can('edit_others_pages') && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    // public function prevent_plugin_update_conflicts($value) {
    //     // Syntax is 'plugin-folder-name/plugin-file-name.php'
    //     $plugins = array(
    //         'custom-comment-management' => 'custom_comment_management_loader.php',
    //     );
    //     foreach ($plugins as $name => $file) {
    //         if( isset( $value->response[$name . '/' . $file] ) )
    //             unset( $value->response[$name . '/' . $file] );
    //     }
    //     return $value;
    // }

}
