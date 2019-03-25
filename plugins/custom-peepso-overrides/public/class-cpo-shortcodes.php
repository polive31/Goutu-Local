<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
class CPO_Shortcodes {

	public function get_user_avatar_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'user' => 'current', // 'view', 'author', or ID
	        'size' => '', //'full',
	        'page' => 'profile',
	        'aclass' => '', // 
	        'wraptag' => '', // 'div', 'span'...
	        'wrapclass' => '', //
		), $atts );
		
		$html = PeepsoHelpers::get_avatar( $atts );
		return $html;
	}
	
	public function get_user_field_shortcode( $a, $content ) {	
		$a = shortcode_atts( array(
			'user' 		=> 'current',	// view, author, ID... 
        	'field' 	=> 'nicename',	// pseudo, avatar, cover
			'page' 		=> 'profile', 	// 
			'subpage' 	=> '', 			// about
			'class' => '', 				// 
		), $a );

		extract($a);
		$content = empty($content)?'%s':esc_html($content);
		
		$user = PeepsoHelpers::get_user( $user );
		$html = PeepsoHelpers::get_field( $user, $field );

		if ( !empty($page) ) {
			$html = '<a class="' . $class . '" href="' . PeepsoHelpers::get_url( $user, $page, $subpage ) . '">' . sprintf( $content, $html) . '</a>';
		}	

		return $html;    	
	}

}