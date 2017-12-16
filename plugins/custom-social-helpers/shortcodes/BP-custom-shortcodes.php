<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =                   Display BP Menu Entry   
/* =================================================================*/
add_shortcode('menu-entry', 'display_menu_entry');

function display_menu_entry($atts) {
		$a = shortcode_atts( array(
		'html' => 'true',
  	'item' => 'friendship_requests', // id, pseudo, name
	), $atts );
	
	$class='';

	if ( is_user_logged_in() ) {
	
		$user_id = bp_loggedin_user_id(); 
		$url = bp_core_get_user_domain( $user_id );
		
		switch ($a['item']) {
			
			case "friendship_requests":
				$count = bp_friend_get_total_requests_count( $user_id );
				$url .= 'friends/requests/';
				$text = __('Friendship Requests', 'foodiepro');
				break;	 		
				
			case "notifications":				
				$count = bp_notifications_get_unread_notification_count( $user_id );
				$url .= 'notifications/';
				$text = __('Notifications', 'foodiepro');
				break;	
				
			case "profile_edit":	
				$count = '';			
				$url .= 'profile/edit/group/1/';
				$text = __('My Profile', 'foodiepro');
;				$class = 'fa-user-circle';
				break;
				
			} /* End switch */
				
			/* Render output */
			
			if ($a['html']=='true') { // return whole html link markup
				if ($count=="0"||$count=="") $count=NULL;
				else $count = ' (' . $count . ')';
				$link = '<a class="' . $class . ' mega-menu-link" href="' . $url . '">' . $text . $count . '</a>';
			}
			else //only return url
				$link = $url;
	
	} /* End if loggued-in */

	return $link;
	
} /* End funtion */
	
/* =================================================================*/
/* =                   BP Author shortcode 
/* =================================================================*/

function bp_author_shortcode( $atts ) {
		$a = shortcode_atts( array(
    	'type' => 'pseudo', // id, pseudo, name
		), $atts );
		
		$user_id=get_the_author_meta('ID');
		
		switch ($a['type']) {
			case "id":
				$name=$user_id;
				break;
			case "pseudo":
				$name=bp_core_get_username($user_id);
				break;
			case "name":
				$name=bp_core_get_user_displayname($user_id);
				break;
		} 
		return $name; 
}
add_shortcode('bp-author', 'bp_author_shortcode');	

	
/* =================================================================*/
/* =                   Social Buttons shortcode
/* =================================================================*/

function bp_social_button( $atts ) {
		$a = shortcode_atts( array(
    	'text' => '', // backup text
    	'type' => 'like', // like, unlike, comment, delete
		), $atts );
		
		switch ($a['type']) {
			case "like":
				$html='<a href="' . bp_get_activity_favorite_link() . '" class="social-button fav bp-secondary-action" title="' . esc_attr( 'I like that', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-thumbs-up"></i>';
				$html.='</a>';
				break;
			case "unlike":
				$html='<a href="' . bp_get_activity_unfavorite_link() . '" class="social-button unfav bp-secondary-action" title="' . esc_attr( 'I don\'t like that anymore', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-thumbs-down"></i>';
				$html.='</a>';			
				break;
			case "comment":
				$html='<a href="' . bp_get_activity_comment_link() . '" class="social-button acomment-reply bp-primary-action" id="acomment-comment-' . bp_get_activity_id() . '" title="' . esc_attr( 'Comment', 'foodiepro' ) . '">';
				//$html='<a href="' . bp_get_activity_comment_link() . '" class="social-button acomment-reply bp-primary-action" id="acomment-comment-' . bp_get_activity_id() . '" title="' . esc_attr( 'Comment', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-commenting-o"></i><span>' . bp_activity_get_comment_count() . '</span>';
				$html.='</a>';			
				break;
			case "delete":
				$html=custom_get_activity_delete_link();
				break;
		} 
		return $html; 
}
add_shortcode('social-button', 'bp_social_button');	

	

/**
 * Return the activity delete link.
 *
 * @since 1.1.0
 *
 * @global object $activities_template {@link BP_Activity_Template}
 *
 * @return string $link Activity delete link. Contains $redirect_to arg
 *                      if on single activity page.
 */
function custom_get_activity_delete_link() {

	$url   = bp_get_activity_delete_url();
	$class = 'delete-activity';
	$delete_icon = '<i class="fa fa-trash-o"></i>';

	// Determine if we're on a single activity page, and customize accordingly.
	if ( bp_is_activity_component() && is_numeric( bp_current_action() ) ) {
		$class = 'delete-activity-single';
	}

	$link = '<a href="' . esc_url( $url ) . '" class="social-button item-button bp-secondary-action ' . $class . ' confirm" rel="nofollow">' . $delete_icon . '</a>';

	/**
	 * Filters the activity delete link.
	 *
	 * @since 1.1.0
	 *
	 * @param string $link Activity delete HTML link.
	 */
	return apply_filters( 'bp_get_activity_delete_link', $link );
}	
	
	
	
/* =================================================================*/
/* =                   BP Displayed User shortcode 
/* =================================================================*/

function bp_displayed_user_shortcode( $atts ) {
		$a = shortcode_atts( array(
    	'type' => 'pseudo', // id, pseudo, name
		), $atts );
		
		$user_id=bp_displayed_user_id();
		
		switch ($a['type']) {
			case "id":
				$name=$user_id;
				break;
			case "pseudo":
				$name=bp_core_get_username($user_id);
				break;
			case "name":
				$name=bp_core_get_user_displayname($user_id);
				break;
		} 
		return $name; 
}
add_shortcode('bp-displayed', 'bp_displayed_user_shortcode');

/* =================================================================*/
/* =                   BP User Avatar Shortcode 
/* =================================================================*/


function bp_user_avatar_shortcode( $atts ) {
	if ( is_user_logged_in() ) {
		global $current_user;
		$a = shortcode_atts( array(
        'size' => 'full',
    ), $atts );
		$args = array( 
    'item_id' =>  $current_user->ID, 
    'object' => 'user', //default
    'type' => $a['size'] 
	); 
		return bp_core_fetch_avatar( $args );
	}
}
add_shortcode('bp_user_avatar', 'bp_user_avatar_shortcode');


/* =================================================================*/
/* =               BP Loggued-in name shortcode
/* =================================================================*/

function bp_user_name_shortcode( $atts ) {
	if ( is_user_logged_in() ) {
		$user_id = bp_loggedin_user_id();
		 
		$a = shortcode_atts( array(
        'type' => 'first',// pseudo or first
    ), $atts );
    
    if ($a['type']=="pseudo") {
			$name=bp_core_get_username($user_id);
		}
		else {
			$name=bp_core_get_user_displayname($user_id);
		}
		
		return $name;
	}
}
add_shortcode('bp_user_name', 'bp_user_name_shortcode');

/* =================================================================*/
/* =                      PC SCREEN MENU ICONS                        
/* =================================================================*/

function logged_in_unread_notification_count_shortcode() {
	if ( is_user_logged_in() ) {
		$user_id = bp_loggedin_user_id(); 
		$count = bp_notifications_get_unread_notification_count( $user_id );
		if ($count=="0"||$count=="") {
			$count = NULL;}
		else {
			$count = '<span class="bp_counter" id="notifications">' . $count . '</span>';}
		return $count;
	}
}
add_shortcode('bp-unread-notes-count', 'logged_in_unread_notification_count_shortcode');


function pending_friendship_count_shortcode() {
	if ( is_user_logged_in() ) {
		$user_id = bp_loggedin_user_id(); 
		$count = bp_friend_get_total_requests_count( $user_id );
		if ($count=="0"||$count=="") {
			$count = NULL;}
		else {
			$count = '<span class="bp_counter" id="friends">' . $count . '</span>';}
		return $count;
	}
}
add_shortcode('bp-pending-friendship-count', 'pending_friendship_count_shortcode');

/* =================================================================*/
/* =               SMARTPHONE (TOGGLE) MENU ICONS                        
/* =================================================================*/

add_shortcode('toggle-icon', 'display_toggle_icon');

function display_toggle_icon($atts) {
	$a = shortcode_atts( array(
			'item' => 'profile', // id, pseudo, name
			), $atts );

	$user_id = bp_loggedin_user_id(); 
	$html = '';
	switch ($a['item']) {
		case "profile":
			if ( is_user_logged_in() )
				$html = bp_core_get_userlink( $user_id );
			else
				$html='<a class="dashicons-profile toggle-menu" href="' . get_page_link(7013) . '"></a>';
			break;
		case "friends":
			if ( is_user_logged_in() ) {
				$count = bp_friend_get_total_requests_count( $user_id );
				if ($count=="0"||$count=="") 
					$count=NULL;
				else 
					$count = '<span class="bp_counter" id="friendships">' . $count . '</span>';
				$html='<a class="fa-friendships toggle-menu" href="' . bp_core_get_user_domain( $user_id ) . 'friends/requests/">' . $count . '</a>';
				/* Variant : only display latest friendship requests */
				//$html='<a class="fa-friendships toggle-menu" href="' . bp_core_get_user_domain( $user_id ) . '/friends/requests/?new">' . $count . '</a>';
			}
			break;		
		case "notifications":
			if ( is_user_logged_in() ) {
				$count = bp_notifications_get_unread_notification_count( $user_id );
				if ($count=="0"||$count=="")
					$count=NULL;
				else 
					$count = '<span class="bp_counter" id="notifications">' . $count . '</span>';
				$html='<a class="fa-notifications toggle-menu" href="' . bp_core_get_user_domain( $user_id) . 'notifications/">' . $count . '</a>';
			}
			break;		
		case "events":
			if ( is_user_logged_in() ) {
				//$count = EM_Events::count( array( 'scope' => 'future') );
				$count = "";
				if ($count=="0"||$count=="") 
					$count=NULL;
				else 
					$count = '<span class="bp_counter" id="events">' . $count . '</span>';
				$html='<a class="fa-events toggle-menu" href="' . bp_core_get_user_domain( $user_id ) . 'events/">' . $count . '</a>';
			}
			break;
	}
	return $html;
}