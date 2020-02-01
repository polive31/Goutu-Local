<?php

/**
 * Various functions used by the plugin.
 *
 * @package    Recent_Posts_Widget_Extended
 * @since      0.9.4
 * @author     Satrya
 * @copyright  Copyright (c) 2014, Satrya
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Sets up the default arguments.
 *
 * @since  0.9.4
 */
function rpwe_get_default_args()
{

	$css_defaults = ".rpwe-block ul{\nlist-style: none !important;\nmargin-left: 0 !important;\npadding-left: 0 !important;\n}\n\n.rpwe-block li{\nborder-bottom: 1px solid #eee;\nmargin-bottom: 10px;\npadding-bottom: 10px;\nlist-style-type: none;\n}\n\n.rpwe-block a{\ndisplay: inline !important;\ntext-decoration: none;\n}\n\n.rpwe-block h3{\nbackground: none !important;\nclear: none;\nmargin-bottom: 0 !important;\nmargin-top: 0 !important;\nfont-weight: 400;\nfont-size: 12px !important;\nline-height: 1.5em;\n}\n\n.rpwe-thumb{\nborder: 1px solid #eee !important;\nbox-shadow: none !important;\nmargin: 2px 10px 2px 0;\npadding: 3px !important;\n}\n\n.rpwe-summary{\nfont-size: 12px;\n}\n\n.rpwe-time{\ncolor: #bbb;\nfont-size: 11px;\n}\n\n.rpwe-comment{\ncolor: #bbb;\nfont-size: 11px;\npadding-left: 5px;\n}\n\n.rpwe-alignleft{\ndisplay: inline;\nfloat: left;\n}\n\n.rpwe-alignright{\ndisplay: inline;\nfloat: right;\n}\n\n.rpwe-aligncenter{\ndisplay: block;\nmargin-left: auto;\nmargin-right: auto;\n}\n\n.rpwe-clearfix:before,\n.rpwe-clearfix:after{\ncontent: \"\";\ndisplay: table !important;\n}\n\n.rpwe-clearfix:after{\nclear: both;\n}\n\n.rpwe-clearfix{\nzoom: 1;\n}\n";

	$defaults = array(
		'title'             => esc_attr__('Recent Posts', 'rpwe'),
		'title_url'         => '',

		'limit'            	=> 5,
		'offset'           	=> 0,
		'order'            	=> 'DESC',
		'orderby'          	=> 'date',
		'cat'              	=> array(),
		'tag'              	=> array(),
		'tax-overlay'   	=> array(),
		'taxonomy'         	=> '',
		'post_type'        	=> array('post'),
		'post_status'      	=> 'publish',
		'ignore_sticky'    	=> 1,
		'exclude_current'  	=> 1,
		'author'			=> '',

		'excerpt'          	=> false,
		'length'           	=> 10,
		'link'             	=> true,
		'display_author'	=> false,
		'display_rating'	=> false,
		'display_avatar'	=> false,
		'thumb'            	=> true,
		'first_thumb_height'     => 0,
		'first_thumb_width'      => 0,
		'thumb_height'     	=> 45,
		'thumb_width'      	=> 45,
		'thumb_default'    	=> 'http://placehold.it/45x45/f0f0f0/ccc',
		'thumb_align'      	=> 'rpwe-alignleft',
		'date'             	=> true,
		'date_relative'    	=> false,
		'date_modified'    	=> false,
		'readmore'         	=> false,
		'readmore_text'    	=> __('Read More &raquo;', 'recent-posts-widget-extended'),
		'comment_count'    	=> false,

		'styles_default'   	=> true,
		'css'              	=> $css_defaults,
		'cssID'            	=> '',
		'css_class'        	=> '',
		'before'           	=> '',
		'after'            	=> '',
		'shortcode'         => false
	);

	// Allow plugins/themes developer to filter the default arguments.
	return apply_filters('rpwe_default_args', $defaults);
}

/**
 * Outputs the recent posts.
 *
 * @since  0.9.4
 */
function rpwe_recent_posts($args = array())
{
	echo rpwe_get_recent_posts($args);
}

/**
 * Generates the posts markup.
 *
 * @since  0.9.4
 * @param  array  $args
 * @return string|array The HTML for the random posts.
 */
function rpwe_get_recent_posts($args = array())
{

	// Set up a default, empty variable.
	$html = '';
	$first = ($args['limit'] & 1);

	// Merge the input arguments and the defaults.
	$args = wp_parse_args($args, rpwe_get_default_args());

	// Extract the array to allow easy use of variables.
	extract($args);

	// Allow devs to hook in stuff before the loop.
	do_action('rpwe_before_loop');

	// Display the default style of the plugin.
	if ($args['styles_default'] === true) {
		rpwe_custom_styles();
	}

	// Initialize first_thumb_width/height
	$args['first_thumb_width'] = $args['first_thumb_width'] ? $args['first_thumb_width'] : $args['thumb_width'];
	$args['first_thumb_height'] = $args['first_thumb_height'] ? $args['first_thumb_height'] : $args['thumb_height'];

	// Link display
	$link = isset($args['link']) ? (bool) $args['link'] : false;
	$entry_class = $link ? '' : 'nolink';

	// If the default style is disabled then use the custom css if it's not empty.
	if ($args['styles_default'] === false && !empty($args['css'])) {
		echo '<style>' . $args['css'] . '</style>';
	}

	// Get the posts query.
	$posts = rpwe_get_posts($args);

	// echo '<pre>' . print_r( $posts ) . '</pre>';

	if ($posts->have_posts()) :

		// Recent posts wrapper
		$html = '<div ' . (!empty($args['cssID']) ? 'id="' . sanitize_html_class($args['cssID']) . '"' : '') . ' class="rpwe-block ' . (!empty($args['css_class']) ? '' . sanitize_html_class($args['css_class']) . '' : '') . '">';
		$html .= '<ul class="rpwe-ul">';
		while ($posts->have_posts()) : $posts->the_post();
			include(RPWE_PARTIALS . 'items-list.php');
		endwhile;
		$html .= '</ul>';
		$html .= '</div>';

		// Restore original Post Data.
		wp_reset_postdata();

		// Allow devs to hook in stuff after the loop.
		do_action('rpwe_after_loop');

		// Return the  posts markup.
		// return wp_kses_post( $args['before'] ) . apply_filters( 'rpwe_markup', $html, $args ) . wp_kses_post( do_shortcode($args['after']) );
		if ($args['shortcode']) {
			// $args['before']= do_shortcode(shortcode_unautop( $args['before'] ));
			// $args['after'] = do_shortcode(shortcode_unautop( $args['after'] ));
			$args['before'] = do_shortcode($args['before']);
			$args['after'] = do_shortcode($args['after']);
		}
		return wp_kses($args['before'], ALLOWED_TAGS) . apply_filters('rpwe_markup', $html, $args) . wp_kses($args['after'], ALLOWED_TAGS);

	endif;
}

/**
 * The posts query.
 *
 * @since  0.0.1
 * @param  array  $args
 * @return array
 */
function rpwe_get_posts($args = array())
{

	// Query arguments.
	$query = array(
		'offset'              	=> $args['offset'],
		'posts_per_page'      	=> $args['limit'],
		'orderby'             	=> $args['orderby'],
		'order'               	=> $args['order'],
		'post_type'           	=> $args['post_type'],
		'post_status'         	=> $args['post_status'],
		'ignore_sticky_posts' 	=> $args['ignore_sticky'],
		'author' 				=> $args['author'],
	);

	// Exclude current post
	if ($args['exclude_current']) {
		$query['post__not_in'] = array(get_the_ID());
	}

	// Limit posts based on category.
	if (!empty($args['cat'])) {
		$query['category__in'] = $args['cat'];
	}

	// Limit posts based on post tag.
	if (!empty($args['tag'])) {
		$query['tag__in'] = $args['tag'];
	}

	// Limit posts based on author.
	if (!empty($args['author'])) {
		$query['author'] = $args['author'];
	}



	/**
	 * Taxonomy query.
	 * Prop Miniloop plugin by Kailey Lampert.
	 */
	if (!empty($args['taxonomy'])) {

		parse_str(html_entity_decode($args['taxonomy']), $taxes);

		$operator  = 'IN';
		$tax_query = array();
		foreach (array_keys($taxes) as $k => $slug) {
			$ids = explode(',', $taxes[$slug]);
			if (count($ids) == 1 && $ids['0'] < 0) {
				// If there is only one id given, and it's negative
				// Let's treat it as 'posts not in'
				$ids['0'] = $ids['0'] * -1;
				$operator = 'NOT IN';
			}
			$tax_query[] = array(
				'taxonomy' => $slug,
				'field'    => 'id',
				'terms'    => $ids,
				'operator' => $operator
			);
		}

		$query['tax_query'] = $tax_query;
	}

	// Allow plugins/themes developer to filter the default query.
	$query = apply_filters('rpwe_default_query_arguments', $query);

	// Perform the query.
	$posts = new WP_Query($query);

	return $posts;
}

/**
 * Custom Styles.
 *
 * @since  0.8
 */
function rpwe_custom_styles()
{
?>
	<style>
		.rpwe-block ul {
			list-style: none !important;
			margin-left: 0 !important;
			padding-left: 0 !important;
		}

		.rpwe-block li {
			border-bottom: 1px solid #eee;
			margin-bottom: 10px;
			padding-bottom: 10px;
			list-style-type: none;
		}

		.rpwe-block a {
			display: inline !important;
			text-decoration: none;
		}

		.rpwe-block h3 {
			background: none !important;
			clear: none;
			margin-bottom: 0 !important;
			margin-top: 0 !important;
			font-weight: 400;
			font-size: 12px !important;
			line-height: 1.5em;
		}

		.rpwe-thumb {
			border: 1px solid #EEE !important;
			box-shadow: none !important;
			margin: 2px 10px 2px 0;
			padding: 3px !important;
		}

		.rpwe-summary {
			font-size: 12px;
		}

		.rpwe-time {
			color: #bbb;
			font-size: 11px;
		}

		.rpwe-comment {
			color: #bbb;
			font-size: 11px;
			padding-left: 5px;
		}

		.rpwe-alignleft {
			display: inline-block;
			/* float: left; */
		}

		.rpwe-alignright {
			display: inline-block;
			text-align: right;
		}

		.rpwe-aligncenter {
			display: inline-block;
			margin-left: auto;
			margin-right: auto;
		}

		.rpwe-clearfix:before,
		.rpwe-clearfix:after {
			content: "";
			display: table !important;
		}

		.rpwe-clearfix:after {
			clear: both;
		}

		.rpwe-clearfix {
			zoom: 1;
		}
	</style>
<?php
}
