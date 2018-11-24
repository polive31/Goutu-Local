<?php
/*
Plugin Name: Custom Lightbox
Plugin URI: http://goutu.org/
Description: Customized lightbox for post images & galleries
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
require_once 'includes/Custom_Lightbox.php';
new CustomLightbox();









