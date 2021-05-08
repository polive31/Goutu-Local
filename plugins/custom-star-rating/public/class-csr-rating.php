<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


class CSR_Rating
{

	const COMMENT_RATING_META = 'comment_rating_';
	const POST_RATING_META = 'user_rating_';
	const POST_VOTES_META = 'user_votes_';


	/* ===========================================================================
	/* ===================         META UPDATE           =========================
	/* ===========================================================================

	/* Post rating meta structure :
		'user_ratings' = array(
							'user' => user ID (0 if unregistered user)
							'ip' => user IP address
							'cat1' => rating for category "cat1"
							...
							'catN' => rating for category "catN"
						)

		'user_rating_cat1' = val1
		...
		'user_rating_catN' = valN
		'user_rating_global' = val
	*/

	/* Comment rating meta structure :
		'user_rating_cat1' = val1
		...
		'user_rating_catN' = valN
	*/


	/* CALLBACKS
-------------------------------------------------------------------------*/

	/**
	 * New post submission callback
	 * Add ratings default value (required for proper sorting in archives)
	 * IMPORTANT : hook activated during post submission, therefore the main Custom_Star_Rating class
	 * is not created and a new CSR_Assets instance needs to be created in order
	 * to trigger the rating cats hydrate
	 *
	 * @param  mixed $post_ID
	 * @return void
	 */
	public function add_default_rating($post_ID)
	{
		$Assets = new CSR_Assets();

		$has_rating = !empty( get_post_meta( $post_ID, self::POST_RATING_META . 'global', true) );

		$ratedTypes = $Assets->post_types();
		$is_rated_type = in_array(get_post_type($post_ID), $ratedTypes);

		$stats=array();

		if ( $is_rated_type && !$has_rating) {
			foreach ($Assets->rating_cats() as $cat => $value) {
				$rating = foodiepro_rand(4, 5, 1);
				$votes = foodiepro_rand(5, 10, 0);

				$stats[$cat]['rating']=$rating;
				$stats[$cat]['votes']=$votes;

				update_post_meta($post_ID, self::POST_RATING_META . $cat, $rating);
				update_post_meta($post_ID, self::POST_VOTES_META . $cat, $votes);
			}

			$global=self::compute_global_stats( $stats );
			update_post_meta($post_ID, self::POST_RATING_META . 'global', $global['rating']);
			update_post_meta($post_ID, self::POST_VOTES_META . 'global', $global['votes']);
		}

	}

	/**
	 * Callback for new comment insertion
	 * * Adds comment meta with user rating
	 * * Calls transition comment status cb if the comment is directly approved
	 *
	 * @param  mixed $comment_id
	 * @param  mixed $comment_approved
	 * @param  mixed $comment
	 * @return void
	 */
	public function comment_post_cb($comment_id, $comment_approved, $comment)
	{
		$rating = $this->update_comment_meta_user_rating($comment_id);
		$comment['comment_ID'] = $comment_id;
		if ($comment_approved === 1 && !empty($rating)) {
			$this->transition_comment_status_cb('approved', 'new', $comment);
		}
	}

	/**
	 * Callback for comment status update and also used for new comment insertion
	 * Depending on the origin, the $comment variable will have a different format
	 * * Callback => $comment is a WP_Comment object
	 * * New comment (called from comment_post_cb) => $comment is an array
	 *
	 * @param  mixed $new_status
	 * @param  mixed $old_status
	 * @param  mixed $comment
	 * @return void
	 */
	public function transition_comment_status_cb($new_status, $old_status, $comment)
	{
		if (is_array($comment)) {
			$comment_id = $comment['comment_ID']; // this array element was added in comment_post_cb
			$post_id = $comment['comment_post_ID'];
		} elseif (is_object($comment)) {
			$comment_id = $comment->comment_ID;
			$post_id = $comment->comment_post_ID;
		} else
			return false;

		if ($new_status == 'approved') {
			/* Update post meta with the new rating values per category */
			$this->update_post_meta_user_rating($post_id, $comment_id);
		} elseif ($new_status == 'trash') {
			$this->delete_comment_meta_user_rating($comment_id);
			$this->update_post_meta_user_rating($post_id, $comment_id);
		} else { // spam, unapproved
			$this->update_post_meta_user_rating($post_id, $comment_id);
		}
	}

	public function display_rating_in_comment()
	{
		echo self::render('comment',false);
	}


	/* POST META MANAGEMENT
-------------------------------------------------------------------------*/

	/**
	 * update_comment_meta_user_rating
	 *
	 * @param  mixed $comment_id
	 * @return void
	 */
	public function update_comment_meta_user_rating($comment_id)
	{
		$comment_rating = array();

		foreach (CSR_Assets::rating_cats() as $cat => $values) {
			if (isset($_POST['rating-' . $cat])) {
				$rating_form_value = $_POST['rating-' . $cat];
				//otherwise let the cell empty, important for stats function
				add_comment_meta($comment_id, self::COMMENT_RATING_META . $cat, $rating_form_value);
				$comment_rating[$cat] = $rating_form_value;
			}
		}
		return $comment_rating;
	}

	public function delete_comment_meta_user_rating($comment_id)
	{
		$Assets = new CSR_Assets();

		foreach ($Assets->rating_cats() as $cat => $values) {
			delete_comment_meta($comment_id, self::COMMENT_RATING_META . $cat);
		}
	}


	/**
	 * Update "user_rating" post meta for each rating category
	 *
	 * @param  mixed $post_id
	 * @return void
	 */
	public function update_post_meta_user_rating($post_id, $comment_id)
	{
		$stats = array();

		foreach (CSR_Assets::rating_cats() as $cat => $values) {
			$comment_rating = get_comment_meta($comment_id, self::COMMENT_RATING_META . $cat, true);
			if (empty($comment_rating))
				continue; // Jump to next category

			$stats[$cat] = self::get_post_stats($post_id, $cat);
			extract($stats[$cat]);

			$rating = round(($rating * $votes + $comment_rating) / ($votes + 1), 2);
			$votes++;

			$stats[$cat]['rating'] = $rating;
			$stats[$cat]['votes'] = $votes;

			update_post_meta($post_id, self::POST_RATING_META . $cat, $rating);
			update_post_meta($post_id, self::POST_VOTES_META . $cat, $votes);
		}

		$global_stats = self::compute_global_stats($stats);
		update_post_meta($post_id, self::POST_RATING_META . 'global', $global_stats['rating']);
		update_post_meta($post_id, self::POST_VOTES_META . 'global', $global_stats['votes']);
	}



	/* GETTERS / SETTERS
-------------------------------------------------------------------------*/

	/**
	 * Comptute global stats based on array of ratings & votes
	 * For robustness purposes, we don't take into account the CSR Rating cats, only the content of $stats
	 *
	 * @param  mixed $stats
	 * @return array $global_stats :
	 * * 'rating'=>float
	 * * 'votes'=>int
	 */
	public static function compute_global_stats($stats)
	{
		$rating = 0;
		$votes = 0;
		$count = 0;
		foreach ($stats as $cat => $values) {
			$params = CSR_Assets::rating_cats($cat);
			$weight= $params?$params['weight']:1;

			$rating += $stats[$cat]['rating'] * $stats[$cat]['votes'] * $params['weight'];
			$votes += $stats[$cat]['votes'];
			$count += $stats[$cat]['votes'] * $params['weight'];
		}
		$global_stats = array(
			'rating'	=> $count?round($rating / $count, 2):0,
			'votes'		=> $votes
		);
		return $global_stats;
	}


	/**
	 * get_post_stats
	 *
	 * @param  mixed $post_id
	 * @param  mixed $category
	 * @return array
	 */
	public static function get_post_stats($post_id, $category = 'global')
	{
		$rating = get_post_meta($post_id, self::POST_RATING_META . $category, true);
		$rating = empty($rating) ? 0 : $rating;
		$votes = get_post_meta($post_id, self::POST_VOTES_META . $category, true);
		$stats = array(
			'rating' => floatval($rating),
			'votes' => intval($votes)
		);
		return $stats;
	}


	/**
	 * Get Comment Rating
	 *
	 * @param  mixed $comment_id
	 * @param  mixed $cat_id
	 * @return void
	 */
	public static function get_comment_rating($comment_id, $cat_id = 'rating')
	{
		$rating = get_comment_meta($comment_id, self::COMMENT_RATING_META . $cat_id, true);
		return $rating;
	}



	/**
	 * Get the user ip (from WP Beginner)
	 *
	 * @return void
	 */
	private static function get_user_ip()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters('wpb_get_ip', $ip);
	}



	/* DISPLAY
-------------------------------------------------------------------------*/
	/**
	 * Simplified function for global rating display on the current post or comment
	 *
	 * @param  mixed $target 'post', 'entry', 'comment'
	 * @param  mixed $details
	 * @param  mixed $markup
	 * @return void
	 */
	public static function render($target='post', $details = true, $markup='span')
	{
		if ($target=='post' || $target == 'entry') {
			$post_id = get_the_id();
			$stats = self::get_post_stats($post_id);
			$args['rating'] = $stats['rating'];
			$args['votes'] = $stats['votes'];
		}
		elseif ($target == 'comment') {
			$comment_id = get_comment_ID();
			$rating = self::get_comment_rating($comment_id);
			$args['rating'] =$rating;
			if (!$rating) return '';
			$args['votes'] = 0;
		}

		$args['details'] = $details;
		$args['stars'] = floor($args['rating']);
		$args['half'] = ( floatval($args['rating'] - $args['stars'] ) ) >= 0.5;

		$args['rating_title'] = CSR_Assets::get_rating_caption($args['rating']);
		$args['tooltip_id'] = 'recipe_rating_form';
		$args['rating_id'] = 'recipe-review';

		if (empty($args['votes'])) {
			$args['details_url']   = '#respond';
			$args['details_class'] = '';
			$args['details_label'] = __('Evaluate me !', 'foodiepro');
		} else {
			$args['details_url']   = '#comments';
			$args['details_class'] = '';
			$args['details_label'] = sprintf(_n('%s review', '%s reviews', $args['votes'], 'foodiepro'), $args['votes']);
		}

		if ($target=='post') {
			$args['rating_href']   = 'href="#"';
			$args['rating_class'] = 'tooltip-onclick';
		} else {
			$args['rating_href']   = '';
			$args['rating_class'] = '';
		}

		// Output
		$html = CSR_Assets::get_template_part( $markup, false, $args);

		return $html;
	}


	/**
	 * Output stars markup
	 *
	 * @param  mixed $stars
	 * @param  mixed $half
	 * @return void
	 */
	public static function output_stars($stars, $half)
	{
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<div class="fa-stack full"><i class="fas fa-star fa-stack-1x"></i><i class="far fa-star fa-stack-1x"></i></div>';
		}
		for ($i = $stars + 1; $i <= 5; $i++) {
			if (($i == ($stars + 1)) && $half) {
				$html .= '<div class="fa-stack full"><i class="fas fa-star-half fa-stack-1x"></i><i class="far fa-star fa-stack-1x"></i></div>';
			} else {
				$html .= '<div class="fa-stack null"><i class="far fa-star fa-stack-1x"></i></div>';
			}
		}
		return $html;
	}


	/* ARCHIVE
-------------------------------------------------------------------------*/
	public function sort_entries_by_rating($query)
	{
		// Select any archive. For custom post type use: is_post_type_archive( $post_type )
		//if (is_archive() || is_search() ): => ne pas utiliser car r�sultats de recherche non relevants

		$order = get_query_var('orderby', false);

		if (($order == 'rating')) {
			$query->set('orderby', 'meta_value_num');
			$query->set('meta_key', 'user_rating_global');
			$query->set('order', 'DESC');
		}
	}
}
