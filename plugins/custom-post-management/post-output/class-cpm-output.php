<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Customize Post Appearance */

class CPM_Output {

	private static $PLUGIN_PATH;
	private static $PLUGIN_URI;

	public function __construct( $type='post' ) {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		$this->post_type = $type;
	}

	public function enqueue_post_meta( $meta )
	{
		$Post_Meta = CSD_Meta::get_instance('post');
		if (!$Post_Meta->is_output_here()) return;

		// Prepare data array
		$post = get_post();
		$data = array(
			'type'				=> 'Article',
			'title'				=> get_the_title(),
			'url'				=> get_permalink(),
			'date-published'	=> get_the_date(DATE_ATOM),
			'date-modified'		=> get_post_modified_time(DATE_ATOM),
			'thumbnail'			=> get_the_post_thumbnail_url(),
			'tags'				=> wp_get_post_tags(),
			'author'			=> ucfirst(get_the_author_meta('user_nicename', $post->post_author)),
			'description'		=> get_post_meta($post->ID, '_yoast_wpseo_metadesc', true),
		);
		$Post_Meta->set( $meta, $data);
		return $meta;
	}


	public function escape_and_cleanup_title($text) {
		$text=esc_html(stripslashes($text));
		return $text;
	}

	public function remove_status_prefix_from_title($content) {
		return '%s';
	}

	public function foodie_pro_read_more_link() {
		return '...</p><p><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More', 'foodiepro' ) . ' &raquo;</a></p>';
	}

	public function add_featured_image($content) {
		if ( !is_singular( 'post' )) return $content;

		$imgAlt = get_post_meta( get_the_ID(), '_wp_attachment_image_alt', true);
		if (empty($imgAlt)) $imgAlt = get_the_title();
		$image = get_the_post_thumbnail(null, 'vertical-thumbnail', array('alt'=>$imgAlt));
		if (empty($image))
			$image = CPM_Assets::get_fallback_img('post');

		$content = sprintf( '<div class="featured-image-class">%s</div>', $image ) . $content; // wraps the featured image in a div with css class you can control

		return $content;
	}

	public function add_lightbox_link($content) {
		if ( !is_singular( $this->post_type ) ) return $content;

		$search = "/<img(.*?)src=\"(.*?)\"(.*?)>/i";
		$replace = "<a href='$2' id='lightbox'><img$1src='$2'$3></a>";
		$content = preg_replace($search, $replace, $content);

    	return $content;
	}

	//* Customize the entry meta in the entry header (requires HTML5 theme support)
	public static function custom_post_meta( $post_info ) {
		if ( !is_singular( CPM_Assets::get_post_types() ) ) return;

		$post_type = get_post_type();
		$avatar = PeepsoHelpers::get_avatar(array(
				'user'		=> 'author',
				'link'		=> 'profile',
				'wraptag'	=> 'span',
				'wrapclass'	=> 'entry-avatar',
				'size'		=> 'small',
		));
		$profile = foodiepro_get_permalink(array(
				'user'		=> 'author',
				'display'	=> 'profile',
				'text'		=> '%s',
		));
		$date = '[post_date]';
		$post_info = sprintf(__('Published on %s by %s<span id="username">%s</span>', 'foodiepro'), $date, $avatar, $profile);
		// $post_info .= '[post_comments hide_if_off=false]';

		/* Edit Post Button */
		$post_info .= CPM_Assets::get_edit_button( get_post(), $post_type );

		$post_info .= '<div class="share-buttons">';
		$post_info .= '<span>' . __('Share on ', 'foodiepro') . '</span>' . do_shortcode('[social-sharing-buttons target="recipe" class="small bubble"]');
		$post_info .= '</div>';

		return $post_info;
  }

	public function add_post_type_toolbar_action() {
		if ( !is_singular( $this->post_type) ) return;
		do_action( 'cpm_' . $this->post_type . '_toolbar', null);
	}



	/* Default callbacks for 'post' type
	--------------------------------------------------------*/
	public function add_post_toolbar() {
		CPM_Assets::echo_template_part('screen', 'toolbar');
	}

}
