<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomSitePopups {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	// const CONTACT_NAME = "Goutu.org";
	// const CONTACT_EMAIL = "contact@goutu.org";
	
	/* POST_TYPES provides :
		- the list of supported post types
		- the location (hook) where to place the popup
		- which popup(s) to display for this post type
	*/
	const POST_TYPES = array(
		'post'	=> array(
			'genesis_before_loop',
			array( 
				'add_join_us_popup',
			)
		),		
		'recipe'=> array(
			'wpurp_in_container',
			array(
				'add_join_us_popup',
			)
		)
	);

	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		/* Actions */
		// add_action('init',array($this,'hydrate'));
		add_action( 'wp', array( $this, 'create_popup_actions') );
        add_action( 'wp_enqueue_scripts', array( $this, 'popups_styles_enqueue' ) );

	}


    public function popups_styles_enqueue() {
        if (! is_singular( array_keys(self::POST_TYPES) ) ) return;
		if ( is_user_logged_in() ) return;
        // if (! is_singular(self::POST_TYPES) ) return;
            // wp_enqueue_script( 'custom-post-like', self::$PLUGIN_URI . '/assets/js/social-like-post.js', array( 'jquery' ), CHILD_THEME_VERSION, false );
		custom_enqueue_style(
			'custom-site-popups', 
			self::$PLUGIN_URI . '/assets/css/', 
			self::$PLUGIN_PATH . '/assets/css/', 
			'custom_site_popups.css'
		);
    }

	
	public function create_popup_actions() {
		if ( is_user_logged_in() ) return;
		foreach (self::POST_TYPES as $type => $params) {
			if ( is_singular($type) ) {
				$hook=$params[0];
				$popups=$params[1];
				foreach ($popups as $callback) {
					add_action( $hook, array($this, $callback ) );
				}
				break;
			}
		}
	}
	
	
	public function add_join_us_popup() {
		$args=array(
			'content' 	=> $this->get_join_us_form(),
			'action'	=> 'click',
			'id'		=> 'join_us',
			'class'		=> 'join-us modal',
		);
		Tooltip::display( $args ); 
	}


	public function get_join_us_form( $class='' ) {

		$html='<div class="form">';
		$html.='<div class="full">';
		$html.='<div class="textbox"><h4>' . __('Becoming a member allows you to : ','foodiepro') . '</h4></div>';
		$html.='</div>';
			$html.='<div class="column ' . $class . '" id="group1">';
			$html.='<ul>';
			$html.='<li class="icon cookbook textbox">' . __('Store your favorite recipes in your cookbook,','foodiepro') . '</li>';
			$html.='<li class="icon pan textbox">' . __('Publish your own recipes and posts,','foodiepro') . '</li>';
			$html.='<li class="icon profile textbox">' . __('Create your personal profile and exchange with other members,','foodiepro') . '</li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="column ' . $class . '" id="group2">';
			$html.='<ul>';
			$html.='<li class="icon rate textbox">' . __('Comment and rate recipes','foodiepro') . '</li>';
			$html.='<li class="icon blogs textbox">' . __('Subscribe to other members and access their private recipes,','foodiepro') . '</li>';
			$html.='<li class="icon more textbox">' . __('And many more other benefits...','foodiepro') . '</li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="full">';
				$html.='<div class="register-button">[permalink slug="bp-register-captcha"]' . __('Register','foodiepro') . '[/permalink]</div>';
				// $html.='<p>' . __('Registration is simple and free !','foodiepro') . '</p>';
			$html.='</div>';
		$html.='</div>';
		
		return $html;

	}



}