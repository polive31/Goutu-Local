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

require_once 'includes/CustomNavigationHelpers.php';
require_once 'includes/CustomArchiveHeadline.php';
require_once 'includes/CustomArchiveEntries.php';
require_once 'includes/CustomArchiveEntryTags.php';
require_once 'includes/CustomNavigationShortcodes.php';
require_once 'includes/RPWE_Customizations.php';

// new CustomArchiveMeta();
new CustomArchiveHeadline();
new CustomArchiveEntryTags();
new CustomNavigationShortcodes();
new RPWE_Customizations();

require_once 'widgets/dropdown-posts-sort-widget.php';
require_once 'widgets/taxonomy-dropdown-widget.php';
require_once 'widgets/taxonomy-accordion-widget.php';


