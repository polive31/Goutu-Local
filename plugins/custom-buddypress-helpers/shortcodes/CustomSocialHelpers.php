<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
class CustomSocialHelpers {

	private static $permalinks; 

	public function __construct() {
		add_action('init', array( $this , 'hydrate' ));

		add_shortcode('menu-entry', array($this, 'display_menu_entry'));
		add_shortcode('bp-author', array($this,'bp_author_shortcode'));	
		add_shortcode('if', array($this,'bp_conditional_text'));	
		add_shortcode('social-button', array($this,'bp_social_button'));	
		add_shortcode('bp-displayed', array($this,'bp_displayed_user_shortcode'));
		add_shortcode('bp_user_avatar', array($this,'bp_user_avatar_shortcode'));
		add_shortcode('bp_user_name', array($this,'bp_user_name_shortcode'));
		// PC screen menu icons
		add_shortcode('bp-unread-notes-count', array($this,'logged_in_unread_notification_count_shortcode'));
		add_shortcode('bp-pending-friendship-count', array($this,'pending_friendship_count_shortcode'));
		// Mobile phone menu icon
		add_shortcode('toggle-icon', array($this,'display_toggle_icon'));
	}

	public function hydrate() {
		self::$permalinks = array(
			'friendship_requests' => array(
				'url' 		=> 'friends/requests/',
				'headline' 	=> __('Friendship Requests', 'foodiepro'),
			),
			'notifications' => array(
				'url' 		=> 'notifications/',
				'headline' 	=> __('Notifications', 'foodiepro'),
			),
			'edit_profile' => array(
				'url' 		=> 'profile/edit/group/1/',
				'headline' 	=> __('My Profile', 'foodiepro'),
				'class'		=> 'fa-user-circle'
			),
			'events' => array(
				'url' 		=> 'events',
				'headline' 	=> __('My Events', 'foodiepro'),
			)									
		);

	}

	public static function url( $page, $user_id=false ) {
		if ( !is_user_logged_in() ) return wp_login_url();
		$user_id = $user_id?$user_id:bp_loggedin_user_id(); 
		$url = bp_core_get_user_domain( $user_id );
		$url .= isset(self::$permalinks[$page]['url'])?self::$permalinks[$page]['url']:'';
		return $url;	
	}

	public static function headline( $page ) {		
		$headline = isset(self::$permalinks[$page]['headline'])?self::$permalinks[$page]['headline']:'';	
		return $headline;	
	}	

	public static function class( $page ) {
		$class = isset(self::$permalinks[$page]['class'])?self::$permalinks[$page]['class']:'';	
		return $class;	
	}		

	public static function count( $page ) {
		if ( !is_user_logged_in() ) return false;
		$user_id = bp_loggedin_user_id(); 

		$count=0;
		if ( $page == 'friends')
			$count = bp_friend_get_total_requests_count( $user_id );
		elseif ( $page == 'notifications')
			$count = bp_notifications_get_unread_notification_count( $user_id );
		
		$count=(empty($count) || $count==0)?false:$count;
		return $count;	
	}	


	/* =================================================================*/
	/* =                   Display BP Menu Entry   
	/* =================================================================*/

	public function display_menu_entry($atts) {
			$a = shortcode_atts( array(
			'html' => 'true',
	  		'item' => 'friendship_requests', // id, pseudo, name
		), $atts );

		if ( !is_user_logged_in() ) return;

		$item = $a['item'];		

		$count = self::count( $item );
		$url = self::url( $item );
		$headline = self::headline( $item );

		/* Render output */
		if ($a['html']=='true') { // return whole html link markup
			$count= $count?" ($count)":'';
			$link = '<a class="' . $class . 'mega-menu-link" href="' . $url . '">' . $headline . $count . '</a>';
		}
		else //only return url
			$link = $url;
	
		return $link;
		
	}

		
	/* =================================================================*/
	/* =                   BP Author shortcode 
	/* =================================================================*/	
	public function bp_author_shortcode( $atts ) {
		$a = shortcode_atts( array(
    	'profile' => 'true', // adds link towards profile page
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
		
	/* =================================================================*/
	/* =           Conditional Text shortcode
	/* =================================================================*/

	public function bp_conditional_text( $atts, $content=null ) {
		$a = shortcode_atts( array(
	  	'tag' => 'p', // div, p, ...
	  	'user' => 'logged-in', // logged-in, logged-out, profile
		), $atts );
		
		$tag=esc_html($a['tag']);
		$open='<' . $tag. '>';
		$close='</' . $tag. '>';
		$content=esc_html($content);
		
		$html='';
		$user=($a['user']);
		
		if ((($user=="logged-in") && (is_user_logged_in())) || (($user=="logged-out") && (!is_user_logged_in()))) {
			$html=$open . $content . $close;			
		}
		$html=do_shortcode($html);
		return $html;
	}



	/* =================================================================*/
	/* =                   Social Buttons shortcode
	/* =================================================================*/

	public function bp_social_button( $atts ) {
		$a = shortcode_atts( array(
    	'text' => '', // backup text
    	'type' => 'like', // like, unlike, comment, tete
		), $atts );
		
		switch ($a['type']) {
			case "like":
				$html='<a href="' . bp_get_activity_favorite_link() . '" class="social-button fav bp-secondary-action" title="' . __( 'I like that', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-thumbs-o-up"></i>';
				// $html.='<i class="fi fi-glove fi-off"></i>';
				$html.='</a>';
				break;
			case "unlike":
				$html='<a href="' . bp_get_activity_unfavorite_link() . '" class="social-button unfav bp-secondary-action" title="' . __( 'I don\'t like that anymore', 'foodiepro' ) . '">';
				// $html.='<span class="fa fa-stack">';
				// $html.='<i class="fa fa-thumbs-up fa-stack-1x"></i>';
				// $html.='<i class="fa fa-thumbs-o-up fa-stack-1x"></i>';
				$html.='<i class="fa fa-thumbs-up"></i>';
				// $html.='</span>';		
				// $html.='<i class="fi fi-glove fi-on"></i>';
				$html.='</a>';			
				break;
			case "comment":
				$html='<a href="' . bp_get_activity_comment_link() . '" class="social-button acomment-reply bp-primary-action" id="acomment-comment-' . bp_get_activity_id() . '" title="' . __( 'Comment', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-commenting-o"></i><span>' . bp_activity_get_comment_count() . '</span>';
				$html.='</a>';			
				break;
			case "reply":
				$html='<a href="#acomment-' . bp_get_activity_comment_id() . '" class="acomment-reply bp-primary-action social-button" id="acomment-reply-' . bp_get_activity_id() . '-from-' . bp_get_activity_comment_id() . '" title="' . __( 'Reply', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-reply"></i>';
				$html.='</a>';			
				break;	
			case "conversation":
				$html='<a href="' . bp_get_activity_thread_permalink() . '" class="social-button view bp-secondary-action" title="' . esc_attr__( 'Show conversation', 'foodiepro' ) . '">';
				$html.='<i class="fa fa-comments-o"></i>';
				$html.='</a>';			
				break;								
			case "delete":
				$html=$this->custom_get_activity_delete_link();
				break;
			case "delete-comment":
				//$delete_icon = '<i class="fa fa-trash-o"></i>'; 
				$delete_icon = '<i class="fa fa-times"></i>'; 
				$html='<a href="' . bp_get_activity_comment_delete_link() . '" class="social-button delete acomment-delete confirm bp-secondary-action" rel="nofollow" title="' . __( 'Delete', 'foodiepro' ) . '">' . $delete_icon . '</a>';
				break;
		} 
		return $html; 
	}



	/* =================================================================*/
	/* =                   Activity Delete Link
	/* =================================================================*/
		
	public function custom_get_activity_delete_link() {

		$url   = bp_get_activity_delete_url();
		$class = 'delete-activity';
		//$delete_icon = '<i class="fa fa-trash-o"></i>';
		$delete_icon = '<i class="fa fa-times"></i>';

		// Determine if we're on a single activity page, and customize accordingly.
		if ( bp_is_activity_component() && is_numeric( bp_current_action() ) ) {
			$class = 'delete-activity-single';
		}

		$link = '<a href="' . esc_url( $url ) . '" class="social-button item-button bp-secondary-action ' . $class . ' confirm" rel="nofollow" title="' . __( 'Delete', 'foodiepro' ) . '">' . $delete_icon . '</a>';

		return apply_filters( 'bp_get_activity_delete_link', $link );
	}	
		
		
		
	/* =================================================================*/
	/* =                   BP Displayed User shortcode 
	/* =================================================================*/

	public function bp_displayed_user_shortcode( $atts ) {
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


	/* =================================================================*/
	/* =                   BP User Avatar Shortcode 
	/* =================================================================*/
	public function bp_user_avatar_shortcode( $atts ) {
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

	/* =================================================================*/
	/* =               BP Loggued-in name shortcode
	/* =================================================================*/

	public function bp_user_name_shortcode( $atts ) {
		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'type' => 'first',// pseudo or first
    	), $atts );
	    
		$user_id = bp_loggedin_user_id();
	    if ($a['type']=="pseudo")
			$name=bp_core_get_username($user_id);
		else 
			$name=bp_core_get_user_displayname($user_id);
		return $name;
	}

	/* =================================================================*/
	/* =                      PC SCREEN MENU ICONS                        
	/* =================================================================*/
	public function logged_in_unread_notification_count_shortcode() {
		if ( !is_user_logged_in() ) return;
		$count = self::count('notifications');
		$count = $count?'<span class="bp_counter" id="notifications">' . $count . '</span>':'';
		return $count;
	}

	public function pending_friendship_count_shortcode() {
		if ( is_user_logged_in() ) return;
		$count = self::count('friendship_requests');
		$count = $count?'<span class="bp_counter" id="friends">' . $count . '</span>':'';
		return $count;
	}


	/* =================================================================*/
	/* =               SMARTPHONE (TOGGLE) MENU ICONS                        
	/* =================================================================*/
	public function display_toggle_icon($atts) {
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
					$count = self::count('friendship_requests');
					$count = $count?'<span class="bp_counter" id="friendships">' . $count . '</span>':'';
					$html='<a class="fa-friendships toggle-menu" href="' . self::url( 'friendship_requests' ) . '">' . $count . '</a>';
					/* Variant : only display latest friendship requests */
					//$html='<a class="fa-friendships toggle-menu" href="' . bp_core_get_user_domain( $user_id ) . '/friends/requests/?new">' . $count . '</a>';
				}
				break;		
			case "notifications":
				if ( is_user_logged_in() ) {
					$count = self::count('notifications');
					$count = $count?'<span class="bp_counter" id="notifications">' . $count . '</span>':'';
					$html='<a class="fa-notifications toggle-menu" href="' . self::url( 'notifications' ) . '">' . $count . '</a>';
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
					$html='<a class="fa-events toggle-menu" href="' . self::url( 'events' ) . '">' . $count . '</a>';
				}
				break;
		}
		return $html;
	}


}