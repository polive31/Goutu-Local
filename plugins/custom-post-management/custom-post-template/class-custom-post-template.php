<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Customize Post Appearance */

class Custom_Post_Template {

	private static $PLUGIN_PATH;
	private static $PLUGIN_URI;

	public function __construct( $type='post' ) {
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		$this->post_type = $type;
	}

	public function title_format($content) {
		return '%s';
	}

	public function foodie_pro_read_more_link() {
		return '...</p><p><a class="more-link" href="' . get_permalink() . '">' . __( 'Read More', 'foodiepro' ) . ' &raquo;</a></p>';
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

		$avatar = do_shortcode('[peepso-user-avatar user="author" page="profile" wraptag="span" wrapclass="entry-avatar"]');
		$profile = do_shortcode('[permalink user="author" display="profile"]%s[/permalink]');
		$date = do_shortcode('[post_date]');
		$post_info = sprintf(__('Published on %s by %s<span id="username">%s</span>', 'foodiepro'), $date, $avatar, $profile);

		/* Edit Post Button */
		$post_info .= CPM_Assets::get_edit_button( get_post(), $post_type );

		return $post_info;
  }

	public function add_post_type_toolbar_action() {
		if ( !is_singular( $this->post_type) ) return;
		do_action( 'cpm_' . $this->post_type . '_toolbar', null);
	}


	/* Default callbacks for 'post' type
	--------------------------------------------------------*/

	public function add_post_toolbar() {
		ob_start();
		require( self::$PLUGIN_PATH . 'custom-post-template/partials/post_toolbar.php' );
		$toolbar = ob_get_contents();
    	ob_end_clean();
    	echo $toolbar;
	}

}
