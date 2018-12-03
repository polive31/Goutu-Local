<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* =================================================================*/
/* =                LATEST REGISTERED MEMBERS WIDGET               =*/
/* =================================================================*/

add_action( 'widgets_init', function(){
     register_widget( 'BP_Latest' );
});	

/**
 * Adds BP_Latest widget.
 */
class BP_Latest extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bp_latest', // Base ID
			__('(Buddypress) Custom latest registered members', 'text_domain'), // Name
			array( 'description' => __( 'Displays latest registered members', 'text_domain' ), ) // Args
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
		
		/* Code start */
		
		if ( bp_has_members( 'type=newest&max=' . $instance['limit'] ) ) {    
			echo '<div class="avatar-block">';
			while ( bp_members() ) {
				bp_the_member();
				
				/* Exclude admins from the output */
				$user_id = bp_get_member_user_id(); 
		   		$user = new WP_User( $user_id );
		   		if (  !in_array( 'administrator', (array) $user->roles ) && !in_array( 'pending', (array) $user->roles ) )  {
		   			//if ( $user->roles[0] != 'administrator' && $user->roles[0] != 'pending') {
					echo '<div class="item-avatar">';
					//echo '<a href="' . bp_get_member_permalink() . '" title="' . bp_core_get_user_displayname(bp_get_member_user_id()) . '">';
					echo '<a href="' . bp_get_member_permalink() . '" title="' . bp_core_get_username(bp_get_member_user_id()) . '">';
					/*echo bp_member_avatar('type=full&width=100&height=100&id=square');*/
					echo bp_member_avatar('type=thumb&id=square');
					echo '</a>';
					echo '</div>';
				}
				
			}
			echo '</div>';
		}
		/* Code end */
		
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
		
		if ( isset( $instance[ 'title' ] ) ) 
			$title = $instance[ 'title' ];
		else 
			$title = __( 'New title', 'text_domain' );
		
		if ( isset( $instance[ 'limit' ] ) ) 
			$limit = $instance[ 'limit' ];
		else 
			$limit = 8;
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
				<?php _e( 'Number of users to show', 'foodiepro' ); ?>
			</label>
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

} // class BP_Latest
