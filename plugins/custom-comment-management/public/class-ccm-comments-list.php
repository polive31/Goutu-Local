<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class CCM_Comments_List
{

	public function remove_comment_link() {
		return '';
	}

	public function move_comments_form()
	{
		if (is_singular('')) {
			remove_action('genesis_comment_form', 'genesis_do_comment_form');
			add_action('genesis_before_comments', 'genesis_do_comment_form');
		}
	}

	public function add_comment_author_link( $author, $comment_id, $comment ) {
		if ($comment->user_id!=0) {
			$author = foodiepro_get_permalink( array(
				'user'		=> $comment->user_id,
				'display'	=> 'profile',
				'text'		=> ucfirst($author),
			));
		}
		return $author;
	}

	public function custom_comment_author_says()
	{
		return '';
	}

	public function custom_comment_date( $comment_date ) {
		$time = sprintf( __('%1$s at %2$s', 'genesis'), get_comment_date(), get_comment_time());
		printf(
			'<div %s><time %s>%s</time></div>',
			genesis_attr('comment-meta'),
			genesis_attr('comment-time'),
			$time
		);
		// Return false so that the parent function doesn't output the comment date, time and link
		return false;
	}

	public function custom_comment_author_link($content, $args) {
		$html = $content;
		return $html;
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


	// public function get_comment_author_link($comment)
	// {
	// 	if (!empty($comment->user_id)) {
	// 		$user = PeepsoHelpers::get_user($comment->user_id);
	// 		$html = '<a href="' . foodiepro_get_permalink(array('current'=> $comment->user_id, 'display'=>'profile')) . '">' . ucfirst(PeepsoHelpers::get_field($user, 'nicename')) . '</a>';
	// 	} else {
	// 		$html = $comment->comment_author;
	// 	}
	// 	return $html;
	// }

	// public function get_comment_author_name($comment)
	// {
	// 	if (!empty($comment->user_id)) {
	// 		$user = PeepsoHelpers::get_user($comment->user_id);
	// 		$html = PeepsoHelpers::get_field($user, 'pseudo');
	// 	} else {
	// 		$html = $comment->comment_author;
	// 	}
	// 	return $html;
	// }


	// public function get_comment_author_avatar($comment)
	// {
	// 	if (!empty($comment->user_id)) {
	// 		$user = $comment->user_id;
	// 		$size = 'small';
	// 		$html = PeepsoHelpers::get_avatar(array(
	// 			'user' => $user,
	// 			'size' => $size
	// 		));
	// 	} else {
	// 		// $html = get_avatar($comment->comment_author_email, $size = '48', CHILD_THEME_URL . '/images/social/avatars/user-neutral-thumb.png', $comment->comment_author);
	// 		// if (!$html) {
	// 			$html = '<img class="avatar" src="' . CHILD_THEME_URL . '/images/social/avatars/user-neutral-thumb.png' . '" width="48" height="48">';
	// 		// }
	// 	}
	// 	return $html;
	// }

}
