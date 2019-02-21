<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CPM_Assets {

	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	

	private static $slugs; 
	private static $labels; 
	private static $required; 
	private static $taxonomies; 
	private static $enqueued_styles; 
	private static $enqueued_scripts; 
	
	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
		// IMPORTANT : use wp as a hook, otherwise the archive will not be set yet and errors will occur
	}

	public function hydrate() {
		$default_slugs = array(
			'post_list'         => 'publier-articles',
            'post_form'         => 'saisie-article',
            // 'post_edit'              => 'modifier-article'
        );
		self::$slugs = apply_filters( 'cpm_page_slugs', $default_slugs );
		
		/* Location can be either a key of the self::$slugs array, or a post type */
		$default_enqueued_styles = array(
			'cpm-list'	=> array(
				'file'				=> 'assets/css/custom_posts_list.css', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'location'			=> array('post_list')
			),
			// 'cpm-post'	=> array(
			// 	'file'				=> 'assets/css/custom-submission-form.css', 
			// 	'uri'				=> self::$PLUGIN_URI, 
			// 	'dir'				=> self::$PLUGIN_PATH, 
			// 	'deps'				=> array('post-font'),
			// 	'location'			=> array('post')
			// ),			
			'cpm-submission-form'	=> array(
				'file'				=> 'assets/css/custom-submission-form.css', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array('cpm-select2'),
				'location'			=> array('post_form')
			),
			'cpm-select2'	=> array(
				'file'				=> 'vendor/select2/css/select2.min.css', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'location'			=> array('post_form')
			),
			'post-font'		=> array(
				'handle'			=> 'post-font', 
				'uri'				=> '//fonts.googleapis.com/css?family=Cabin', 
				'location'			=> array('post')
				)
			);
			self::$enqueued_styles = apply_filters( 'cpm_enqueued_styles', $default_enqueued_styles );
			
			$default_enqueued_scripts = array(
			'cpm-list'		=> array(
				'file'				=> 'assets/js/custom_posts_list.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array( 'jquery' ), 
				'footer'			=> true,
				'location'			=> array('post_list'),
				'data' 				=> array(
					'name'				=> 'custom_posts_list',
					'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
					'nonce' 			=> wp_create_nonce( 'custom_posts_list' ),
					'confirm_message' 	=> __( 'Are you sure you want to delete this post :', 'foodiepro' ),
					)
				),
			'cpm-submission'		=> array(
				'file'				=> 'assets/js/custom_post_submission.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array('cpm-tinymce', 'cpm-select2-taxonomies', 'jquery' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
			),
			'cpm-select2-taxonomies'=> array(
				'file'				=> 'assets/js/select2_taxonomies.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array( 'jquery', 'cpm-select2' ),  
				'footer'			=> true, 
				'location'			=> array('post_form'),
			),
			'cpm-select2'		=> array(
				'file'				=> 'vendor/select2/js/select2.min.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array( 'jquery' ),
				'footer'			=> true, 
				'location'			=> array('post_form'),
			),
			'cpm-select2-fr'	=> array(
				'file'				=> 'vendor/select2/js/i18n/fr.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array( 'cpm-select2' ),
				'footer'			=> true, 
				'location'			=> array('post_form'),
			),
			'cpm-tinymce'		=> array(
				'file'				=> 'vendor/tinymce/tinymce.min.js', 
				'uri'				=> self::$PLUGIN_URI, 
				'dir'				=> self::$PLUGIN_PATH, 
				'deps'				=> array( 'jquery' ), 
				'footer'			=> true,
				'location'			=> array('post_form'),
				'data'				=> array(
					'name'			=> 'custom_post_submission_form',
					'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
					'nonce' 			=> wp_create_nonce( 'custom_post_submission_form' ),
					// 'postid' 			=> get_the_ID(),
					'placeholder' 		=> self::$PLUGIN_URI . '/img/image_placeholder.png',
					'fileTypes' 		=> self::ATTACHMENT_FORMATS,
					'wrongFileType' 	=> sprintf(__('Authorized file formats are : %s','foodiepro'),implode(', ',self::ATTACHMENT_FORMATS)),
					'maxFileSize' 		=> self::MAX_ATTACHMENT_SIZE_KB,
					'fileTooBig' 		=> sprintf(__('The maximum file size is %s kB','foodiepro'), self::MAX_ATTACHMENT_SIZE_KB),
					'deleteImage' 		=> __('Do you really want to delete this image ?','foodiepro')
				),
			),
		);
		self::$enqueued_scripts = apply_filters( 'cpm_enqueued_scripts', $default_enqueued_scripts );
		
		$default_labels = array(
			'post' => array(
				'title'				=> _x( 'Post Title', 'post', 'foodiepro' ),
				'edit_button'		=> _x( 'Edit Post', 'post', 'foodiepro' ),
				'new_button'		=> _x( 'New Post', 'post', 'foodiepro' ),
				'new1'				=> _x( 'Write your new post on this page.', 'post', 'foodiepro' ),
                'new2'				=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it will be visible to others according to your visibility preferences.', 'post', 'foodiepro' ),
				'edit1'				=> _x( 'Edit your existing post on this page.', 'post', 'foodiepro' ),
                'edit2' 			=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it wil be visible to others according to your visibility preferences.', 'post', 'foodiepro' ),
				'draft1'			=> _x( 'Post saved as a draft.','post', 'foodiepro'),
				'draft2'			=> _x( 'It will not be visible on the site, but you can edit it at any time and submit it later.','post','foodiepro'),
				'back'				=> _x( 'Back to <a href="%s">my posts</a>.', 'post', 'foodiepro'),
				'publish-admin'		=> _x( 'Dear administrator, this post is now <a href="%s">published</a>.', 'post', 'foodiepro' ),
				'publish-user'		=> _x( 'Post submitted! Thank you, your post is now awaiting moderation.', 'post', 'foodiepro' ),
				'required'			=> _x( 'In order for your post to be published, please fill-in those required fields:', 'post', 'foodiepro' ),
			),
		);
		self::$labels = apply_filters( 'cpm_labels', $default_labels );		
		
        $default_taxonomies=array(
			'post'	=> array(
				'category' => array(
					'multiselect' 	=> false,
					'exclude' 		=> array('9543'),
					'exclude_tree' 	=> array('10021'),
					'orderby' 		=> 'name',
					'labels'		=> array(
						'singular_name'=>__( 'Categories', 'foodiepro' ),
					),
				),         
				'post_tag' => array(
					'multiselect' => true,
					'exclude' => '',
					'orderby' => 'name',
					'labels'=>array(
						'singular_name'=>__( 'Keywords', 'foodiepro' ),
					),
				),
			),
        );
		self::$taxonomies = apply_filters( 'cpm_taxonomies', $default_taxonomies );		
		
        $default_required = array(
			'post'	=> array(
				'post_title'  		=> __('Post Title','foodiepro'),
				'post_content'      => __('Post Content', 'foodiepro'),
				'post_category'		=> __('Post Category', 'foodiepro'),// IMPORTANT for taxonomies, name here must be <post_type>_<taxonomy>
				// 'post_thumbnail'		=> __('Post Featured Image.', 'foodiepro'),
				// 'post_post_tag'		=> __('Post Tag.', 'foodiepro'),//
			),
        );    
        self::$required = apply_filters( 'cpm_required', $default_required );
	}


	public function scripts_styles_enqueue() {
		foreach (self::$enqueued_styles as $handle => $style) {
			$enqueue = false;
			foreach ($style['location'] as $location) {
				// $singular = is_singular($location);
				// $page = is_page( self::get_slug($location) );
				if ( is_page( self::get_slug($location) )  || is_singular($location) ) {
					$enqueue=true;
					break;
				}
			} 
			if ($enqueue) {
				$args=$style;
				$args['handle']=$handle;
				custom_enqueue_style( $args );
			}
		}
		
		foreach (self::$enqueued_scripts as $handle => $script) {
			$enqueue = false;
			foreach ($script['location'] as $location) {
				if ( is_page( self::get_slug($location)) || is_singular($location) ) {
					$enqueue=true;
					break;
				}
			} 
			if ($enqueue) {
				$args=$script;
				$args['handle']=$handle;
				custom_enqueue_script( $args );
			}
		}
	}

	
	/********************************************************************************
	*********************         GETTERS / SETTERS       ***************************
	********************************************************************************/
    public static function get_slug( $action ) {
		if ( !isset(self::$slugs[$action]) ) return '';
        return self::$slugs[$action];
	}

    public static function get_label( $post_type, $id ) {
		if ( !isset(self::$labels[$post_type][$id]) ) return '';
        return self::$labels[$post_type][$id];
	}	

    public static function get_required( $post_type ) {
		if ( !isset(self::$required[$post_type]) ) return '';
        return self::$required[$post_type];
	}	

    public static function get_taxonomies( $post_type, $taxonomy=false, $field=false ) {
		if ( !isset(self::$taxonomies[$post_type]) ) return '';
		$tax = self::$taxonomies[$post_type];
		if ($taxonomy) 
			$tax = isset($tax[$taxonomy])?$tax[$taxonomy]:false;
		if ($field)
			$tax = isset($tax[$field])?$tax[$field]:false;
		return $tax;
	}		

    // public static function get_post_image_url( $post ) {
    //     $url = get_the_post_thumbnail_url( $post->ID ,'mini-thumbnail');
    //     return $url;
	// }
	





}