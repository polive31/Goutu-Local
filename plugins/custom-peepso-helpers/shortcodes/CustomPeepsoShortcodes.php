<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
class CustomPeepsoShortcodes {

	public function __construct() {
		add_shortcode('peepso-user-avatar', array($this,'get_user_avatar'));
		add_shortcode('peepso-user-field', array($this,'get_user_field'));
		// add_shortcode('peepso-user-icon', array($this,'get_user_icon'));
		add_shortcode('peepso-page-url', array($this,'get_page_url'));
	}

	public function get_peepso_user( $user_type ) {
		$user_id=false;
		switch ( $user_type ) {
			case 'current':
				$user_id = get_current_user_id();
				break;
			case 'view':
				$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
				break;
			case 'author':
				if ( is_single() )
					$user_id = get_the_author_meta('ID');
				break;				
			default :
				$user_id = $a['user'];
				break;
		}
		$user = $user_id?PeepsoUser::get_instance( $user_id ):false;

		return $user;
	}

	public function get_user_avatar( $atts ) {
		$a = shortcode_atts( array(
	        'user' => 'current', // 'view', 'author', or ID
	        'size' => '', //'full',
	        'link' => 'profile',
	        'wrap' => '',
	        'wrapclass' => '',
		), $atts );
		
		$user = $this->get_peepso_user( $a['user'] );
		$html = '<img class="avatar user-' . $user->get_id() . '-avatar" src="' . $user->get_avatar( $a['size'] ) . '" alt="' . sprintf( __('Picture of %s','foodiepro') , ucfirst($user->get_username()) ) . '">';

		if ( !empty($a['link']) ) {
			$html = '<a href="' . $user->get_profileurl() . '">' . $html . '</a>';
		}

		if ( !empty($a['wrap']) ) {
			$html = '<' . $a['wrap'] . ' class="' . $a['wrapclass'] . '">' . $html . '</' . $a['wrap'] . '>';
		}

		return $html;
	}

	public function get_user_field( $atts ) {
		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'user' => 'current',// view, author, ID... 
        	'field' => 'nicename',// pseudo, avatar, cover
			'link' => 'profile', // 
			'class' => '', // 
		), $atts );

		$user = $this->get_peepso_user( $a['user'] );

		$field = '';
		switch ($a['field']) {
			case "pseudo" :
				$field=$user->get_username();
				break;
			case "firstname" : 
				$field=$user->get_firstname();
				break;
			case "lastname" : 
				$field=$user->get_lastname();
				break;				
			case "nicename" : 
				$field=$user->get_nicename();
				break;				
			case "fullname" :
				$field=$user->get_fullname();
				break;						
		}

		if ( !empty($a['link']) ) {
			$field = '<a class="' . $a['class'] . '" href="' . $user->get_profileurl() . '">' . $field . '</a>';
		}	

		return $field;    	
	}

	public function get_user_icon( $atts ) {
		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'icon' => 'notifications',// friends, badges, messages
		), $atts );
		

		$field = '';

		switch ($a['icon']) {
			case "notifications" :
				$field=$this->get_notifications();
				break;							
		}

		return $field;    	
	}	

	public function get_notifications() {

		ob_start();
		?>

        <!-- <div class="ps-widget--userbar__notifications"> -->
		
		<span class="dropdown-notification ps-js-notifications">
			<a href="#" class="" title="Notifications en cours">
				<div class="ps-bubble__wrapper">
					<i class="fa fa-bell"></i>
					<span class="js-counter ps-bubble ps-bubble--widget ps-js-counter" style="display:none"></span>
				</div>
			</a>
		</span>
        
        <!-- </div> -->
		
		<?php 
		$html = ob_get_contents();
		ob_clean();

		return $html;
	}

	public function get_page_url( $atts, $content ) {

		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
			'user' => 'current', // 'view', id of user
			'type' => 'profile',// activity, members, account
			'link' => 'no', //
			'class' => '', //
			'id' => '', //
			'text' => '' //
		), $atts );
	
		switch ( $a['user'] ) {
			case 'current':
				$out = PeepSo::get_page( $a['type'] );
				break;
			case 'view':
				break;						
			default:
				break;
		}

		if ( $a['link']=='yes' ) {
			$text = strip_tags( $a['text'] . $content);
			$out = '<a class="" id="" href="' . $out . '">' . $text . '</a>';
		}

		return $out;

	}

}

new CustomPeepsoShortcodes();