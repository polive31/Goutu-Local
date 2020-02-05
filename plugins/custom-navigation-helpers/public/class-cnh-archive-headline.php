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
				$output = $this->post_from_msg($type, $name);
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

		elseif ((is_archive() || is_tag()) && !is_post_type_archive())  {
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
		$term = $query->term_id;
		/* Return the updated archive description  */
		// Check archive intro text field
		$intro = get_term_meta($term, 'intro_text', true);
		if (empty($intro)) {
			// Check parent intro text field
			$parent = $query->parent;
			$intro = get_term_meta($query->parent, 'intro_text', true);
		}
		// For ingredient archive, we add the months table if available for this ingredient
		if (is_tax('ingredient')) {
			$intro .= '<br>' . do_shortcode('[ingredient-months id="' . $term . '"]');
		}
		return $intro;
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
			// Check first for existing custom headline defined for the taxonomy
			$headline = get_term_meta($query->term_id, 'headline', true);
			if (!empty($headline))
				$msg = $headline;

			// If custom headline not found, generate a title based on the archive type
			elseif ( $query->taxonomy=='ingredient' ) {
				$ingredient = $query->name;
				if ( initial_is_vowel($ingredient) )
				$msg=sprintf(_x('All recipes containing %s','vowel','foodiepro'), $ingredient);
				else
				$msg=sprintf(_x('All recipes containing %s','consonant','foodiepro'), $ingredient);
			}

			elseif ( $query->taxonomy=='cuisine' ) {
				$msg = $this->post_from_msg( 'recipe', $query->name);
			}

			elseif ( $query->taxonomy=='course' ) {
				$course=get_query_var('course',false);
				$term='';

				if (get_query_var('season',false) ) {
				$term=get_query_var('season',false);
				$msg = $this->course_of_msg( $course, $term);
			}
			elseif ( !empty($_GET['author']) ) {
				$user = get_user_by( 'slug', $_GET['author'] );
				$user=PeepsoHelpers::get_user( $user->ID );
				$term=PeepsoHelpers::get_field($user, "nicename");
				$msg = $this->course_of_msg( $course, $term);
			}
			else
			$msg = single_term_title( '', false);
		}
		elseif ( is_tax() || is_tag() ) {
			$msg = single_term_title( '', false);
			/* If a custom headline was set for this archive then return it */
		}
		else {
			$msg = single_term_title( '', false);
		}
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

	public function get_post_type_archive_title( $post_type ) {
		if ($post_type=='recipe')
			$title=__('All the recipes','foodiepro');
		elseif ($post_type=='post')
			$title=__('All the posts','foodiepro');
		else
			$title='';
		return $title;
	}


	public function course_of_msg( $course, $context='' ) {
		$html=array(
			'masculine'		=> array(
				'vowel' 	=> _x('All %s from %s','masculine-vowel','foodiepro'),
				'consonant' => _x('All %s from %s','masculine-consonant','foodiepro'),
			),
			'feminine'	=> array(
				'vowel' 	=> _x('All %s from %s','feminine-vowel','foodiepro'),
				'consonant' => _x('All %s from %s','feminine-consonant','foodiepro'),
			),
		);
		$gender_course = $this->gender($course);
		$vowel_context = initial_is_vowel($context)?'vowel':'consonant';
		$string = $html[$gender_course][$vowel_context];
		$html = sprintf( $html[$gender_course][$vowel_context], $course, $context );

		return $html;
	}

	public function gender( $word ) {
		$masculine = array(
			'dessert',
			'plat',
			'aperitif',
		);
		$feminine = array(
			'soupe',
			'boisson',
			'base',
			'entree'
		);
		$word = remove_accents( $word );
		if ( $word[-1]=='s') $word=substr($word, 0, -1);

		$out=false;
		if ( in_array( $word, $masculine ) ) {
			$out = 'masculine';
		}
		elseif ( in_array( $word, $feminine) ) {
			$out = 'feminine';
		}

		return $out;
	}

	public function post_from_msg( $object, $origin ) {
		$html=array(
			'generic' 	=> array(
				'plural' 	=> _x('All posts from %s','generic-plural','foodiepro'),
				'vowel' 	=> _x('All posts from %s','generic-vowel','foodiepro'),
				'consonant' => _x('All posts from %s','generic-consonant','foodiepro'),
			),
			'post'		=> array(
				'plural' 	=> _x('All posts from %s','post-plural','foodiepro'),
				'vowel' 	=> _x('All posts from %s','post-vowel','foodiepro'),
				'consonant' => _x('All posts from %s','post-consonant','foodiepro'),
			),
			'recipe'		=> array(
				'plural' 	=> _x('All posts from %s','recipe-plural','foodiepro'),
				'vowel' 	=> _x('All posts from %s','recipe-vowel','foodiepro'),
				'consonant' => _x('All posts from %s','recipe-consonant','foodiepro'),
			),
		);
		if (!$object) $object='generic';
		$context = $this->is_plural($origin)?'plural':(initial_is_vowel($origin)?'vowel':'consonant');
		$html = sprintf( $html[$object][$context], $origin );

		return $html;
	}


// HELPËRS
	protected function is_plural($word) {
		$last = strtolower($word[strlen($word)-1]);
		return ($last=='s');
	}

}
