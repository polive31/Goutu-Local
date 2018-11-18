<?php

class Custom_Recipe_Shortcodes extends Custom_WPURP_Templates {
    
    private $post_ID;
    private $post_content;

    public static $id;    
    
    public function __construct() {
        parent::__construct();
        add_shortcode( 'display-recipe', array( $this, 'recipe_shortcode' ) );

        // Customize recipe timer shortcode
        remove_shortcode('recipe-timer');
        add_shortcode('recipe-timer', array($this,'custom_recipe_timer')); 

        // Initialize timer shortcode instance counter
        self::$id=0; 
    }

    public function recipe_shortcode( $options ) {
        $options = shortcode_atts( array(
            'id' => 'random', // If no ID given, show a random recipe
            'template' => 'default'
        ), $options );

        $recipe_post = null;

        if( $options['id'] == 'random' ) {
            $posts = get_posts(array(
                'post_type' => WPURP_POST_TYPE,
                'posts_per_page' => 1,
                'orderby' => 'rand',
            ));

            $recipe_post = isset( $posts[0] ) ? $posts[0] : null;
        } elseif( $options['id'] == 'latest' ) {
            $posts = get_posts(array(
                'post_type' => WPURP_POST_TYPE,
                'posts_per_page' => 1,
            ));

            $recipe_post = isset( $posts[0] ) ? $posts[0] : null;
        } else {
            $recipe_post = get_post( intval( $options['id'] ) );
        }

        if( !is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' && ( !is_feed() || WPUltimateRecipe::option( 'recipe_rss_feed_shortcode', '1' ) == '1' ) ) {

            if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
                $type = 'amp';
                $template = null;
            } else {
                $type = is_feed() ? 'feed' : 'recipe';
                $template = is_feed() ? null : $options['template'];
            }

            $output = $this->display_recipe( '', $recipe_post );

        }
        else
        {
            $output = '';
        }

        return do_shortcode( $output );
    }


    public function custom_recipe_timer( $atts, $content ) {
        self::$id++;

        $atts = shortcode_atts( array(
            'seconds' => '0',
            'minutes' => '0',
            'hours' => '0',
        ), $atts );

        $seconds = intval( $atts['seconds'] );
        $minutes = intval( $atts['minutes'] );
        $hours = intval( $atts['hours'] );

        $seconds = $seconds + (60 * $minutes) + (60 * 60 * $hours);
        if ( $seconds <= 0 ) return $content;

        // $msg = sprintf(__('Timer started for %s','foodiepro'), self::$id);

        $timer = '<a href="#" class="wpurp-timer-link" title="' . __( 'Click to Start Timer', 'foodiepro' ) . '">';
        $timer .= '<span class="wpurp-timer" data-seconds="' . esc_attr( $seconds ) . '">';
        $timer .= $content;
        $timer .= '</span></a>';

        return $timer;
    } 




}