<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class Custom_Google_Recaptcha {

    public function __construct() {

        $Public = new CGR_Public();
        add_action('wp_enqueue_scripts',    array($Public, 'register_recaptcha_script'));
        add_shortcode('g-recaptcha',        array($Public, 'display_google_recaptcha'));

        $Admin = new CGR_Admin();
        add_action('admin_menu',            array($Admin, 'add_cgr_options_page'));
        add_action('admin_init',            array($Admin, 'register_cgr_settings'));

    }
}
