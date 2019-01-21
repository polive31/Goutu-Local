<?php

/* CustomPeepso class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Set of actions & filters used to customize some Peepso functions
*/

class PeepsoCustomizations {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	
	
	public function __construct() {	
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		// add_action( 'wp', array($this,'hydrate'));		

		// add_filter( 'genesis_attr_content', 'add_columns_class_to_content' );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_masonry_scripts'));

        // Filters the post meta information under the headline
        add_filter( 'peepso_postbox_message', array($this, 'custom_postbox_message') );		
		
	}


	public function custom_postbox_message( $msg ) {

		
		// 	break;
		// case "firstname" : 
		// 	$field=$user->get_firstname();
		// 	break;
		// case "lastname" : 
		// 	$field=$user->get_lastname();
		// 	break;				
		// case "nicename" : 
		// 	$field=$user->get_nicename();
		
		$PeepSoProfile=PeepSoProfile::get_instance();
		if ( $PeepSoProfile->is_current_user() ) {
			$me_id = get_current_user_id();
			$me = PeepsoUser::get_instance( $me_id );
			$myname=$me->get_firstname();
			$msg = sprintf(__('What\'s new today, %s ?','foodiepro'),$myname);
		}
		else {
			$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			$user = PeepsoUser::get_instance( $user_id );
			$username=$user->get_firstname();
			$msg = sprintf(__('Post a message on %s\'s news feed...','foodiepro'), $username);
		}
		
		return $msg;
	}

}

new PeepsoCustomizations();