<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

add_action( 'widgets_init', function(){
     register_widget( 'Peepso_Cover_Image_Header' );
});

class Peepso_Cover_Image_Header extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Peepso_Cover_Image_Header', // Base ID
			__('(Peepso) Member Cover Image Header', 'text_domain'), // Name
			array( 'description' => __( 'Displays the cover image header of the current displayed user', 'text_domain' ), ) // Args
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

     	echo $args['before_widget'];

		// if ( ! empty( $instance['title'] ) ) {
		// 	echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		// }


		// Widget content starts
		// $edit_my_profile_slug = Peepso::get_option('page_profile');
		// $view_member_profile_slug = Peepso::get_option('page_profile');

		?>


		<div id="cProfileWrapper" class="ps-clearfix">
			<?php

				$shortcode = PeepSoProfileShortcode::get_instance();
	            $shortcode->set_page('profile');
				$current_view_user = $shortcode->get_view_user_id();

				$PeepSoProfile = PeepSoProfile::get_instance();
				$PeepSoProfile->init( $current_view_user );

				PeepSoTemplate::exec_template('profile', 'focus');
			?>
        </div>

		<?php
		// Widget content ends

		echo '<div class="clear"></div>';

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else
			$title='';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}
