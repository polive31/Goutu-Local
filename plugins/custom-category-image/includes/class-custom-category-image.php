<?php

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Custom_Category_Image
{

    public function __construct()
    {
        $Admin = new CCI_Admin();
        // Actions
        add_action('admin_init',                  array($Admin, 'admin_init'));
        add_action('admin_enqueue_scripts',       array($Admin, 'admin_enqueue_assets'));
        add_action('edit_term',                   array($Admin, 'save_image'));
        add_action('create_term',                 array($Admin, 'save_image'));
        add_action('admin_notices',               array($Admin, 'show_admin_notice'));

        $Public = new CCI_Public();
        // Shortcode
        add_shortcode('wp_custom_image_category', array($Public, 'shortcode'));

    }
}
