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

		$args = array(
			// 'blog_id'      => $GLOBALS['blog_id'],
			// 'role'         => '',
			// 'role__in'     => array(),
			'role__not_in' => array('administrator','pending'),
			// 'meta_key'     => 'registered',
			// 'meta_value'   => '',
			// 'meta_compare' => '',
			// 'meta_query'   => array(),
			// 'date_query'   => array(),        
			// 'include'      => array(),
			// 'exclude'      => array(),
			'orderby'      => 'ID',
			'order'        => 'DESC',
			// 'offset'       => '',
			// 'search'       => '',
			'number'       => $instance['limit'],
			// 'count_total'  => false,
			// 'fields'       => 'all',
			// 'who'          => '',
		 ); 
		$users = get_users( $args );
		// echo $instance['limit'];

		foreach ($users as $user) {
			echo '<div class="item-avatar">';
			echo '<a href="' . bp_core_get_userlink( $user->ID, false, true ) . '" title="' . ucfirst( bp_core_get_username( $user->ID ) ) . '">';
			echo bp_core_fetch_avatar( array(
				'item_id' => $user->ID,
				'type' => 'thumb',
				'css_id' => 'square',
				'class' => 'avatar',
				'alt' => bp_core_get_username( $user->ID ),
				// 'title' => bp_core_get_username( $user->ID ),
				)
			);
			echo '</a>';
			echo '</div>';
		}


		
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
