<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Social_Interactions {

    protected static $PLUGIN_PATH;
    protected static $PLUGIN_URI;

    // public function __construct( $logged_in ) {
    public function __construct() {
        // $this->logged_in = $logged_in;
        self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
        self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

        // Assets
        add_action( 'wp_enqueue_scripts', array( $this, 'social_like_post_script' ) );

        // Ajax
        add_action( 'wp_ajax_like_post', array( $this, 'ajax_like_post' ) );
        add_action( 'wp_ajax_nopriv_like_post', array( $this, 'ajax_like_post' ) );

        // Debug
        // add_action( 'genesis_before_content', array( $this, 'display_debug_information' ) );
    }

    public function display_debug_information() {
        $userid = get_current_user_id();
        $postid = get_the_id();
        // $usermeta = get_user_meta( $userid, 'liked_posts', true );
        $postmeta = get_post_meta( $postid, 'liking_users', true );
        echo '<pre>' . 'Current user : ' . $userid . '</pre>';
        // echo '<pre>' . print_r( $usermeta) . '</pre>';
        echo '<pre>' . print_r( $postmeta) . '</pre>';
    }

    public function social_like_post_script() {
        if (! is_single() ) return;
            // wp_enqueue_script( 'custom-post-like', self::$PLUGIN_URI . '/assets/js/social-like-post.js', array( 'jquery' ), CHILD_THEME_VERSION, false );
            custom_enqueue_script(
                'custom-post-like',
                '/assets/js/social-like-post.js',
                self::$PLUGIN_URI,
                self::$PLUGIN_PATH,
                array( 'jquery' ),
                CHILD_THEME_VERSION,
                false
            );
            wp_localize_script( 'custom-post-like', 'custom_post_like', array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce' => wp_create_nonce( 'custom_post_like' ))
        );
    }

    public function ajax_like_post() {
        if(check_ajax_referer( 'custom_post_like', 'security', false ) ) {
            // echo 'Ajax security OK';
            $post_id = $_POST['post_id'];
            $liking_users = get_post_meta( $post_id, 'liking_users', true );
            $liking_users = is_array( $liking_users ) ? $liking_users : array();

            $user_id = is_user_logged_in()?get_current_user_id():self::get_user_ip();

            if ( in_array( $user_id, $liking_users ) ) {
                $key = array_search( $user_id, $liking_users );
                unset( $liking_users[$key] );
                // echo '\n Removed user ID from post liking_users meta';
                // echo print_r( $liking_users );
                do_action( 'post_dislike', $user_id, $post_id);
            } else {
                $liking_users[] = $user_id;
                // echo '\n Added user ID to post liking_users meta';
                do_action( 'post_like', $user_id, $post_id);
            }

            $count = count($liking_users);
            echo sprintf( _n( '%s like', '%s likes', $count, 'foodiepro'), $count);
            update_post_meta( $post_id, 'liking_users', $liking_users );
            do_action( 'after_post_like_meta_update', $user_id, $post_id);

        }
        else {
            // echo __('Please refresh the page before','foodiepro');
        }

        die();
    }

    public static function is_liked_post( $post_id ) {
        $user_id = is_user_logged_in()?get_current_user_id():self::get_user_ip();

        $liking_users = get_post_meta( $post_id, 'liking_users', true );
        $liking_users = is_array( $liking_users ) ? $liking_users : array();

        return in_array( $user_id, $liking_users );
    }

    public static function like_count( $post_id ) {
        if ( is_null($post_id) ) return false;

        $liking_users = get_post_meta( $post_id, 'liking_users', true );
        $liking_users = is_array( $liking_users ) ? $liking_users : array();

        return count( $liking_users );
    }


    /* Get the user ip (from WP Beginner)
    -------------------------------------------------------------*/
    public static function get_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return apply_filters( 'wpb_get_ip', $ip );
    }

}

new Custom_Social_Interactions();
