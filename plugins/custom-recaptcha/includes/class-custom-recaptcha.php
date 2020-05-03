<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class Custom_Recaptcha {

    public function __construct() {

        $Public = new CGR_Public();
        // adds the captcha to the WordPress form
        add_filter('comment_form_submit_button',    array($Public, 'comment_form_add_recaptcha'), 1000);
        // Server side recaptcha verification
        add_filter('preprocess_comment',            array($Public, 'verify_comment_recaptcha'), 1, 1);

        $Google = new CRCA_Google();
        add_action('wp_enqueue_scripts',    array($Google, 'register_recaptcha_script'));

        $Admin = new CGR_Admin();
        add_action('admin_menu',            array($Admin, 'add_cgr_options_page'));
        add_action('admin_init',            array($Admin, 'register_cgr_settings'));

    }
}
