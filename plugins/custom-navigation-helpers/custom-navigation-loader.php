<?php
/*
Plugin Name: Custom Navigation Helpers
Plugin URI: http://goutu.org/
Description: Custom shortcodes & widgets for site navigation purposes
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

//if (! class_exists( 'PHP_Debug')) {
//	class PHP_Debug {
//		public function log( $msg, $var=false) {}
//		public function trace( $msg, $var=false) {}
//	}
//}

require_once 'includes/class-custom-navigation-helpers.php';
require_once 'includes/class-cnh-assets.php';

require_once 'public/class-cnh-archive-entries.php';
require_once 'public/class-cnh-archive-headline.php';
require_once 'public/class-cnh-tags-overlay.php';
require_once 'public/class-cnh-shortcodes.php';
require_once 'public/class-cnh-rpwe-customizations.php';

require_once 'widgets/dropdown-posts-sort-widget.php';
require_once 'widgets/taxonomy-dropdown-widget.php';
require_once 'widgets/taxonomy-accordion-widget.php';

new Custom_Navigation_Helpers();
