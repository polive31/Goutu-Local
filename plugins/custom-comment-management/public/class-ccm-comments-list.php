<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCM_Comments_List
{

	public function move_comments_form()
	{
		if (is_singular('')) {
			remove_action('genesis_comment_form', 'genesis_do_comment_form');
			add_action('genesis_before_comments', 'genesis_do_comment_form');
		}
	}

	public function custom_comment_text()
	{
		$title = __('Comments', 'genesis');
		return ('<h3>' . $title . '</h3>');
	}

	public function custom_comments_prev_link_text()
	{
		$text = __('Previous comments', 'foodiepro');
		return $text;
	}

	public function custom_comments_next_link_text()
	{
		$text = __('Next comments', 'foodiepro');
		return $text;
	}

	public function add_comments_title_markup($html)
	{
		$html .= '<a id="comments-section"></a>';
		return $html;
	}


	/* Remove the genesis_default_list_comments function
		 Replace comment list with one including ratings
	-------------------------------------------------------*/
	public function custom_genesis_list_comments()
	{
		remove_action('genesis_list_comments', 	'genesis_default_list_comments');
		add_action('genesis_list_comments', 	array($this, 'custom_list_comments'));
	}


	public function custom_list_comments()
	{
		$args = array(
			'type'          		=> 'comment',
			'avatar_size'   		=> 50,
			'callback'      		=> array($this, 'custom_list_comments_callback'),
			// 'style'							=> 'ul',
			// 'reverse_children'	=> false
			//'per_page' 			=> '2',
		);
		$args = apply_filters('genesis_comment_list_args', $args);
		wp_list_comments($args);
	}


	/* Custom Comment Template */
	public function custom_list_comments_callback($comment, $args, $depth)
	{

		$GLOBALS['comment'] = $comment;
?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>" data-author="<?php echo $this->get_comment_author_name($comment) ?>">
			<div class="comment-item">
				<?php do_action('genesis_before_comment'); ?>

				<div class="comment-intro">

					<div class="comment-author">
						<div class="comment-avatar">
							<!-- <?php //echo get_avatar($comment,$size='48');
									?> -->
							<?php
							echo $this->get_comment_author_avatar($comment);
							?>
						</div>
						<?php
						if ($depth == '1')
							printf(__('%s says:', 'foodiepro'), $this->get_comment_author_link($comment));
						else
							printf(__('%s responds:', 'foodiepro'), $this->get_comment_author_link($comment)); ?>
					</div>

					<?php if ($comment->comment_approved == '0') : ?>
						<em><?php _e('Your comment is awaiting moderation.') ?></em>
						<br />
					<?php endif; ?>

				<?php
				/* Comment Meta section, leveraging an existing genesis filter there */

					$comment_date = apply_filters('genesis_show_comment_date', true, get_post_type());

					if ($comment_date) {
					?>

					<div class="comment-meta">
						<a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>">
							<?php printf(__('%1$s at %2$s', 'foodiepro'), get_comment_date(),  get_comment_time()) ?>
						</a>
					</div>

					<?php
					}

				?>

				</div>

				<div class="comment-content">
					<?php comment_text() ?>
				</div>

				<div class="comment-reply">
					<?php
					$args = array_merge($args, array(
						'depth' 	=> $depth,
						'max_depth' => $args['max_depth'],
						// 'respond_id'	=> $this->respondId,
					));
					comment_reply_link($args);
					?>
				</div>

				<div class="comment-edit">
					<?php edit_comment_link(__('(Edit)'), '  ', '') ?>
				</div>

				<?php do_action('genesis_after_comment'); ?>

			</div>
			<!-- Pas </li> pour commentaires imbriqu�s -->

	<?php
	}


	/* Change the comment reply link to display our own comment form */
	//add_filter('comment_reply_link', 'remove_nofollow', 420, 4);
	public function remove_nofollow($link, $args, $comment, $post)
	{
		return str_replace("rel='nofollow'", "", $link);
	}


	public function get_comment_author_link($comment)
	{
		if (!empty($comment->user_id)) {
			$user = PeepsoHelpers::get_user($comment->user_id);
			$html = '<a href="' . PeepsoHelpers::get_url($user, 'profile') . '">' . ucfirst(PeepsoHelpers::get_field($user, 'nicename')) . '</a>';
		} else {
			$html = $comment->comment_author;
		}
		return $html;
	}

	public function get_comment_author_name($comment)
	{
		if (!empty($comment->user_id)) {
			$user = PeepsoHelpers::get_user($comment->user_id);
			$html = PeepsoHelpers::get_field($user, 'pseudo');
		} else {
			$html = $comment->comment_author;
		}
		return $html;
	}

	public function get_comment_author_avatar($comment)
	{
		if (!empty($comment->user_id)) {
			$user = $comment->user_id;
			$size = 'small';
			$html = PeepsoHelpers::get_avatar(array(
				'user' => $user,
				'size' => $size
			));
		} else {
			// $html = get_avatar($comment->comment_author_email, $size = '48', CHILD_THEME_URL . '/images/social/avatars/user-neutral-thumb.png', $comment->comment_author);
			// if (!$html) {
				$html = '<img class="avatar" src="' . CHILD_THEME_URL . '/images/social/avatars/user-neutral-thumb.png' . '" width="48" height="48">';
			// }
		}
		return $html;
	}

}
