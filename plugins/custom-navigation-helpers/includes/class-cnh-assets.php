<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CNH_Assets {

    const QUERY_VARS = array(
        'post_type' ,
		'author' 	,
        'ingredient',
        'course'    ,
        'cuisine'   ,
        'season'    ,
        'occasion'  ,
        'diet'      ,
        'difficult' ,
        'category'  ,
        'post_tag'  ,
	);

    const TAXONOMY = array(
        'ingredient'    => array( 'orderby'=> 'name'),
        'course'        => array( 'orderby'=> 'description'),
        'cuisine'       => array( 'orderby'=> 'name'),
        'season'        => array( 'orderby'=> 'description'),
        'occasion'      => array( 'orderby'=> 'description'),
        'diet'          => array( 'orderby'=> 'description'),
        'difficult'     => array( 'orderby'=> 'description'),
        'category'      => array( 'orderby'=> 'name'),
		'post_tag'      => array( 'orderby'=> 'name')
	);

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

    public function __construct()
    {
        self::$PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
        self::$PLUGIN_URI = plugin_dir_url(dirname(__FILE__));
    }

	/* Class attributes initialization
	--------------------------------------------- */
	public function enqueue_cnh_scripts() {
	  	if ( is_archive() || is_search() ) {
			wp_enqueue_script( 'jquery-masonry' );
			custom_enqueue_script( 'cnh-masonry-layout', '/assets/js/masonry-layout.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery', 'jquery-masonry' ), CHILD_THEME_VERSION, true);
			// custom_enqueue_script( 'cnh-infinite-scroll', '/assets/js/infinite-scroll.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array( 'jquery' ), CHILD_THEME_VERSION, true);
	  	};
    }

	/* GETTER functions
	---------------------------------------------------------------------------*/

    public static function get_orderby($tax) {
        if (!isset(self::TAXONOMY[$tax])) return;
        return self::TAXONOMY[$tax]['orderby'];
    }

    public static function get_queryvars() {
        return self::QUERY_VARS;
	}

    public static function get_term_image($term=null, $size = 'full', $class = '', $imgclass='', $fallback_url='')
    {
        $html='';
        if (class_exists('WPCustomCategoryImage')) {
            $id=is_object($term)?$term->term_id:null;
            $name=is_object($term)?$term->name:null;
            $atts = array(
                'size'       => $size,
                'term_id'    => $id,
                'alt'        => $name,
                'class'      => $imgclass,
                'onlysrc'    => false,

            );
            $html = WPCustomCategoryImage::get_category_image($atts);
        }
        if (empty($html)) {
            if (empty($fallback_url))
                $url= self::$PLUGIN_URI . '/assets/img/fallback-ingredient.png';
            else
                $url= $fallback_url;
            $html=picture($url);
        }
        $html = "<div class='$class'>$html</div>";
        return $html;
    }

}
