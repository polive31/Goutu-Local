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
			'url' => 'site', //site : site url, current : current page url
			'size' => 'medium',
			'facebook' => 'true', 
			'twitter' => 'true', 
			'googleplus' => 'true', 
			'mailto' => 'true', 
			'whatsapp' => 'false', 
			'linkedin' => 'false', 
			'pinterest' => 'false', 
			'buffer' => 'false',
	   ),$atts);
	    
	  $networks=array(
			'facebook', 
			'twitter',
			'googleplus',
			'whatsapp',
			'mailto',
			'whatsapp',
			'linkedin',
			'pinterest',
			'buffer'
	  );
	  
	  foreach ($networks as $id) {
	  	$supported_networks[$id]=($atts[$id]==='true');
	  	//echo '$atts for ' . $id . '=' . $atts[$id] . '<br>';
	  }  
	
		if ($atts['url']=='site') $url=get_permalink();
		elseif ($atts['url']=='current') $url=get_permalink();
		
		$html=$this->get_sharing_buttons($url, $atts['size'],$supported_networks);
		
    return $html;
	}



}



