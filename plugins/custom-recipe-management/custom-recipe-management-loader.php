<?php
/*
Plugin Name: Custom Recipe Management
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


// add_action( 'plugins_loaded', 'CRM_init', PHP_INT_MAX );
add_action( 'wp_loaded', 'CRM_init' );


function CRM_init() {
	/* Launch Plugin
	------------------------------------*/
	$dependencies = array(
		'WP Ultimate Recipe' => 'WPUltimateRecipe',
		'Custom Gallery Shortcode' => 'Custom_Gallery_Shortcode',
		'Custom Tooltip' => 'Tooltip',
		'Custom Post Management' => 'CPM_Assets'
	);
	$plugin_missing=false;
	$count=0;
	foreach ($dependencies as $plugin=>$class) {
		if (class_exists($class)) {
			$count++;
			$plugin_missing=$plugin_missing || false;
		}
		else {
			$plugin_missing=true;
			$message='Custom Recipe Management requires plugin <strong>' . $plugin . '</strong> to be installed.';
			new foodiepro_admin_notice( $message );
		}
	}
	if (!$plugin_missing) {
	/* Includes (contain actions & filters, class loaded at startup)
	------------------------------------*/
		require_once 'includes/class-custom-recipe-management.php';
		require_once 'includes/class-crm-assets.php';
		require_once 'includes/class-crm-helpers.php';

	/* 			PUBLIC CLASSES
	------------------------------------*/
		require_once 'public/class-crm-ingredient.php';
		require_once 'public/class-crm-recipe.php';

	/* Custom Recipe Template
	------------------------------------*/
		require_once 'public/custom-recipe-template/CRM_Recipe_Template.php';

		require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Shortcodes.php';
		require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Metadata.php';

	/* Custom Recipe Favorite
	------------------------------------*/
		require_once 'public/custom-recipe-favorite/class-crm-favorite.php';
		require_once 'public/custom-recipe-favorite/class-crm-favorite-shortcodes.php';

	/* Custom Recipe Submission
	------------------------------------*/
		require_once 'public/custom-recipe-submission/class-crm-submission.php';

	/* ADMIN CLASSES
	------------------------------------*/
		require_once 'admin/class-custom-ingredient-meta.php';

	/* Widgets
	------------------------------------*/
		require_once 'widgets/crm_lists_dropdown_widget.php';
		require_once 'widgets/crm_nutrition_label_widget.php';

		Custom_Recipe_Management::get_instance();
	}
}
