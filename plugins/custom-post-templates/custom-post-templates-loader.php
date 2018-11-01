<?php
/*
Plugin Name: Custom Post Templates
Plugin URI: http://goutu.org/
Description: Customized templates & functionality for Genesis Posts
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* Includes
------------------------------------*/
require_once 'includes/Custom_Post_Templates.php';


/* Templates
------------------------------------*/
// require_once 'templates/Custom_Post_Template.php';

/* Helpers
------------------------------------*/


new CustomPostTemplates();
// new Custom_WPURP_Helpers();
// new Custom_Recipe_Template();
// new Custom_WPURP_Ingredient();
// new Custom_Recipe_Submission_Template();






