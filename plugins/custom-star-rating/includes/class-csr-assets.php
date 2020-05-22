<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSR_Assets {

	const RATED_POST_TYPES = array( 'recipe', 'menu' );

	private static $ratingCats;

	public function __construct() {
		/* Allows to access the class's functions from a submission or ajax callback
		In this case, the Custom_Star_Rating class is not created, so the only way is to create a new CSR_Assets instance
		in order to force the hydrate */
		self::hydrate();
	}

	public static function Plugin_path() {
		return  plugin_dir_path( dirname( __FILE__ ) );
	}

	public static function Plugin_uri() {
		return  plugin_dir_url( dirname( __FILE__ ) );
	}

	/* Register stylesheet, will be enqueued in the shortcode itself  */
	public static function register_csr_assets() {

		$args = array(
			'handle'	=> 'custom-star-rating-form',
			'file' 		=> 'assets/css/custom-star-rating-form.css',
			'uri' 		=> self::Plugin_uri(),
			'path' 		=> self::Plugin_path(),
			'deps' 		=> array(),
			'version' 	=> CHILD_THEME_VERSION,
		);
		foodiepro_register_style( $args );
	}

	// public static function enqueue_comment_reply_script() {
	// 	if ( get_option( 'thread_comments' ) && is_singular() ) {
	// 		// wp_enqueue_script( 'comment_reply' );
	// 		// wp_enqueue_script( 'comment_reply', get_site_url() . '/wp-includes/js/comment-reply.min.js', array(), false, true );
	// 	}
	// }

	// Initialize all strings needing a translation (doesn't work in __construct)
	public static function hydrate() {
		self::$ratingCats = array(
			'rating' => array (
				'weight' => 2,
				'title'=> __('Dish','foodiepro'),
				'legend'=> __('Appreciation of the dish','foodiepro'),
				'question'=> __('How did you like this dish ?','foodiepro'),
				'caption' => array(
					__('Disappointing','foodiepro'),
					__('Average','foodiepro'),
					__('Good','foodiepro'),
					__('Very good','foodiepro'),
					__('Delicious','foodiepro'),
					)
				),
				'global'=>array (
					'title'=> __('Overall','foodiepro'),
					'legend'=> __('Global appreciation of the dish','foodiepro'),
					'caption' => array(
					__('Disappointing','foodiepro'),
					__('Average','foodiepro'),
					__('Good','foodiepro'),
					__('Very good','foodiepro'),
					__('Delicious','foodiepro'),
				)
			),
			/*
			'difficulty' => array(
				'weight' => 1,
				'title'=> __('Recipe difficulty','foodiepro'),
				'legend'=> __('Complexity of the recipe'),
				'question'=> __('How difficult was the recipe to realize ?','foodiepro'),
				'caption' => array(
					__('Elementary','foodiepro'),
					__('Easy','foodiepro'),
					__('Tedious','foodiepro'),
					__('Complicated','foodiepro'),
					__('Difficult','foodiepro'),
					)
				),
			'clarity' => array(
				'weight' => 1,
				'title'=> __('Recipe clarity','foodiepro'),
				'legend'=> __('Clarity of the recipe'),
				'question'=> __('How clear was the recipe ?','foodiepro'),
				'caption' => array(
					__('Confusing','foodiepro'),
					__('Not so clear','foodiepro'),
					__('Rather clear','foodiepro'),
					__('Very clear','foodiepro'),
					__('Crystal clear even for kitchen dummies','foodiepro'),
					)
				),*/

		);
	}


	/* Get Rated Post Types
	------------------------------------------------------------*/
	public static function post_types() {
		return self::RATED_POST_TYPES;
	}


	/**
	 * Get Rating Categories
	 * * $cat_ids = 'all', 'global', <catN>, array(<cat2>, <cat5>, ...)
	 * * $global = false, true => only valid with 'all'
	 *
	 * @param  mixed $cat_ids
	 * @param  mixed $global
	 * @return array $cats array of different categories or single category array
	 */
	public static function rating_cats( $cat_ids='all', $global=false ) {
		$cats=array();
		if ($cat_ids=='all') {
			// Return all rating categories
			$cats = self::$ratingCats;
			if ( !$global ) unset( $cats['global'] );
		}
		elseif ( is_array($cat_ids) ) {
			// Return a selection of rating categories
			foreach ( $cat_ids as $id ) {
				if (isset(self::$ratingCats[$id])) {
					$cats[$id]=self::$ratingCats[$id];
				}
			}
		}
		elseif (isset(self::$ratingCats[$cat_ids]))
			// Return one rating category
			$cats=self::$ratingCats[$cat_ids];
		else
			$cats=false;

		return $cats;
	}


	/* Get Rating caption
	------------------------------------------------------------*/
	public static function get_rating_caption($val, $cat='global') {
		//$val = intval($rating_val);
		if ($val==0) return __('Not rated','foodiepro');
		$index=floor($val-1);
		if ( isset( self::$ratingCats[$cat]['caption'] ) ) {
			$caption = $val . ' : ' . self::$ratingCats[$cat]['caption'][$index];
			return $caption;
		}

	}



	/* TEMPLATES
	---------------------------------------------------------------------- */
	public static function echo_template_part($slug, $name = false, $args = array())
	{
		extract($args);

		$templates_path = trailingslashit( self::Plugin_path() ) . 'templates/';
		$template = 'template-' . $slug;
		$template .= $name ? '-' . $name : '';
		$template .= '.php';
		include($templates_path . $template);
	}

	public static function get_template_part($slug, $name = false, $args = array())
	{
		ob_start();
		self::echo_template_part($slug, $name, $args);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

}
