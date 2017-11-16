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
		
	private $query='';
	

	public function __construct() {
		parent::__construct();
		add_action( 'wp_head', array($this,'init') ); /* Get queried object */
		add_action( 'genesis_before_content', array($this,'genesis_remove_default_archive_headline') );
			
		/* Add customized page title and description 
	 	------------------------------------------------------------*/
		add_action( 'genesis_before_loop', array($this,'custom_archive_title' ));
		add_action( 'genesis_before_loop', array($this,'custom_archive_description' ));
		add_filter( 'genesis_term_intro_text_output', 'wpautop' );	
		
		/* WPURP Detailed search : customized page title and description 
 		------------------------------------------------------------*/
		//add_filter( 'genesis_search_title_text', array($this,'custom_search_title_text') );4
		
	}

	public function init() {
		if ( is_archive() ) {
			$this->query = get_queried_object();
		}
	}
	
	public function custom_search_title_text() {	
		$url = $_SERVER["REQUEST_URI"];
		$WPURP_search = strpos($url, 'wpurp-search');

		if ($WPURP_search!==false)
			$html = __('Detailed Search Results', 'foodiepro');
		else 
			$html = __('Search Results for:', 'foodiepro');

	  return $html;
	}
	
	public function genesis_remove_default_archive_headline() {
		
		//$this->dbg('In Genesis Custom Archive function !','');
		
		if ( !is_archive() && !is_search() ) return;
		
		//Removes Title and Description on Custom Post Type Archive
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

	public function custom_archive_title() {
		if ( is_archive() ) {
		  if ( is_author() ) {
		  	echo $this->get_archive_headline('author', $this->query->user_login, '');
		  }
			else {
				$headline = get_term_meta( $this->query->term_id, 'headline', true );
				if ( is_tax() ) {
					$obj = get_queried_object();
					$tax = $obj->taxonomy;			
					//echo $tax;
			    if ( $tax=='ingredient' )
						echo $this->get_archive_headline('ingredient', $this->query->name, $headline);			
					elseif ( $tax=='cuisine' )
						echo $this->get_archive_headline('cuisine', $this->query->name, $headline);
					elseif( $tax=='difficult' ) 
						echo $this->get_archive_headline('difficult', $this->query->name, $headline);
					else 
						echo $this->get_archive_headline('', $this->query->name, $headline);
				}
				else 
					echo $this->get_archive_headline('', $this->query->name, $headline);
			}
		}
	}
	
	public function custom_archive_description() {
		if ( is_archive() ) {
			if ( is_tax() ) {
				$intro_text = get_term_meta( $this->query->term_id, 'intro_text', true );
				if ( !empty($intro_text) )
					echo self::$intro_style['begin'] . $intro_text . self::$intro_style['end'];				  
			}
		}
	}	
	
	protected function get_archive_headline($tax,$value,$headline) {
		
		if ( !empty($headline) ) 
			$msg = $headline;
		
		elseif ($tax=='author'||$tax=='cuisine') {

			////PC::debug(array('value'=>$value));	
			////PC::debug(array('value'=>$this->initial_is_vowel($value)));	
			if ($this->is_plural($value)) 
				$msg = sprintf(_x('All recipes from %s','plural','foodiepro'), $value);
			elseif ($this->initial_is_vowel($value)) 
				$msg = sprintf(_x('All recipes from %s','vowel','foodiepro'), $value);
			else 
				$msg = sprintf(_x('All recipes from %s','consonant','foodiepro'), $value);
		}
		
		elseif ($tax=='ingredient') {
			if ($this->initial_is_vowel($value))
				$msg = sprintf(_x('All recipes containing %s','vowel','foodiepro'), $value);
			else 
				$msg = sprintf(_x('All recipes containing %s','consonant','foodiepro'), $value);
		}
		
		elseif ($tax=='') {
				$msg = single_term_title('', false);
		}

		$msg = self::$head_style['begin'] . $msg . self::$head_style['end'];

		return $msg;
			
	}




}











