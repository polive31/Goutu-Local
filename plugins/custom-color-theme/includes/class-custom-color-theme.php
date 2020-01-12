<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class Custom_Color_Theme {

    public function __construct() {

        $Admin = new CCT_Admin();
        add_action('admin_menu',            array($Admin, 'add_cct_options_page'));
        add_action('admin_init',            array($Admin, 'register_cct_settings'));

    }
}
