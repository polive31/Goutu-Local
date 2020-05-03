<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CCM_Assets {

	private static $Plugin_path;
	private static $Plugin_uri;

	const POST_TYPES = array( 'post', 'recipe' );

	public function __construct() {
		self::$Plugin_uri = plugin_dir_url(dirname(__FILE__));
		self::$Plugin_path = plugin_dir_path(dirname(__FILE__));
	}

	// public static function remove_comment_reply_script() {
	// 	wp_deregister_script('comment-reply');
	// }
	public static function post_types() {
		return self::POST_TYPES;
	}

	/* Register stylesheet, will be enqueued in the shortcode itself  */
	public static function enqueue_ccm_assets() {
		if (!is_single()) return;

		/* Script for JS-based comment form validation and recaptcha result processing */
		$args = array(
			'handle'	=> 'ccm-helpers',
			'file' 		=> 'assets/js/ccm-helpers.js',
			'uri' 		=> self::$Plugin_uri,
			'path' 		=> self::$Plugin_path,
			'footer' 	=> true,
			'deps' 		=> array(),
			'data' 		=> array(
				'name'			 => 'csr',
				'emptyComment' 	 => __('Please enter a text before submitting your comment.', 'foodiepro'),
				'emptyAuthor' 	 => __('Please provide your name before submitting your comment.', 'foodiepro'),
				'invalidEmail' 	 => __('Please provide a valid email adress before submitting your comment.', 'foodiepro'),
				'emptyRecaptcha' => __('Please complete the antispam verification.', 'foodiepro'),
			)
		);
		foodiepro_enqueue_script($args);
	}


}
