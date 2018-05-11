<?php

class Custom_Social_Like_Post {

    protected $version = '0.0.1';

    public function __construct(  ) {
        // Actions
        add_action( 'init', array( $this, 'assets' ) );

        // Ajax
        add_action( 'wp_ajax_like_post', array( $this, 'ajax_like_post' ) );
        add_action( 'wp_ajax_nopriv_like_post', array( $this, 'ajax_like_post' ) );

        add_action( 'wp_enqueue_scripts', 'social_like_post_script' );
    }

    public function social_like_post_script() {
        wp_enqueue_script( 'social_like_post', plugin_dir_url( __FILE__ ) . 'assets/social-like-post.js', array(), $this->version, false );
    }

    public function ajax_like_post() {
        if(check_ajax_referer( 'social_like_post', 'security', false ) ) {
            $post_id = $_POST['post_id'];;
            $user_id = get_current_user_id();

            $liked_posts = get_user_meta( $user_id, 'liked_posts', true );
            $liked_posts = is_array( $liked_posts ) ? $liked_posts : array();

            if( in_array( $post_id, $liked_posts ) ) {
                $key = array_search( $post_id, $liked_posts );
                unset( $liked_posts[$key ] );
            } else {
                $liked_posts[] = $post_id;
            }

            update_user_meta( $user_id, 'liked_posts', $liked_posts );
        }

        die();
    }

    public static function is_liked_post( $post_id ) {
        $user_id = get_current_user_id();

        $liked_posts = get_user_meta( $user_id, 'liked_posts', true );
        $liked_posts = is_array( $liked_posts ) ? $liked_posts : array();

        return in_array( $post_id, $liked_posts );
    }

    public function output() {
        $post_id = get_the_id();
        $link_class='social-like-post';
        $link_id='';
        $link_url='#';

        if( is_user_logged_in() ) {
            $link_id='join-us';
            $link_url = '/connexion';
        } 
        else {
            $link_class .= ' logged-in';
        }
        
        $tooltip_like = __('I like it','foodiepro');
        $tooltip_dislike = __('I don\'t like it anymore','foodiepro');
                
        if( $this->is_liked_post( $post_id ) ) {
            $link_class .= ' is-favorite';
            $tooltip=$tooltip_dislike;
            $tooltip_alt=$tooltip_like;
        }
       else {
            $tooltip=$tooltip_like;
            $tooltip_alt=$tooltip_dislike;
        }
                
        $tooltip='<div class="toggle">' . $tooltip . '</div>';
        $tooltip_alt='<div class="toggle" style="display:none">' . $tooltip_alt . '</div>';
                
        // $output = $this->before_output();
        
        ob_start();
?>
                <a href="<?php echo $link_url;?>" id="<?php echo $link_id;?>" class="<?php echo $link_class; ?>" data-recipe-id="<?php echo $post_id; ?>">
                <!-- <div class="button-caption"><?php echo __('Like It','foodiepro'); ?></div> -->
                </a>

        <?php echo do_shortcode( '[tooltip text="' . $tooltip . '" alt="' . $tooltip_alt . '" pos="top"]');

        $output .= ob_get_contents();
        ob_end_clean();

        // $output = $this->after_output( $output );
        return $output;
    }

}
