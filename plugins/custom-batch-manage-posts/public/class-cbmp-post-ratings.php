<?php
/*
Description: Administrator shortcodes for Goutu
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

class CBMP_Post_Ratings {

	/* Batch update user_ratings_ratings custom field */
	public function batch_migrate_ratings_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'post-type' => 'recipe',
			'include' => '',
		), $atts);
		$a['cmd'] = 'migrate';

		static $script_id; // allows several shortcodes on the same page
		++$script_id;

		$script_name = 'MigrateRatings';

		echo "<h3>BATCH MIGRATE RATINGS SHORTCODE#" . $script_id . "</h3>";

		$jsargs = CBMP_Helpers::create_ajax_arg_array($a, $script_name, $script_id);

		wp_enqueue_script('ajax_call_batch_manage');
		wp_localize_script('ajax_call_batch_manage', 'script' . $script_name . $script_id, $jsargs);

		echo CBMP_Helpers::batch_manage_form($script_id, $script_name, $a['cmd']);
	}

	public function ajax_migrate_ratings() {

		// PC::debug( array('In AJAX MIGRATE RATINGS') );
		echo "<p>Batch Migrate Ratings script started...</p>";

		$post_type = CBMP_Helpers::get_ajax_arg('post-type');
		$include = CBMP_Helpers::get_ajax_arg('include',__('Limit to posts','batch-manage-posts'));

		if ( !(CBMP_Helpers::is_secure('MigrateRatings' . 'migrate') ) ) exit;


		// PC::debug( array('Nonce check PASSED') );
		//PC:debug( array('$value after explode : '=>$value) );


		//$post_type_object = get_post_type_object($post_type);
		//$label = $post_type_object->label;

		//	$cats = CustomStarRatings::getRatingCats( true );
		//	PC::debug( array('$cats : '=> $cats) );

		$include = ($include=='all')?'':$include;

		$posts = get_posts(array('include'=>$include, 'post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

		foreach ($posts as $post) {

			$user_ratings = get_post_meta($post->ID, 'recipe_user_ratings', false);
			//if ( empty($user_rating) && empty($user_ratings) ) continue;
			if ( !empty($user_ratings) ) {
				delete_post_meta($post->ID, 'user_ratings', '');

				// PC::debug( array('$Post title : '=> $post->post_title ) );
				// PC::debug( array('$user_ratings : '=> $user_ratings) );

				echo sprintf("Post : %s",$post->post_title);
				echo "<br>";
				print_r($user_ratings,false );
				echo "<br>";
				echo "----------------------------";
				echo "<br>";

				$rating_global = 0;

				foreach ( $user_ratings as $user_rating ) {
					$ip = $user_rating['ip'];
					$user = $user_rating['user'];
					$rating = $user_rating['rating'];
					add_post_meta($post->ID, 'user_ratings', $user_rating);
					PC::debug( array('$user_rating : '=> $user_rating) );
					$rating_global += $rating;
				}

				PC::debug( array('$rating_global : '=> $rating_global ) );
				PC::debug( array('count : '=> count($user_ratings) ) );
				$rating_global = $rating_global/count($user_ratings);
				update_post_meta($post->ID, 'user_rating_rating', $rating_global);
				update_post_meta($post->ID, 'user_rating_global', $rating_global);

				echo sprintf("APRES MIGRATION : %s",$post->post_title);
				echo "<br>";
				$user_ratings = get_post_meta($post->ID, 'user_ratings', false);
				print_r($user_ratings,false );
				echo "<br>";
				$user_rating = get_post_meta($post->ID, 'user_rating_rating', true);
				echo sprintf("user_rating_rating : %s", $user_rating );
				echo "<br>";
				$user_rating = get_post_meta($post->ID, 'user_rating_global', true);
				echo sprintf("user_rating_global : %s", $user_rating );
				echo "<br>";
				echo "----------------------------";
				echo "<br>";


			}

			else {
				add_post_meta($post->ID, 'user_rating_global', '0');

			}


		} // End posts loop
	}

}
