<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =                   Display Menu Entry   
/* =================================================================*/

function display_menu_entry($atts) {
		$a = shortcode_atts( array(
  	'item' => 'friendship_requests', // id, pseudo, name
	), $atts );

	if ( is_user_logged_in() ) {
	
		$user_id = bp_loggedin_user_id(); 
		$url = bp_core_get_user_domain( $user_id );
		
		switch ($a['item']) {
			
			case "friendship_requests":
				$count = bp_friend_get_total_requests_count( $user_id );
				$url .= '/friends/requests/';
				$text = __('Friendship Requests', 'foodiepro');
				break;	 		
				
			case "notifications":				
				$count = bp_notifications_get_unread_notification_count( $user_id );
				$url .= '/notifications/';
				$text = __('Notifications', 'foodiepro');
				break;	
				
			case "profile_edit":	
				$count = '';			
				$url .= '/profile/edit/group/1/';
				$text = __('My Profile', 'foodiepro');
				break;
				
			} /* End switch */
				
			/* Render HTML output */
			if ($count=="0"||$count=="") $count=NULL;
			else $count = ' (' . $count . ')';
			
			$html = '<a class="mega-menu-link" href="' . $url . '">' . $text . $count . '</a>';
	
	} /* End if loggued-in */

	return $html;
	
} /* End funtion */
	
add_shortcode('menu-entry', 'display_menu_entry');
	
	
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
add_shortcode('bp_displayed', 'bp_displayed_user_shortcode');

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

function display_toggle_icon($atts) {
		$a = shortcode_atts( array(
  			'item' => 'profile', // id, pseudo, name
				), $atts );
	
		$user_id = bp_loggedin_user_id(); 
		
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
add_shortcode('toggle-icon', 'display_toggle_icon');



?>