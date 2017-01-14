<?php
/**
 * Plugin Name: WP Rocket - Disable LazyLoad on Buddypress Profile Page
 * * Author:      WP Rocket
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Basic security, prevents file from being loaded directly.
defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );



/*** Make sure BuddyPress is loaded ********************************/
if ( class_exists( 'BuddyPress' ) ) {

	add_filter( 'wp', '__deactivate_rocket_lazyload_if_page' );
	function __deactivate_rocket_lazyload_if_page() {
		if( bp_is_change_avatar() ) {
			add_filter( 'do_rocket_lazyload', '__return_false' );
		}
	}

} 
