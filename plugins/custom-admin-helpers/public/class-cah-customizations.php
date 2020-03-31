<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CAH_Customizations
{

    /* Disable admin bar for all users except admin */
    public function remove_admin_bar() {
        if (is_admin()) {
            show_admin_bar(true);
            return;
        }

        switch ( CAH_Assets::get_option('adminbar_visibility') ) {
            case "admin":
                if ( current_user_can('administrator') )
                    show_admin_bar(true);
                else
                    show_admin_bar(false);
            break;
            case "loggedin":
                if ( !is_user_logged_in() )
                    show_admin_bar(false);
                else
                    show_admin_bar(true);
                break;
            case "all":
                show_admin_bar(true);
            break;
        }

    }

    // public function show_admin_bar() {
    //     if (WP_ALWAYS_SHOW_ADMIN_BAR===true)
    //         return true;
    // }

    /* Disable dashboard for non admin */
    public function blockusers_init() {
        if ( is_admin() && !current_user_can('edit_others_pages') && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

}
