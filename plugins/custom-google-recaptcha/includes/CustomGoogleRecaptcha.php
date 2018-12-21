<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomGoogleRecaptcha {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;
	public static $PUBLIC_KEY='6LeIb84SAAAAALIrAdEQoV5GUsuc5WzMfP4Z5ctc';
	public static $PRIVATE_KEY='6LeIb84SAAAAACusroz8joX8TCl1NUXL2xWDlzM8';

	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		
		add_action('wp_enqueue_scripts', array($this, 'register_recaptcha_script'));
		add_shortcode('g-recaptcha', array($this,'display_google_recaptcha')); 
	}

	public function register_recaptcha_script() {
		// $js_uri = self::$PLUGIN_URI . '/assets/js/';
		// $js_path = self::$PLUGIN_PATH . '/assets/js/';
		// custom_enqueue_script( 'g-recaptcha', $js_uri, $js_path, 'contact-form.js', array(), CHILD_THEME_VERSION, true);
		
		wp_register_script( 'g-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1.0.0' );
	}
	
	public static function enqueue_scripts() {
		wp_enqueue_script( 'g-recaptcha' );
	}

	public function verify() {
		$retries=4;
		if(isset($_POST['g-recaptcha-response'])){
			$captcha=$_POST['g-recaptcha-response'];
	    }
        if(!$captcha){
          return 'missing';
        }
        do {	
	        $ip = $_SERVER['REMOTE_ADDR'];
	        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=". self::$PRIVATE_KEY ."&response=".$captcha."&remoteip=".$ip);
	        $responseKeys = json_decode($response,true);
	        if(intval($responseKeys["success"]) !== 1) {
	          $result ='success';
	        } else {
	          $result = 'fail';
	        }
	        --$retries;
        } while ($result=='fail' && $retries>0);
        return $result;
	}


	/* =================================================================*/
	/* = DISPLAY GOOGLE RECAPTCHA   
	/* =================================================================*/

    public function display( $classes='' ) {
    	?>
        <div class="g-recaptcha <?php echo $classes; ?>" data-sitekey="<?php echo self::$PUBLIC_KEY; ?>"></div>
        <?php
    }	


}

new CustomGoogleRecaptcha();

