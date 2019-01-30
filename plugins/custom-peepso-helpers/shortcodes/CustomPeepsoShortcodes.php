<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
class CustomPeepsoShortcodes {

	public function __construct() {
		add_shortcode('peepso-user-avatar', array($this,'get_user_avatar_shortcode'));
		add_shortcode('peepso-user-field', array($this,'get_user_field'));
		// add_shortcode('peepso-user-icon', array($this,'get_user_icon'));
		add_shortcode('peepso-page-url', array($this,'get_page_url'));
	}


	public function get_user_avatar_shortcode( $atts ) {
		if ( !class_exists('PeepsoHelpers') ) return '';
		
		$atts = shortcode_atts( array(
			'user' => 'current', // 'view', 'author', or ID
	        'size' => '', //'full',
	        'link' => 'profile',
	        'aclass' => '', // 
	        'wraptag' => '', // 'div', 'span'...
	        'wrapclass' => '', //
		), $atts );
		
		$html = PeepsoHelpers::get_avatar( $atts );
		return $html;
	}
	
	
	public function get_user_field( $a ) {
		if ( !class_exists('PeepsoHelpers') ) return '';
		if ( !is_user_logged_in() ) return;
		 
		$a = shortcode_atts( array(
        	'user' => 'current',// view, author, ID... 
        	'field' => 'nicename',// pseudo, avatar, cover
			'link' => 'profile', // 
			'class' => '', // 
		), $a );

		$user = PeepsoHelpers::get_user( $a['user'] );

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

}

new CustomPeepsoShortcodes();