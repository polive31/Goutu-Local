<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Social_Like_Post extends Custom_Social_Interactions {

    // private $logged_in;
    private $post_type;

    public function __construct( $type ) {
        $this->post_type = $type;
    }

    public function get_html() {
        $post_id = get_the_id();
        $link_class='social-like-post';
        $link_id='';
        $link_url='#';
        $output='';

        // if( is_user_logged_in() ) {
        //     $link_class .= ' logged-in';
        // } 
        // else {
        //     $link_id='join-us';
        //     $link_url = '/connexion';
        // }
        
        if ($this->post_type=='recipe') {
            $tooltip_like = __('Like this recipe','foodiepro');
            $tooltip_dislike = __('Do not like this recipe anymore','foodiepro');
        }
        else {
            $tooltip_like = __('Like this post','foodiepro');
            $tooltip_dislike = __('Do not like this post anymore','foodiepro');           
        } 
                
        if( $this->is_liked_post( $post_id ) ) {
            $link_class .= ' is-liked';
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
                <a href="<?php echo $link_url;?>" id="<?php echo $link_id;?>" class="<?php echo $link_class; ?>" data-post-id="<?php echo $post_id; ?>">
                <div class="button-caption">
                    <?php 
                    $count_likes = $this->like_count( $post_id );
                    echo sprintf( _n( '%s like', '%s likes', $count_likes, 'foodiepro'), $count_likes);
                    ?>     
                </div>
                </a>
            <?php 
            // echo do_shortcode('[tooltip text="' . $tooltip . $tooltip_alt . '" pos="top"]');      
            Tooltip::display( $tooltip . $tooltip_alt, 'top');      
        
        $output = ob_get_contents();
        ob_end_clean();

        // $output = $this->after_output( $output );
        return $output;
    }

    public function display() {
        echo $this->get_html();
    }



}