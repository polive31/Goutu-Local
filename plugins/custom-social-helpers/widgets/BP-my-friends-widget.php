<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =                 MY FRIENDS WIDGET			
/* =================================================================*/


add_action( 'widgets_init', function(){
     register_widget( 'BP_My_Friends' );
});	

/**
 * Adds BP_My_Friends widget.
 */
class BP_My_Friends extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bp_my_friends', // Base ID
			__('(Buddypress) My Friends', 'text_domain'), // Name
			array( 'description' => __( 'Displays a chosen number of my friends', 'text_domain' ), ) // Args
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
		
		/* Code Start */
	
		if ($instance['user']=='loggedin') {
			if ( !(is_user_logged_in() ) ) return;
			$user_id=bp_loggedin_user_id();
			$user_id=bp_loggedin_user_id();
			$title= __('My Friends','foodiepro');
		}
		else {
			$user_id=bp_displayed_user_id();
			$title= sprintf( __('Friends of %s','foodiepro'), bp_core_get_username($user_id));
		}
		$limit=($instance['limit']=='')?'3':$instance['limit'];

		//echo $user_id;
		
		if ( bp_has_members( 'type=newest&max=' . $limit . '&user_id=' . $user_id ) ) {
			
			$this->display_title($args, $instance, $title);				
			echo	'<div class="avatar-block">';
			
			while ( bp_members() ) {
				bp_the_member();  
				echo '<div class="item-avatar">';
					//echo '<a href="' . bp_get_member_permalink() . '" title="' . bp_core_get_user_displayname(bp_get_member_user_id()) . '">';
					echo '<a href="' . bp_get_member_permalink() . '" title="' . bp_core_get_username(bp_get_member_user_id()) . '">';
					echo bp_member_avatar('type=thumb&id=square');
					echo '</a>';
				echo '</div>';
			}

			echo '</div>';
		}
		elseif ($instance['user']=='loggedin') {
			$this->display_title($args, $instance, $title);				
			echo '<div class="text aligncenter"><a href="' . get_site_url() . '\social\membres">' . __('Make new friends','foodiepro') . '</a></div>';
		}
		
		/* Code End */
		
		echo '<div class="clear"></div>';
		echo $args['after_widget'];
	}
	
	
	
	public function display_title($args, $instance, $title) {
	echo $args['before_widget'];
		if ( ! (empty( $instance['title']) ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		else
			echo $args['before_title'] . apply_filters( 'widget_title', $title ). $args['after_title'];		
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
		if ( isset( $instance[ 'user' ] ) ) {
			$user = $instance[ 'user' ];
		}
		else {
			$user = 'loggedin';
		}	
		if ( isset( $instance[ 'limit' ] ) )
			$limit = $instance[ 'limit' ];
		else 
			$limit = '8';	
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
		
		<p>
		  <label for="<?php echo $this->get_field_id('text'); ?>">
		  	<?php _e( 'Friends of', 'foodiepro' );?> :
		  </label>
		    <select class='widefat' id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text">
		      <option value='loggedin'<?php echo ($user=='loggedin')?'selected':''; ?>>
		        Logged-in User
		      </option>
		      <option value='displayed'<?php echo ($user=='displayed')?'selected':''; ?>>
		        Displayed User
		      </option>
		    </select>                
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
		$instance['user'] = $new_instance['user'];	
		$instance['limit'] = $new_instance['limit'];	
		return $instance;
	}

} // class BP_My_Friends

