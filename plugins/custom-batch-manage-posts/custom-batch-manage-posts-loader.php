<?php
/*
Plugin Name: Custom Batch Manage Posts
Plugin URI: http://goutu.org/
Description: Shortcodes for post & comments batch processing
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* =================================================================*/
/* =               PLUGIN INITIALIZATION
/* =================================================================*/

require 'includes/class-custom-batch-manage-posts.php';
// require 'admin/class-cbmp-admin.php';
require 'public/class-cbmp-public.php';
require 'helpers/class-cbmp-helpers.php';
// require 'includes/class-cbmp-comments.php';
// require 'includes/class-cbmp-post-ratings.php';
require 'public/class-cbmp-post-meta.php';
require 'public/class-cbmp-taxonomy-meta.php';


/* Start plugin */
add_action('wp_loaded', 'cbmp_start_plugin');
function cbmp_start_plugin()
{
	new Custom_Batch_Manage_Posts();
}
