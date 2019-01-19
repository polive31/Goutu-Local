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
			'genesis_before_content',
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
		// The following action is used whenever the popup has to be placed selectively depending on the post type
		add_action( 'wp', array( $this, 'create_popup_actions') );
		// The following action allows to instatiate the popup on any page
		add_action( 'genesis_before_content', array( $this, 'add_join_us_popup') );
        add_action( 'wp_enqueue_scripts', array( $this, 'popups_styles_register' ) );

	}

    public function popups_styles_register() {
		custom_register_style(
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
		if ( is_user_logged_in() ) return;
		wp_enqueue_style('custom-site-popups');
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
			$html.='<li class="icon cookbook textbox left"><span>' . __('Store your favorite recipes in your cookbook,','foodiepro') . '</span></li>';
			$html.='<li class="icon pan textbox right"><span>' . __('Publish your own recipes and posts,','foodiepro') . '</span></li>';
			$html.='<li class="icon profile textbox left"><span>' . __('Create your personal profile and exchange with other members,','foodiepro') . '</span></li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="column ' . $class . '" id="group2">';
			$html.='<ul>';
			$html.='<li class="icon rate textbox right"><span>' . __('Comment and rate recipes','foodiepro') . '</span></li>';
			$html.='<li class="icon blogs textbox left"><span>' . __('Subscribe to other members and access their private recipes,','foodiepro') . '</span></li>';
			$html.='<li class="icon more textbox right"><span>' . __('And many more other benefits...','foodiepro') . '</span></li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="full">';
				$html.='<div class="register-button">' . do_shortcode('[permalink slug="bp-register-captcha"]' . __('Register','foodiepro') . '[/permalink]') . '</div>';
				// $html.='<p>' . __('Registration is simple and free !','foodiepro') . '</p>';
			$html.='</div>';
		$html.='</div>';
		
		return $html;

	}



}