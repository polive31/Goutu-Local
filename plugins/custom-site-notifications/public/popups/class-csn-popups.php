<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSN_Popups {

	const JOIN_US_ID = 'join_us_popup';

	/**
	 * get_popups provides :
	 * * the list of supported post types
	 * * the conditions (loginout, mobile, post, page, archive, cpt...) where to display the popup
	 * * the stylesheet to enqueue for this popup
	 *
	 * @return array $popups
	 */
	private static function get_popups() {
		$popups= array(
				'add_join_us_popup'	=> array(
					'stylesheet'	=> array(
						'handle' => 'custom-site-popups',
						'file'	 => 'custom_site_popups.css',
						'uri'	 => trailingslashit( CSN_Assets::plugin_url() ) . 'assets/css/',
						'dir'	 => trailingslashit( CSN_Assets::plugin_path() ) . 'assets/css/',
					),
					'conditions' => array(
						'login'	=> false,
						'or'	=> array(
							'home' =>'',
							'post' => 'recipe'
						),
					),
				),
			);
		return $popups;
	}

	/**
	 * Creates actions & enqueues styles for the different popups
	 *
	 * @return void
	 */
	public function create_popup_actions() {
		foreach (self::get_popups() as $callback => $atts) {
			if ($this->match_conditions( $atts['conditions'])) {
				$hook=isset($atts['hook'])?$atts['hook']:'genesis_before_content';
				add_action( $hook, array( $this, $callback ) );
				$stylesheet= $atts['stylesheet'];
				add_action('wp_enqueue_scripts', function () use ($stylesheet) {
					foodiepro_enqueue_style($stylesheet);
				});
			}
		}
	}


/* JOIN US POPUP
---------------------------------------------------------------*/
	public function add_join_us_popup() {
		/* This popoup is reserved to unregistered users */
		if ( is_user_logged_in() ) return;

		// wp_enqueue_style('custom-site-popups');
		$args=array(
			'content' 	=> $this->get_join_us_form(),
			'action'	=> 'click',
			'id'		=> self::JOIN_US_ID,
			'class'		=> 'join-us modal',
		);
		Tooltip::display( $args );
	}

	public static function get_join_us_id() {
		return self::JOIN_US_ID;
	}

	public function get_join_us_form( $class='' ) {
		$html='<div class="form">';
		$html.='<div class="full">';
		$html.='<div class="textbox"><h4>' . __('Becoming a member allows you to : ','foodiepro') . '</h4></div>';
		$html.='</div>';
			$html.='<div class="column ' . $class . '" id="group1">';
			$html.='<ul>';
			$html.='<li class="icon cookbook textbox left"><span>' . __('Store your favorite recipes in your cookbook,','foodiepro') . '</span></li>';
			$html.='<li class="icon pen textbox right"><span>' . __('Publish your own recipes and posts,','foodiepro') . '</span></li>';
			$html.='<li class="icon profile textbox left"><span>' . __('Create your personal profile and exchange with other members,','foodiepro') . '</span></li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="column ' . $class . '" id="group2">';
			$html.='<ul>';
			$html.='<li class="icon rate textbox right"><span>' . __('Comment and rate recipes','foodiepro') . '</span></li>';
			$html.='<li class="icon blogs textbox left"><span>' . __('Subscribe to other members and access their private recipes,','foodiepro') . '</span></li>';
			$html.='<li class="icon more textbox right"><span>' . __('And many more features to come...','foodiepro') . '</span></li>';
			$html.='</ul>';
			$html.='</div>';
			$html.='<div class="full">';
			$html.='<div class="register-button">' . foodiepro_get_permalink(array('community'=>'register', 'text' => __('Register', 'foodiepro'))) . '</div>';
			// $html.='<p>' . __('Registration is simple and free !','foodiepro') . '</p>';
			$html.='</div>';
		$html.='</div>';

		return $html;

	}


	/* HELPERS
-------------------------------------------------------------- */
	public function match_conditions($conditions, $or=false)
	{
		$match = $or?false:true;
		foreach ($conditions as $type => $value) {
			if ($type=='or') {
				$temp=$this->match_conditions($value, true);
			}
			switch ($type) {
				case 'post':
					$temp = is_singular($value);
				break;
				case 'page':
					$temp = is_page($value);
				break;
				case 'home':
					$temp= (is_home() || is_front_page());
				break;
				case 'login':
					$temp= is_user_logged_in()==$value;
				break;
			}
			$match=$or?$match||$temp:$match&&$temp;
		}
		return $match;
	}


}
