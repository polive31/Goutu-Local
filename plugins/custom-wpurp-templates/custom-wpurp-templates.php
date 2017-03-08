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
	
	if ( ! class_exists('WPUltimateRecipe') ) return;

	require_once 'includes/WPURP_Custom_Custom_Templates.php';
	require_once 'includes/WPURP_Custom_Recipe_Favorite.php';
	require_once 'includes/WPURP_Custom_Recipe_Add_To_Shopping_List.php';
	require_once 'includes/WPURP_Custom_Metadata.php';
	require_once 'includes/WPURP_Custom_Recipe_Template.php';

	new WPURP_Custom_Custom_Templates();
	new WPURP_Custom_Recipe_Template();

}




