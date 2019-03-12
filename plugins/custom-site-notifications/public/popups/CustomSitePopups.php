<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomSitePopups {

	// const CONTACT_NAME = "Goutu.org";
	// const CONTACT_EMAIL = "contact@goutu.org";
	
	/* POPUPS provides :
		- the list of supported post types
		- the location (hook) where to place the popup
		- which popup(s) to display for this post type
	*/
	const POPUPS = array(
		'add_join_us_popup'	=> array(
			array( 
				'hook_name'	=> 'genesis_before_content',
				'locations' => 'home', // post-post
			),
			array( 
				'hook_name'	=> 'wpurp_in_container',
				'locations' => 'post-recipe',
			)
		),
	);
	
	public function create_popup_actions() {
		foreach (self::POPUPS as $callback => $hooks) {
			foreach ($hooks as $hook ) {
				$locations = explode( ' ', $hook['locations']);
				foreach ($locations as $location) {
					$type = substr( $location, 0, 4);
					$value = substr( $location, 5);

					$is_post = $type=='post' && is_singular($value);
					$is_page = $type=='page' && is_page($value);
					$is_home = $type =='home' && ( is_home() || is_front_page() );
					if ( $is_post || $is_page || $is_home ) {
						add_action( $hook['hook_name'], array( $this, $callback ) );
					}
				}
			}
		}
	}
	
	public function add_join_us_popup() {
		/* This popoup is reserved to unregistered users */
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
				$html.='<div class="register-button">' . do_shortcode('[registration]' . __('Register','foodiepro') . '[/registration]') . '</div>';
				// $html.='<p>' . __('Registration is simple and free !','foodiepro') . '</p>';
			$html.='</div>';
		$html.='</div>';
		
		return $html;

	}



}