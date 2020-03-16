<?php
/*
Plugin Name: Custom Archive Management
Plugin URI: http://goutu.org/
Description: Custom Archive pages and helpers
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* Main
------------------------------------------------------------*/
require_once 'includes/class-custom-archive-management.php';
require_once 'includes/class-cnh-assets.php';

require_once 'public/class-cnh-archive-entries.php';
require_once 'public/class-cnh-archive-headline.php';
require_once 'public/class-cnh-tags-overlay.php';
// require_once 'public/class-cnh-structured-data.php';

require_once 'widgets/dropdown-posts-sort-widget.php';
require_once 'widgets/taxonomy-dropdown-widget.php';
require_once 'widgets/taxonomy-search-widget.php';
require_once 'widgets/taxonomy-accordion-widget.php';

add_action('plugins_loaded', 'custom_archive_management_start');
function custom_archive_management_start()
{
	new Custom_Archive_Management();
}
