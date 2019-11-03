<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSR_Form {

	protected static $_PluginPath;
	protected static $_PluginUri;
	protected static $instance;
	protected $captchaError;

	public function __construct() {
		self::$_PluginUri = plugin_dir_url( dirname( __FILE__ ) );
		self::$_PluginPath = plugin_dir_path( dirname( __FILE__ ) );
		self::$instance = 0;
	}

	/* COMMENT FORM FILTERS (COMMON TO ALL COMMENT FORMS ON THE POST)
	-----------------------------------------------*/
	public function customize_comment_form($fields) {
		static $instance=0;
		unset($fields['url']);
		$fields['author'] = '<p class="comment-form-author"><label for="author">Nom <span class="required">*</span></label> <input class="author" id="author_' . $instance . '" name="author" type="text" value="" size="30" maxlength="245" required="required" /></p>';
		$fields['email']  = '<p class="comment-form-email"><label for="email">Adresse de messagerie <span class="required">*</span></label> <input class="email" id="email_' . $instance . '" name="email" type="email" value="" size="30" maxlength="100" aria-describedby="email-notes" required="required" /></p>';

		$instance++;
		return $fields;
	}

	public function change_comment_form_defaults( $defaults ) {
		static $instance=0;

		$defaults['logged_in_as'] = '';
		// $defaults['title_reply'] = $defaults['title_reply'];
		$defaults['id_form'] = 'foodiepro_comment' . $instance;
		$defaults['title_reply_to'] = __('Your answer here','foodiepro');
		$defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment_' . $instance . '" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

		$instance++;
		return $defaults;
	}


	/* RECAPTCHA
	-----------------------------------------------*/
	public function rating_form_add_recaptcha($submit_button, $args) {
		$recaptcha = '';
		if ( !is_user_logged_in() && class_exists('Custom_Google_Recaptcha' ) ) {
			CGR_Public::enqueue_scripts();
			if ( isset($_POST['captcha']) && ($_POST['captcha']!='success') ) {
				$recaptcha .= '<p class="error">' . __( '<strong>ERROR</strong>: please complete the CAPTCHA verification.', 'foodiepro' ) . '</p>';
			}
			// Invisible not working yet
			// $recaptcha = CGR_Public::display( '', 'recaptcha' . self::$instance, 'invisible', 'csrOnSubmit' );
			$recaptcha .= CGR_Public::display( '', 'recaptcha' . self::$instance, 'normal', '' );
			self::$instance++;
		}
		return $recaptcha . $this->get_submit_button_html();
	}

	public function comment_form_add_recaptcha($submit_button, $args) {
		$recaptcha = '';
		if ( !is_user_logged_in() && class_exists('Custom_Google_Recaptcha' ) ) {
			if ( isset($_GET['captcha']) && ($_GET['captcha']!='success') ) {
				$recaptcha .= '<p class="error">' . __( '<strong>ERROR</strong>: please complete the CAPTCHA verification.', 'foodiepro' ) . '</p>';
			}
			$recaptcha .= CGR_Public::display( '', 'recaptcha' . self::$instance, 'normal', '' );
			self::$instance++;
		}
		return $recaptcha . $this->get_submit_button_html();
	}

	public function get_submit_button_html() {
		$submit_button .= '<input name="submit' . self::$instance . '" type="submit" id="submit' . self::$instance . '" data-instance="' . self::$instance . '" class="submit" value="' . __('Submit','foodiepro') . '">';
		return $submit_button;
	}

	public function verify_comment_recaptcha( $commentdata )
	{
		$captchaResult = CGR_Public::verify();
		if ( $captchaResult=='success' )
			return $commentdata;
		else {
			// wp_die( __( '<strong>ERROR</strong>: please complete the CAPTCHA verification.', 'foodiepro' ) );
			$url = get_permalink( $commentdata['comment_post_ID'] );
			$args = array(
				'comment-id' 	=> $commentdata['comment_ID'],
				'captcha'		=> $captchaResult
			);
			$url = add_query_arg($args, $url) . '#respond';
			wp_redirect($url);
			exit;
		}
	}


	/* SHORTCODES
	-----------------------------------------------*/
	public function display_comment_form_with_rating_shortcode() {
		$comment_notes = is_user_logged_in()?'':'<p class="comment-notes">' . __('Your name and mail address are required for authentification of your comment.<br>Your mail address will not be published.', 'foodiepro') . '</p>';

		$args = array (
			'comment_notes_before' 	=> '',
			'comment_notes_after' 	=> $comment_notes,
			'title_reply' 			=> '', //Default: __( 'Leave a Reply� )
			'label_submit' 			=> __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
			'comment_field' 		=> $this->output_rating_form(),
			'logged_in_as' 			=> '', //Default: __( 'Leave a Reply to %s� )
			'title_reply_to' 		=> __( 'Leave a Reply to %s', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
			'cancel_reply_link' 	=> __( 'Cancel', 'custom-star-rating' ), //Default: __( �Cancel reply� )
			'rating_cats' 			=> 'all',  //Default: "id1 id2..."
		);

		wp_enqueue_style('custom-star-rating');
		wp_enqueue_script('custom-star-rating');

		ob_start();
		include( self::$_PluginPath . 'public/partials/comment-form-template.php' );
		$cr_form = ob_get_contents();
		ob_end_clean();

		return $cr_form;
	}

	/* Rating Form
	------------------------------------------------------------ */
	public function output_rating_form() {

		ob_start();?>

		<div>
			<table class="ratings-table">
			<?php
			foreach (CSR_Assets::rating_cats() as $id => $cat) {?>

			<tr>
				<td align="left" class="rating-title"><?= $cat['question'];?></td>
				<td align="left"><?= $this->get_category_rating( $id );?></td>
			</tr>

			<?php
			}?>
			</table>
		</div>

		<div class="comment-reply">
			<label for="comment"><?= __('Add a comment','custom-star-rating' );?></label>
			<textarea id="comment" name="comment" cols="50" rows="4" aria-required="true"></textarea>
		</div>

	<?php
		$rating_form = ob_get_contents();
		ob_end_clean();

		return $rating_form;
	}

	public function get_category_rating( $id ) {
		$html= '<div class="rating-wrapper" id="star-rating-form">';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
		$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . CSR_Assets::get_rating_caption(5, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . CSR_Assets::get_rating_caption(4, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . CSR_Assets::get_rating_caption(3, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . CSR_Assets::get_rating_caption(2, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . CSR_Assets::get_rating_caption(1, $id) . '"></label>';
		$html.='</div>';

	  return $html;

	}


	/*************************************************************
	 * ************       DEPRECATED         ****************
	 *************************************************************/



		// /* Output stars
	// -------------------------------------------------------------*/
	// public function output_stars_simple($stars, $half) {
	// 	$html = '';
	// 	for ($i = 1; $i <= $stars; $i++) {
	// 		$html .= '<i class="fa fa-star"></i>';
	// 	}
	// 	for ($i = $stars+1; $i <= 5; $i++) {
	// 		if ( ($i == ($stars+1) ) && $half ) {
	// 			$html .= '<i class="fa fa-star-half-o"></i>';
	// 		}
	// 		else {
	// 			$html .= '<i class="fa fa-star-o"></i>';
	// 		}
	// 	}
	// 	return $html;
	// }

	// 	/* Output stars stacked
	// -------------------------------------------------------------*/
	// public function output_stars_table($stars, $half) {
	// 	$html = '';
	// 	for ($i = 1; $i <= $stars; $i++) {
	// 		$html .= '<span class="fa-stack full"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>';
	// 	}
	// 	for ($i = $stars+1; $i <= 5; $i++) {
	// 		if ( ($i == ($stars+1) ) && $half ) {
	// 			$html .= '<span class="fa-stack full"><i class="fa fa-star-half-o fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>';
	// 		}
	// 		else {
	// 			$html .= '<span class="fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>';
	// 		}
	// 	}
	// 	return $html;
	// }
	// 	/* Output stars FI
	// -------------------------------------------------------------*/
	// public function output_stars_png($stars, $half) {
	// 	$html = '';
	// 	for ($i = 1; $i <= $stars; $i++) {
	// 		$html .= '<i class="fi fi-star fi-on"></i>';
	// 	}
	// 	for ($i = $stars+1; $i <= 5; $i++) {
	// 		if ( ($i == ($stars+1) ) && $half ) {
	// 			$html .= '<i class="fi"><i class="fi fi-star fi-half fi-on"></i><i class="fi fi-star fi-half fi-off"></i></i>';
	// 		}
	// 		else {
	// 			$html .= '<i class="fi fi-star fi-off"></i>';
	// 		}
	// 	}
	// 	return $html;
	// }

/* RECAPTCHA
----------------------------------------------------------*/

	// public function display_recaptcha_error() {
	// 	if (isset($_GET['captcha']) && $_GET['captcha'] == 'missing') {
	// 		echo '<div class="errorbox">' . __('ERROR : CAPTCHA should not be empty', 'foodiepro') . '</div>';
	// 	} elseif (isset($_GET['captcha']) && $_GET['captcha'] == 'failed') {
	// 		echo '<div class="errorbox">' . __('ERROR : CAPTCHA response was incorrect', 'foodiepro') . '</div>';
	// 	}
	// }

		// /**
	//  * Verify the captcha answer */
	// public function validate_captcha_field($commentdata)
	// {
	// 	// Check Captcha0
	// 	if (!is_user_logged_in() && class_exists('Custom_Google_Recaptcha') ) {
	// 		$this->captchaError = CGR_Public::verify();
	// 	}
	// 	return $commentdata;
	// }

	// /**
	//  * Add query string to the comment redirect location
	//  *
	//  * @param $location string location to redirect to after comment
	//  * @param $comment object comment object
	//  *
	//  * @return string
	//  */
	// function redirect_fail_captcha_comment($location, $comment)
	// {
	// 	if (!empty($this->captchaError) && ($this->captchaError!='success') ) {
	// 		$args = array('comment-id' => $comment->comment_ID);
	// 		$args['captcha'] = $this->captchaError;
	// 		$location = add_query_arg($args, $location);
	// 	}
	// 	return $location;
	// }

	// /** Delete comment that fail the captcha test. */
	// function delete_failed_captcha_comment()
	// {
	// 	if (isset($_GET['comment-id']) && !empty($_GET['comment-id'])) {
	// 		wp_delete_comment(absint($_GET['comment-id']));
	// 	}
	// }
}
