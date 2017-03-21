<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
add_action( 'widgets_init', function(){
     register_widget( 'Taxonomy_Navigation_Widget' );
});	

// Creating the widget 
class Taxonomy_Navigation_Widget extends WP_Widget {

function __construct() {
parent::__construct(
	// Base ID of your widget
	'taxonomy_navigation_widget', 

	// Widget name will appear in UI
	__('Taxonomy Navigation widget', 'foodiepro'), 

	// Widget description
	array( 'description' => __( 'Displays a dropdown list allowing to navigate between taxonomy terms', 'foodiepro' ), ) 
	);
}


// Creating widget front-end
public function widget( $args, $instance ) {
	global $wp;
	
	/* Do not display in the case of a WPURP dropdown search widget result */
	$url = $_SERVER["REQUEST_URI"];
	$WPURP_search = strpos($url, 'wpurp-search');
	if ($WPURP_search)
		return '';
	
	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	

	if ( is_tax('course') )
		echo $args['before_title'] . __('Other Courses', 'foodiepro') . $args['after_title'];
	elseif ( is_tax('cuisine') ) {
		$term = get_term_by( 'slug', get_query_var( 'term' ), 'cuisine' );
		$parent = get_term_by('id', $term->parent,'cuisine');
		if ($term->slug=='france') 
			echo $args['before_title'] . __('Region', 'foodiepro') . $args['after_title'];
		elseif ($parent->slug=='france')
			echo $args['before_title'] . __('Other Regions', 'foodiepro') . $args['after_title'];
		elseif ( !empty($parent->slug) ) 
			echo $args['before_title'] . __('Other Countries', 'foodiepro') . $args['after_title'];
		else 
			echo $args['before_title'] . __('Country', 'foodiepro') . $args['after_title'];
	}
	elseif ( is_tax('season') )
		echo $args['before_title'] . __('Other Seasons', 'foodiepro') . $args['after_title'];
	elseif ( is_tax('occasion') )
		echo $args['before_title'] . __('Other Occasions', 'foodiepro') . $args['after_title'];
	elseif ( is_tax('diet') )
		echo $args['before_title'] . __('Other Diets', 'foodiepro') . $args['after_title'];
	elseif ( is_tax('difficult') )
		echo $args['before_title'] . __('Other Levels', 'foodiepro') . $args['after_title'];
	elseif ( is_tag() )
		echo $args['before_title'] . __('All Tags', 'foodiepro') . $args['after_title'];
	else 
		echo $args['before_title'] . __('Filter recipes', 'foodiepro') . $args['after_title'];

	// Start of widget code
	
	echo do_shortcode('[ct-terms taxonomy="url" dropdown="true"]');

	// Output end
	echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) 
		$title = $instance[ 'title' ];
	else
		$title = __( 'New title', 'foodiepro' );
// Widget admin form
?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Register and load the widget
add_action( 'widgets_init', 'wpb_load_widget_taxonomy' );
function wpb_load_widget_taxonomy() {
	register_widget( 'taxonomy_navigation_widget' );
}



