<?php 

class BP_Activity_Customizations {

    public function __construct() {
		add_post_type_support( 'recipe', 'buddypress-activity' );
		add_action( 'bp_register_activity_actions', array($this,'customize_posts_tracking_args'), 15 );
		add_filter('bp_get_activity_content_body', array($this,'customize_post_publish_activity_feed'), 0, 2);
		//add_action('wp_head', 'display_bp_tracking_args');

		// Customize post publication activity feed content
		// Be careful this is recorded and never goes afterwards !!!
		// add_filter('bp_blogs_record_activity_action', array($this,'record_cpt_activity_content'));
		// add_filter('bp_blogs_record_activity_content', array($this,'record_cpt_activity_content'));		
    }	


	public function display_bp_tracking_args() {
		$tracking_args = bp_activity_get_post_type_tracking_args('post');
		echo '<pre>' . print_r($tracking_args,true) . '</pre>';
		$tracking_args = bp_activity_get_post_type_tracking_args('recipe');
		echo '<pre>' . print_r($tracking_args,true) . '</pre>';
	}


	public function customize_posts_tracking_args() {
	  // Check if the Activity component is active before using it.
	  // if ( ! bp_is_active( 'activity' ) ) return;

	  bp_activity_set_post_type_tracking_args( 'post', array(
	      'component_id'             => buddypress()->blogs->id,
	      'action_id'                => 'new_blog_post',
	      'format_callback'			 => array($this,'custom_format_activity_action_post'),
		)	);

	  bp_activity_set_post_type_tracking_args( 'recipe', array(
	      'component_id'             	=> buddypress()->blogs->id,
	      'action_id'                	=> 'new_recipe_post',
	      'bp_activity_admin_filter' 	=> __( 'Published a new recipe', 'foodiepro' ),
	      'bp_activity_front_filter' 	=> __( 'Recipes', 'foodiepro' ),
	      'singular' 					=> __( 'Recipe', 'foodiepro' ),
	      'contexts'                 	=> array( 'activity', 'member' ),
	      'format_callback'				=> array($this,'custom_format_activity_action_post'),
	      'activity_comment'         	=> true,
	      'position'                 	=> 100,
	  ) );
	}


	// Customize post publication activity feed content
	public function customize_post_publish_activity_feed( $text, $activity ) {
		if (  in_array( $activity->type, array('new_recipe_post','new_blog_post') ) ) {
			$blogpost_id = $activity->secondary_item_id;
			$post_url = get_permalink($blogpost_id);
			// $post_img = wp_get_attachment_image_src(  get_post_thumbnail_id( $blogpost_id ), 'square-thumbnail' );
			$post_img = wp_get_attachment_image_src(  get_post_thumbnail_id( $blogpost_id ) );
			$post_content = get_post_field('post_content', $blogpost_id);
			//$post_excerpt = get_the_excerpt($blogpost_id);
			// $post_intro = get_post_meta($blogpost_id, 'recipe_description', true);
			// $content = $post_content;	
			$text = '<div class="excerpt-image"><a class="excerpt-image" href="' . $post_url . '"><img src="' . $post_img[0] . '" ></a></div>' . $text;
		}
		return $text;
	}

	public function record_cpt_activity_content( $cpt ) {
			echo "Any text";
		  //if ( 'new_blog_post' === $cpt['type'] ) {
		      //$cpt['content'] = 'what you need';
		  $cpt='Any text';
		  $cpt['content']='Any text';
		  //}
	  return $cpt;
	}

	/* =================================================================*/
	/* =     ACTIVITY CALLBACKS
	/* =================================================================*/

	/**
	 * Format activity action strings for custom post types.
	 */
	public function custom_format_activity_action_post( $action, $activity ) {

		global $wpdb, $post, $bp;
	 
		// Fetch all the tracked post types once.
		if ( empty( $bp->activity->track ) ) {
			$bp->activity->track = bp_activity_get_post_types_tracking_args();
		}

		if ( empty( $activity->type ) || empty( $bp->activity->track[ $activity->type ] ) ) {
			return $action;
		}
		
		//echo '<prep>' . print_r($activity->type, true) . '</pre>';

		$user_link = bp_core_get_userlink( $activity->user_id );
		$blog_url  = get_home_url( $activity->item_id );

		if ( empty( $activity->post_url ) ) {
			$post_url = add_query_arg( 'p', $activity->secondary_item_id, trailingslashit( $blog_url ) );
		} else {
			$post_url = $activity->post_url;
		}
		
		if ( isset( $activity->post_title ) )
			$post_title = $activity->post_title; // Should be the case when the post has just been published.
		// If activity already exists try to get the post title from activity meta.
		elseif ( ! empty( $activity->id ) )
			$post_title = bp_activity_get_meta( $activity->id, 'post_title' );

		/**
		 * In case the post was published without a title
		 * or the activity meta was not found.
		 */
		if ( empty( $post_title ) ) {
			$post_title = esc_html__( '(no title)', 'buddypress' );// Defaults to no title.
			switch_to_blog( $activity->item_id );

			$post = get_post( $activity->secondary_item_id );
			if ( is_a( $post, 'WP_Post' ) ) {
				// Does the post have a title ?
				if ( ! empty( $post->post_title ) ) {
					$post_title = $post->post_title;
				}

				// Make sure the activity exists before saving the post title in activity meta.
				if ( ! empty( $activity->id ) ) {
					bp_activity_update_meta( $activity->id, 'post_title', $post_title );
				}
			}
			restore_current_blog();
		}
		
		switch ($activity->type) {
			case 'new_blog_post' : 
				$action = sprintf( __( '%1$s wrote a new post, <a class="activity-post-title" href="%2$s">%3$s</a>', 'foodiepro' ), $user_link, esc_url( $post_url ), $post_title );
				break;
			case 'new_recipe_post' : 
				$action = sprintf( __( '%1$s wrote a new recipe, <a class="activity-post-title" href="%2$s">%3$s</a>', 'foodiepro' ), $user_link, esc_url( $post_url ), $post_title );
				break;
		}
		
		//$recipe_img = wp_get_attachment_image_src(  get_post_thumbnail_id( $activity->secondary_item_id ) );
		//$action .=  '<img src="' . $recipe_img[0] . '" >';	
		
		/**
		 * Filters the formatted custom post type activity post action string.
		 *
		 * @since 2.2.0
		 *
		 * @param string               $action   Activity action string value.
		 * @param BP_Activity_Activity $activity Activity item object.
		 */
		return apply_filters( 'bp_activity_custom_post_type_post_action', $action, $activity );
	}


}


new BP_Activity_Customizations();
