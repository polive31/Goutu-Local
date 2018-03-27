<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomSocialButtons {

	public function __construct() {	
		//add_action( 'wp_loaded', array($this,'hydrate'));		
	}
	
	public function get_sharing_buttons($url, $size, $networks) {
		global $post;
		$html = '';
		$onClick = 'onclick="javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=250,width=600\');return false;"';

		// Get current page title
		$title = do_shortcode('[seo-friendly-title]');
			
		// Get Post Thumbnail for pinterest
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		// Construct sharing URL without using any script
		$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$url;
		$twitterURL = 'https://twitter.com/intent/tweet/?text='.$title.'&amp;url='.$url; //.'&amp;via=';
		$googleURL = 'https://plus.google.com/share?url='.$url;
		$mailtoURL = 'mailto:contact@domaine?subject=Rejoins Goûtu.org !&body=Bonjour';
		$whatsappURL = 'whatsapp://send?text='.$title . ' ' . $url;
		$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$url.'&amp;media='.$thumbnail[0].'&amp;description='.$title;
		$bufferURL = 'https://bufferapp.com/add?url='.$url.'&amp;text='.$title;
		$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&amp;title='.$title;

		// Add sharing button at the end of page/page content
		$html .= '<ul class="cssb share-icons">';
		if ($networks['facebook'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="facebook"><a ' . $onClick . ' class="cssb-link cssb-facebook" href="'.$facebookURL.'" target="_blank" title="Facebook">&nbsp;</a></li>';
		if ($networks['twitter'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="twitter"><a ' . $onClick . ' class="cssb-link cssb-twitter" href="'. $twitterURL .'" target="_blank" title="Twitter">&nbsp;</a></li>';
		if ($networks['googleplus'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="googleplus"><a ' . $onClick . ' class="cssb-link cssb-googleplus" href="' . $googleURL . '" target="_blank" title="Google+">&nbsp;</a></li>';
		if ($networks['mailto'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="mailto"><a ' . $onClick . ' class="cssb-link cssb-mailto" href="'.$mailtoURL.'" data-pin-custom="true" target="_blank" title="Mail To">&nbsp;</a></li>';
		if ($networks['whatsapp'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="whatsapp"><a ' . $onClick . ' class="cssb-link cssb-whatsapp" href="'.$whatsappURL.'" target="_blank" title="WhatsApp">&nbsp;</a></li>';
		if ($networks['pinterest'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="pinterest"><a ' . $onClick . ' class="cssb-link cssb-pinterest" href="'.$pinterestURL.'" data-pin-custom="true" target="_blank" title="Pin It">&nbsp;</a></li>';
		if ($networks['linkedin'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="linkedin"><a ' . $onClick . ' class="cssb-link cssb-linkedin" href="'.$linkedInURL.'" target="_blank" title="LinkedIn">&nbsp;</a></li>';
		if ($networks['buffer'])
			$html .= '<li class="cssb share-icons size-' . $size . '" id="buffer"><a ' . $onClick . ' class="cssb-link cssb-buffer" href="'.$bufferURL.'" target="_blank" title="Buffer">&nbsp;</a></li>';
		$html .= '</ul>';
		
		return $html;

	}
	


}
