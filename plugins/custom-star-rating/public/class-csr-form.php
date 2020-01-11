<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CSR_Form
{

	protected static $_PluginPath;
	protected static $_PluginUri;

	public function __construct()
	{
		self::$_PluginUri = plugin_dir_url(dirname(__FILE__));
		self::$_PluginPath = plugin_dir_path(dirname(__FILE__));
	}


	/* SHORTCODES
	-----------------------------------------------*/
	public function get_comment_form_with_rating_shortcode()
	{
		$comment_notes = is_user_logged_in() ? '' : '<p class="comment-notes">' . __('Your name and mail address are required for authentification of your comment.<br>Your mail address will not be published.', 'foodiepro') . '</p>';

		$args = array(
			'comment_notes_before' 	=> '',
			'comment_notes_after' 	=> $comment_notes,
			'title_reply' 			=> '',
			'label_submit' 			=> __('Send', 'foodiepro'), //default=�Post Comment�
			'comment_field' 		=> $this->output_eval_form_input_fields(),
			'logged_in_as' 			=> '', //Default: __( 'Leave a Reply to %s� )
			'rating_cats' 			=> 'all',  //Default: "id1 id2..."
		);

		wp_enqueue_style('custom-star-rating');
		wp_enqueue_script('custom-star-rating');

		/* The customized comment form (to be used in a popup or separate from the post content) is stripped out of any "reply" features,
			in order not to conflict with the comment form in the  main section of the post */

		ob_start();
		$this->custom_eval_comment_form($args);
		$rating_form = ob_get_contents();
		ob_end_clean();

		return $rating_form;
	}

	/* Rating Form
	------------------------------------------------------------ */
	public function output_eval_form_input_fields()
	{

		ob_start(); ?>

		<div>
			<table class="ratings-table">
				<?php
						foreach (CSR_Assets::rating_cats() as $id => $cat) { ?>

					<tr>
						<td align="left" class="rating-title">
							<div class="rating-wrapper">
								<?= $cat['question']; ?>
							</div>
						</td>
						<td align="left"><?= $this->get_category_rating($id); ?></td>
					</tr>

				<?php
						} ?>
			</table>
		</div>

		<div class="comment-reply">
			<label for="comment"><?= __('Provide details', 'foodiepro'); ?></label>
			<textarea id="comment" name="comment" cols="50" rows="4" aria-required="true"></textarea>
		</div>

<?php
		$fields = ob_get_contents();
		ob_end_clean();

		return $fields;
	}

	public function get_category_rating($id)
	{
		$html = '<div class="rating-wrapper" id="star-rating-form">';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
		$html .= '<label for="rating-input-' . $id . '-5" class="rating-star" title="' . CSR_Assets::get_rating_caption(5, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html .= '<label for="rating-input-' . $id . '-4" class="rating-star" title="' . CSR_Assets::get_rating_caption(4, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html .= '<label for="rating-input-' . $id . '-3" class="rating-star" title="' . CSR_Assets::get_rating_caption(3, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html .= '<label for="rating-input-' . $id . '-2" class="rating-star" title="' . CSR_Assets::get_rating_caption(2, $id) . '"></label>';
		$html .= '<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html .= '<label for="rating-input-' . $id . '-1" class="rating-star" title="' . CSR_Assets::get_rating_caption(1, $id) . '"></label>';
		$html .= '</div>';

		return $html;
	}

	public function custom_eval_comment_form($args) {
		/* comment_form() function modified with :
			- <div> wrapper id is changed to "custom_respond" instead of "respond" in order to be "invisible" to comment-reply.js script
			- cancel button removed (in order not to conflict with the main form in the comments list )
			- comment_parent div removed below reply button (in order not to conflict with the main form in the comments list )
			*/

		if ( empty($post_id) ) {
			$post_id = get_the_ID();
		}

		// Exit the function when comments for the post are closed.
		if ( ! comments_open( $post_id ) ) {
			/**
			 * Fires after the comment form if comments are closed.
			 *
			 * @since 3.0.0
			 */
			do_action( 'comment_form_comments_closed' );

			return;
		}

		$commenter     = wp_get_current_commenter();
		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = wp_parse_args( $args );
		if ( ! isset( $args['format'] ) ) {
			$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
		}

		$req      = get_option( 'require_name_email' );
		$html_req = ( $req ? " required='required'" : '' );
		$html5    = 'html5' === $args['format'];
		$fields   = array(
			'author' => '<p class="comment-form-author">' . '<label for="author"' . ( $req ? ' class="requiredField"' : '' ) . '>' . __( 'Name' ) . '</label> ' .
			'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $html_req . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email"' . ($req ? ' class="requiredField"' : '') . '>' . __( 'Email' ) . '</label> ' .
			'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $html_req . ' /></p>',
			'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label> ' .
			'<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" /></p>',
		);

		if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
			$consent           = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
			$fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .
			'<label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.' ) . '</label></p>';

			// Ensure that the passed fields include cookies consent.
			if ( isset( $args['fields'] ) && ! isset( $args['fields']['cookies'] ) ) {
				$args['fields']['cookies'] = $fields['cookies'];
			}
		}

		$required_text = sprintf( ' ' . __( 'Required fields are marked %s' ), '<span class="required">*</span>' );

		/**
		 * Filters the default comment form fields.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $fields Array of the default comment fields.
		 */
		$fields   = apply_filters( 'comment_form_default_fields', $fields );
		$defaults = array(
			'fields'               => $fields,
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea></p>',
			/** This filter is documented in wp-includes/link-template.php */
			'must_log_in'          => '<p class="must-log-in">' . sprintf(
				/* translators: %s: login URL */
				__( 'You must be <a href="%s">logged in</a> to post a comment.' ),
				wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
				) . '</p>',
				/** This filter is documented in wp-includes/link-template.php */
				'logged_in_as'         => '<p class="logged-in-as">' . sprintf(
					/* translators: 1: edit user link, 2: accessibility text, 3: user name, 4: logout URL */
					__( '<a href="%1$s" aria-label="%2$s">Logged in as %3$s</a>. <a href="%4$s">Log out?</a>' ),
					get_edit_user_link(),
					/* translators: %s: user name */
					esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.' ), $user_identity ) ),
					$user_identity,
					wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
					) . '</p>',
					'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
					'comment_notes_after'  => '',
					'action'               => site_url( '/wp-comments-post.php' ),
					'id_form'              => 'commentform',
					'id_submit'            => 'submit',
					'class_form'           => 'comment-form',
					'class_submit'         => 'submit',
					'name_submit'          => 'submit',
					'title_reply'          => __( 'Leave a Reply' ),
					'title_reply_before'   => '<h3 id="rating-form-reply-title" class="rating-form-reply-title">',
					'title_reply_after'    => '</h3>',
					'label_submit'         => __( 'Post Comment' ),
					'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
					'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
					'format'               => 'xhtml',
				);

				/**
				 * Filters the comment form default arguments.
				 *
				 * Use {@see 'comment_form_default_fields'} to filter the comment fields.
				 *
				 * @since 3.0.0
				 *
				 * @param array $defaults The default comment form arguments.
				 */
		$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

		// Ensure that the filtered args contain all required default values.
		$args = array_merge( $defaults, $args );

		/**
		 * Fires before the comment form.
		 *
		 * @since 3.0.0
		 */
		do_action( 'comment_form_before' );
		?>
		<div id="custom_respond" class="comment-respond">
			<?php
			// echo $args['title_reply_before'];

			// comment_form_title( $args['title_reply'], $args['title_reply_to'] );

			// echo $args['cancel_reply_before'];

			// cancel_comment_reply_link( $args['cancel_reply_link'] );

			// echo $args['cancel_reply_after'];

			// echo $args['title_reply_after'];

			if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) :
				echo $args['must_log_in'];
				/**
				 * Fires after the HTML-formatted 'must log in after' message in the comment form.
				 *
				 * @since 3.0.0
				 */
				do_action( 'comment_form_must_log_in_after' );
				else :
					?>
				<form action="<?php echo esc_url( $args['action'] ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>" class="<?php echo esc_attr( $args['class_form'] ); ?>"<?php echo $html5 ? ' novalidate' : ''; ?>>
				<?php
					/**
					 * Fires at the top of the comment form, inside the form tag.
					 *
					 * @since 3.0.0
					 */
					do_action( 'comment_form_top' );

					if ( is_user_logged_in() ) :
						/**
						 * Filters the 'logged in' message for the comment form for display.
						 *
						 * @since 3.0.0
						 *
						 * @param string $args_logged_in The logged-in-as HTML-formatted message.
						 * @param array  $commenter      An array containing the comment author's
						 *                               username, email, and URL.
						 * @param string $user_identity  If the commenter is a registered user,
						 *                               the display name, blank otherwise.
						 */
						echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity );

						/**
						 * Fires after the is_user_logged_in() check in the comment form.
						 *
						 * @since 3.0.0
						 *
						 * @param array  $commenter     An array containing the comment author's
						 *                              username, email, and URL.
						 * @param string $user_identity If the commenter is a registered user,
						 *                              the display name, blank otherwise.
						 */
						do_action( 'comment_form_logged_in_after', $commenter, $user_identity );

						else :

							echo $args['comment_notes_before'];

						endif;

						// Prepare an array of all fields, including the textarea
						$comment_fields = array( 'comment' => $args['comment_field'] ) + (array) $args['fields'];

						/**
						 * Filters the comment form fields, including the textarea.
						 *
						 * @since 4.4.0
						 *
						 * @param array $comment_fields The comment fields.
						 */
						$comment_fields = apply_filters( 'comment_form_fields', $comment_fields );

						// Get an array of field names, excluding the textarea
						$comment_field_keys = array_diff( array_keys( $comment_fields ), array( 'comment' ) );

						// Get the first and the last field name, excluding the textarea
						$first_field = reset( $comment_field_keys );
						$last_field  = end( $comment_field_keys );

						foreach ( $comment_fields as $name => $field ) {

							if ( 'comment' === $name ) {

								/**
								 * Filters the content of the comment textarea field for display.
								 *
								 * @since 3.0.0
								 *
								 * @param string $args_comment_field The content of the comment textarea field.
								 */
								echo apply_filters( 'comment_form_field_comment', $field );

								echo $args['comment_notes_after'];

							} elseif ( ! is_user_logged_in() ) {

								if ( $first_field === $name ) {
									/**
									 * Fires before the comment fields in the comment form, excluding the textarea.
									 *
									 * @since 3.0.0
									 */
									do_action( 'comment_form_before_fields' );
								}

								/**
								 * Filters a comment form field for display.
								 *
								 * The dynamic portion of the filter hook, `$name`, refers to the name
								 * of the comment form field. Such as 'author', 'email', or 'url'.
								 *
								 * @since 3.0.0
								 *
								 * @param string $field The HTML-formatted output of the comment form field.
								 */
								echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";

								if ( $last_field === $name ) {
									/**
									 * Fires after the comment fields in the comment form, excluding the textarea.
									 *
									 * @since 3.0.0
								 */
								do_action( 'comment_form_after_fields' );
							}
						}
					}

					$submit_button = sprintf(
						$args['submit_button'],
						esc_attr( $args['name_submit'] ),
						esc_attr( $args['id_submit'] ),
						esc_attr( $args['class_submit'] ),
						esc_attr( $args['label_submit'] )
					);

					/**
					 * Filters the submit button for the comment form to display.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_button HTML markup for the submit button.
					 * @param array  $args          Arguments passed to comment_form().
					 */
					$submit_button = apply_filters( 'comment_form_submit_button', $submit_button, $args );

					$submit_field = sprintf(
						$args['submit_field'],
						$submit_button,
						"<input type='hidden' name='comment_post_ID' value='$post_id' id='comment_post_ID' />\n"
					);

					/**
					 * Filters the submit field for the comment form to display.
					 *
					 * The submit field includes the submit button, hidden fields for the
					 * comment form, and any wrapper markup.
					 *
					 * @since 4.2.0
					 *
					 * @param string $submit_field HTML markup for the submit field.
					 * @param array  $args         Arguments passed to comment_form().
					 */
					echo apply_filters( 'comment_form_submit_field', $submit_field, $args );

					/**
					 * Fires at the bottom of the comment form, inside the closing </form> tag.
					 *
					 * @since 1.5.0
					 *
					 * @param int $post_id The post ID.
					 */
					do_action( 'comment_form', $post_id );
					?>
				</form>
				<?php endif; ?>
			</div><!-- #respond -->
			<?php

	/**
	 * Fires after the comment form.
	 *
	 * @since 3.0.0
	 */
	do_action( 'comment_form_after' );

	}

}
