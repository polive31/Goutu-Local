<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =                 WHAT's NEW WIDGET			
/* =================================================================*/


add_action( 'widgets_init', function(){
     register_widget( 'BP_Activity_Feed' );
});	

/**
 * Adds BP_Activity_Feed widget.
 */
class BP_Activity_Feed extends WP_Widget {
	
	const DEFAULT_LIMIT = '2';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bp_activity_feed', // Base ID
			__('(Buddypress) Activity Feed', 'foodiepro'), // Name
			array( 'description' => __( 'Displays a Buddypress Activity Feed', 'foodiepro' ), ) // Args
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
    echo $args['before_widget'];
    
		if ( !(is_user_logged_in()) && !(bp_is_user()) ) return;
    
		$this->display_title($args, $instance);
		
		$limit=(empty($instance['limit']))?self::DEFAULT_LIMIT:$instance['limit'];

		/**
		 * Fires before the start of the activity loop.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_before_activity_loop' ); 

		//if ( bp_has_activities( bp_ajax_querystring( 'activity' )  ) ) : 
		if ( bp_has_activities( bp_ajax_querystring( 'activity' ).'&scope=just-me&max=' . $limit ) ) : 
		// Accepted Parameters :
		// scope=just-me, friends, groups, favorites, mentions
		// max=MAX NUMBER OF FEEDS
		// search_terms=XXX
		// Accepted Filters :
		// object=profile (profile updates, or new profiles), friends, groups, status, blogs
		// action=activity_update
		?>

				<ul id="activity-stream" class="activity-list item-list">

			<?php while ( bp_activities() ) : bp_the_activity(); ?>

				<?php bp_get_template_part( 'activity/entry' ); ?>

			<?php endwhile; ?>

			<?php if ( bp_activity_has_more_items() ) : ?>

			<?php endif; ?>

				</ul>

				<p class="more-from-category">
					<a href="<?php bp_activity_load_more_link() ?>"><?php echo __( 'Previous Feeds', 'foodiepro' ); ?></a>
				</p>
				
				
		<?php else : ?>

			<div id="message" class="info">
				<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>
		

		<?php

		/**
		 * Fires after the finish of the activity loop.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_after_activity_loop' ); ?>


		<?php
			
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
		if ( isset( $instance[ 'limit' ] ) )
			$limit = $instance[ 'limit' ];
		else 
			$limit = self::DEFAULT_LIMIT;		
		if ( isset( $instance[ 'autotitle' ] ) )
			$autotitle = $instance[ 'autotitle' ];
		else 
			$autotitle = false;						
		?>

		<p>
			<input class="checkbox" type="checkbox" <?php echo $autotitle?"checked":""; ?> id="<?php echo $this->get_field_id( 'autotitle' ); ?>" name="<?php echo $this->get_field_name( 'autotitle' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'autotitle' ); ?>">
				<?php _e( 'Automatic title ?', 'foodiepro' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
				<?php _e( 'Number of feeds to show', 'foodiepro' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="-1" value="<?php echo (int)( $limit ); ?>" />
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
		$instance['limit'] = $new_instance['limit'];			
		$instance['autotitle'] = $new_instance['autotitle'];			
		return $instance;
	}
	
	public function display_title($args, $instance) {
		
    $user_id=empty(bp_displayed_user_id())?bp_loggedin_user_id():bp_displayed_user_id();
    $user_name = bp_core_get_username($user_id);
		$default_title=sprintf(__('Activity Feed for %s','foodiepro'),$user_name);		
	
		$autotitle = (empty($instance['autotitle']))?false:$instance['autotitle'];
	
		if ( $autotitle )
			echo $args['before_title'] . apply_filters( 'widget_title', $default_title ). $args['after_title'];		
		elseif (! empty($instance['title']))
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];	
	}	

} // class BP_Whats_New

