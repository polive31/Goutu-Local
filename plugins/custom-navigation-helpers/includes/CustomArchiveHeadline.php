<?php

/* Archive Headline customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomArchiveHeadline extends CustomNavigationHelpers {

	public function __construct() {
		parent::__construct();
		// Headline text
		add_filter( 'genesis_search_title_text', array($this,'custom_search_title_text') );
		add_filter( 'genesis_archive_title_text', array($this,'custom_archive_title') );
		// Intro text
		add_filter( 'genesis_term_intro_text_output', 'wpautop' );		
		add_filter( 'genesis_archive_description_text', array($this,'custom_archive_description') );
		// add_filter( 'genesis_term_intro_text_output', 'wpautop' );	
		// Page title text
		// remove_filter('wp_title','genesis_default_title', 10, 3);
		// add_filter('wp_title', 'custom_archive_title', 10, 3);
		// Shortcode
		add_shortcode('seo-friendly-title', array($this,'get_seo_friendly_page_title')); 
	}


	public function custom_search_title_text() {	
		// $url = $_SERVER["REQUEST_URI"];
		// $WPURP_search = strpos($url, 'wpurp-search');
		// if ( $WPURP_search!==false )
		if ( isset( $_GET['wpurp-search'] ) )
			return __('Detailed Search Results', 'foodiepro');
		else 
			return sprintf( __('Search Results for:%s', 'foodiepro'), get_search_query());
	}


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

		$msg='';

		/* If a custom headline was set for this archive then return it */
		if ( get_class($this->query)=='WP_Post_Type' ) {
			// Return the post type queried
			$msg = $this->get_post_type_archive_title( $this->queryvars['post_type'] );			
		}
		else {
			$headline = get_term_meta( $this->query->term_id, 'headline', true );
			if ( empty($headline) ) {
				$parent = $this->query->parent;
				$headline = get_term_meta( $parent, 'headline', true );
			}
			if ( !empty($headline) ) {
				$msg = '<span class="archive-image">' . do_shortcode( '[wp_custom_image_category]' ) . '</span>' . $headline;
				return $msg;
			}		
			
			/* Check archive type */
			if ( is_author() ) {
				$id=$this->queryvars['author'];
				$user=PeepsoHelpers::get_user($id);
				$name=PeepsoHelpers::get_field($user, "nicename");
				$type=$this->queryvars['post_type'];
				
				$msg = $this->post_from_msg( $type, $name );
			}
			
			elseif ( is_tax('ingredient') ) {
				$ingredient = $this->query->name;
				if ( initial_is_vowel($ingredient) )
				$msg=sprintf(_x('All recipes containing %s','vowel','foodiepro'), $ingredient);
				else 
				$msg=sprintf(_x('All recipes containing %s','consonant','foodiepro'), $ingredient);		
			}
			
			elseif ( is_tax('cuisine') ) {
				$msg = $this->post_from_msg( 'recipe', $term);
			}
			
			elseif ( is_tax('course') ) {
				$course=$this->queryvars['course'];
				$term='';
				
				if ($this->queryvars['season']) {
					$term=$this->queryvars['season'];
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
			}
			
			else {
				$msg = single_term_title( '', false);
			}
		}
			
			$msg = '<span class="archive-image">' . do_shortcode( '[wp_custom_image_category]' ) . '</span>' . $msg;
			return $msg;
			
		}
		
		public function get_post_type_archive_title( $post_type ) {
			switch ($post_type) {
				case 'recipe':
				$title=__('All the recipes','foodiepro');
				break;
			case 'post':
				$title=__('All the posts','foodiepro');
				break;
			default:
				$title='';				
		}
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

	public function custom_archive_description( $description ) {
		if ( !is_archive() && !is_tag() ) return;
		
		/* Retrieve maybe archive term */
		if ( get_class($this->query)=='WP_Post_Type' ) {
			$empty=true;
			foreach ($this->queryvars as $term=>$var) {
				if ( $term!='post_type' && $var) {
					$empty=false;
					break;
				}
			}
		}
		else {
			$empty=false;
			$term = $this->query->term_id;
		}
		
		/* Return the updated archive description  */
		if ($empty) {
			/* No taxonomy term found, then get default post type archive description */
			$intro = $this->get_post_type_archive_intro_text( $this->queryvars['post_type'] );
		}
		else {
			// Check archive intro text field
			$intro = get_term_meta( $term, 'intro_text', true );
			if (empty($intro)) {
				// Check parent intro text field
				$parent = $this->query->parent;
				$intro = get_term_meta( $this->query->parent, 'intro_text', true );
			}
		}	
			
		if ( is_tax('ingredient') ) {
			$intro .= '<br>' . do_shortcode('[ingredient-months id="' . $this->query->term_id . '"]');
		}
			  
		return $description . $intro;
	}	
	
	public function get_post_type_archive_intro_text( $post_type ) {
		switch ($post_type) {
			case 'recipe':
				$intro_text=__('You will find here all the recipes, which you can further sort by date or evaluation.','foodiepro');
				break;
			case 'post':
				$intro_text=__('You will find here all the posts, which you can further sort by date.','foodiepro');
				break;				
			default:
				$intro_text='';
				break;
		}
		return $intro_text;
	}	


}











