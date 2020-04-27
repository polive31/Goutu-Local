<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



// Creating the widget
class Social_Share_Buttons_Widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'social_share_buttons_widget',

// Widget name will appear in UI
__('Custom Social Share Buttons', 'foodiepro'), /*$widget_ops ???*/

// Widget description
array( 'description' => __( 'Displays share buttons of selected social networks', 'foodiepro' ), )
);
}


// Creating widget front-end
public function widget( $args, $instance ) {
	global $wp;

	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];

	// Start of widget code
	echo do_shortcode('[social-sharing-buttons target="site" class="medium" pinterest="true" linkedin="false"]');

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
add_action( 'widgets_init', 'cssb_load_widget' );
function cssb_load_widget() {
	register_widget( 'social_share_buttons_widget' );
}
