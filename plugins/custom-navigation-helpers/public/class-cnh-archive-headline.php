<?php

/* Archive Headline customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CNH_Archive_Headline {

	private $trans;

	public function __construct() {
	}

	public function hydrate() {
		$this->trans = array(
			'post_type'	=> array(
				'feminine'	=> _x('All %s', 'feminine', 'foodiepro'),
				'masculine'	=> _x('All %s', 'masculine', 'foodiepro'),
			),
			'containing'	=> array(
				'vowel'	=> _x('%s containing %s', 'vowel', 'foodiepro'),
				'consonant'	=> _x('%s containing %s', 'consonant', 'foodiepro'),
			),
			'of'	=> array(
				'vowel'	=> _x('%s of %s', 'vowel', 'foodiepro'),
				'consonant'	=> _x('%s of %s', 'consonant', 'foodiepro'),
			),
			'from'	=> array(
				'vowel'	=> _x('%s from %s', 'vowel', 'foodiepro'),
				'consonant'	=> _x('%s from %s', 'consonant', 'foodiepro'),
			),
			'for'	=> array(
				'vowel'		=> _x('%s for %s', 'vowel', 'foodiepro'),
				'consonant'	=> _x('%s for %s', 'consonant', 'foodiepro'),
			),
			'vegetarien' =>  array(
				'masculine' => _x('All vegetarian %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All vegetarian %s', 'feminine', 'foodiepro'),
			),
			'leger' =>  array(
				'masculine' => _x('All light %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All light %s', 'feminine', 'foodiepro'),
			),
			'sans-gluten' =>  array(
				'masculine' => _x('All gluten-free %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All gluten-free %s', 'feminine', 'foodiepro'),
			),
			'prise-de-masse' =>  array(
				'masculine' => _x('All %s for weight gain', 'masculine', 'foodiepro'),
				'feminine' => _x('All %s for weight gain', 'feminine', 'foodiepro'),
			),
			'printemps' =>  array(
				'masculine' => _x('All spring %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All spring %s', 'feminine', 'foodiepro'),
			),
			'ete' =>  array(
				'masculine' => _x('All summer %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All summer %s', 'feminine', 'foodiepro'),
			),
			'automne' =>  array(
				'masculine' => _x('All autumn %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All autumn %s', 'feminine', 'foodiepro'),
			),
			'hiver' =>  array(
				'masculine' => _x('All winter %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All winter %s', 'feminine', 'foodiepro'),
			),
			'elementaire' =>  array(
				'masculine' => _x('All evident %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All evident %s', 'feminine', 'foodiepro'),
			),
			'facile' =>  array(
				'masculine' => _x('All simple %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All simple %s', 'feminine', 'foodiepro'),
			),
			'complique' =>  array(
				'masculine' => _x('All complicated %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All complicated %s', 'feminine', 'foodiepro'),
			),
			'difficile' =>  array(
				'masculine' => _x('All difficult %s', 'masculine', 'foodiepro'),
				'feminine' => _x('All difficult %s', 'feminine', 'foodiepro'),
			),
		);
	}

	public function get_seo_friendly_page_title( $atts ) {
		$atts = shortcode_atts( array(
			'url' => 'true',
			), $atts );

			if ( is_archive() )
				$title = $this->get_archive_title();
			elseif ( is_search() )
				$title = $this->custom_search_title_text();
			elseif ( is_singular() )
				$title = get_the_title();
			else
				$title = __('Visit Goutu.org', 'foodiepro');

			if ($atts['url']=='true')
			$title = str_replace( ' ', '%20', $title);

			return $title;
		}

	public function custom_archive_description( $description ) {
		$output = '';

		if (is_author()) {
			if (class_exists('PeepsoHelpers')) {
				$id = get_query_var('author', false);
				// $user=get_userdata( $id );
				// $args = array(
				// 	'user' 		=> $id,
				// 	'size' 		=> '120',
				// 	'link' 		=> 'profile',
				// 	'title' 	=> __('%s', 'foodiepro'),
				// 	'aclass' 	=> 'archive-avatar',
				// );
				$output = PeepsoHelpers::get_profile_field($id, 'user_bio');
			}
		} elseif (is_archive() || is_tag()) {
			$query = get_queried_object();
			$output = $this->get_archive_description($query);
		} else {
			// If post type, return the title and descriptions for the post type queried
			$output .= $this->get_post_type_archive_intro_text(get_query_var('post_type', false));
		}

		//Format and output
		$output = do_shortcode($output);
		$output = wpautop($output, true);

		return $output;
	}

	public function get_archive_title() {

		if (is_tag()) {
			$title = single_term_title('', false);
			$term_image = foodiepro_get_term_image('', 'thumbnail');
		}
		elseif ( is_tax() || is_author() || is_post_type_archive() ) {
			$subject_slug = get_query_var('course',false);
			if ($subject_slug) {
				$subject = $this->get_term_name($subject_slug,'course');
				$subject_gender = $this->get_gender($subject_slug);
			}
			elseif (get_query_var('post_type', false)) {
				$post_type_slug = get_query_var('post_type');
				$post_type_object = get_post_type_object($post_type_slug);
				$subject = $post_type_object->label;
				$subject_gender = $this->get_gender($subject);
			}
			elseif( is_tax() ) {
				$subject = __('recipes','foodiepro');
				$subject_gender = 'feminine';
			}
			else {
				$subject = __('posts','foodiepro');
				$subject_gender = 'masculine';
			}
			$title=sprintf($this->trans['post_type'][$subject_gender], $subject);
			$term_image = foodiepro_get_term_image('', 'thumbnail');

			if (get_query_var('ingredient',false)) {
				$ingredient_slug = get_query_var('ingredient',false);
				$ingredient=$this->get_term_name($ingredient_slug,'ingredient');
				$initial=foodiepro_check_initial($ingredient);
				$title=sprintf($this->trans['containing'][$initial], $title, $ingredient);
				$term = get_term_by('slug',$ingredient_slug, 'ingredient');
				$term_image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('cuisine',false)) {
				$origin_slug = get_query_var('cuisine',false);
				$term = get_term_by('slug', $origin_slug, 'cuisine');
				$origin_headline = get_term_meta($term->term_id, 'headline', true);
				if ($origin_headline) {
					$title=sprintf('%s %s', $title, $origin_headline);
				}
				else {
					$origin= $this->get_term_name($origin_slug,'cuisine');
					$initial=foodiepro_check_initial($origin);
					$title=sprintf($this->trans['from'][$initial], $title, $origin);
				}
				$term_image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('season', false)) {
				$season_slug = get_query_var('season',false);
				if (isset($this->trans[$season_slug]))
				$title = sprintf($this->trans[$season_slug][$subject_gender], $subject);
				$term = get_term_by('slug', $season_slug, 'season');
				$term_image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('occasion', false)) {
				$occasion_slug = get_query_var('occasion',false);
				$occasion = $this->get_term_name($occasion_slug,'occasion');
				$initial = foodiepro_check_initial($occasion);
				$title=sprintf($this->trans['for'][$initial], $title, $occasion);
				$term = get_term_by('slug', $occasion_slug, 'occasion');
				$term_image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('difficult', false)) {
				$difficult_slug = get_query_var('difficult',false);
				// $difficult = get_query_var($difficult_slug,'difficult');
				if (isset($this->trans[$difficult_slug]))
				$title = sprintf($this->trans[$difficult_slug][$subject_gender], $subject);
				$term = get_term_by('slug', $difficult_slug, 'difficult');
				$term_image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('diet', false)) {
				$diet_slug = get_query_var('diet',false);
				if (isset($this->trans[$diet_slug]))
				$title=sprintf($this->trans[$diet_slug][$subject_gender], $subject);
				$term = get_term_by('slug', $diet_slug, 'diet');
				$image = foodiepro_get_term_image($term, 'thumbnail', '', '', '', $term_image);
			}
			elseif (get_query_var('author', false)) {
				$author = get_query_var('author');
				// $user=get_userdata( $id );
				$user = PeepsoHelpers::get_user($author);
				$author_nicename = PeepsoHelpers::get_field($user, "nicename");
				$initial = foodiepro_check_initial($author_nicename);
				$title=sprintf($this->trans['from'][$initial], $title, $author_nicename);
				$args = array(
					'user' 		=> $author,
					'size' 		=> '150',
					'link' 		=> 'profile',
					'title' 	=> __('%s', 'foodiepro'),
					'aclass' 	=> 'archive-avatar',
				);
				$term_image = PeepsoHelpers::get_avatar($args);
			}
		}
		else {
			$title = single_term_title('', false);
			$term_image = foodiepro_get_term_image('', 'thumbnail');
		}

		$title = '<span class="archive-image">' . $term_image . '</span>' . $title;
		return $title;
	}


	public function get_archive_description($query)
	{
		$text = '';
		if (is_tax() || is_tag()) {
			$term = $query->term_id;
			/* Return the updated archive description  */
			// Check archive intro text field
			$text = get_term_meta($term, 'intro_text', true);
			if (empty($text)) {
				// Check parent intro text field
				$parent = $query->parent;
				$text = get_term_meta($parent, 'intro_text', true);
			}
			// For ingredient archive, we add the months table if available for this ingredient
			if (is_tax('ingredient')) {
				$text .= '<br>' . do_shortcode('[ingredient-months id="' . $term . '"]');
			}
		}
		return $text;
	}

	public function get_post_type_archive_intro_text($post_type)
	{
		switch ($post_type) {
			case 'recipe':
				$intro_text = __('You will find here all the recipes, which you can further sort by date or evaluation.', 'foodiepro');
				break;
			case 'post':
				$intro_text = __('You will find here all the posts, which you can further sort by date.', 'foodiepro');
				break;
			default:
				$intro_text = '';
				break;
		}
		return $intro_text;
	}


	/*  GETTERS
	---------------------------------------------------------- */

	public function get_gender( $word ) {
		$feminine = array(
			'soupe',
			'boisson',
			'base',
			'entree',
			'sauce',
			'recette',
			'publication'
		);
		$word = strtolower(remove_accents( $word ));
		if ( $word[-1]=='s') $word=substr($word, 0, -1);

		$out='masculine';
		if ( in_array( $word, $feminine) ) {
			$out = 'feminine';
		}

		return $out;
	}

	public function get_term_name($slug,$tax) {
		$term = get_term_by('slug', $slug, $tax );
		if (!$term) return '';
		return $term->name;
	}

	public function custom_search_title_text()
	{
		// $url = $_SERVER["REQUEST_URI"];
		// $WPURP_search = strpos($url, 'wpurp-search');
		// if ( $WPURP_search!==false )
		if (isset($_GET['wpurp-search']))
			return __('Detailed Search Results', 'foodiepro');
		else
			return sprintf(__('Search Results for %s', 'foodiepro'), get_search_query());
	}


}
