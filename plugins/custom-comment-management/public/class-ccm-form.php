<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCM_Form
{

	protected static $_PluginPath;
	protected static $_PluginUri;

	protected $captchaError;

	public function __construct()
	{
		self::$_PluginUri = plugin_dir_url(dirname(__FILE__));
		self::$_PluginPath = plugin_dir_path(dirname(__FILE__));
	}

	public function force_comment_form_display($open, $post_id)
	{
		$post_type = get_post_type($post_id);
		if (in_array($post_type, CCM_ASSETS::post_types())) {
			$open = true;
		}
		return $open;
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
		$post_type = get_post_type();

		if (class_exists('CPM_Assets')) {
			$defaults['title_reply'] = CPM_Assets::get_label($post_type, 'comment_form_headline');
			$defaults['title_reply_to'] = 'Leave a reply to %s';
		}

		$defaults['id_form'] = 'foodiepro_comment' . $instance;
		$defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment_' . $instance . '" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		$defaults['format'] = 'html5';

		$instance++;
		return $defaults;
	}


	/**
	 * Generates multiple comment submit button instances
	 * since there can be at least 2 with the rating form.
	 *
	 * @param  mixed $recaptcha
	 * @return void
	 */
	public function get_submit_button_instance()
	{
		static $instance = 0;
		$submit_button = '<input name="submit' . $instance . '" type="submit" id="submit' . $instance . '" data-instance="' . $instance . '" class="submit" value="' . __('Submit', 'foodiepro') . '">';
		$instance++;
		return $submit_button;
	}

}
