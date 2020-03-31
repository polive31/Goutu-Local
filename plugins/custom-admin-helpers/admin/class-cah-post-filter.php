<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CAH_Post_Filter {

    const POST_TYPE ='recipe';
    const META_KEY ='ingredient_note';

    public function restrict_manage_posts(){
        $type = 'post';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }

        //only add filter to post type you want
        if (self::POST_TYPE == $type){
            //change this to the list of values you want to show
            //in 'label' => 'value' format
            $values = array(
                'label' => 'value',
                'label1' => 'value1',
                'label2' => 'value2',
            );
            ?>
            <select name="ADMIN_FILTER_FIELD_VALUE">
            <option value=""><?php _e('Filter By ', 'foodiepro'); ?></option>
            <?php
                $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
                foreach ($values as $label => $value) {
                    printf
                        (
                            '<option value="%s"%s>%s</option>',
                            $value,
                            $value == $current_v? ' selected="selected"':'',
                            $label
                        );
                    }
            ?>
            </select>
            <?php
        }
    }

    public function add_posts_filter( $query ){
        global $pagenow;
        $type = 'post';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }
        if ( self::POST_TYPE == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
            $query->query_vars['meta_key'] = self::META_KEY;
            $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
        }
    }


    public function add_toolbar_items($wp_admin_bar) {
        $menu_id = 'foodiepro';
        $wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => 'Foodiepro', 'href' => '/'));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Users'), 'id' => 'foodiepro_users', 'href' => get_site_url( null, 'wp-admin/users.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Ingredients'), 'id' => 'foodiepro_ingredients', 'href' => get_site_url( null, 'wp-admin/edit-tags.php?taxonomy=ingredient&post_type=recipe'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Peepso'), 'id' => 'foodiepro_peepso', 'href' => get_site_url( null, 'wp-admin/admin.php?page=peepso'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Plugins'), 'id' => 'foodiepro_plugins', 'href' => get_site_url( null, 'wp-admin/plugins.php'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Color Theme'), 'id' => 'foodiepro_colortheme', 'href' => get_site_url( null, 'wp-admin/themes.php?page=cct-options'), 'meta' => array('target' => '_blank')));
        $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Contact Forms'), 'id' => 'foodiepro_contactforms', 'href' => get_site_url( null, 'wp-admin/edit.php?post_type=contact'), 'meta' => array('target' => '_blank')));
        // $wp_admin_bar->add_menu(array('parent' => $menu_id, 'title' => __('Drafts'), 'id' => 'dwb-drafts', 'href' => 'edit.php?post_status=draft&post_type=post'));
    }
}
