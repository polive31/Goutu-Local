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
	/* Includes (contain actions & filters, class loaded at startup)
	------------------------------------*/
	require_once 'includes/class-custom-recipe-management.php';
	require_once 'includes/class-crm-assets.php';
	require_once 'includes/class-crm-helpers.php';

	/*
	* 			PUBLIC CLASSES
	------------------------------------*/
	require_once 'public/class-crm-ingredient.php';
	require_once 'public/class-crm-recipe.php';

	/* Custom Recipe Template
	------------------------------------*/
	require_once 'public/custom-recipe-template/CRM_Recipe_Template.php';

	require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Shortcodes.php';
	require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Metadata.php';

	// /* Custom Recipe Favorite
	// ------------------------------------*/
	require_once 'public/custom-recipe-favorite/class-crm-favorite.php';
	require_once 'public/custom-recipe-favorite/class-crm-favorite-shortcodes.php';

	/* Custom Recipe Submission
	------------------------------------*/
	require_once 'public/custom-recipe-submission/class-crm-submission.php';

	/*
	* 			ADMIN CLASSES
	------------------------------------*/
	require_once 'admin/class-custom-ingredient-meta.php';

	/* Widgets
	------------------------------------*/
	require_once 'widgets/crm_lists_dropdown_widget.php';
	require_once 'widgets/crm_nutrition_label_widget.php';

	/* Launch Plugin
	------------------------------------*/
	$dependencies = array(
		'WPUltimateRecipe',
		'Custom_Gallery_Shortcode',
		'Tooltip',
		'CPM_Assets'
	);
	$plugin_missing=false;
	foreach ($dependencies as $dep) {
		if (!class_exists($dep)) {
			$plugin_missing=true;
			$message='Custom Recipe Management requires plugin ' . $dep . ' to be installed.';
			new foodiepro_admin_notice( $message );
		}
	}
	if (!$plugin_missing)
		Custom_Recipe_Management::get_instance();
}


class foodiepro_admin_notice {
    private $_message;
    function __construct( $message ) {
        $this->_message = $message;
        add_action( 'admin_notices', array( $this, 'render' ) );
    }
    function render() {
        printf( $this->_message );
    }
}
