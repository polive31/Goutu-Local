<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}


class CSR_Rating
{


	/* ===========================================================================
	/* ===================         SHORTCODES           =========================
	/* ===========================================================================

	/* Rating in string (not graphical) format for json encode
	-----------------------------------------------*/
	public function display_json_ld_rating_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'category' => 'global', //any rating category...
		), $atts);

		$post_id = get_the_id();

		$ratings = get_post_meta($post_id, 'user_ratings');
		$votes = count($ratings);

		$rating = get_post_meta($post_id, 'user_rating_' . $a['category'], true);

		//		$ratings_cat = array_column($ratings, $a['category']);
		//		if ( isset($ratings_cat) )
		//			$stats = $this->get_rating_stats( $ratings_cat );
		//$stats = implode(' ', $stats);

		$stats = $rating . ' ' . $votes;
		return $stats;
	}

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


	/* New post submission callback
	/* Add ratings default value (required for proper sorting in archives)
	-------------------------------------------------------------*/
	public function add_default_rating()
	{

		$Assets = new CSR_Assets();

		if (is_singular($Assets->post_types()) && (!wp_is_post_revision($post->ID))) {
			foreach (self::$ratingCats as $slug => $values) {
				$this->update_post_meta($post->ID, 'user_rating_' . $slug, '0');
			}
			$this->update_post_meta($post->ID, 'user_rating_global', '0');
		}
	}


	/* Update comment callback
	-------------------------------------------------------------*/
	public function comment_status_change_callback($new_status, $old_status, $comment)
	{
		$post_id = $comment->comment_post_ID;
		if ($new_status == 'approved') {
			/* Update post meta with the new rating values per category */
			$this->update_post_meta_user_rating($post_id);
		} elseif ($new_status == 'trash') {
			$this->delete_comment_meta_user_rating($comment_id);
			$this->update_post_meta_user_rating($post_id);
		} elseif ($new_status == 'unapproved' || $new_status == 'spam') {
			$this->update_post_meta_user_rating($post_id);
		}
	}

	public function comment_edit_callback($comment_id, $commentdata)
	{
		$post_id = $commentdata['comment_post_ID'];
	}


	public function update_comment_post_meta($comment_id, $comment_approved, $comment)
	{
		$post_id = $comment['comment_post_ID'];
		$current_post_type = get_post_type($post_id);
		/* CSR_Assets::post_types() cannot be called statically because we are in a submit callback
		and the CSR_Assets::hydrate function will not be called in this case */
		$Assets = new CSR_Assets();
		if (in_array($current_post_type, $Assets->post_types())) {
			/* Update comment meta and get corresponding rating */
			$new_rating = $this->update_comment_meta_user_rating($comment_id);
			/* Update post meta with the new rating and get updated ratings table */
			if ($comment_approved === 1) {
				$this->update_post_meta_user_rating($post_id);
			}
		}
	}


	/* Update comment meta
	-------------------------------------------------------------*/
	public function update_comment_meta_user_rating($comment_id)
	{
		$user_rating = array();
		$Assets = new CSR_Assets();

		$cats = $Assets->rating_cats();
		foreach ($cats as $cat => $values) {
			if (isset($_POST['rating-' . $cat])) {
				$rating_form_value = $_POST['rating-' . $cat];
				//otherwise let the cell empty, important for stats function
				add_comment_meta($comment_id, 'user_rating_' . $cat, $rating_form_value);
				$user_rating[$cat] = $rating_form_value;
			}
		}
		return $user_rating;
	}

	public function delete_comment_meta_user_rating($comment_id)
	{
		$Assets = new CSR_Assets();

		foreach ($Assets->rating_cats() as $cat => $values) {
			delete_comment_meta($comment_id, 'user_rating_' . $cat);
		}
	}

	/* Update "user_rating" post meta for each rating category
	-------------------------------------------------------------*/
	public function update_post_meta_user_rating($post_id)
	{
		$ratings = array();
		$count = array();

		$Assets = new CSR_Assets();
		$cats = $Assets->rating_cats();

		$votes = 0;

		$args = array(
			'post_id' 	=> $post_id,
			'status' 	=> 'all',
			'fields'	=> 'ids',
			'status'	=> 'approve',
		);
		$comment_ids = get_comments($args);
		/* Loop through all rating categories  */
		foreach ($cats as $cat => $values) {
			/* Loop through all post comments */
			foreach ($comment_ids as $comment_id) {
				$value = get_comment_meta($comment_id, 'user_rating_' . $cat, true);
				if ($value) {
					$ratings[$cat] += $value;
					$ratings['global'] += $values['weight'] * $value;
					$count[$cat]++;
					$count['global'] += $values['weight'];
				}
			}
			update_post_meta($post_id, 'user_rating_' . $cat, round($ratings[$cat] / $count[$cat], 1));
			update_post_meta($post_id, 'user_votes_' . $cat, $count[$cat]);
			$votes = ($count[$cat] > $votes) ? $count[$cat] : $votes;
		}
		update_post_meta($post_id, 'user_rating_global', round($ratings['global'] / $count['global'], 1));
		update_post_meta($post_id, 'user_votes_global', $votes);
	}


	/* ===========================================================================
	/* ===================         GETTERS              =======================
	/* ===========================================================================

	/* Get Comment Rating
	------------------------------------------------------------*/
	static function get_comment_rating($comment_id, $cat_id)
	{
		$rating = get_comment_meta($comment_id, 'user_rating_' . $cat_id, true);
		return $rating;
	}

	/* Get Post Rating
	------------------------------------------------------------*/
	static function get_post_rating($post_id, $cat_id = 'global')
	{
		$rating = get_post_meta($post_id, 'user_rating_' . $cat_id, true);
		return $rating;
	}

	/* Get Votes Count
	------------------------------------------------------------*/
	static function get_votes_count($post_id, $cat_id = 'global')
	{
		$rating = get_post_meta($post_id, 'user_votes_' . $cat_id, true);
		return $rating;
	}




	/* Get the user ip (from WP Beginner)
	-------------------------------------------------------------*/
	static function get_user_ip()
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


	/*************************************************************
	 * ************       RATING DISPLAY          ****************
	 *************************************************************/

	/* Output star rating shortcode
	---------------------------------------------*/
	public function display_star_rating_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'source' => 'post', //comment
			'display' => 'normal', //minimal = only stars, normal = caption + stars, full = with votes
			'category' => 'global',  // which rating(s) is(are) to be displayed : "all", "global", "rating", "clarity"...
			'markup' => 'table',  // span, list...
		), $atts);

		wp_enqueue_style('custom-star-rating');

		$display_style = $a['display'];
		$comment_rating = ($a['source'] == 'comment');


		/* Define markup */
		if ($a['markup'] == 'table') {
			$otag = '<table class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</table>'; // mai opening tag
			$rotag = '<tr>'; // row opening tag
			$rctag = '</tr>'; // row closing tag
			$cotag = '<td'; // cell opening tag
			$cctag = '</td>'; // cell closing tag
		} elseif ($a['markup'] == 'list') {
			$otag = '<ul class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</ul>'; // mai opening tag
			$rotag = '<li>'; // row opening tag
			$rctag = '</li>'; // row closing tag
			$cotag = '<div'; // cell opening tag
			$cctag = '</div>'; // cell closing tag
		} elseif ($a['markup'] == 'span') {
			$otag = '<span class="ratings-table" id="rating">'; // main opening tag
			$ctag = '</span>'; // mai opening tag
			$rotag = ''; // row opening tag
			$rctag = ''; // row closing tag
			$cotag = '<span'; // cell opening tag
			$cctag = '</span>'; // cell closing tag
		}

		// Setup categories to be displayed
		if ($a['category'] == 'all') $display_cats = CSR_Assets::rating_cats();
		elseif ($a['category'] == 'global') $display_cats = CSR_Assets::rating_cats('global');
		else {
			$shortcode_cats = explode(' ', $a['category']);
			foreach ($shortcode_cats as $key) {
				$display_cats[$key] = CSR_Assets::rating_cats($key);
			}
		}

		// Setup ratings source
		if ($comment_rating) {
			$comment_id = get_comment_ID();
		} else { // Rating in post meta
			$post_id = get_the_id();
		}

		ob_start();

		?>
		<?= $otag; ?>
		<?php

				foreach ($display_cats as $id => $cat) {

					if ($comment_rating) {
						$rating = self::get_comment_rating($comment_id, $id);
					} else {
						$rating = self::get_post_rating($post_id, $id);
						if ($display_style == 'full')  // displays number of votes
							$votes = self::get_votes_count($post_id, $id);
					}

					$rating = empty($rating) ? 0 : $rating;
					$stars = floor($rating);
					$half = ((int) $rating - $stars) >= 0.5;
					?>
			<?= $rotag; ?>
			<?php
						if (!($comment_rating && $rating == 0)) { // Don't show empty ratings in comments
							if ($display_style != 'minimal') {
								?>
					<?= $cotag; ?> class="rating-category" title="<?= $cat['legend']; ?>"><?= $cat['title']; ?>
					<?= $cctag; ?>
				<?php
								} ?>
				<?= $cotag; ?> class="rating" title="<?= $rating ?> : <?= CSR_Assets::get_rating_caption($rating, $id); ?>">
				<a class="pum-trigger" id="recipe-review"><?= $this->output_stars($stars, $half); ?></a>
				<?= $cctag; ?>
				<?php
							}
							if ($display_style == 'full') {
								if (!empty($votes)) {
									$rating_plural = sprintf(_n('%s review', '%s reviews', $votes, 'custom-star-rating'), $votes); ?>
					<?= $cotag; ?> class="rating-details"><a href="#comments-section"><?= $rating_plural ?></a><?= $cctag; ?>
				<?php
								} else { ?>
					<?= $cotag; ?> class="rating-details pum-trigger"><a href="#recipe-review"><?= __('Evaluate me !', 'foodiepro') ?></a><?= $cctag; ?>
			<?php
							}
						} ?>
			<?= $rctag; ?>
		<?php
				} ?>
		<?= $ctag; ?>
<?php
		//else {
		//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','custom-star-rating') . '</div>';
		//}

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/* Output stars div
	-------------------------------------------------------------*/
	public function output_stars($stars, $half)
	{
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<div class="fa-stack full"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>';
		}
		for ($i = $stars + 1; $i <= 5; $i++) {
			if (($i == ($stars + 1)) && $half) {
				$html .= '<div class="fa-stack full"><i class="fa fa-star-half-o fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></div>';
			} else {
				$html .= '<div class="fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></div>';
			}
		}
		return $html;
	}


	/* ===========================================================================
	/* ===================         ARCHIVE              =======================
	/* =========================================================================== */

	public function sort_entries_by_rating($query)
	{
		// Select any archive. For custom post type use: is_post_type_archive( $post_type )
		//if (is_archive() || is_search() ): => ne pas utiliser car r�sultats de recherche non relevants
		if (!is_post_type_archive(CSR_Assets::post_types())) return;
		$order = get_query_var('orderby', 'ASC');
		if ($order == 'rating') {
			$query->set('orderby', 'meta_value_num');
			$query->set('meta_key', 'user_rating_global');
			$query->set('order', 'DESC');
		}
	}


	/* ===========================================================================
	/* ===================         DEPRECATED              =======================
	/* ===========================================================================

	/* Update post meta
	-------------------------------------------------------------*/
	// public function update_post_meta_user_ratings( $post_id, $new_rating ) {

	// 	$user_ratings = get_post_meta( $post_id, 'user_ratings' );
	// 	$user_id = ( is_user_logged_in() )?get_current_user_id():0;
	// 	$user_ip = $this->get_user_ip();

	// 	/* Search this user's previous rating and delete it */
	// 	foreach ( $user_ratings as $id => $user_rating ) {
	// 		if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
	// 			delete_post_meta($post_id, 'user_ratings', $user_rating);
	// 			unset( $user_ratings[$id] );
	// 		}
	// 	}

	// 	/* Complete rating array with user IP & user ID */
	// 	$new_rating['user'] = $user_id;
	// 	$new_rating['ip'] = $user_ip;
	// 	add_post_meta($post_id, 'user_ratings', $new_rating);

	// 	$user_ratings[]=$new_rating;

	// 	/* return the updated value of the 'user_rating' meta */
	// 	return $user_ratings;
	// }


	/* Calculate rating stats
	-------------------------------------------------------------*/
	// static function get_rating_stats( $cat_ratings ) {
	// 	/* cat_ratings is the list of user ratings for one category */
	// 	$votes='';
	// 	$avg_rating='';
	// 	if ( ! empty($cat_ratings) ) {
	// 		$total = array_sum( $cat_ratings );
	// 		$votes = count( array_filter( $cat_ratings ) );

	// 		if( $votes !== 0 ) {
	// 		$avg_rating = $total / $votes; // TODO Just an average for now, implement some more functions later
	// 		$avg_rating = round( $avg_rating, 1 );
	// 		}
	// 	}
	// 	return array(
	// 		'votes' => $votes,
	// 		'rating' => $avg_rating,
	// 	);
	// }

}
