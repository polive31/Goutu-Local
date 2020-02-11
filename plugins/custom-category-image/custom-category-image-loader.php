<?php

/**
 * Plugin Name: Custom Category Image
 * Version: 2.1.13
 * Plugin URI: https:/goutu.org
 * Text Domain: wpcustom-category-image
 * Domain Path: /lang
 * Description: This plugin allows users to upload their own  category/taxonomy image.
 * Author: P. Olive
 * Tested up to: 4.5.2
 * License: GPL v3
 */

 if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

define('WPCCI_WP_VERSION',      get_bloginfo('version'));
define('WPCCI_WP_MIN_VERSION',  3.5);
define('WPCCI_MIN_PHP_VERSION', '5.3.0');
define('WPCCI_PATH_BASE',       plugin_dir_path(__FILE__) );
define('WPCCI_PATH_TEMPLATES',  WPCCI_PATH_BASE . 'templates/');

function wpcustomcategoryimage_textdomain()
{
    load_plugin_textdomain('wpcustom-category-image', false, plugin_basename(WPCCI_PATH_BASE) . '/lang/');
}

require_once WPCCI_PATH_BASE . 'includes/helpers.php';
require_once WPCCI_PATH_BASE . 'includes/WPCustomCategoryImage.php';

add_action('init', array('WPCustomCategoryImage', 'initialize'));
add_action('plugins_loaded', 'wpcustomcategoryimage_textdomain');

register_activation_hook(__FILE__, array('WPCustomCategoryImage', 'activate'));
register_deactivation_hook(__FILE__, array('WPCustomCategoryImage', 'deactivate'));
