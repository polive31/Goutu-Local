<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomSiteNotifications {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	private $headers=array();

	const CONTACT = "contact@goutu.org";
	const PROVIDER = 'mailchimp';

	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('init',array($this,'hydrate'));

		// Notification types 
		add_action(  'pending_to_publish',  array($this, 'published_post_notification'), 10, 1 );

		// WP Mail customization 
		add_filter ( 'wp_mail_content_type', array($this, 'html_mail_content_type'));
		// add_filter ( 'wp_mail_from', array($this, 'custom_from_name'));
		// add_filter ( 'wp_mail_from_name', array($this, 'custom_from_address'));
	}

	// public function custom_from_name() {
	//     return get_bloginfo('name');
	// }

	// public function custom_from_address() {
	//     return self::CONTACT;
	// }

	public function hydrate() {
		$this->headers[] = 'From: ' . get_bloginfo('name') . ' <' . self::CONTACT . '>';
	}

	public function bcc_admin() {
		$this->headers[] = 'Bcc: ' . get_bloginfo('admin_email');
	}

	public function headers() {
		return $this->headers;
	}

	public function html_mail_content_type() {
	    return "text/html";
	}

	public function hello() {
		return __('Hello %s,','foodiepro');
	}

	public function signature() {
		$signature =  __('The <a href="%s">Goûtu.org</a> Team.','foodiepro');
		$signature = sprintf($signature,get_bloginfo('url'));
		return wpautop($signature);
	}

	public function contact() {
		$contact =  __('Any problem or question ? Contact us <a href="%s">here</a>','foodiepro');
		$contact = sprintf($contact, 'mailto:' . self::CONTACT );
		return wpautop($contact);
	}	

	public function copyright() {
		return do_shortcode('[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"]');
	}	

	public function unsubscribe() {
		$unsubscribe = __('Want to change how you receive these emails?','foodiepro');
		$unsubscribe1 = __('You can <a href="%s">update your preferences</a> on %s.','foodiepro');
		$unsubscribe1 = sprintf( $unsubscribe1, CustomSocialHelpers::url('edit_profile',$post->post_author), get_bloginfo());
		return $unsubscribe . '<br>' . $unsubscribe1;
	}			

	public function published_post_notification( $post ) {	
		$text='';
		if ($post->post_type == 'recipe') {
		    $subject = __('Your recipe just got published !','foodiepro');
		}
		else 
		    $subject = __('Your post just got published !','foodiepro');
			
		$to = get_the_author_meta('user_email', $post->post_author);

		// Adds admin to the list of hidden recipients
		$this->bcc_admin();

		if( $to ) {
			$author = ucfirst(get_the_author_meta('display_name', $post->post_author));
			$headline = wpautop(sprintf($this->hello(), $author));

			$essai = wpautop($headline);

		    $content = __('Greetings, your recipe <a href="%s">%s</a> just got published !', 'foodiepro');
		    $content = sprintf( $content, get_permalink($post), $post->post_title);
		    $content = wpautop( $content );

		    $content1 = __('It is visible on the website, and appears on <a href="%s">your blog</a>.','foodiepro');
		    $content1 = sprintf( $content1, bp_core_get_user_domain( $post->post_author ));
		    $content1 = wpautop( $content1 );

		    $img_url = get_the_post_thumbnail_url($post, 'post-thumbnail');

		    $data = array(
		    	'title' => $post->post_title,
		    	'headline' => $headline,
		    	'content' => $content . $content1,
		    	'signature' => $this->signature(),
		    	'contact' => $this->contact(),
		    	'image_url' => $img_url,
		    	'copyright' => $this->copyright(),
		    	'unsubscribe' => $this->unsubscribe(),
		    	'facebook_url' => CustomSocialButtons::facebookURL($post),
		    	'facebook_text' => __('Share this recipe on Facebook','foodiepro'),
		    	'twitter_url' => CustomSocialButtons::twitterURL($post),
		    	'twitter_text' => __('Share this recipe on Twitter','foodiepro'),
		    	'pinterest_url' => CustomSocialButtons::pinterestURL($post),
		    	'pinterest_text' => __('Share this recipe on Pinterest','foodiepro'),
		    	'mail_url' => CustomSocialButtons::mailURL($post),
		    	'mail_text' => __('Send this recipe to a friend','foodiepro'),
		    );
		    $message = $this->get_html($data, self::PROVIDER.'_generic' );
		    $headers = $this->headers();
		    wp_mail( $to, $subject, $message, $headers );
		}	
	}

	public function get_html( $data, $template, $tag='%%' ) {
		$path = self::$PLUGIN_PATH . 'templates/' . $template . '.php';
		$content = file_get_contents( $path );
		if (preg_match_all("/$tag(.*?)$tag/i", $content, $m)) {
		    foreach ($m[1] as $i => $varname) {
		        $content = str_replace($m[0][$i], sprintf('%s', $data[strtolower($varname)]), $content);
		    }
		}
		return do_shortcode($content);
	}



}

