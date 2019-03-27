<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Social_Interactions_Shortcodes extends Custom_Social_Interactions {

	public function __construct() {
		add_shortcode( 'like-count', array($this,'like_count_shortcode') );
	}

	public function like_count_shortcode( $atts ) {
		$a = shortcode_atts( array(
			'post' => 'current', // defaults to current post otherwise post id given in this attribute 
			'tag' => 'span',
			'class' => 'like-count',
			'icon' => true,
		), $atts );		

		if ($a['post'] == 'current')
			$post_id=get_the_id();
		else 
			$post_id=intval($a['post']);

		$count = $this->like_count($post_id);

		if ($a['icon']) {
			$html = '<span class="like-count ' . ($count==0?'nolike':'') . '" title="' . sprintf( _n( '%s like', '%s likes', $count, 'foodiepro'), $count) . '"><i class="fa fa-thumbs-o-up">' . $count . '</i></span>';
		}
		else {
			$html = sprintf( _n( '%s like', '%s likes', $count, 'foodiepro'), $count);
			$html = '<' . $a['tag'] . ' class="' . $a['class'] . '">' . $html . '</' . $a['tag'] . '>';
		}

		return $html; 
	}

}

new Custom_Social_Interactions_Shortcodes();