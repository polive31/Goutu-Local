<?php

/* Archive Headline customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CNH_Archive_Headline {

	public function get_seo_friendly_page_title( $atts ) {
		$atts = shortcode_atts( array(
			'url' => 'true',
			), $atts );

			if ( is_archive() )
			$title = $this->custom_archive_title( '' );
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

	public function custom_archive_title( $headline ) {
		return $this->custom_archive_intro( 'title', $headline);
	}

	public function custom_archive_description( $description ) {
		return $this->custom_archive_intro( 'description', $description);
	}

	public function custom_archive_intro( $type, $default ) {
		$output=$default;

		if (is_author()) {
			if ($type == 'title') {
				$id = get_query_var('author', false);
				$user = get_userdata($id);
				$name = $user->user_nicename;
				$type = get_query_var('post_type', false);
				$output = $this->get_post_type_archive_title($type, $name);
				if (class_exists('PeepsoHelpers')) {
					$id = get_query_var('author', false);
					// $user=get_userdata( $id );
					$args = array(
						'user' 		=> $id,
						'size' 		=> '150',
						'link' 		=> 'profile',
						'title' 	=> __('%s', 'foodiepro'),
						'aclass' 	=> 'archive-avatar',
					);
					$output = PeepsoHelpers::get_avatar($args) . $output;
				}
			}
			else {
				if (class_exists('PeepsoHelpers')) {
					$id = get_query_var('author', false);
					// $user=get_userdata( $id );
					$args = array(
						'user' 		=> $id,
						'size' 		=> '120',
						'link' 		=> 'profile',
						'title' 	=> __('%s', 'foodiepro'),
						'aclass' 	=> 'archive-avatar',
					);
					$output .= PeepsoHelpers::get_profile_field($id, 'user_bio');
				}
			}
		}

		elseif ( is_archive() || is_tag() )  {
			$query = get_queried_object();
			if ($type == 'title') {
				$output = $this->get_archive_title( $query );
				$output = '<span class="archive-image">' . CNH_Assets::get_term_image('', 'thumbnail') . '</span>' . $output;
			}
			else {
				$output = $this->get_archive_description( $query );
			}
		}

		else {
			// If post type, return the title and descriptions for the post type queried
			if ($type == 'title') {
				$output = $this->get_post_type_archive_title(get_query_var('post_type', false));
			}
			else {
				$output .= $this->get_post_type_archive_intro_text(get_query_var('post_type', false));
			}
		}

		//Format and output
		if ($type=='description') {
			$output = do_shortcode($output);
			$output = wpautop($output, true);
		}
		return $output;
	}

	public function get_archive_description($query) {
		$text='';
		if (is_tax() || is_tag()) {
			$term = $query->term_id;
			/* Return the updated archive description  */
			// Check archive intro text field
			$text = get_term_meta($term, 'intro_text', true);
			if (empty($text) ) {
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

	public function get_post_type_archive_intro_text($post_type) {
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

	public function get_archive_title($query) {
		if ( is_tax() || is_tag() ) {
			// Check first for existing custom headline defined for the taxonomy
			$headline = get_term_meta($query->term_id, 'headline', true);
			if (!empty($headline)) return $headline;

			// If custom headline not found, generate a title based on the archive type
			if ( $query->taxonomy=='ingredient' ) {
				$ingredient = $query->name;
				if ( initial_is_vowel($ingredient) )
				$msg=sprintf(_x('All recipes containing %s','vowel','foodiepro'), $ingredient);
				else
				$msg=sprintf(_x('All recipes containing %s','consonant','foodiepro'), $ingredient);
			}
			elseif ( $query->taxonomy=='cuisine' ) {
				$msg = $this->get_post_type_archive_title( 'recipe', $query->name);
			}
			elseif ( $query->taxonomy=='course' ) {
				$course=get_query_var('course',false);
				$term='';
				if (get_query_var('season',false) ) {
					$term=get_query_var('season',false);
				}
				elseif ( !empty($_GET['author']) ) {
					$user = get_user_by( 'slug', $_GET['author'] );
					$user=PeepsoHelpers::get_user( $user->ID );
					$term=PeepsoHelpers::get_field($user, "nicename");
				}
				$msg = $this->get_course_archive_title( $course, $term);
			}
			else
				$msg = single_term_title('', false);
		}
		elseif (is_post_type_archive()) {
			$post_type = get_queried_object()->name;
			$term='';
			if (!empty($_GET['author'])) {
				$user = get_user_by('slug', $_GET['author']);
				$user = PeepsoHelpers::get_user($user->ID);
				$term = PeepsoHelpers::get_field($user, "nicename");
			}
			$msg = $this->get_post_type_archive_title($post_type, $term);
		}
		else
			$msg = single_term_title('', false);

		return $msg;
	}

	public function custom_search_title_text() {
		// $url = $_SERVER["REQUEST_URI"];
		// $WPURP_search = strpos($url, 'wpurp-search');
		// if ( $WPURP_search!==false )
		if ( isset( $_GET['wpurp-search'] ) )
			return __('Detailed Search Results', 'foodiepro');
		else
			return sprintf( __('Search Results for %s', 'foodiepro'), get_search_query());
	}

	public function get_post_type_archive_title( $post_type, $subject='' ) {

		$html = array(
			'recipe' => array(
				'vowel' 	=> _x('All recipes from %s', 'vowel','foodiepro'),
				'consonant' => _x('All recipes from %s', 'consonant','foodiepro'),
				'none'		=> __('All the recipes','foodiepro'),
			),
			'post' => array(
				'vowel' 	=> _x('All posts from %s', 'vowel','foodiepro'),
				'consonant' => _x('All posts from %s', 'consonant','foodiepro'),
				'none'		=> __('All the posts','foodiepro'),
			)
		);

		$post_type=empty($post_type)?'post':$post_type;
		$context = foodiepro_check_initial($subject);
		$string = $html[$post_type][$context];
		$title = sprintf($string, $subject);
		return $title;
	}


	public function get_course_archive_title( $course, $subject='' ) {
		$course = str_replace('-',' ', $course);
		// Subject can be either season or author
		$html=array(
			'masculine'		=> array(
				'vowel' 	=> _x('All %s from %s','masculine-vowel','foodiepro'),
				'consonant' => _x('All %s from %s','masculine-consonant','foodiepro'),
				'none' 		=> _x('All %s', 'masculine','foodiepro'),
			),
			'feminine'	=> array(
				'vowel' 	=> _x('All %s from %s','feminine-vowel','foodiepro'),
				'consonant' => _x('All %s from %s','feminine-consonant','foodiepro'),
				'none' 		=> _x('All %s','feminine','foodiepro'),
			),
		);
		$gender = $this->course_gender($course);
		$context = foodiepro_check_initial($subject);
		$string = $html[$gender][$context];
		$title = sprintf( $string, $course, $subject );
		return $title;
	}

	public function course_gender( $word ) {
		$feminine = array(
			'soupe',
			'boisson',
			'base',
			'entree',
		);
		$word = remove_accents( $word );
		if ( $word[-1]=='s') $word=substr($word, 0, -1);

		$out='masculine';
		if ( in_array( $word, $feminine) ) {
			$out = 'feminine';
		}

		return $out;
	}


}
