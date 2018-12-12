<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
class CustomPeepsoShortcodes {

	public function __construct() {
		// add_shortcode('bp-user-avatar', array($this,'bp_user_avatar_shortcode'));
		add_shortcode('peepso-user-field', array($this,'get_user_field'));
		add_shortcode('peepso-user-icon', array($this,'get_user_icon'));
		add_shortcode('peepso-page-url', array($this,'get_page_url'));
	}

	public function get_user_field( $atts ) {
		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'user-id' => 'current',// 
        	'field' => 'nicename',// pseudo, avatar, cover
			'size' => 'full',// full
			'nav' => '', // profile, settings, activity, friends
			'navclass' => '',
		), $atts );
		

		$user_id = ($a['user-id']=='current')?get_current_user_id():$a['user-id'];
		$user = PeepsoUser::get_instance( $user_id );

		// echo var_dump($user);
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
			case "avatar" :
				$field=$user->get_avatar( $a['size'] );
				$field = '<img class="avatar" title="' . __('Edit my profile','foodiepro') . '" src="' . $field . '">';
				break;				
		}

		if ( !empty($a['link']) ) {
			$field = '<a class="' . $a['linkclass'] . '" href="' . Peepso::get_page(  $a['link'] ) . '">' . $field . '</a>';
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

        <div class="ps-widget--userbar__notifications">
		
		<span class="dropdown-notification ps-js-notifications">
		<a href="#" title="Notifications en cours">
		<div class="ps-bubble__wrapper">
		<i class="ps-icon-globe"></i>
		<span class="js-counter ps-bubble ps-bubble--widget ps-js-counter"  style="display:none">
		</span>
		</div>
		</a>
		</span>
        
        </div>
		
		<?php 
		$html = ob_get_contents();
		ob_clean();

		return $html;
	}

	public function get_page_url( $atts ) {

		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'type' => 'profile',// activity, members, account
		), $atts );
	
		return PeepSo::get_page( $a['type'] );

	}

}

new CustomPeepsoShortcodes();