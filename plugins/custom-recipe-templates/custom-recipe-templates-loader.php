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
	
	if ( class_exists('WPUltimateRecipe') || class_exists('Custom_Gallery_Shortcode') && class_exists('Tooltip') ) {

		/* Includes (contain actions & filters, class loaded at startup)
		------------------------------------*/
		require_once 'includes/Custom_WPURP_Assets.php';
		require_once 'includes/Custom_WPURP_Templates.php';
		require_once 'includes/Custom_WPURP_Recipe_Submission.php';
		require_once 'includes/Custom_WPURP_Ingredient.php';
		require_once 'includes/Custom_Recipe_Shortcodes.php';
		require_once 'includes/Custom_Recipe_Submission_Shortcodes.php';
		require_once 'includes/Custom_Recipe_Favorite.php';
		
		/* Helpers (class loaded on demand=)
		------------------------------------*/
		require_once 'helpers/Custom_WPURP_Recipe.php';
		require_once 'helpers/Custom_Recipe_Metadata.php';
		require_once 'helpers/Custom_Recipe_Add_To_Shopping_List.php';

		/* Widgets
		------------------------------------*/
		require_once 'widgets/custom_recipe_list_widget.php';
		require_once 'widgets/custom_nutrition_label_widget.php';
		require_once 'widgets/custom_postlist_dropdown_widget.php';


	}

	else {
		add_action( 'admin_notices', 'wpur_custom_install_notice' );
	}
	
}


function wpur_custom_install_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('<strong>WPUR Custom Templates Plugin</strong></a> requires the following plugins to work. Please <a href="plugins.php">activate/install those plugins.</a>.');
	echo '<ul>';
	echo '<li>WP Ultimate Recipe</li>';
	echo '<li>Custom Tooltip</li>';
	echo '<li>Custom Image Gallery</li>';
	echo '</ul>';
	echo '</p></div>';
}	



