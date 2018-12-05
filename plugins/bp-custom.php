<?php
/**
 * BuddyPress - bp-custom.php in /plugins folder
 *
 * From: Pascal Olive
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Change BuddyPress default Members landing tab. */
define ( 'BP_DEFAULT_COMPONENT', 'profile' );

/* Change default thumbnail size */
define ( 'BP_AVATAR_THUMB_WIDTH', 65 );
define ( 'BP_AVATAR_THUMB_HEIGHT', 65 );
define ( 'BP_AVATAR_FULL_WIDTH', 150 );
define ( 'BP_AVATAR_FULL_HEIGHT', 150 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', 640 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_FILESIZE', 2*1024);

/* Define cover thumbnail size */
define ( 'BP_COVER_FULL_WIDTH', 1140 );
define ( 'BP_COVER_FULL_HEIGHT', 400 );
define ( 'BP_COVER_THUMB_WIDTH', 350 );
define ( 'BP_COVER_THUMB_HEIGHT', 300 );

/* Set default paths */
//define ( 'BP_IMAGES_PATH', get_bloginfo('stylesheet_directory') . '/images/buddypress/');
define ( 'BP_IMAGES_PATH', get_stylesheet_directory() . '/images/buddypress/');
define ( 'BP_IMAGES_URL', get_stylesheet_directory_uri() . '/images/buddypress/');

/* Change default avatar picture */
define ( 'BP_AVATAR_DEFAULT', BP_IMAGES_URL . 'cook_avatar.png' );
define ( 'BP_AVATAR_DEFAULT_THUMB', BP_IMAGES_URL . 'cook_avatar_thumb.png' );


/* =================================================================*/
/* =            CUSTOM JAVASCRIPT
/* =================================================================*/

//add_action('wp_enqueue_scripts', 'custom_bp_js'); // Reserved for debug, better not replace core BP javascript !
function custom_bp_js() {
	$version = '1.0.0';
	wp_deregister_script( 'dtheme-ajax-js');
	wp_enqueue_script( 'dtheme-ajax-js', get_bloginfo('stylesheet_directory') . '/buddypress/js/global.js', array( 'jquery' ), $version );
}


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
/* =           AVATAR & COVER IMAGE 
/* =================================================================*/


// Define cover image size
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'foodiepro_xprofile_cover_image', 10, 1 );
function foodiepro_xprofile_cover_image( $settings = array() ) {
	$settings['width']  = BP_COVER_FULL_WIDTH;
	$settings['height'] = BP_COVER_FULL_HEIGHT;
	$settings['default_cover_url'] = BP_IMAGES_URL . 'cover_default.jpg';
	$settings['default_cover_path'] = BP_IMAGES_PATH . 'cover_default.jpg';
	return $settings;
}

// Inline css not used in foodiepro theme (replaced it with custom cover image php)
remove_action( 'bp_enqueue_scripts', 'bp_add_cover_image_inline_css', 11 );



/* =================================================================*/
/* =              LAYOUT
/* =================================================================*/

// Force to use assigned Page template with BuddyPress Register page (not working !) 
add_filter( 'template_include', 'set_register_full_width', 100 );
function set_register_full_width( $template ) {
	if ( bp_is_register_page() ) {
		$template_slug = get_page_template_slug( buddypress()->pages->register->id );

		if ( $template_slug && ( $located = locate_template( array( $template_slug ), false ) ) ) {
			$template = $located;
		}
	}
	return $template;
}


/* =================================================================*/
/* =              MISC
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

add_filter( 'bp_after_has_members_parse_args', 'buddydev_exclude_users_by_role' );

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


// add_action('bp_ajax_querystring','bpdev_exclude_users',20,2);
// Remove admin from the member directory
function bpdev_exclude_users($qs=false,$object=false){
    
    $excluded_user='1'; // Id's to remove, separated by comma
	
    if($object != 'members' && $object != 'friends')// hide admin to members & friends 
    return $qs;
	
    $args=wp_parse_args($qs);

    if(!empty($args['user_id']))
    return $qs;	
	
    if(!empty($args['exclude']))
		$args['exclude'] = $args['exclude'].','.$excluded_user;
    else
		$args['exclude'] = $excluded_user;
	
    $qs = build_query($args);

    return $qs;
	
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
/* =     COVER IMAGE MGT FUNCTIONS  
/* =================================================================*/

//add_action( 'xprofile_cover_image_uploaded', 'generate_cover_thumbnails', 15, 4 );

function generate_cover_thumbnails($user_id, $cover_url, $name, $feedback_code) {

	$path = parse_url($cover_url, PHP_URL_PATH);	
	$path_parts = pathinfo($path, PATHINFO_EXTENSION | PATHINFO_FILENAME);
	$extension = $path_parts['extension'];
	$filename = $path_parts['filename'];

	$dirname = bp_attachments_uploads_dir_get( 'basedir');
	$dirname .= '/members/' . $user_id . '/cover-image/'; 
		
	$image_path = $dirname . $filename . '.' . $extension;	
	$image = wp_get_image_editor( $image_path );
	if ( ! is_wp_error( $image ) ) {
    $image->resize( BP_COVER_THUMB_WIDTH, BP_COVER_THUMB_HEIGHT, true );
    $image->save( $dirname . 'thumb-' . $filename . '-' . BP_COVER_THUMB_WIDTH . 'x' . BP_COVER_THUMB_HEIGHT );
		$result= 'success';
	}
	else
		$result = 'failure';

	add2log('user_id',$user_id);
	add2log('cover_url', $cover_url);
	add2log('name',$name);
	add2log('feedback_code',$feedback_code);
	add2log('dirname', $dirname );
	add2log('image_path', $image_path );
	add2log('media upload outcome : ', $result);

}

function add2log($string, $var) {
	$err_str=$string . ":" . $var;
	error_log( $err_str );
}

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}


/* =================================================================*/
/* =     BP-DEPENDANT AVATAR MGT FUNCTIONS  
/* =================================================================*/

//* Prevents Gravatar to be fetched from internet
add_filter('bp_core_fetch_avatar_no_grav', '__return_true');



// Add post author to RPWE  widget
add_filter('rpwe_post_title_meta', 'rpwe_add_author', 10, 2);
function rpwe_add_author($output, $args ) {
	$disp_author = substr($args['cssID'],2,1);
	if ( $disp_author == '1') {
		$id = get_the_author_meta( 'ID' );
		$name = bp_core_get_username( $id );
		$url = bp_core_get_user_domain( $id );
		$link = '<a href="' . $url . '">' . $name . '</a>';
		$output .= '<span class="rpwe-author">' . sprintf(__('by %s','foodiepro'), $link ) . '</span>';
	}
	return $output;
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
/* Workaround for shortcodes in rpwe "after" html not executing 
in social-bottom widgeted area  */
add_filter( 'rpwe_markup', 'add_more_from_author_link',15,2);
function add_more_from_author_link($html,$args) {
	$user_id=bp_displayed_user_id();
	$name=bp_core_get_username($user_id);	
  	if ($args['author']=='bp_member') {
		$html.='<p class="more-from-category">'; 
		$html.='<a href="' . get_author_posts_url($user_id) . '">' . sprintf(_x('All posts from %s','consonant','foodiepro'), $name) . '</a>';
		$html.='</p>';
	}
	return $html;
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

