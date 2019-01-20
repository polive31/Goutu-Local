<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

add_action( 'widgets_init', function(){
     register_widget( 'Peepso_About_Widget' );
});	

class Peepso_About_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Peepso_About_Widget', // Base ID
			__('(Peepso) Peepso Profile Details', 'text_domain'), // Name
			array( 'description' => __( 'Displays chosen details about a given user', 'text_domain' ), ) // Args
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
		if ( ! empty( $instance['fields'] ) ) {
			$visible=$instance['fields'];
		}
		else 
			$visible=array();

		// Widget content starts 

		$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

		$args = array('post_status'=>'publish');

		$user->profile_fields->load_fields($args);
		$fields = $user->profile_fields->get_fields();
		?>

		<!-- <div class="peepso ps-page-profile"> -->
		<div class="peepso-about-widget">
			<?php
			if( count($fields) ) {
				foreach ($fields as $key => $field) {
					if ( !empty($visible[$field->id]) && !empty($field->value) ) {
					?>

						<div class="profile-field-container">
							<strong class="profile-field-title" id="profile-field-title-<?php echo $field->id; ?>"><?php _e($field->title, 'peepso-core');?> : </strong>
							<span class="profile-field-content"><?php echo $field->render(false); ?></span>
						</div>

					<?php
					}
				}
			} 
			else {
				echo __('Sorry, no data to show', 'peepso-core');
			}
			?>
		</div> <!--end row -->
					
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
		/* Format : array( 
			(int) field Id => (bool) field visibility
			) */
		if ( isset( $instance[ 'fields' ] ) ) {
			$is_visible = $instance[ 'fields' ];
		}
		else 
			$is_visible = array();
		
		$user_admin = PeepSoUser::get_instance( get_current_user_id() );
		$args = array('post_status'=>'publish');
		$user_admin->profile_fields->load_fields($args);
		$fields = $user_admin->profile_fields->get_fields();

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<fieldset>
			<legend for="<?php echo $this->get_field_id( 'fields' ); ?>"><?php _e( 'Fields visibility :' ); ?></label> 
			<?php 
			
			foreach ($fields as $field) {
				if ( isset($is_visible[$field->id]) )
					$checked = $is_visible[$field->id];
				else
					$checked = '';

				?>
				<p>
				<input type='checkbox' <?= $checked; ?>  id="field_<?= $field->id; ?>" name="<?php echo $this->get_field_name( 'fields' ); ?>[<?= $field->id; ?>]" value="checked">
				<label for="field_<?= $field->id; ?>"> <?= $field->title; ?> </label>
				</p>
				<?php	
			}
			
			?>
			</fieldset>
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

		if ( isset ( $new_instance['fields'] ) ) {	
			foreach ( $new_instance['fields'] as $key => $value ) {
				$instance['fields'][$key] = ( ! empty( $value ) ) ? strip_tags( $value ) : '';
			}
		}

		return $instance;
	}

} 