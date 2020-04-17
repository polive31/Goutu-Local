<?php
/*
Description: Administrator shortcodes for Goutu
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


class CBMP_Post_Content {

	private $action='ManageContent';

	public function batch_manage_post_content_shortcode($atts)
	{
		$a = shortcode_atts(array(
			/* Generic arguments */
			'post-type' => '',
			'post-count'=> '', // max post number to process
			'include' 	=> '', // post ids to include
			'cmd' 		=> 'view', // update
			/* Specific arguments */
			'key' 		=> "/wpurp-searchable-recipe/",
			'search' 	=> "/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/",
			'replace' 	=> '',
		), $atts);

		static $script_id; // allows several shortcodes on the same page
		++$script_id;

		$html = "<h3>BATCH MANAGE POST CONTENT SHORTCODE#" . $script_id . "</h3>";
		$html .= CBMP_Helpers::show_params($a);

		$jsargs = CBMP_Helpers::get_ajax_arg_array($a, $this->action);

		wp_enqueue_script( 'ajax_call_batch_manage' );
		wp_localize_script('ajax_call_batch_manage', 'script' . $this->action . $script_id, $jsargs);

		$html .= CBMP_Helpers::get_submit_button($script_id, $this->action, $a['cmd']);
		return $html;
	}


	public function ajax_batch_manage_content() {
		$cmd = CBMP_Helpers::get_ajax_arg('cmd');
		if ( !(CBMP_Helpers::is_secure( $this->action . $cmd) ) ) exit;

		// Shortcode parameters display
		$post_type = CBMP_Helpers::get_ajax_arg('post-type');
		$post_count = CBMP_Helpers::get_ajax_arg('post-count', '', false);

		$include = CBMP_Helpers::get_ajax_arg('include', __('Limit to posts'));
		$include = ($include=='all')?'':$include;

		$key = CBMP_Helpers::get_ajax_arg('key');
		$search = CBMP_Helpers::get_ajax_arg('search');
		$replace = CBMP_Helpers::get_ajax_arg('replace');

		echo "<p>Batch Manage Post Content script started...</p>";

		if ( $cmd=='update' && empty($include) ) {
			echo "Please provide post IDs or 'all' for deletion to take place";
			die();
		}

		$posts = get_posts(array(
			'post_type'			=> $post_type,
			'numberposts'		=> -1,
			'posts_per_page'	=> -1,
			'include'			=> $include,
			'suppress_filters'  => false,
		));


		$i=1;
		foreach ($posts as $post) {
			if (!empty($post_count) && $i > $post_count) break;

			$content = get_the_content(null, false, $post);
			if ( !preg_match( $key, $content) ) continue;

			switch ($cmd) {
				case 'view':
					echo sprintf("WPURP shortcode found in post #%s : %s", $post->ID, $post->post_title);
					echo "<br>";
				break;

				case 'update':
					echo sprintf("WPURP shortcode found in post %s \n", $post->post_title);
					$content = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content));
					if (empty($content)) {
						$recipe = new CRM_Recipe( $post->ID );
            			$content = $recipe->description();
					}
					$content=wpautop($content);
					//     $post = array(
					//         'ID'            => '',
					//         'post_author'   => '',
					//         'post_type'     => '',
					//         'post_status'   => '',
					//         'post_title'    => '',
					//         'post_content'  => '',
					//     );
					$result = wp_update_post( array(
						'ID'			=> $post->ID,
						'post_content'	=> $content,
					));
					if ($result) {
						echo sprintf("Post update successful, content is now : %s \n", $content);
					}
					else {
						echo 'Post update FAILED';
					}
					echo "<br>";
				break;
			} // End switch
			$i++;

		}

	}

}
