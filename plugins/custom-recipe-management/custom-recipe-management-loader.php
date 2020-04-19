<?php
/*
Plugin Name: Custom Recipe Management
Plugin URI: http://goutu.org/
Description: Customized templates & functionality for WP Ultimate Recipe
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
Text Domain: crm
Domain Path: ./languages
*/

// Block direct requests
if ( !defined('ABSPATH') )
die('-1');


// add_action( 'plugins_loaded', 'CRM_init', PHP_INT_MAX );
add_action('plugins_loaded', 'crm_load_textdomain');
// add_action('init', 'CRM_init' );
add_action('plugins_loaded', 'CRM_init' );


/* Chargement du text domain */
function crm_load_textdomain()
{
	load_plugin_textdomain('crm', false, 'custom-recipe-management/lang/');
}


function CRM_init() {

	/* Check Dependencies
	-------------------------------------------------------------*/
	$dependencies = array(
		// 'WP Ultimate Recipe' => 'WPUltimateRecipe',
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
		// require_once 'includes/class-crm-helpers.php';

		/* 		PUBLIC CLASSES
		------------------------------------*/
		/* Custom Recipe Post Type and associated Taxonomies
		------------------------------------*/
		// require_once 'public/recipe-post-type/class-wpurp-recipe.php';
		require_once 'public/recipe-post-type/class-crm-recipe.php';
		require_once 'public/recipe-post-type/class-crm-ingredient.php';

		/* Custom Recipe Template
		------------------------------------*/
		require_once 'public/recipe-output/class-crm-output.php';
		require_once 'public/recipe-output/helpers/class-crm-recipe-meta.php';
		require_once 'public/recipe-output/helpers/class-crm-shortcodes.php';
		require_once 'public/recipe-output/helpers/class-crm-favorite.php';

		/* Custom Recipe Submission
		------------------------------------*/
		require_once 'public/recipe-submission/class-crm-submission.php';
		require_once 'public/recipe-submission/helpers/class-crm-recipe-save.php';

		/* ADMIN CLASSES
		------------------------------------*/
		require_once 'admin/class-crm-ingredient-month.php';
		require_once 'admin/class-crm-ingredient-metadata.php';
		require_once 'admin/class-wpurp-taxonomy-metadata.php';
		require_once 'admin/class-crm-recipe-post-type.php';
		require_once 'admin/class-crm-taxonomies.php';

		require_once 'admin/helpers/class-crm-notices.php';
		// require_once 'vendor/taxonomy-metadata/Taxonomy_MetaData.php';

		/* Widgets
		------------------------------------*/
		require_once 'widgets/crm_nutrition_label_widget.php';

		/* Create class using the Singleton method */
		Custom_Recipe_Management::get_instance();
	}
}
