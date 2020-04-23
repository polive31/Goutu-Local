<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCM_Form
{

	protected static $_PluginPath;
	protected static $_PluginUri;
	protected static $captchaInstance;
	protected $captchaError;

	public function __construct()
	{
		self::$_PluginUri = plugin_dir_url(dirname(__FILE__));
		self::$_PluginPath = plugin_dir_path(dirname(__FILE__));
		self::$captchaInstance = 0;
	}

	/* COMMENT FORM FILTERS (COMMON TO ALL COMMENT FORMS ON THE POST)
	-----------------------------------------------*/
	public function customize_comment_form($fields)
	{
		static $instance = 0;
		unset($fields['url']);
		$fields['author'] = '<p class="comment-form-author"><label for="author">Nom <span class="required">*</span></label> <input class="author" id="author_' . $instance . '" name="author" type="text" value="" size="30" maxlength="245" required="required" /></p>';
		$fields['email']  = '<p class="comment-form-email"><label for="email">Adresse de messagerie <span class="required">*</span></label> <input class="email" id="email_' . $instance . '" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" required="required" /></p>';

		$instance++;
		return $fields;
	}

	public function change_comment_form_defaults($defaults)
	{
		static $instance = 0;

		$defaults['logged_in_as'] = '';
		if ( is_singular('recipe') )
		// 	// $defaults['title_reply'] = _x('Leave a comment', 'recipe', 'foodiepro');
			$title_new = __('Leave a comment on this recipe', 'foodiepro');
		else
		// 	// $defaults['title_reply'] = _x('Leave a comment', 'post', 'foodiepro');
			$title_new = __('Leave a comment on this post', 'foodiepro');
		// $title_reply = __('Leave a reply to ', 'foodiepro');

		/* Since a known WP issue prevents the title_reply_to to work, a workaround is setup in order to allow
		for 2 different comment form headlines depending on the situation (new comment or answer)
		The goal is to add a new h3 title after the main one. The main one is reserved for the answer, whereas the new one is reserved for the new comment.
		Since the cancel button is added to the main title_reply section, this section will be considered as the "reply"one, and therefore hidden by default, whereas the
		second section will be the "new form" one therefore shown by default.
		*/
		// $defaults['title'] = $title_new;
		// $defaults['title_reply'] = __('Leave a reply to %s', 'foodiepro');
		// $defaults['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title" data-text="' . $title_reply . '" style="display:none">';
		// $defaults['title_reply_after'] = '</h3><h3 id="new-title" class="comment-new-title">' . $title_new . '</h3>';

		$defaults['id_form'] = 'foodiepro_comment' . $instance;
		$defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment_' . $instance . '" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		$defaults['format'] = 'html5';

		$instance++;
		return $defaults;
	}


	/* RECAPTCHA
	-----------------------------------------------*/
	public function comment_form_add_recaptcha($submit_button, $args)
	{
		$recaptcha = '';
		if (!is_user_logged_in() && class_exists('Custom_Google_Recaptcha')) {
			if (isset($_GET['captcha']) && ($_GET['captcha'] != 'success')) {
				$recaptcha .= '<p class="error">' . __('<strong>ERROR</strong>: please complete the CAPTCHA verification.', 'foodiepro') . '</p>';
			}
			$recaptcha .= CGR_Public::display('', 'recaptcha' . self::$captchaInstance, 'normal', '');
			self::$captchaInstance++;
		}
		return $this->get_submit_button_html($recaptcha);
	}

	public function get_submit_button_html($recaptcha)
	{
		static $instance = 0;
		$submit_button = '<input name="submit' . $instance . '" type="submit" id="submit' . $instance . '" data-instance="' . $instance . '" class="submit" value="' . __('Submit', 'foodiepro') . '">';
		$instance++;
		return $recaptcha . $submit_button;
	}

	public function verify_comment_recaptcha($commentdata)
	{
		if (is_user_logged_in()) return;

		$captchaResult = CGR_Public::verify();
		if ($captchaResult == 'success')
			return $commentdata;
		else {
			wp_die( __( '<strong>ERROR</strong>: please complete the CAPTCHA verification.', 'foodiepro' ) );
			// $url = get_permalink($commentdata['comment_post_ID']);
			// $args = array(
			// 	'comment-id' 	=> $commentdata['comment_ID'],
			// 	'captcha'		=> $captchaResult
			// );
			// $url = add_query_arg($args, $url) . '#respond';
			// wp_redirect($url);
			// exit;
		}
	}

}
