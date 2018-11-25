<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomSocialButtons {

	protected static $onClick = 'onclick="javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=250,width=600\');return false;"';

	protected static $networks = array(
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

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	

	const LINEBREAK = '%0D%0A%0D%0A';
	
	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('wp_enqueue_scripts', array($this, 'enqueue_social_buttons_css'));
	}	

	public function enqueue_social_buttons_css() {
  		$uri = self::$PLUGIN_URI . '/assets/css/';
  		$path = self::$PLUGIN_PATH . '/assets/css/';
		custom_enqueue_style( 'social-buttons', $uri, $path, 'social_sharing_buttons.css', array(), CHILD_THEME_VERSION );	
	} 	
	
	public function get_sharing_buttons($target, $class, $networks) {
		global $post;
		$html = '';

		// URL
		if ($target=='site') 
			$url=get_site_url(null,'','https');
		else
			$url=get_permalink();
		$url=esc_html($url);

		// SEO Friendly current page title
		$title = do_shortcode('[seo-friendly-title]');
			
		// Get Post Thumbnail for pinterest
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

		// Construct sharing URL without using any script
		
		$bufferURL = 'https://bufferapp.com/add?url='.$url.'&amp;text='.$title;
		$linkedInURL = 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&amp;title='.$title;

		// Add sharing button at the end of page/page content
		$html .= '<ul class="cssb share-icons">';

		if ($networks['facebook']) 
			$html = self::getFacebookButton($url, $class);
			
		if ($networks['twitter'])
			$html .= self::getTwitterButton($url, $title, $class);

		if ($networks['mailto'])
			$html .= self::getMailButton($target, $class);

		if ($networks['pinterest'])
			$html .= self::getPinterestButton( $url, $title, $thumbnail, $class);

		if ($networks['whatsapp'])
			$html .= self::getWhatsappButton($url, $title, $class); 

		if ($networks['linkedin'])
			$html .= '<li class="cssb share-icons ' . $class . '" id="linkedin"><a ' . self::$onClick . ' class="cssb-link cssb-linkedin" href="'.$linkedInURL.'" target="_blank" title="LinkedIn">&nbsp;</a></li>';

		if ($networks['buffer'])
			$html .= '<li class="cssb share-icons ' . $class . '" id="buffer"><a ' . self::$onClick . ' class="cssb-link cssb-buffer" href="'.$bufferURL.'" target="_blank" title="Buffer">&nbsp;</a></li>';

		$html .= '</ul>';
		
		return $html;

	}

	// Facebook URL
	public static function getFacebookURL( $url ) {
		return 'https://www.facebook.com/sharer/sharer.php?u='.$url;
	}

	public static function facebookURL( $post ) {
		return self::getFacebookURL( get_permalink($post) );
	}

	public static function getFacebookButton( $url, $class ) {
		// return '<li class="cssb share-icons ' . $class . '" id="facebook"><a ' . self::$onClick . ' class="cssb-link cssb-facebook" href="'. self::getFacebookURL($url) . '" target="_blank" title="' . __('Share on Facebook','foodiepro') . '">&nbsp;</a></li>';
		return '<li class="cssb share-icons ' . $class . '" id="facebook"><a ' . self::$onClick . ' class="cssb-link cssb-facebook" href="'. self::getFacebookURL($url) . '" target="_blank" title="' . __('Share on Facebook','foodiepro') . '"> </a></li>';
	}	

	// Twitter URL
	public static function getTwitterURL( $url, $title ) {
		return 'https://twitter.com/intent/tweet/?text='.$title.'&amp;url='.$url; //.'&amp;via=';
	}

	public static function twitterURL( $post ) {
		return self::getTwitterURL( get_permalink($post), $post->post_title );
	}	
	
	public static function getTwitterButton( $url, $title, $class ) {
		// return '<li class="cssb share-icons ' . $class . '" id="twitter"><a ' . self::$onClick . ' class="cssb-link cssb-twitter" href="'. self::getTwitterURL($url,$title) .'" target="_blank" title="' . __('Share on Twitter','foodiepro') . '">&nbsp;</a></li>';	
		return '<li class="cssb share-icons ' . $class . '" id="twitter"><a ' . self::$onClick . ' class="cssb-link cssb-twitter" href="'. self::getTwitterURL($url,$title) .'" target="_blank" title="' . __('Share on Twitter','foodiepro') . '"> </a></li>';	
	}

	// Pinterest URL
	public static function getPinterestURL( $url, $title, $thumb ) {
		return 'https://pinterest.com/pin/create/button/?url='.$url.'&amp;media='. $thumb[0] .'&amp;description='. $title;
	}

	public static function pinterestURL( $post ) {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'square-thumbnail' );
		return self::getPinterestURL( get_permalink($post), $post->post_title, $thumb );
	}	

	public function getPinterestButton($url, $title, $thumb, $class ) {
		return '<li class="cssb share-icons ' . $class . '" id="pinterest"><a ' . self::$onClick . ' class="cssb-link cssb-pinterest" href="' . self::getPinterestURL($url,$title,$thumb) . '" data-pin-custom="true" target="_blank" title="' . __('Pin It','foodiepro') . '"> </a></li>';
	}

	// Mail URL
	public static function getPostMailURL( $post, $target ) {
		$email = get_the_author_meta('user_email', $post->post_author);
		$name = ucfirst(get_the_author_meta('display_name', $post->post_author));

		$from = ($target=='recipe')?'Une recette de':'Un article de';
		$posttype = ($target=='recipe')?'cette recette':'cet article';
		$it = ($target=='recipe')?'la':'le';
		$subject = $post->post_title . " - $from " . $name;

		$mailURL = $author . '?subject=' . $subject . '&body=Bonjour,' . self::LINEBREAK . 'J\'ai publié ' . $posttype . ' sur Goûtu.org et voudrais ' . $it . ' partager avec toi : ' . $post->post_title .  ' (' . get_permalink($post) . ').' . self::LINEBREAK . 'A bientôt !' . self::LINEBREAK . $name . ', blogueur sur Goûtu.org';
		return $mailURL;
	}

	public static function getSiteMailURL() {
		$subject = 'Rejoins Goûtu.org !';
		$body = 'Bonjour,' . self::LINEBREAK . 'Je te propose de découvrir Goûtu.org (' . get_site_url(null,'','https') . '), un site de partage autour des thèmes de la Cuisine et de l\'Alimentation.' . self::LINEBREAK . 'Tu pourras y découvrir des idéees de recettes, trouver des informations sur les différents ingrédients, et apprendre de nouvelles techniques et tours de main.' . self::LINEBREAK . 'Mais Goûtu te permet également de classer tes recettes préférées dans ton carnet personnel, et de publier tes propres recettes et articles. Tu peux ainsi créer un véritable blog culinaire en toute simplicité, et partager ton actualité et tes publications avec le plus grand nombre. Rejoins-nous, l\'inscription est rapide et gratuite.' . self::LINEBREAK . 'A bientôt sur la communauté des Gourmets !' . self::LINEBREAK;
		$body .= 'L\'équipe Goûtu.org';

		return 'mailto:remplacez@cetteadresse?subject=' . $subject . '&body=' . $body;
		// return 'mailto:someone@example.com?Subject=Hello%20again';
	}

	public static function getMailButton( $target, $class ) {
		if ($target == 'recipe' || $target == 'post') {
			global $post;
			$url = 'mailto:remplacez@cetteadresse' . self::getPostMailURL($post, $target);
		}
		else 
			$url = self::getSiteMailURL($post);

		return '<li class="cssb share-icons ' . $class . '" id="mailto"><a ' . self::$onClick . ' class="cssb-link cssb-mailto" href="'. $url . '" data-pin-custom="true" target="_blank" title="' . __('Send an email','foodiepro') . '"> </a></li>';
	}

	// WhatsApp
	public static function getWhatsappURL( $url, $title ) {
		return 'whatsapp://send?text='.$title . ' ' . $url;
	}

	public static function whatsappURL( $post ) {
		return self::getWhatsappURL( get_permalink($post), $post->post_title );
	}	
	
	public static function getWhatsappButton( $url, $title, $class ) {
		return '<li class="cssb share-icons ' . $class . '" id="whatsapp"><a ' . self::$onClick . ' class="cssb-link cssb-whatsapp" href="'. self::getWhatsappURL($url,$title) .'" target="_blank" title="' . __('Share on Whatsapp','foodiepro') . '"> </a></li>';	
	}	


}
