<?php
/**
 * BuddyPress - bp-custom.php in /plugins folder
 *
 * From: Pascal Olive
 */


/* Change BuddyPress default Members landing tab. */
define('BP_DEFAULT_COMPONENT', 'profile' );

/* Change default thumbnail size */
define ( 'BP_AVATAR_THUMB_WIDTH', 65 );
define ( 'BP_AVATAR_THUMB_HEIGHT', 65 );
define ( 'BP_AVATAR_FULL_WIDTH', 150 );
define ( 'BP_AVATAR_FULL_HEIGHT', 150 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', 640 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_FILESIZE', 2*1024);

/* Change default avatar picture */
define ( 'BP_AVATAR_DEFAULT', 'https://goutu.org/wp-content/themes/foodiepro-2.1.8/images/cook_avatar.png' );
define ( 'BP_AVATAR_DEFAULT_THUMB', 'https://goutu.org/wp-content/themes/foodiepro-2.1.8/images/cook_avatar_thumb.png' );



/* =================================================================*/
/* =             CUSTOM ACTIVATION MAIL => FONCTIONNE PAS !!!
/* =================================================================*/
//
//
//function set_content_type( $content_type ) {
//    return 'text/html';
//}
//
///* Set back plain text format to prevent any conflict with WP mails which require it */
//function default_mail_format() {
//	remove_filter( 'wp_mail_content_type', 'set_content_type' );
//}
////add_action( 'bp_core_sent_user_validation_email', 'default_mail_format' );
//
///* Customize registration mail subject */
//function custom_buddypress_activation_subject( $subject, $user_id ) {
//	$user = get_userdata( $user_id );
//	$text = 'essai3' . ' – Activate your ' . get_bloginfo( 'name' ) . ' account';
//	return $text;
//}
//add_filter( 'bp_core_signup_send_validation_email_subject', 'custom_buddypress_activation_subject', 10, 2 );
//
///* Customize registration mail message */
//add_filter('bp_core_signup_send_validation_email_message', 'add_username_to_activation_email',10,3);
//
//function add_username_to_activation_email($msg, $user_id, $activation_url) {
//    // $username = $_POST['signup_username'];
//    $userinfo = get_userdata($user_id);
//    $username = $userinfo->user_login;
//    $msg .= sprintf( __("After successful activation, you can log in using your username (%1\$s) along with password you choose during registration process.", 'textdomain'), $username);
//    $msg .= 'ESSAI 3';
//    return $msg;
//}

/* =================================================================*/
/* =              PLUGIN INIT
/* =================================================================*/

/* Enqueue Buddypress scripts in the footer rather than the header 
--------------------------------------------------------------------*/
//add_filter( 'bp_core_register_common_scripts', 'enqueue_bp_core_scripts', 15, 1 );
function enqueue_bp_core_scripts($scripts) {
	if (is_admin()) return $scripts;
		
  foreach ( $scripts as $id => $script ) { 
      if (!$script['footer']) 
      	$scripts[$id]['footer']=TRUE; 
  } 

//	print "<pre>";
//	print_r($scripts);
//	print "</pre>";
	
	return $scripts;
}


/* =================================================================*/
/* =              COVER IMAGE SETTINGS
/* =================================================================*/

// Define cover image size
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'foodiepro_xprofile_cover_image', 10, 1 );
function foodiepro_xprofile_cover_image( $settings = array() ) {
    /*$settings['width']  = 1140;
    $settings['height'] = 350;*/
    $settings['default_cover'] = 'https://goutu.org/wp-content/themes/foodiepro-2.1.8/images/cover_default.jpg';
 
    return $settings;
}


 
/* =================================================================*/
/* =              OTHER SETTINGS
/* =================================================================*/

/* Removing the links automatically created in a member’s profile */
//add_action( 'bp_init', 'remove_xprofile_links' );
function remove_xprofile_links() {
    remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
}



/* Defining custom slugs */
// change 'discuss' to whatever you want
//define( 'BP_FORUMS_SLUG', 'discuss' );


/* =================================================================*/
/* =    EXCLUDE ADMIN FROM MEMBERS DIRECTORY & SEARCH
/* =================================================================*/

//add_filter( 'bp_after_has_members_parse_args', 'buddydev_exclude_users_by_role' );

function buddydev_exclude_users_by_role( $args ) {
	//do not exclude in admin
	if( is_admin() && ! defined( 'DOING_AJAX' ) ) return $args;
	
	$excluded = isset( $args['exclude'] )? $args['exclude'] : array();

	if( !is_array( $excluded ) ) $excluded = explode(',', $excluded );
	
	$roles = array('administrator','pending');
	$user_ids =  get_users( array( 'role__in' => $roles ,'fields'=>'ID') );
	
	$excluded = array_merge( $excluded, $user_ids );
	
	$args['exclude'] = $excluded;
	
	return $args;
}

/* =================================================================*/
/* =     PROFILE FIELDS MARKUP
/* =================================================================*/

// Remove <p> from profile fields display
add_action( 'bp_init', 'custom_field_rendering' );
function custom_field_rendering() {
	remove_filter( 'bp_get_the_profile_field_value', 'wpautop' );
}


/* =================================================================*/
/* =     NAV MENUS DISPLAY
/* =================================================================*/

//add_action( 'bp_setup_nav', 'bp_rename_profile_tabs', 999 );
//function bp_rename_profile_tabs() {
//	global $bp;
//	
//	$bp->bp_nav['activity']['name'] = 'wall';
//}

add_action( 'bp_setup_nav', 'custom_subnav_name', 100 );
function custom_subnav_name() {
  buddypress()->members->nav->edit_nav( array('name' => __('Display Profile', 'foodiepro')), 'public', 'profile' );
  buddypress()->members->nav->edit_nav( array('name' => __('Edit Profile', 'foodiepro')), 'edit', 'profile' );
  buddypress()->members->nav->edit_nav( array('name' => __('Change Avatar', 'foodiepro')), 'change-avatar', 'profile' );
  buddypress()->members->nav->edit_nav( array('name' => __('Change Cover', 'foodiepro')), 'change-cover-image', 'profile' );
  //buddypress()->members->nav->edit_nav( array('name' => __('Recent Notifications', 'foodiepro')), 'notifications', 'notifications' );
  //buddypress()->members->nav->edit_nav( array('name' => __('Archived Notifications', 'foodiepro')), 'read', 'notifications' );
}



/* =================================================================*/
/* =     BP-DEPENDANT AVATAR MGT FUNCTIONS  
/* =================================================================*/


//* Prevents Gravatar to be fetched from internet
add_filter('bp_core_fetch_avatar_no_grav', '__return_true');


//* Add gravatar or picture before entry title
add_action( 'genesis_entry_header', 'bg_entry_image', 7 );

function bg_entry_image() {
	if ( is_singular( 'recipe' ) | is_singular( 'post' ) ) /*&& ( function_exists('bp_is_active') ) */{ /* Post or Custom Post */
		$id = get_the_author_meta( 'ID' );
		$pseudo = bp_core_get_username( $id );
		$url = bp_core_get_user_domain( $id );
		$args = array( 
	    'item_id' => $id, 
	    'type' => 'thumb',
	    'title' => $pseudo 
		); 
		echo '<div class="entry-avatar">';
		echo '<a href="' . $url . '">';
		echo bp_core_fetch_avatar( $args );
		echo '</a>';
		echo '</div>';
	}

	elseif ( is_page() ) {
		$key_val = get_post_meta( get_the_ID(), 'entry_header_image', true );
		if ( ! empty( $key_val ) ) {
			echo '<div class="entry-header-image">';
			//echo '<img src="' . site_url( NULL, 'https' ) . '/wp-content/themes/foodiepro-2.1.8/images/' . $key_val . '">';	
			echo '<img src="/wp-content/themes/foodiepro-2.1.8/images/' . $key_val . '">';	
			echo '</div>';	
		}
	}
}


/* Modify WP Recent Posts extended output, depending on the css ID field value */
add_filter('rpwe_after_thumbnail', 'wprpe_add_gravatar', 20, 2);
function wprpe_add_gravatar($output, $args) {
	//PC::debug( array('WPRPE Output add gravatar'=>$output) );
	$disp_avatar = substr($args['cssID'],0,1);
	if ( $disp_avatar == '1') {
		$output .= '<a class="auth-avatar" href="' . bp_core_get_user_domain( get_the_author_meta( 'ID' )) . '" title="' . bp_core_get_username(get_the_author_meta( 'ID' )) . '">';
		$output .= get_avatar( get_the_author_meta( 'ID' ), '45');
		$output .= '</a>';
	}
	//$output = print_r($args, true);
	return $output;
}

/* Modify WPRPE output, displaying posts from current logged-in user */
add_filter( 'rpwe_default_query_arguments', 'wprpe_query_displayed_user_posts' );
function wprpe_query_displayed_user_posts( $args ) {
	if ( $args['author']=='bp_member' )  {
  	$args['author'] = bp_displayed_user_id();
	}
  return $args;
}

//
///* Display current member posts and more link  */
//function wprpe_author_more_link( $args ) {
//		//print_r($args);
//		//if ( $args['author']=='bp_member' ) {
//    	$args['after'] = '
//    		<p class="more-from-category"> 
//				<a href="/author/' . do_shortcode( '[bp_displayed]' ) . '">Toutes les recettes de' . do_shortcode( '[bp_displayed type="name"]' ) . '?</a>
//				</p><br>
//    	';
//		//}
//    return $args;
//}
////add_filter( 'rpwe_default_args', 'wprpe_author_more_link' );


