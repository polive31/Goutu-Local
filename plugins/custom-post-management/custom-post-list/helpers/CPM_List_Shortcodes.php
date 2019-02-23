<?php

class CPM_List_Shortcodes {

    public function new_post_button( $atts ) {
        $atts = shortcode_atts( array(
            'post_type' => 'post', // 'post', 'recipe'
            'text' => '', 
        ), $atts );
    
        $post_type = $atts['post_type'];
        $text = CPM_Assets::get_label( $post_type, 'new_button' );
        $text = esc_html( $text );

        $out = do_shortcode('[permalink slug="' . CPM_Assets::get_slug( $post_type . '_form') . '" class="black-button"]' . $text . '[/permalink]');

        return $out;
    }

   public function custom_submission_form_list_shortcode( $atts, $content ) {
        $atts = shortcode_atts( array(
			'post_type' => 'post', // 'post', 'recipe'
		), $atts );

        extract($atts);
        $content = esc_html( $content );

        $output = '';
        $author = get_current_user_id();

        // wp_enqueue_style( 'csf-list' );
        // wp_enqueue_script( 'csf-list' );

        if( $author !== 0 ) {
 
            $args = array(
                'author' => $author,
                'numberposts' => -1,
                'category' => 0, 
                'orderby' => 'date',
                'order' => 'DESC', 
                'include' => array(),
                'exclude' => array(), 
                'meta_key' => '',
                'meta_value' =>'', 
                'post_type' => $post_type,
                'post_status' => array( 'publish', 'private', 'pending', 'draft' ),
                'suppress_filters' => true
            );

            $posts = get_posts( $args );

            if( count( $posts ) !== 0 ) {

                $List = new CPM_List( $post_type );
                $output .= $content?$content:'<p>' . __('Here is the list of the posts that you created, and their status. You can choose to edit them, change their visibility, or delete them.', 'foodiepro') . '</p>';
                $output .= $List->display( $posts, true );
            }
        }
        return $output;
    }  




}
