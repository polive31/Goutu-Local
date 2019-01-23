<?php
/**
 * Genesis Framework.
 *
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

add_action( 'genesis_before_content', 'genesis_do_archive_title_description' );
//Removes Title and Description on Archive, Taxonomy, Category, Tag
remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );

remove_action('genesis_loop','genesis_do_loop');
add_action('genesis_loop','custom_loop_with_child_terms');

// Widgeted areas
add_action( 'genesis_before_content', 'add_archive_top_area', 15);
add_action( 'genesis_after_loop', 'add_archive_bottom_area');



function add_archive_top_area() {
	genesis_widget_area( 'archives-top', array(
	    'before' => '<div class="top archives-top widget-area">',
	    'after'  => '</div>',
	));
}

function add_archive_bottom_area() {
	genesis_widget_area( 'archives-bottom', array(
	    'before' => '<div class="bottom archives-bottom widget-area">',
	    'after'  => '</div>',
	));   
}



/* Add child terms in Ingredients taxonomy archive
---------------------------------------------------------------------------*/

function custom_loop_with_child_terms() {
	$obj=get_queried_object();
	$query_args = array(
		'post_type' => 'recipe',
		'tax_query' => array(
			array(
				'taxonomy' => 'ingredient',
				'field'    => 'slug',
				'terms'    =>  $obj->slug,
				'has_children'    =>  true,
			),
		),
	);
	genesis_custom_loop( $query_args );
}


/**
 * Echo the title with the archive title and description
 *
 * @since 1.9.0
 */
function genesis_do_archive_title_description() {
	echo '<div class="archive-description taxonomy-archive-description taxonomy-description">';
	  	echo '<h1 class="archive-title">';
			$title = '';
			echo apply_filters( 'genesis_archive_title_text', $title ) . "\n";
		echo '</h1>';
		echo '<p>';
			$description = '';
			echo apply_filters( 'genesis_archive_description_text', $description ) . "\n";
		echo '</p>';
	echo '</div>';
}


genesis();
