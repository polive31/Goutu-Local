<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class CustomSocialButtonsShortcodes extends CustomSocialButtons {
	
	public function __construct() {
		parent::__construct();
		add_shortcode('social-sharing-buttons', array($this,'display_social_sharing_buttons')); 	
	}
	

	/* Outputs HTML of Social Sharing Buttons
	------------------------------------------------------*/

	public function display_social_sharing_buttons($atts) {
		$atts = shortcode_atts(array(
			'target' => 'site', //site : site url, recipe, post
			'class' => 'medium',
			'facebook' => 'true', 
			'twitter' => 'true', 
			'mailto' => 'true', 
			'pinterest' => 'true', 
			'whatsapp' => 'true', 
			'linkedin' => 'false', 
			'buffer' => 'false',
			'googleplus' => 'false', 
	   	),$atts);
	   
	  
		foreach (self::$networks as $id) {
			$supported_networks[$id]=($atts[$id]==='true');
			//echo '$atts for ' . $id . '=' . $atts[$id] . '<br>';
		}  

		$html=$this->get_sharing_buttons($atts['target'], $atts['class'], $supported_networks);
		
    return $html;
	}

}



