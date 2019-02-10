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

	/*
	* 			PUBLIC CLASSES
	------------------------------------*/
	require_once 'public/class-crm-ingredient.php';
	require_once 'public/class-crm-recipe.php';

	/* Custom Recipe Template
	------------------------------------*/
	require_once 'public/custom-recipe-template/CRM_Recipe_Template.php';

	require_once 'public/custom-recipe-template/helpers/CRM_Favorite.php';
	require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Shortcodes.php';
	require_once 'public/custom-recipe-template/helpers/CRM_Recipe_Metadata.php';

	// /* Custom Recipe List
	// ------------------------------------*/
	// require_once 'public/custom-recipe-list/CRM_List.php';
	// require_once 'public/custom-recipe-list/helpers/CRM_List_Shortcodes.php';		

	/* Custom Recipe Submission
	------------------------------------*/
	require_once 'public/custom-recipe-submission/class-crm-submission.php';

	/*
	* 			ADMIN CLASSES
	------------------------------------*/
	require_once 'admin/class-custom-ingredient-meta.php';	

	/* Widgets
	------------------------------------*/
	// require_once 'widgets/custom_recipe_list_widget.php';
	// require_once 'widgets/custom_nutrition_label_widget.php';
	// require_once 'widgets/custom_postlist_dropdown_widget.php';

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

// function foodiepro_check_dependencies( $plugin, $classes ) {
// 	$message = '<div id="message" class="error fade"><p style="line-height: 150%">';
// 	$message .= '<strong>' . $plugin . '</strong></a> requires the following plugins to work. Please <a href="plugins.php">activate/install those plugins.</a>.';
// 	$message .= '<ul>';

// 	$missing_class = false;
// 	foreach ($classes as $class) {
// 		if (!class_exists( $class )) {
// 			$message .= '<li>' . $class . '</li>';
// 			$missing_class = $missing_class || true;
// 		}
// 	}
// 	$message .= '</ul>';
// 	$message .= '</div>';

// 	if ($missing_plugin) {
// 		new foodiepro_admin_notice( $message );
// 		die();
// 	}

// }

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

