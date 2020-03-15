<?php


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CNH_RPWE_Customizations {

	public function rpwe_add_author( $output, $args ) {
		if ( !class_exists('Peepso') ) return '';
		if ( $args['display_author'] == '1') {
			$user = PeepsoUser::get_instance( get_the_author_meta( 'ID' ) );
			$name = $user->get_nicename();
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
				'user' 		=> 'author', // 'view', 'author', or ID
				'size' 		=> '', //'full',
				'link' 		=> 'profile',
				'title' 	=> __('by %s','foodiepro'),
				'aclass' 	=> 'auth-avatar',
			);
			$output .= PeepsoHelpers::get_avatar( $args );
		}
		return $output;
	}

	// Filter the author rpwe argument to allow dynamic value here (post's author, current user, view user...)
	public function wprpe_query_displayed_user_posts( $args ) {
		$author = $args['author'];
		if ( $author=='view_user' && class_exists('Peepso') ) {
			$args['author'] = PeepSoProfileShortcode::get_instance()->get_view_user_id();
		}
		elseif ('post_author'==$author) {
			$args['author'] = get_the_author_meta('ID');
		}
		elseif ('current_user'==$author) {
			$args['author'] = get_current_user_id();
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


	// Customize taxonomies list in the "display overlay" widget form's option list
	public function rpwe_custom_overlay_tax_list($args) {
		$list = get_object_taxonomies('recipe');
		return $list;
	}

	// Add overlay to RPWE widget
	public function rpwe_add_overlay($output, $args) {
		$post_id = get_the_ID();
		$overlay_tax_list = $args['tax-overlay'];
		if (!is_array($overlay_tax_list))
			$overlay_tax_list=explode(' ', $overlay_tax_list);

		$origin 	= in_array('cuisine', $overlay_tax_list)?wp_get_post_terms( $post_id, 'cuisine' ):'';
		$season 	= in_array('season', $overlay_tax_list)?wp_get_post_terms( $post_id, 'season' ):'';
		$occasion 	= in_array('occasion', $overlay_tax_list)?wp_get_post_terms( $post_id, 'occasion' ):'';
		$diet 		= in_array('diet', $overlay_tax_list)?wp_get_post_terms( $post_id, 'diet' ):'';
		$output .= CNH_Tags_Overlay::output_tags( $origin, $diet, $occasion, $season);
		return $output;
	}


	public function wprpe_orderby_rating( $args ) {
		if ( $args['orderby'] == 'meta_value_num')
			$args['meta_key'] = 'user_rating_global';
		return $args;
	}



}
