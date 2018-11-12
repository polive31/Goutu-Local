<?php

class Custom_Recipe_Display_Shortcodes extends Custom_WPURP_Templates {
    
    private $post_ID;
    private $post_content;
    
    public function __construct() {

        parent::__construct();
        add_shortcode( 'display-recipe', array( $this, 'recipe_shortcode' ) );

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



}