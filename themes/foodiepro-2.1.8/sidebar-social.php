<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

//* Output primary sidebar structure
genesis_markup( array(
	'html5'   => '<aside %s>' . genesis_sidebar_title( 'sidebar' ),
	'xhtml'   => '<div id="sidebar" class="sidebar widget-area">',
	'context' => 'sidebar-primary',
) );

    genesis_structural_wrap( 'sidebar' );
    do_action( 'genesis_before_sidebar_widget_area' );
    dynamic_sidebar('social-sidebar');
    do_action( 'genesis_after_sidebar_widget_area' );
    genesis_structural_wrap( 'sidebar', 'close' );

genesis_markup( array(
	'html5' => '</aside>', //* end .sidebar-primary
	'xhtml' => '</div>', //* end #sidebar
) );
