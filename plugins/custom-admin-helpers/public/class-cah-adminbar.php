<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CAH_Adminbar
{

    /* Disable admin bar for all users except admin */
    public function admin_bar_visibility() {
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

    public function show_admin_bar_unlogged_users() {
        if (CAH_Assets::get_option('adminbar_visibility') == "all") {
            return true;
        }
    }

    /* Disable dashboard for non admin */
    public function blockusers_init() {
        if ( is_admin() && !current_user_can('edit_others_pages') && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }


    public function add_toolbar_items($wp_admin_bar)
    {
        $menu_id = 'foodiepro';
        $wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => 'Foodiepro', 'href' => '/'));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('FoodiePro Settings'), 'id' => 'foodiepro_colortheme', 'href' => get_site_url(null, 'wp-admin/themes.php?page=foodiepro-options'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Users'), 'id' => 'foodiepro_users', 'href' => get_site_url(null, 'wp-admin/users.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Ingredients'), 'id' => 'foodiepro_ingredients', 'href' => get_site_url(null, 'wp-admin/edit-tags.php?taxonomy=ingredient&post_type=recipe'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Peepso'), 'id' => 'foodiepro_peepso', 'href' => get_site_url(null, 'wp-admin/admin.php?page=peepso'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Plugins'), 'id' => 'foodiepro_plugins', 'href' => get_site_url(null, 'wp-admin/plugins.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Contact Forms'), 'id' => 'foodiepro_contactforms', 'href' => get_site_url(null, 'wp-admin/edit.php?post_type=contact'), 'meta' => array('target' => '_blank')));
        // $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Drafts'), 'id' => 'dwb-drafts', 'href' => 'edit.php?post_status=draft&post_type=post'));
    }

}
