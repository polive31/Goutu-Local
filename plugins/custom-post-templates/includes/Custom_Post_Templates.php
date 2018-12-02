<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomPostTemplates {
	
	protected static $vowels = array('a','e','i','o','u');
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	
	
	public function __construct() {	
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
		// add_action( 'wp', array($this,'hydrate'));		

		// add_filter( 'genesis_attr_content', 'add_columns_class_to_content' );
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_masonry_scripts'));

		add_action( 'genesis_before_entry_content', array($this, 'add_post_toolbar'), 15);

        // Filters the post meta information under the headline
        add_filter( 'genesis_post_info', array($this, 'custom_post_meta'), 1, 10 );		
		
		// Filters post thumbnail output in order to let lightbox plugin format them accordingly
		// add_filter( 'wp_get_attachment_image_attributes', array($this, 'custom_post_thumbnail_html'), 10, 5 );
		
		// add_filter( 'the_content', array($this, 'new_content') );
}


	public function new_content($content) {
		if ( !is_singular( 'post' ) ) return $content;

		$search = "/<img(.*?)src=\"(.*?)\"(.*?)>/i";
		$replace = "<a href='$2' id='lightbox'><img$1src='$2'$3></a>";

		$content = preg_replace($search, $replace, $content);
	    
    	return $content;
	}


	// public function custom_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	public function custom_post_thumbnail_html( $attr, $attachment, $size ) {
		if ( !is_singular( 'post' ) ) return;
		$attr['id'] = 'lightbox';
		$attr['class'] .= ' lightbox';
	    // if( '' == $html ) {
	    //     global $post, $posts;
	    //     ob_start();
	    //     ob_end_clean();
	    //     $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	    //     $first_img = $matches [1] [0];
	    //     if ( empty( $first_img ) ){
	    //         $image_id = 129; // default image ID
	    //     }
	    //     else {
	    //         $image_id = get_attachment_id_from_src($first_img);
	    //     }
	    //     $html = wp_get_attachment_image( $image_id, $size, false, $attr );
	    // }
	    return $attr;
	}

	public function add_post_toolbar() {
		if ( !is_singular( 'post' ) ) return;
		ob_start();
		require( self::$PLUGIN_PATH . 'templates/post_toolbar.php' );
		$toolbar = ob_get_contents();
    	ob_end_clean();
    	echo $toolbar;
	} 


    //* Customize the entry meta in the entry header (requires HTML5 theme support)
    public function custom_post_meta($post_info) {
        if ( !is_single() ) return $post_info;
		$post_info = sprintf(__('Published on %s by <span id="username">%s</span>', 'foodiepro'), '[post_date]', '[bp-author profile="true"]');
        return $post_info;
    }    	


}
