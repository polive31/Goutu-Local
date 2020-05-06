<?php
/**
 * Shortcode helper
 *
 * @package    Recent_Posts_Widget_Extended
 * @since      0.9.4
 * @author     Satrya
 * @copyright  Copyright (c) 2014, Satrya
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Display recent posts with shortcode.
 *
 * @since  0.9.4
 */
function rpwe_shortcode( $atts, $content ) {
	if ( isset( $atts['cssid'] ) ) {
		$atts['cssID'] = $atts['cssid'];
		unset( $atts['cssid'] );
	}
	$args = shortcode_atts( rpwe_get_default_args(), $atts );
	$html = '';
	if (!empty($content)) {
		// output widget markup with $content as title
		$html = '<div class="widget rpwe_widget tilde"><div class="widget-wrap"><h3 class="widgettitle"><span>' . $content . '</span></h3>';
	}
	$html .= rpwe_get_recent_posts( $args );
	if (!empty($content)) {
		$html .= '</div></div>';
	}
	return $html;
}
add_shortcode( 'rpwe', 'rpwe_shortcode' );
