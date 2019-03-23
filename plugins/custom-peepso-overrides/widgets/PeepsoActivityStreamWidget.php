<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

add_action( 'widgets_init', function(){
     register_widget( 'Peepso_Activity_Stream' );
});	

class Peepso_Activity_Stream extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Peepso_Activity_Stream', // Base ID
			__('(Peepso) Configurable Activity Stream', 'text_domain'), // Name
			array( 'description' => __( 'Displays the filtered activity stream of a given user', 'text_domain' ), ) // Args
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
		?>	 
		<!-- Widget begins -->

		<?php
			$PeepSoProfile=PeepSoProfile::get_instance();

			//$instance['limit'];
		?>

		<div class="activity-stream-front">
			<?php
			PeepSoTemplate::exec_template('general', 'postbox-legacy', array('is_current_user' => $PeepSoProfile->is_current_user()));
			?>

			<div class="tab-pane active" id="stream">
				<div id="ps-activitystream-recent" class="ps-stream-container" style="display:none"></div>
				<div id="ps-activitystream" class="ps-stream-container" style="display:none"></div>

				<div id="ps-activitystream-loading">
					<?php PeepSoTemplate::exec_template('activity', 'activity-placeholder'); ?>
				</div>

				<div id="ps-no-posts" class="ps-alert" style="display:none"><?php _e('No posts found.', 'peepso-core'); ?></div>
				<div id="ps-no-posts-match" class="ps-alert" style="display:none"><?php _e('No posts found.', 'peepso-core'); ?></div>
				<div id="ps-no-more-posts" class="ps-alert" style="display:none"><?php _e('Nothing more to show.', 'peepso-core'); ?></div>
			</div>

		</div><!-- end activity-stream-front -->

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
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of items to display', 'foodiepro' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="-1" value="<?php echo (int)( $instance['limit'] ); ?>" />
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
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

		return $instance;
	}

} 