<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =                   WELCOME  WIDGET	
/* =================================================================*/
	
add_action( 'widgets_init', function(){
     register_widget( 'BP_Cover_Image_Header' );
});	

/**
 * Adds BP_Cover_Image_Header widget.
 */
class BP_Cover_Image_Header extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'BP_Cover_Image_Header', // Base ID
			__('(Buddypress) Member Cover Image Header', 'text_domain'), // Name
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
		
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}	


		// Code starts 

		do_action( 'bp_before_member_home_content' ); ?>

		<div id="item-header" role="complementary">

			<?php
			//if (!bp_is_home() ) : /* Do not dispay header if my profile */
				if ( bp_displayed_user_use_cover_image_header() ) :
					//echo '<h1>Appel à cover-image-header</h1>';
					bp_get_template_part( 'members/single/cover-image-header' );
				else :
					bp_get_template_part( 'members/single/member-header' );
				endif;
			//endif;
			?>

		</div><!-- #item-header -->

		<?php 

		// Code ends

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
		else {
			$title = __( 'New title', 'text_domain' );
		}
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

} // class BP_Cover_Image_Header

