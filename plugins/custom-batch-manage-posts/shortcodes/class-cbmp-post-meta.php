<?php
/*
Description: Administrator shortcodes for Goutu
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


class CBMP_Post_Meta {

	private $action = 'ManageMeta';

	/* Batch update user_ratings_ratings custom field */
	public function batch_manage_meta_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'post-type' => '',
			'key' 		=> '',
			'new-key' 	=> '',
			'include' 	=> '', // tax term ids to include
			'post-count'=> '', // max post number to process
			'cmd' 		=> 'read', //add, update, delete, rename
		), $atts);

		static $script_id; // allows several shortcodes on the same page
		++$script_id;

		$html = "<h3>BATCH MANAGE META SHORTCODE#" . $script_id . "</h3>";
		$html .= CBMP_Helpers::show_params($a);

		$jsargs = CBMP_Helpers::get_ajax_arg_array($a, $this->action);

		wp_enqueue_script('ajax_call_batch_manage');
		wp_localize_script('ajax_call_batch_manage', 'script' . $this->action . $script_id, $jsargs);

		$html .= CBMP_Helpers::get_submit_button($script_id, $this->action, $a['cmd']);
		return $html;
	}


	public function ajax_batch_manage_meta() {
		$cmd = CBMP_Helpers::get_ajax_arg('cmd');
		if ( !(CBMP_Helpers::is_secure($this->action . $cmd) ) ) exit;


		$key = CBMP_Helpers::get_ajax_arg('key');
		$new_key = CBMP_Helpers::get_ajax_arg('new-key');
		$post_type = CBMP_Helpers::get_ajax_arg('post-type');
		$post_count = CBMP_Helpers::get_ajax_arg('post-count');
		if (!$post_count) $post_count='';
		$include = CBMP_Helpers::get_ajax_arg('include',__('Limit to posts'));
		$value = CBMP_Helpers::get_ajax_arg('value');


		if ( is_array($value) )
			$value = CBMP_Helpers::extractKeyValuePairs( $value );
		// else
		// 	$value = $a['value'];

		//PC:debug( array('$value after explode : '=>$value) );

		echo "<p>Batch Manage Meta script started...</p>";

		//$post_type_object = get_post_type_object($post_type);
		//$label = $post_type_object->label;

		if ( $cmd=='delete' && empty($include) ) {
			echo "Please provide post IDs or 'all' for deletion to take place";
			return '';
		}
		$include = ($include=='all')?'':$include;

		$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'posts_per_page' => $post_count, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

		foreach ($posts as $post) {

			$meta_value = get_post_meta($post->ID, $key, True);
			switch ($cmd) {
				case 'add':
					if ( empty($meta_value) ) add_post_meta($post->ID, $key, $value);
					echo sprintf("%s=%s added to %s",$key,$value,$post->post_title) . "<br>"; //Prints updated after ran.
					break;
				case 'rename':
					if ( ! empty($meta_value) ) {
						update_post_meta($post->ID, $new_key, $meta_value);
						delete_post_meta($post->ID, $key);
						echo sprintf("%s renamed to %s in %s",$key,$new_key,$post->post_title) . "<br>"; //Prints updated after ran.
					}
					else {
						update_post_meta($post->ID, $new_key, '0');
						echo sprintf("Key %s not found in %s. Updated %s to '0'.",$key, $post->post_title, $new_key);
					}
						$new_value = get_post_meta($post->ID, $new_key, True);
						// PC::debug(array('Value for renamed key' => $new_value));
					break;
				case 'update':
					update_post_meta($post->ID, $key, $value);
					echo sprintf("%s updated to %s in %s",$key,$value,$post->post_title) . "<br>"; //Prints updated after ran.
					break;
				case 'addcount':
					$liking_users = is_array($meta_value)?count($meta_value):0;
					echo sprintf("%s liking users in %s", $liking_users, $post->post_title) . "<br>"; //Prints updated after ran.
					add_post_meta($post->ID, 'like_count', $liking_users, true);
					break;
				case 'delete':
					echo sprintf("%s value for post %s : ",$key,$post->post_title); //Prints updated after ran.
					print_r($meta_value);
					echo "<br>";
					if ( ! empty($meta_value) ) {
						delete_post_meta($post->ID, $key);
						$meta_value = get_post_meta($post->ID, $key, True);
						echo sprintf("%s key value after deletion : %s",$key,$meta_value) . "<br>"; //Prints updated after ran.
					}
					break;
				case 'read':
					echo sprintf("%s value for post %s :",$key,$post->post_title); //Prints updated after ran.
					print_r($meta_value);
					echo "<br>";
					break;
			}
		}// End post loop

	}

}
