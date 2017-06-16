<?php

/* Archive Headline customization
   Inherits from CustomArchive
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomArchiveHeadline extends CustomArchive {
	
	private static $head_style=array(
		'begin'=>'<h1 class="archive-title">',
		'end'=>'</h1>');

	private static $intro_style=array(
		'begin'=>'<div class="archive-description taxonomy-archive-description taxonomy-description">',
		'end'=>'</div>');
	

	public function __construct() {

		parent::__construct();
		add_action( 'genesis_before_content', array($this,'genesis_remove_default_archive_headline') );
		
		/* Add customized page title and description 
	 	------------------------------------------------------------*/
		add_action( 'genesis_before_content', array($this,'custom_archive_headline' ));
		add_filter( 'genesis_term_intro_text_output', 'wpautop' );	
		
		/* WPURP Detailed search : customized page title and description 
 		------------------------------------------------------------*/
		add_filter( 'genesis_search_title_text', array($this,'custom_search_title_text') );
	}
	
	public function custom_search_title_text() {	
		$url = $_SERVER["REQUEST_URI"];
		$WPURP_search = strpos($url, 'wpurp-search');

		if ($WPURP_search!==false)
			$html = __('Detailed Search Results', 'foodiepro');
		else 
			$html = __('Search Results for:', 'genesis');

	  return $html;
	}
	
	public function genesis_remove_default_archive_headline() {
		
		//$this->dbg('In Genesis Custom Archive function !','');
		
		if ( !is_archive() && !is_search() ) return;
		
		//Removes Title and Description on CPT Archive
		remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
		//Removes Title and Description on Blog Archive
		remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
		//Removes Title and Description on Date Archive
		remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
		//Removes Title and Description on Archive, Taxonomy, Category, Tag
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
		//Removes Title and Description on Author Archive
		remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
		//Removes Title and Description on Author Archive
		remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
		//Removes Title and Description on Blog Template Page
		remove_action( 'genesis_before_loop', 'genesis_do_blog_template_heading' );		

			
	}


	public function custom_archive_headline() {
		
		if ( is_archive() ) {
				
			$query = get_queried_object();	
									
		  if ( is_author() ) {
		  	echo $this->get_archive_headline('author', $query->user_login, '');
			}
			
			elseif ( is_tax() ) {
			  $headline = get_term_meta( $query->term_id, 'headline', true );

		    if ( is_tax('ingredient') )
					echo $this->get_archive_headline('ingredient', $query->slug, $headline);

				elseif ( is_tax('cuisine') )
					echo $this->get_archive_headline('cuisine', $query->slug, $headline);
									
				elseif( is_tax('difficult') ) {
					echo $this->get_archive_headline('difficult', $query->slug, $headline);
		  		$intro_text = get_term_meta( $term_id, 'intro_text', true );
					if ( !empty($intro_text) )
						echo self::$intro_style['begin'] . $intro_text . self::$intro_style['end'];
				}

				else 
					echo $this->get_archive_headline('', $query->slug, $headline);
			}
			
			else 
				echo $this->get_archive_headline('', $query->slug, $headline);

		}
	}
	
	protected function get_archive_headline($tax,$value,$headline) {
		$from_vowel = _x('All recipes from ','vowel','foodiepro');
		$from_consonant = _x('All recipes from ','consonant','foodiepro');
		$with_vowel = _x('All recipes containing ','vowel','foodiepro');
		$with_consonant = _x('All recipes containing ','consonant','foodiepro');
		
		if ( !empty($headline) ) 
			$msg = $headline;
		
		elseif ($tax=='author'||$tax=='cuisine') {
			////PC::debug(array('value'=>$value));	
			////PC::debug(array('value'=>$this->initial_is_vowel($value)));	
			if ($this->initial_is_vowel($value)) {
				$msg = single_term_title($from_vowel, false);
			}
			else 
				$msg = single_term_title(_x('All recipes from ','consonant','foodiepro'), false);
		}
		
		elseif ($tax=='ingredient') {
			if ($this->initial_is_vowel($value))
				$msg = single_term_title($with_vowel, false);
			else 
				$msg = single_term_title($with_consonant, false);
		}
		
		elseif ($tax=='') {
				$msg = single_term_title('', false);
		}

		$msg = self::$head_style['begin'] . $msg . self::$head_style['end'];

		return $msg;
			
	}




}











