<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class CAM_Admin_Helpers {
    /**
     * Show all parents, regardless of post status.
     *
     * @param   array  $args  Original get_pages() $args.
     *
     * @return  array  $args  Args set to also include posts with pending, draft, and private status.
     */

    function my_slug_show_all_parents( $args ) {
        $args['post_status'] = array( 'publish', 'pending', 'draft', 'private' );
        return $args;
    }

    /* Chargement des feuilles de style admin */

    function load_admin_stylesheet() {
        wp_enqueue_style( 'admin-css', CHILD_THEME_URL . '/assets/css/admin.css', array(), CHILD_THEME_VERSION );
    }


    /* Disable admin bar for all users except admin */
    function remove_admin_bar() {
        if (!current_user_can('administrator') && !is_admin())
        show_admin_bar(false);
    }

    /* Disable dashboard for non admin */
    function blockusers_init() {
        if ( is_admin() && !current_user_can('edit_others_pages') && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

}
