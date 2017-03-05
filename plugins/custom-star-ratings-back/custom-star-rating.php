<?php
/*
Plugin Name: Custom Star Ratings Back
Plugin URI: http://goutu.org/custom-star-ratings-back
Description: Ratings via stars in comments
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
Text Domain: custom-star-ratings-back
Domain Path: ./lang
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//*************************************************************************
//**               INITIALIZATION
//*************************************************************************


$admin_panel = star_ratings_load_class( Custom_Star_Ratings_Admin, true) ;



/**
 * Load a class from /php/ directory.
 *
 * There is no file_exists() check to improve performance.
 *
 * @param  string  $class         Class name
 * @param  boolean $create_object Return an object or TRUE
 * @return bool|$class
 */
function star_ratings_load_class( $class, $create_object = FALSE ) {
    // create the path base just once
    static $base = FALSE;
    ! $base && $base = plugins_url( 'includes', __FILE__ );

    echo 'Bienvenue dans le nouveau custom star rating pluging CLASS-BASED';
    echo $base;
    
		$file = str_replace( '_' , '-' , strtolower( $class ) ) . '.php';    
    //! class_exists( $class ) && require "$base/$file.php";
    ! class_exists( $class ) && require "includes/" . $file;
    return $create_object ? new $class : TRUE;
}

