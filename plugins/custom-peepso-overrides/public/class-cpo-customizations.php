<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CPO_Customizations {

	// public function custom_profile_navigation( $links ) {
		// if ( !is_user_logged_in() ) {
		// 	unset( $links['about'] );
		// 	$reordered=array();
		// 	foreach ( Peepso::$TABS as $tab ) {
		// 		$reordered[$tab] = $links[$tab];
		// 	}
		// 	$links = $reordered;
		// } 
		// return $links;
	// }

	public function custom_postbox_message( $msg ) {		
		$PeepSoProfile=PeepSoProfile::get_instance();
		if ( $PeepSoProfile->is_current_user() ) {
			$me_id = get_current_user_id();
			$me = PeepsoUser::get_instance( $me_id );
			$myname=$me->get_firstname();
			$msg = sprintf(__('What\'s new today, %s ?','foodiepro'),$myname);
		}
		else {
			$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			$user = PeepsoUser::get_instance( $user_id );
			$username=$user->get_firstname();
			$msg = sprintf(__('Post a message on %s\'s news feed...','foodiepro'), $username);
		}
		
		return $msg;
	}

    /* * * ACTIVITY STREAM * * */

    /**
     * create an Activity Stream item when a new post is published
     *
     * @param int 		$ID
     * @param WP_Post 	$post
     * @return bool (FALSE - posting, post type disabled/blacklisted, TRUE - success, NULL - already added)
     */
    
    function blogposts_publish_recipe( $ID, $post ) {
        // is this a regular post?
		if( 'recipe' != $post->post_type )			               							{	return( FALSE );	}

        // is the post published?
        if(!in_array($post->post_status,  array('publish')))         			            {	return( FALSE );	}

        // is activity posting enabled?
        if(0 == PeepSo::get_option('blogposts_activity_enable', 0 )) 			{	return( FALSE );	}

        // is this post type enabled?
        // if(!PeepSo::get_option('blogposts_activity_type_'.$post->post_type, 0)) {	return( FALSE );	}

        // check if it's not marked as already posted to activity and has valid act_id
        $act_id = get_post_meta($ID, Peepso::BLOGPOSTS_SHORTCODE, TRUE);
        if(strlen($act_id) && is_numeric($act_id) && 0 < $act_id) 				            {	return( NULL );	}

        // author is not always the current user - ie when admin publishes a post written by someone else
        $author_id = $post->post_author;

        // skip blacklisted author IDs
        $blacklist = array();
        if(in_array($author_id, $blacklist))                                                {   return( FALSE );    }

        // build JSON to be used as post content for later display
        $content = array(
            'post_id' => $ID,
            'post_type' => $post->post_type,
            'shortcode' => Peepso::BLOGPOSTS_SHORTCODE,
            'permalink' => get_permalink($ID),
        );

        $extra = array(
            'module_id' => Peepso::BLOGPOSTS_MODULE_ID,
            'act_access'=> PeepSo::get_option('blogposts_activity_privacy',PeepSoUser::get_instance($author_id)->get_profile_accessibility()),
            'post_date'		=> $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
        );

        $content=json_encode($content);

        // create an activity item
        $act = PeepSoActivity::get_instance();
        $act_id = $act->add_post($author_id, $author_id, $content, $extra);

        update_post_meta($act_id, '_peepso_display_link_preview', 0);
        delete_post_meta($act_id, 'peepso_media');

        // mark this post as already posted to activity
        add_post_meta($ID, Peepso::BLOGPOSTS_SHORTCODE, $act_id, TRUE);

        return TRUE;
	}
	

	public function publish_recipe_activity_stream_action($action, $post) {
		
		if (Peepso::BLOGPOSTS_MODULE_ID == intval($post->act_module_id)) {
			$content = strip_tags(get_post_field('post_content', $post, 'raw'));
			if ($target_post = json_decode($content)) {
				$wp_post = get_post($target_post->post_id);
				if ($wp_post->post_type != 'recipe' ) return ( $action );
				$action = __('wrote a new recipe','foodiepro');
				if(1==PeepSo::get_option('blogposts_activity_title_after_action_text',0)) {
					$action .= sprintf(' : <a class="ps-blogposts-action-title" href="%s">%s</a>', get_the_permalink($wp_post->ID), $wp_post->post_title);		
				}
			}
		}

        return ($action);		
	}


}