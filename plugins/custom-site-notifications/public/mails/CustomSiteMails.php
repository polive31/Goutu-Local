<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomSiteMails {

	private $headers=array();
	private $target;

	// const CONTACT_NAME = "Goutu.org";
	// const CONTACT_EMAIL = "contact@goutu.org";
	const PROVIDER = 'mailchimp';

	/* $target argument is used by the [custom-functions-debug] debug shortcode in functions.php */
	public function __construct( $target='production' ) {	
		$this->headers[] = 'Bcc: ' . get_bloginfo('admin_email');
		$this->target = $target;
	}

	public function site_name( $from_name='' ) {
		$from_name = get_bloginfo( 'name' );
	    return $from_name;
	}

	public function contact_address( $from_address='' ) {
		$domain = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $domain, 0, 4 ) == 'www.' ) {
			$domain = substr( $domain, 4 );
		}
		$from_address = 'contact@' . $domain;
	    return $from_address;
	}

	public function hydrate() {
		// $this->headers[] = 'From: ' . get_bloginfo('name') . ' <' . self::CONTACT_EMAIL . '>';
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
		$contact = sprintf($contact, 'mailto:' . $this->contact_address() );
		return wpautop($contact);
	}	

	public function copyright() {
		return do_shortcode('[footer_copyright before="' . __('All rights reserved','foodiepro') . ' " first="2015"]');
	}	

	public function unsubscribe( $user ) {
		$unsubscribe = __('Want to change how you receive these emails?','foodiepro');
		$unsubscribe1 = __('You can <a href="%s">update your preferences</a> on %s.','foodiepro');
		$unsubscribe1 = sprintf( $unsubscribe1, PeepsoHelpers::get_url( $user ), get_bloginfo());
		return $unsubscribe . '<br>' . $unsubscribe1;
	}			

	public function published_post_notification_callback( $post ) {	

		$Assets = new CPM_Assets(); 
		$subject = CPM_Assets::get_label( $post->post_type, 'mail_title');
		$content = CPM_Assets::get_label( $post->post_type, 'mail_content');
		$content1 = CPM_Assets::get_label( $post->post_type, 'mail_content1');

		$title = $post->post_title;
			
		$to = get_the_author_meta('user_email', $post->post_author);

		if( $to ) {
			$author = ucfirst(get_the_author_meta('display_name', $post->post_author));
			$headline = wpautop(sprintf($this->hello(), $author));

		    $content = sprintf( $content, get_permalink($post), $title);
		    $content = wpautop( $content );

			$user = PeepsoHelpers::get_user( $post->post_author );
		    $content1 = sprintf( $content1, PeepsoHelpers::get_url( $user, 'profile', 'blogposts' ) );
		    $content1 = wpautop( $content1 );

		    $img_url = get_the_post_thumbnail_url($post, 'post-thumbnail');

		    $signature = $this->signature();
		    $contact = $this->contact();
		    $copyright = $this->copyright();
		    $unsubscribe = $this->unsubscribe( $user );
		    $facebook_url = CustomSocialButtons::facebookURL($post);
		    $twitter_url = CustomSocialButtons::twitterURL($post);
		    $pinterest_url = CustomSocialButtons::pinterestURL($post);
		    $mail_url = CustomSocialButtons::mailURL($post, $post->post_type);
		    $whatsapp_url = CustomSocialButtons::whatsappURL($post, $post->post_type);

		    $data = array(
		    	'title' => $title,
		    	'headline' => $headline,
		    	'content' => $content . $content1,
		    	'signature' => $signature,
		    	'contact' => $contact,
		    	'image_url' => $img_url,
		    	'copyright' => $copyright,
		    	'unsubscribe' => $unsubscribe,
		    	'facebook_url' => $facebook_url,
		    	'facebook_text' => __('Share this recipe on Facebook','foodiepro'),
		    	'twitter_url' => $twitter_url,
		    	'twitter_text' => __('Share this recipe on Twitter','foodiepro'),
		    	'pinterest_url' => $pinterest_url,
		    	'pinterest_text' => __('Share this recipe on Pinterest','foodiepro'),
		    	'mail_url' => $mail_url,
		    	'mail_text' => __('Share this recipe by email','foodiepro'),
		    	'whatsapp_url' => $whatsapp_url,
		    	'whatsapp_text' => __('Share this recipe on Whatsapp','foodiepro'),		    	
		    );
		    $message = $this->populate_template($data, self::PROVIDER.'_generic' );
		    $headers = $this->headers();

		    if ( $this->target == 'debug' ) {
		    	echo "From name : " . $this->site_name() . " <br>";
		    	echo "From address : " . $this->contact_address() . " <br>";
		    	echo "To : $to<br>";
		    	echo "Subject : $subject<br>";
		    	echo "Message : $message<br>";
		    }
		    else 
		    	wp_mail( $to, $subject, $message, $headers );
		}	
	}

	public function populate_template( $data, $template, $tag='%%' ) {
		$path = self::$PLUGIN_PATH . 'mails/partials/' . $template . '.php';
		$html = file_get_contents( $path );
		if (preg_match_all("/$tag(.*?)$tag/i", $html, $m)) {
		    foreach ($m[1] as $i => $varname) {
		        $html = str_replace($m[0][$i], sprintf('%s', $data[strtolower($varname)]), $html);
		    }
		}
		return do_shortcode($html);
	}


	public function welcome_user_notification( $user_id, $key = false, $user = false ) {
	 
	    if ( is_multisite() ) {
	        return ;// we don't need it for multisite
	    }

		$subject = __('Welcome to Goûtu.org !','foodiepro');
		$content = __('Greetings, you have successfully activated your account, and we are glad to count you as one of our members !', 'foodiepro');
	 
	    //get user details
	    $user = get_userdata( $user_id );

	    //get site name
		$to = $user->user_email;


		if( $to ) {
			$name = ucfirst($user->display_name);
			$headline = wpautop(sprintf($this->hello(), $name));

		    $content = sprintf( $content, get_permalink($post), $post->post_title);
		    $content = wpautop( $content );

		    $signature = $this->signature();
		    $contact = $this->contact();
		    $copyright = $this->copyright();
		    $unsubscribe = $this->unsubscribe( $user_id );
		    $facebook_url = CustomSocialButtons::facebookURL($post);
		    $twitter_url = CustomSocialButtons::twitterURL($post);
		    $pinterest_url = CustomSocialButtons::pinterestURL($post);
		    $mail_url = CustomSocialButtons::mailURL($post, $post->post_type);
		    $whatsapp_url = CustomSocialButtons::whatsappURL($post, $post->post_type);

		    $data = array(
		    	'title' => $title,
		    	'headline' => $headline,
		    	'content' => $content . $content1,
		    	'signature' => $signature,
		    	'contact' => $contact,
		    	'image_url' => '',
		    	'copyright' => $copyright,
		    	'unsubscribe' => $unsubscribe,
		    	'facebook_url' => $facebook_url,
		    	'facebook_text' => __('Share this recipe on Facebook','foodiepro'),
		    	'twitter_url' => $twitter_url,
		    	'twitter_text' => __('Share this recipe on Twitter','foodiepro'),
		    	'pinterest_url' => $pinterest_url,
		    	'pinterest_text' => __('Share this recipe on Pinterest','foodiepro'),
		    	'mail_url' => $mail_url,
		    	'mail_text' => __('Share this recipe by email','foodiepro'),
		    	'whatsapp_url' => $whatsapp_url,
		    	'whatsapp_text' => __('Share this recipe on Whatsapp','foodiepro'),		    	
		    );
		    $message = $this->populate_template($data, self::PROVIDER.'_generic' );
		    $headers = $this->headers();

		    if ( $this->target == 'debug' ) {
		    	echo "From name : " . $this->site_name() . " <br>";
		    	echo "From address : " . $this->contact_address() . " <br>";
		    	echo "To : $to<br>";
		    	echo "Subject : $subject<br>";
		    	echo "Message : $message<br>";
		    }
		    else 
		    	wp_mail( $to, $subject, $message, $headers );
		}	
	}	

	function custom_activation_link($msg, $user_id, $activation_url) {
		//	Get some globals
		global $bp, $wpdb;
	    
	    $userinfo = get_userdata($user_id);
	    $username = $userinfo->user_login;

	    $sql = 'select meta_value from wp_usermeta where meta_key="activation_key" and user_id in (select ID from wp_users where user_login="' . $username . '" and user_status=2)';
	    $activation_key = $wpdb->get_var($sql);
		

		$msg = sprintf( __('Hey Toto, thanks for registering ! To complete the activation of your account, go to the following link and click on the "Activate" button: %s. If the "Activation Key" field is empty, copy and paste the following into the field : <strong>%s</strong> ', 'foodiepro'), $activation_url, $activation_key);
	    $msg .= sprintf( __("After successful activation, you can log in using your username (%1\$s) along with password you choose during registration process.", 'foodiepro'), $username);
	    return $msg;
	}


}