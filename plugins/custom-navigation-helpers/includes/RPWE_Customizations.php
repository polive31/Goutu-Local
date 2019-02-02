<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RPWE_Customizations {
	
	
	public function __construct() {
		
		// Add post author to RPWE  widget
		add_filter('rpwe_post_title_meta', array($this, 'rpwe_add_author'), 10, 2);
		
		/* Modify WP Recent Posts extended output, depending on the css ID field value */
		add_filter('rpwe_after_thumbnail', array($this, 'wprpe_add_avatar'), 20, 2);
		
		/* Modify WPRPE output, displaying posts from current logged-in user */
		add_filter( 'rpwe_default_query_arguments', array($this, 'wprpe_query_displayed_user_posts') );
		
		/* Workaround for shortcodes in rpwe "after" html not executing */ 
		add_filter( 'rpwe_markup', array($this, 'add_more_from_author_link'),15, 2 );
		
		/* Prevent redundant posts when several rpwe instances are called on the same page */
		add_action('rpwe_loop', array($this, 'rpwe_get_queried_posts') );
		
		/* ??? */
		add_filter('rpwe_default_query_arguments', array($this, 'rpwe_exclude_posts') );

		/* Add user rating to RPWE widget */
		add_filter('rpwe_post_title', array($this, 'rpwe_add_rating'), 10, 2 );

		/* Modify WP Recent Posts ordering, depending on the orderby field value */
		add_filter( 'rpwe_default_query_arguments', array($this, 'wprpe_orderby_rating' ) );
		
	}



	public function rpwe_add_author( $output, $args ) {
		if ( !class_exists('Peepso') ) return '';
		if ( $args['display_author'] == '1') {
			$user = PeepsoUser::get_instance( get_the_author_meta( 'ID' ) );
			$name = $user->get_username();
			$url = $user->get_profileurl();
			$link = '<a href="' . $url . '">' . $name . '</a>';
			$output .= '<span class="rpwe-author">' . sprintf(__('by %s','foodiepro'), $link ) . '</span>';
		}
		return $output;
	}
	
	
	public function wprpe_add_avatar( $output, $args ) {
		if ( !class_exists('PeepsoHelpers') ) return '';
		if ( $args['display_avatar'] == '1') {
			$user = PeepsoUser::get_instance( get_the_author_meta( 'ID' ) );			
			$args = array(
				'user' => 'author', // 'view', 'author', or ID
				'size' => '', //'full',
				'link' => 'profile',
				'aclass' => 'auth-avatar',
			);
			$output .= PeepsoHelpers::get_avatar( $args );
		}
		return $output;
	}
	
	public function wprpe_query_displayed_user_posts( $args ) {
		switch ( $args['author'] ) {
			case 'view_user':
			if ( !class_exists('Peepso') ) return '';
			$args['author'] = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			break;
			case 'post_author':
			$args['author'] = get_the_author_meta('ID');
			break;
		}
		return $args;
	}
	
	public function rpwe_add_rating( $title, $args ) {
		$output = '';
		if ( $args['display_rating'] == '1') {
			$output .= '<span class="entry-rating">';
			$output .= do_shortcode('[display-star-rating display="minimal" category="global" markup="span"]');
			$output .= do_shortcode('[like-count]');
			$output .= '</span>';
		}
		return $title . $output;
	}

	/* in social-after-content widgeted area  */
	public function add_more_from_author_link( $html, $args ) {
		if ( !class_exists('Peepso') ) return '';
		$user_id=PeepSoProfileShortcode::get_instance()->get_view_user_id();
		$user = PeepsoUser::get_instance( $user_id );
		$name=$user->get_username();	
		if ($args['author']=='bp_member') {
			$html.='<p class="more-from-category">'; 
			$html.='<a href="' . get_author_posts_url($user_id) . '">' . sprintf(_x('All posts from %s','consonant','foodiepro'), $name) . '</a>';
			$html.='</p>';
		}
		return $html;
	}

	// $rpwe_exclude_posts=array();
	public function rpwe_get_queried_posts( $post ) {
		$this->noglobal( 'collect', $post->ID);
	}

	public function rpwe_exclude_posts( $query ) {
		$query = $this->noglobal( 'exclude', '', $query);
		return $query;
	}

	public function noglobal( $action, $postId='', $query=array() ) {
		static $rpwe_queried_posts=array();
		if ($action=='collect') {
			$rpwe_queried_posts[]=$postId;
			return;
		}
		else {
			if (isset($query['post__not_in']) && isset($rpwe_queried_posts)) {
				$query['post__not_in'] = array_merge( $query['post__not_in'], $rpwe_queried_posts );	
			} 
			return $query;
		}
	}



	public function wprpe_orderby_rating( $args ) {
		if ( $args['orderby'] == 'meta_value_num')
			$args['meta_key'] = 'user_rating_global';
		return $args;
	}



}











