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
//define ( 'BP_AVATAR_FULL_WIDTH', 150 );
//define ( 'BP_AVATAR_FULL_HEIGHT', 150 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', 640 );
//define ( 'BP_AVATAR_ORIGINAL_MAX_FILESIZE', $max_in_kb );

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
//add_filter( 'bp_core_register_common_scripts', 'enqueue_bp_core_scripts', 15, 1 );

/* Buddypress Friends Widget */
function enqueue_bp_js() {
	if (is_admin()) return;
	
	$min='min';
	
	wp_deregister_script('bp-legacy-js');
  wp_enqueue_script( 'bp-legacy-js', buddypress()->plugin_url . "bp-templates/bp-legacy/js/buddypress{min}.js", array( 'bp-confirm', 'bp-jquery-cookie', 'bp-jquery-query', 'bp-jquery-scroll-to', 'bp-widget-members', 'jquery' ), bp_get_version(), true );

	wp_deregister_script('bp_core_widget_friends-js');
  wp_enqueue_script( 'bp_core_widget_friends-js', buddypress()->plugin_url . "bp-friends/js/widget-friends{min}.js", array( 'jquery' ), bp_get_version(), true );

}
//add_action( 'wp_enqueue_scripts', 'enqueue_bp_js' );

/* =================================================================*/
/* =              COVER IMAGE SETTINGS
/* =================================================================*/

// Define cover image size
function your_theme_xprofile_cover_image( $settings = array() ) {
    $settings['width']  = 1140;
    $settings['height'] = 350;
    $settings['default_cover'] = 'https://goutu.org/wp-content/themes/foodiepro-2.1.8/images/cover_default.jpg';
 
    return $settings;
}
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'your_theme_xprofile_cover_image', 10, 1 );


/**
 * Your theme callback function
 *
 * <a class="bp-suggestions-mention" href="https://buddypress.org/members/see/" rel="nofollow">@see</a> bp_legacy_theme_cover_image() to discover the one used by BP Legacy
 */
function your_theme_cover_image_callback( $params = array() ) {
    if ( empty( $params ) ) return;
 
    ob_start();?>
    
        /* Cover image */
        #buddypress #header-cover-image {
            height: <?php echo $params['height'];?>px;
            background-image: url(<?php echo $params['cover_image'];?>);
        }
        
        /* Avatar */
        #buddypress #item-header-cover-image #item-header-avatar {
				    margin-top:<?php echo ($params['height']-intval(BP_AVATAR_FULL_HEIGHT+10)/2);?>px;
				    float: left;
				    overflow: visible;
				    width: auto;
				}
				
        /* Name & meta */
				#buddypress div#item-header #item-header-cover-image #item-header-content {
				    clear: both;
				    float: left;
				    margin-left:<?php echo BP_AVATAR_FULL_WIDTH+20;?>px;
				    margin-top:-<?php echo BP_AVATAR_FULL_HEIGHT-10;?>px;
				    width: auto;
				} 
	
	<?php
				
	$css = ob_get_contents();
	echo $css;
	ob_end_clean();
	
	return $css;
}

// Override default css stylesheet
function your_theme_cover_image_css( $settings = array() ) {
    /**
     * If you are using a child theme, use bp-child-css
     * as the theme handel
     */
    $theme_handle = 'bp-child-css';
    $settings['theme_handle'] = $theme_handle;
 
    /**
     * Then you'll probably also need to use your own callback function
     * <a class="bp-suggestions-mention" href="https://buddypress.org/members/see/" rel="nofollow">@see</a> the previous snippet
     */
     $settings['callback'] = 'your_theme_cover_image_callback';
 
    return $settings;
}
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'your_theme_cover_image_css', 10, 1 );
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'your_theme_cover_image_css', 10, 1 );

 
/* =================================================================*/
/* =              OTHER SETTINGS
/* =================================================================*/

/* Removing the links automatically created in a member’s profile */
function remove_xprofile_links() {
    remove_filter( 'bp_get_the_profile_field_value', 'xprofile_filter_link_profile_data', 9, 2 );
}
//add_action( 'bp_init', 'remove_xprofile_links' );



/* Defining custom slugs */
// change 'discuss' to whatever you want
//define( 'BP_FORUMS_SLUG', 'discuss' );


 
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

/* TODO Add Comment here */
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


/* File end */
?>