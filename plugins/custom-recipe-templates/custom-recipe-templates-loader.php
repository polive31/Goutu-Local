<?php
/*
Plugin Name: Custom WPURP Templates
Plugin URI: http://goutu.org/
Description: Customized templates & functionality for WP Ultimate Recipe
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


add_action( 'plugins_loaded', 'Custom_WPURP_templates_init' ); 

function Custom_WPURP_templates_init() {
	
	if ( class_exists('WPUltimateRecipe') ) {

		/* Includes
		------------------------------------*/
		require_once 'includes/Custom_WPURP_Templates.php';
		require_once 'includes/Custom_Recipe_Favorite.php';
		require_once 'includes/Custom_Recipe_Add_To_Shopping_List.php';
		require_once 'includes/Custom_Recipe_Metadata.php';
		// require_once 'includes/Custom_Recipe_Templates.php';
		/* Templates
		------------------------------------*/
		require_once 'templates/Custom_Recipe_Template.php';
		// require_once 'templates/Custom_Recipe_Submission_Template.php';
		require_once 'templates/custom-recipe-print-template.php';
		//require_once 'templates/Custom_Menu_Template.php';
		/* Helpers
		------------------------------------*/
		require_once 'helpers/Custom_User_Submissions_Shortcodes.php';
		/* Helpers
		------------------------------------*/
		require_once 'widgets/custom_recipe_list_widget.php';
		require_once 'widgets/custom_nutrition_label_widget.php';

		new Custom_WPURP_Templates();
		new Custom_User_Submissions_Shortcodes();
		new Custom_Recipe_Template();
		// new Custom_Recipe_Submission_Template();

	}

	else {
		add_action( 'admin_notices', 'wpur_custom_install_notice' );
	}

	function wpur_custom_install_notice() {
		echo '<div id="message" class="error fade"><p style="line-height: 150%">';
		_e('<strong>WPUR Custom Templates Plugin</strong></a> requires the WP Ultimate Recipe plugin to work. Please <a href="plugins.php">activate/install WPUR or deactivate this plugin </a>.');
		echo '</p></div>';
	}	


}




