<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



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


public function is_region( $obj ) {
	if ($obj->slug=='france') return true;
	$parent = get_term_by('id',$obj->parent,'cuisine');
	return ($parent->slug=='france');
}


// Creating widget front-end
public function widget( $args, $instance ) {
	if ( !is_archive() && !is_search() ) return;

	global $wp;

	$title = apply_filters( 'widget_title', $instance['title'] );

	// before and after widget arguments are defined by themes
	echo $args['before_widget'];

	/* Do not display in the case of a search query or WPURP dropdown search widget result */

	$WPURP_search = strpos($_SERVER["REQUEST_URI"], 'wpurp-search');

	if ( (! $WPURP_search) && (! is_search() ) ) {

		// Widget title
		$obj = get_queried_object();
		$author = isset($obj->data->user_login);

		if ($author) {
			$title = __('authors','foodiepro');
		}
		elseif ($obj->taxonomy=='cuisine') {
			if ( $this->is_region($obj) )
				$title = __('regions','foodiepro');
			else
				$title = __('countries','foodiepro');
		}
		else {
			$tax = get_taxonomy($obj->taxonomy);
			$title = $tax->label;
		}

		echo $args['before_title'] . $title . $args['after_title'];
		// echo $args['before_title'] . __('Filter', 'foodiepro') . $args['after_title'];

		// Dropdown display
		echo do_shortcode('[ct-terms dropdown="true"]');

	}
	else
		echo '&nbsp;';

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
add_action( 'widgets_init', 'cnh_load_taxonomy_dropdown_widget' );
function cnh_load_taxonomy_dropdown_widget() {
	register_widget( 'taxonomy_navigation_widget' );
}
