<?php
/*
Description: Batch Delete Comments
Author: Pascal Olive
Author URI: http://goutu.org
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class CBMP_Comments {

	private $action = 'DeleteComment';

	/* Batch update user_ratings_ratings custom field */
	public function batch_delete_comments_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'post-type' => 'recipe',
			'include' => '', // Post ids list, separated by commas
		), $atts);
		$a['cmd'] = 'delete';

		static $script_id; // allows several shortcodes on the same page
		++$script_id;

		$html = "<h3>BATCH DELETE COMMENTS SHORTCODE#" . $script_id . "</h3>";
		$html .= CBMP_Helpers::show_params($a);

		$jsargs = CBMP_Helpers::get_ajax_arg_array($a, $this->action);

		wp_enqueue_script('ajax_call_batch_manage');
		wp_localize_script('ajax_call_batch_manage', 'script' . $this->action . $script_id, $jsargs);

		$html .= CBMP_Helpers::get_submit_button($script_id, $this->action, 'delete');
		return $html;
	}


	public function ajax_batch_delete_comments() {
		$cmd=CBMP_Helpers::get_ajax_arg('cmd');
		if ( !(CBMP_Helpers::is_secure( $this->action . $cmd) ) ) exit;

		echo '<p>In Batch Delete Comments function...</p>';

		$post_type=	CBMP_Helpers::get_ajax_arg('post-type');
		$include=	CBMP_Helpers::get_ajax_arg('include');


		$deleted_count='0';

		if ( ! empty($include) ) {
			$include = ($include=='all')?'':$include;
			$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));
			foreach ($posts as $post) {
				$comments = get_comments( array('post_id'=>$post->ID ) );
				echo sprintf(__('Post %s contains %d comments'), $post->post_title, count($comments) );
				echo "<br>";
				if ( ! empty( $comments ) ) {
					foreach ($comments as $comment) {
						$deleted = wp_delete_comment( $comment->comment_ID );
						if ( $deleted ) {
							++$deleted_count;
						}
					}
				}

			}

		}

		else {
			echo "Please provide post IDs or 'all' for deletion to take place";
		}

		echo sprintf('Delete comments operation completed, %s comments deleted',$deleted_count);
		echo "<br>";

	}

}
