<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* =================================================================*/
/* =               MEMBER PROFILE WIDGET               =*/
/* =================================================================*/

add_action( 'widgets_init', function(){
     register_widget( 'BP_Member_Profile' );
});	

/**
 * Adds BP_Latest widget.
 */
class BP_Member_Profile extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bp_member_profile', // Base ID
			__('(Buddypress) Member profile', 'text_domain'), // Name
			array( 'description' => __( 'Shows profile details of the current displayed member', 'text_domain' ), ) // Args
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
		$user_id=bp_displayed_user_id();
		
		if ( 0 ) {
			

			if ( bp_has_profile() ) {
				while ( bp_profile_groups() ) : bp_the_profile_group();
					if ( bp_profile_group_has_fields() ) : ?>
		
						<div class="bp-widget <?php bp_the_profile_group_slug(); ?>">
							<!-- <h4><?php bp_the_profile_group_name(); ?></h4> -->
							
								<div class="bp-profile-fields">						
								<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
								
									<?php if ( bp_field_has_data() ) : ?>
										<div<?php bp_field_css_class(); ?>>
											<span class="label"><?php bp_the_profile_field_name(); ?> : </span>
											<span class="data"><?php bp_the_profile_field_value(); ?></span>
										</div>

									<?php endif; ?>
								<?php endwhile; ?>
								
							</div>
						</div>
									
					<?php endif; ?>
				<?php endwhile; ?>
			
			<?php
			}	
		
		}
		else { ?>
		
		<table class="bp-profile-fields"> 
			<tr>
				<td class="label"><?php echo 'Prénom'?></td>
				<td class="data"><?php echo xprofile_get_field_data( 'Prénom', $user_id ); ?></td>		
			</tr>	
			<?php 
			$birth=xprofile_get_field_data( 'Date de naissance', $user_id );
			if (!empty($birth)) {?>
			<tr>	
				<td class="label"><?php echo __('Age','foodiepro');?></td>
				<td class="data"><?php echo sprintf(__('%s years old','foodiepro'), $this->get_users_age($birth) );?></td>
			</tr>
			<?php
			}?>
			
		<?php
			$city=xprofile_get_field_data( 'Ville', $user_id );
			if (!empty($city)) {?>
			<tr>						
				<td class="label"><?php echo __('Lives in ','foodiepro');?></td>
				<td class="data"><?php echo $city;?></td>
			</tr>
		<?php
			}?>				
			
		<?php
			$preferred=xprofile_get_field_data( 'Plat préféré', $user_id );
			if (!empty($preferred)) {?>
			<tr>						
				<td class="label"><?php echo __('My preferred plate ','foodiepro');?></td>
				<td class="data"><a href="<?php echo get_site_url(null, '/?s=' . $preferred);?>"> 
					<?php echo $preferred;?>
				</a></td>
			</tr>
		<?php
			}?>				
			
		<?php
			$preferred=xprofile_get_field_data( 'Dessert préféré', $user_id );
			if (!empty($preferred)) {?>
			<tr>						
				<td class="label"><?php echo __('My preferred desert ','foodiepro');?></td>
				<td class="data"><a href="<?php echo get_site_url(null, '/?s=' . $preferred);?>"> 
					<?php echo $preferred;?>
				</a></td>
			</tr>
		<?php
			}?>					


		</table>
	
		

				

		<?php
		
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

	public function get_users_age($birth_date,$user_id=false,$format="%y"){
		
		if(!$user_id) $user_id=bp_displayed_user_id ();
		// $dob_time=xprofile_get_field_data($dob_field_name, $user_id);//get the datetime as myswl datetime
		$dob=new DateTime($birth_date);//create a DateTime Object from that
		echo $dob->format("%y years, %m months, %d days");
		$current_date_time=new DateTime();//current date time object
		//calculate difference
		$diff= $current_date_time->diff($dob);//returns DateInterval object
		//format and return
		return $diff->format($format);
	}


} // class BP_Member_Profile
