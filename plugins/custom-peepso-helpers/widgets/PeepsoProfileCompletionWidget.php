<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// register widget
add_action( 'widgets_init', 'peepso_profile_completion_widget_init' );

function peepso_profile_completion_widget_init() {
	return register_widget( "PeepsoProfileCompletionWidget" );	
}

/**
 * Buddy Progress Bar Widget
 *
 * Buils the widget
 *
 * since 1.0
*/
class PeepsoProfileCompletionWidget extends WP_Widget {

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	// constructor
	public function __construct() {
		parent::__construct(
		'PeepsoProfileCompletionWidget', // Base ID
		__( '(Peepso) Profile completion', 'buddy-progress-bar' ), // Name
		array( 'description' => __( 'Displays profile completion bar', 'foodiepro' ), ) // Args
		);

		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('wp_enqueue_scripts', array($this,'register_custom_stylesheet'));
		
    }

	
	public function register_custom_stylesheet() {
		custom_register_style( 'circular-progress-bar', '/assets/css/circular-progress-bar.css', self::$PLUGIN_URI, self::$PLUGIN_PATH, array(), CHILD_THEME_VERSION );	
	}	

	// display widget
	public function widget( $args, $instance ) {

		if ( !is_user_logged_in() ) return;

		wp_enqueue_style('circular-progress-bar');

		/* PROFILE COMPLETION CALCULATION
		------------------------------------------------------*/
		$user = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());
		
		$can_edit = FALSE;
		if($user->get_id() == get_current_user_id() || current_user_can('edit_users')) {
			$can_edit = TRUE;
		}

		$user->profile_fields->load_fields( array('post_status'=>'publish') );
		$fields = $user->profile_fields->get_fields();

		$stats = $user->profile_fields->profile_fields_stats;

		$user_percent = $stats['completeness'];

		// if( $can_edit ) {

		// 	echo '<div class="ps-progress ps-completeness-info"';

		// 	if( $stats['completeness'] >= 100 && $stats['missing_required'] <= 0) {
		// 		echo ' style="display:none" ';
		// 	}

		// 	echo '>';

		// 		echo '<div class="ps-progress-status ps-completeness-status ';

		// 		if(1 === PeepSo::get_option('force_required_profile_fields',0) && $stats['filled_required'] < $stats['fields_required']) {
		// 			echo 'ps-text--danger';
		// 		}

		// 		echo '"';

		// 		if( $stats['completeness'] >= 100) {
		// 			echo ' style="display:none" ';
		// 		}

		// 		echo '>' . $stats['completeness_message'];

		// 		if(isset($stats['completeness_message_detail'])) {
		// 			echo $stats['completeness_message_detail'];
		// 		}

		// 		do_action('peepso_action_render_profile_completeness_message_after', $stats);

		// 		echo '</div>';

		// 		echo '<div class="ps-progress-bar ps-completeness-bar" ';

		// 		if( $stats['completeness'] >= 100) {
		// 			echo ' style="display:none" ';
		// 		}

		// 		echo '><span style="width:' . $stats['completeness'] . '%;"></span>';

		// 		echo "</div>";

		// 		echo '<div class="ps-progress-message ps-missing-required-message" ';

		// 		if( $stats['missing_required'] <= 0) {
		// 			echo ' style="display:none" ';
		// 		}

		// 		echo '>';

		// 			echo '<i class="ps-icon-warning-sign"></i> ' . $stats['missing_required_message'];

		// 		echo '</div>';
		// 	echo "</div>";
		// }


		$trigger_percent=(isset($instance['trigger_percent']))?$instance['trigger_percent']:'100';

//		echo $trigger_percent;
//		echo '<pre>';
//		print_r($instance);
//		echo '</pre>';

		if ( ( (int)$user_percent >= (int)$trigger_percent ) && !($trigger_percent == 0) ) return;

		$profile_completed = __( 'Profile completed !', 'foodiepro' );
		$profile_empty = __( 'Profile empty !', 'foodiepro' );
		$award = false;


		/* WIDGET OUTPUT 
		------------------------------------------------------*/
		extract( $args );

		// widget title
		$title = apply_filters( 'widget_title', $instance['title'] );

		// add a textarea for long messages
		$user = PeepsoHelpers::get_user('current');
		$profile_url=PeepsoHelpers::get_url( $user, 'profile', 'about');

		$textarea = sprintf(__('<a href="%s">Fill-in your profile</a> to make yourself visible from other users.','foodiepro'), $profile_url);


		echo $args['before_widget'];

		// Display the widget content
		echo '<div class="widget-text wp_widget_plugin_box">';

		// Check if title is set
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

			if( empty( $user_percent ) || $user_percent == 0 ) {
				echo '<p class="aligncenter">'. $profile_empty .'</p>';
			}

			if(  $user_percent == 100 && $award )  {
				echo '<div class="bppp-congrats"><span class="dashicons dashicons-awards"></span>' . $profile_completed . '</div>';
			} 
			elseif ( $user_percent == 100 && !$award ) {
				echo  '<div class="bppp-congrats">' . $profile_completed . '</div>';
			}

			if( $user_percent > 0 && $user_percent !=100 ) {
				$this->display_circular_progress_bar($user_percent);					
				echo '<p class="wp_widget_plugin_textarea">' . $textarea . '</p>';
			}
			
		echo '</div>';

		echo $args['after_widget'];


	}
	

	public function display_circular_progress_bar($user_percent) {
		$over50 = ($user_percent>50)?'over50':'';?>
		<div class="stat-wrapper">
			<div class="progress-circle p<?php echo $user_percent . ' ' . $over50;?>">
		  <span><?php echo $user_percent;?>%</span>
			  <div class="left-half-clipper">
			    <div class="first50-bar"></div>
			    <div class="value-bar"></div>
			  </div>
			</div>
		</div>
		<?php
	}

	public function display_linear_progress_bar($user_percent) {?>
		<div class="bppp-stat">
			<div class="bppp-widget-bar">
				<div class="bppp-bar-mask" style="width: <?php echo (int)(100-$user_percent)?>;%"></div>
			</div>
			<div class="bppp-stat-percent"><?php echo $user_percent;?>%</div>
		</div>
		<?php
	}	
	
	
	// widget form creation
	function form( $instance ) {

	// Check values
	if( $instance ) {
		$title = esc_attr( $instance['title'] );
		$textarea = esc_textarea( $instance['textarea'] );
		$trigger_percent = esc_textarea( $instance['trigger_percent'] );
	} 
	else {
		$title = '';
		$textarea = '';
		$trigger_percent= '100';
	}
	?>

	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title', 'buddy-progress-bar' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>

	<p>
	<label for="<?php echo $this->get_field_id( 'textarea' ); ?>"><?php _e( 'Message:', 'buddy-progress-bar' ); ?></label>
	<textarea class="widefat" id="<?php echo $this->get_field_id( 'textarea' ); ?>" name="<?php echo $this->get_field_name( 'textarea' ); ?>"><?php echo $textarea; ?></textarea>
	</p>

  <p>
  <label for="<?php echo $this->get_field_id( 'trigger_percent' ); ?>">Percent threshold for widget hide :</label>
  <table width="100%" >
  	<tr>
  		<td>
		  	<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'trigger_percent' ); ?>" name="<?php echo $this->get_field_name( 'trigger_percent' ); ?>" placeholder="100" value="<?php echo esc_attr( $trigger_percent ); ?>">
  		</td>
  		<td>
	  		<span class="input-unit">%</span>
  		</td>
  	</tr>
  </table>
  </p>

	<?php
	}

	// update widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// fields
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['textarea'] = strip_tags( $new_instance['textarea'] );
		$instance['trigger_percent'] = strip_tags( $new_instance['trigger_percent'] );
	return $instance;
	}	
	
}



