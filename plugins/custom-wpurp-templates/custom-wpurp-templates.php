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


add_action( 'plugins_loaded', 'WPURP_custom_templates_init' ); 

function WPURP_custom_templates_init() {
	
	if ( class_exists('WPUltimateRecipe') ) {

	require_once 'includes/WPURP_Custom_Custom_Templates.php';
	require_once 'includes/WPURP_Custom_Recipe_Favorite.php';
	require_once 'includes/WPURP_Custom_Recipe_Add_To_Shopping_List.php';
	require_once 'includes/WPURP_Custom_Metadata.php';
	require_once 'templates/WPURP_Custom_Recipe_Template.php';
	require_once 'widgets/WPUR_custom_recipe_list.php';
	require_once 'widgets/WPUR_custom_nutrition_label.php';

	new WPURP_Custom_Custom_Templates();
	new WPURP_Custom_Recipe_Template();

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




