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
									
		  if ( is_author() ) {
		  	$name = get_queried_object()->user_login;
		  	$first = strtolower( $name[0] );
		  	
				if ( in_array($first, self::$vocals) )
				  echo self::$head_style['begin'] . _x('All recipes from ','vowel','foodiepro') . $name . self::$head_style['end'];
				else 
				  echo self::$head_style['begin'] . _x('All recipes from ','consonant','foodiepro') . $name . self::$head_style['end'];
		  }
			
			elseif ( is_tax() ) {
				$first = get_queried_object()->slug[0];
				$term_id = get_queried_object()->term_id;
			  $headline = get_term_meta( $term_id, 'headline', true );
			  $intro_text = get_term_meta( $term_id, 'intro_text', true );

		    if( is_tax('ingredient') ) {
			    	if ( !empty($headline) )
						  echo self::$head_style['begin'] . $headline . self::$head_style['end'];
						else {
				    	if ( in_array($first, self::$vocals) )
				        echo self::$head_style['begin'] . single_term_title(_x('All recipes containing ','vowel','foodiepro'), false) . self::$head_style['end'];
				      else 
				        echo self::$head_style['begin'] . single_term_title(_x('All recipes containing ','consonant','foodiepro'), false) . self::$head_style['end'];				
						}
				}

				elseif( is_tax('cuisine') ) {
						if ( !empty($headline) )
							echo self::$head_style['begin'] . $headline . self::$head_style['end'];
					  else {
							if ( in_array($first, self::$vocals) )
				        echo self::$head_style['begin'] . single_term_title(_x('All recipes from ','vowel','foodiepro'), false) . self::$head_style['end'];
				      else 
				        echo self::$head_style['begin'] . single_term_title(_x('All recipes from ','consonant','foodiepro'), false) . self::$head_style['end'];			  	
					  }
				}
									
				elseif( is_tax('difficult') ) {
						if ( !empty($headline) )
							echo self::$head_style['begin'] . $headline . self::$head_style['end'];
						if ( !empty($intro_text) )
							echo self::$intro_style['begin'] . $intro_text . self::$intro_style['end'];
				}

				else {
					if ( !empty($headline) )
						echo self::$head_style['begin'] . $headline . self::$head_style['end'];		
					else 
						echo self::$head_style['begin'] . single_term_title('', false) . self::$head_style['end'];
				}
				
			}
			
			else 
				echo self::$head_style['begin'] . single_term_title('', false) . self::$head_style['end'];

		}
	}




}











