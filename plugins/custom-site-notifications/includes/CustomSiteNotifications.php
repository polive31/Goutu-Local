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

		/* Event hooks
		--------------------------------------------------------------*/
		add_action( 'pending_to_publish',  array($this, 'published_post_notification'), 10, 1 );
		add_action( 'bp_core_activated_user', array($this, 'welcome_user_notification'), 10, 3 );


		/* Mail Customizations
		--------------------------------------------------------------*/
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
		if ($post->post_type == 'recipe')
		    $subject = __('Your recipe just got published !','foodiepro');
		else 
		    $subject = __('Your post just got published !','foodiepro');

		$title = $post->post_title;
			
		$to = get_the_author_meta('user_email', $post->post_author);

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

		    $signature = $this->signature();
		    $contact = $this->contact();
		    $copyright = $this->copyright();
		    $unsubscribe = $this->unsubscribe();
		    $facebook_url = CustomSocialButtons::facebookURL($post);
		    $twitter_url = CustomSocialButtons::twitterURL($post);
		    $pinterest_url = CustomSocialButtons::pinterestURL($post);
		    $mail_url = CustomSocialButtons::mailURL($post,'recipe');
		    $whatsapp_url = CustomSocialButtons::whatsappURL($post,'recipe');

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


	public function welcome_user_notification( $user_id, $key = false, $user = false ) {
	 
	    if ( is_multisite() ) {
	        return ;// we don't need it for multisite
	    }
	    //send the welcome mail to user
	    //welcome message
	 
	    $welcome_email = __( 'Dear USER_DISPLAY_NAME,
	 
	Your new account is set up.
	 
	You can log in with the following information:
	Username: USERNAME
	LOGINLINK
	 
	Thanks!
	 
	--The Team @ SITE_NAME' );
	 
	    //get user details
	    $user = get_userdata( $user_id );
	    //get site name
	    $site_name = get_bloginfo( 'name' );
	    //update the details in the welcome email
	    $welcome_email = str_replace( 'USER_DISPLAY_NAME', $user->first_name, $welcome_email );
	    $welcome_email = str_replace( 'SITE_NAME', $site_name, $welcome_email );
	    $welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	    $welcome_email = str_replace( 'LOGINLINK', wp_login_url(), $welcome_email );
	 
	    //from email
	    $admin_email = get_site_option( 'admin_email' );
	 
	    if ( empty( $admin_email ) ) {
	        $admin_email = 'support@' . $_SERVER['SERVER_NAME'];
	    }
	 
	    $from_name = $site_name . "<$admin_email>" ;//from
	    $message_headers =  array(
	        'from'          => $from_name,
	        'content-type'  => 'text/plain; charset='. get_option('blog_charset')
	    );
	 
	    //EMAIL SUBJECT
	    $subject = sprintf( __( 'Welcome to   %1$s ' ), $site_name ) ;
	    //SEND THE EMAIL
	    wp_mail( $user->user_email, $subject, $welcome_email, $message_headers );
	 
	    return true;
	}	


}