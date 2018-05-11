<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/* =================================================================*/
/* =                 WHAT's NEW WIDGET			
/* =================================================================*/


add_action( 'widgets_init', function(){
     register_widget( 'BP_Whats_New' );
});	

/**
 * Adds BP_Whats_New widget.
 */
class BP_Whats_New extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bp_whats_new', // Base ID
			__('(Buddypress) What\'s New', 'foodiepro'), // Name
			array( 'description' => __( 'Displays a Buddypress "what\'s New ?" input form', 'foodiepro' ), ) // Args
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
		
		if ( !(is_user_logged_in() ) ) return;
		
		/* Code Start */
    echo $args['before_widget'];
     	
		//$user_id=bp_loggedin_user_id();
    //$user_name = bp_get_loggedin_user_fullname(); 	
     	
		//$default_title=sprintf(__('What\'s New %s ?','foodiepro'),$user_name);
		if ( ! (empty( $instance['title']) ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];

		?>

		<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" role="complementary">

			<?php

			/**
			 * Fires before the activity post form.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_before_activity_post_form' ); ?>

			<table id="whats-new-container">

			<tr >
			<td id="whats-new-avatar">
				<div class="avatar-container">
				<a href="<?php echo bp_loggedin_user_domain(); ?>">
					<?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
				</a>
				</td>

			<td>
				
			<div id="whats-new-content">
				<div id="whats-new-textarea">
					<?php $invite = sprintf( __( "What's new, %s?", 'foodiepro' ), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );?>
					<textarea class="bp-suggestions" name="whats-new" id="whats-new" cols="50" rows="1" placeholder="<?php echo $invite;?>"
						<?php if ( bp_is_group() ) : ?>data-suggestions-group-id="<?php echo esc_attr( (int) bp_get_current_group_id() ); ?>" <?php endif; ?>
					><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>
				</div>

				<div id="whats-new-options">
					<div id="whats-new-submit">
						<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php esc_attr_e( 'Post Update', 'buddypress' ); ?>" />
					</div>

					<!--
					<?php if ( bp_is_active( 'groups' ) && !bp_is_my_profile() && !bp_is_group() ) : ?>

						<div id="whats-new-post-in-box">

							<?php _e( 'Post in', 'buddypress' ); ?>:

							<label for="whats-new-post-in" class="bp-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Post in', 'buddypress' );
							?></label>
							<select id="whats-new-post-in" name="whats-new-post-in">
								<option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ); ?></option>

								<?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0&update_meta_cache=0' ) ) :
									while ( bp_groups() ) : bp_the_group(); ?>

										<option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>

									<?php endwhile;
								endif; ?>

							</select>
						</div>
						<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

					<?php elseif ( bp_is_group_activity() ) : ?>

						<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
						<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />

					<?php endif; ?>

					<?php

					/**
					 * Fires at the end of the activity post form markup.
					 *
					 * @since 1.2.0
					 */
					do_action( 'bp_activity_post_form_options' ); ?>  -->

				</div><!-- #whats-new-options -->
			</div><!-- #whats-new-content -->
			</td>
			</table><!-- #whats-new-container-->

			<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
			<?php

			/**
			 * Fires after the activity post form.
			 *
			 * @since 1.2.0
			 */
			do_action( 'bp_after_activity_post_form' ); ?>

		</form><!-- #whats-new-form --> 
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

} // class BP_Whats_New

