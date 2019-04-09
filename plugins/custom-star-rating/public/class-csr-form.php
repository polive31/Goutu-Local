<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CSR_Form {

	/* CALLBACKS
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
		$defaults['id_form'] = 'foodiepro_comment' . $instance;
		$defaults['title_reply_to'] = __('Your answer here','foodiepro');
		$defaults['comment_field'] = '<p class="comment-form-comment"><textarea id="comment_' . $instance . '" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

		$instance++;
		return $defaults;
	}

	public function add_comment_recaptcha( $submit_button, $args ) {
		static $instance=0;
		$submit_button='';
		// if ( false ) {
		if ( !is_user_logged_in() && class_exists( 'CustomGoogleRecaptcha' ) ) {
			$key = CustomGoogleRecaptcha::v3key();
			$submit_button = '<div id="recaptcha" class="g-recaptcha" data-sitekey="' . $key . '" data-callback="csrOnSubmit" data-size="invisible"></div>';
			// $submit_button = '<button name="submit" id="submit" data-instance="' . $instance . '" class="' . $class . '" data-sitekey="' . $key . '" data-callback="' . $callback . '">' . __('Submit','foodiepro') . '</button>';
		}
		// else {
		// $submit_button .= '<input name="submit" type="submit" id="submit" data-loggedin="' . is_user_logged_in() . '" data-instance="' . $instance . '" class="submit" value="' . __('Submit','foodiepro') . '">';
		$submit_button .= '<input name="submit' . $instance . '" type="submit" id="submit' . $instance . '" data-instance="' . $instance . '" class="submit" value="' . __('Submit','foodiepro') . '">';
		// }
		$instance++;
		return $submit_button;
	}


	/* SHORTCODES
	-----------------------------------------------*/

	public function display_comment_form_with_rating_shortcode() {
		$comment_notes = is_user_logged_in()?'':'<p class="comment-notes">' . __('Your name and mail address are required for authentification of your comment.<br>Your mail address will not be published.', 'foodiepro') . '</p>';

		$args = array (
			'comment_notes_before' => '',
			'comment_notes_after' => $comment_notes,
			'title_reply' => '', //Default: __( 'Leave a Reply� )
			'label_submit' => __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
			'comment_field' => $this->output_evaluation_form(),
			'logged_in_as' => '', //Default: __( 'Leave a Reply to %s� )
			'title_reply_to' => __( 'Leave a Reply to %s', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
			'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ), //Default: __( �Cancel reply� )
			'rating_cats' => 'all',  //Default: "id1 id2..."
		);

		wp_enqueue_style('custom-star-rating');
		wp_enqueue_script('custom-star-rating');
		wp_enqueue_script('grecaptcha-invisible', 'https://www.google.com/recaptcha/api.js');

		ob_start();
		//display_rating_form();
		comment_form($args);
		$cr_form = ob_get_contents();
		ob_end_clean();
		return $cr_form;
	}




	/* Custom Comment Form
	------------------------------------------------------------ */
	public function output_evaluation_form() {

		ob_start();?>

		<div>
			<table class="ratings-table">
			<?php
			foreach (CSR_Assets::rating_cats() as $id => $cat) {?>

			<tr>
				<td align="left" class="rating-title"><?= $cat['question'];?></td>
				<td align="left"><?= $this->output_rating_form( $id );?></td>
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

	public function output_rating_form( $id ) {
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
}
