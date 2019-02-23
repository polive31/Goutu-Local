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
	public function custom_post_meta( $post_info ) {
		if ( !is_singular() ) return;
		
		$avatar = do_shortcode('[peepso-user-avatar user="author" page="profile" wraptag="span" wrapclass="entry-avatar"]');
		$profile = do_shortcode('[peepso-user-field user="author" page="profile"]');
		$date = do_shortcode('[post_date]');
		// $post_info = sprintf(_x('Published on %s by %s<span id="username">%s</span>', $this->post_type, 'foodiepro'), $date, $avatar, $profile);
		$post_info = sprintf(__('Published on %s by %s<span id="username">%s</span>', 'foodiepro'), $date, $avatar, $profile);
		
		/* Edit Post Button */
		global $post;
		$current_user = wp_get_current_user();
		if ($post->post_author == $current_user->ID || current_user_can('administrator')) { 
			$edit_url = 'href="' . get_permalink() . CPM_Assets::get_slug( $this->post_type . '_form') . '?edit-' . $this->post_type . '=' . $post->ID . '" ';
			$edit_title = 'title="' . CPM_Assets::get_label( $this->post_type, 'edit_button'). '" ';
			$post_info .= '<span class="edit-button"><a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a></span>';    
		}

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