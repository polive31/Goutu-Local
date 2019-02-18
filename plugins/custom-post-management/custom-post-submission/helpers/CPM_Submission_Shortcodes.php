<?php

class CPM_Submission_Shortcodes {

    public function custom_submission_form_shortcode( $atts ) {
        $atts = shortcode_atts( array(
			'post_type' => 'post', // 'post', 'recipe'
		), $atts );
        extract($atts);

        if( !is_user_logged_in() ) {
            return '<p class="errorbox">' . __( 'Sorry, only registered users may submit recipes.', 'foodiepro' ) . '</p>';
        }         

        // wp_enqueue_style( 'csf-' . $post_type );
        // wp_enqueue_script( 'csf-' . $post_type );        
        
        $output = '';
        $user_id = get_current_user_id();

        $Form = new CPM_Submission( $post_type );
        
        if( isset( $_POST['submit' . $post_type] ) ) {            
            /* Submit post */
            $output .= $Form->submit();
        } 
        elseif( isset( $_GET['edit-' . $post_type ] ) ) {
            /* Edit post */
            $post_id = $_GET['edit-' . $post_type ];
            $post = get_post( $post_id );
            $user = get_userdata( $user_id );
            
            if( $post->post_author == $user_id || current_user_can('administrator') ) {
                $output .= $Form->display( $post_id, 'edit' );
            }
        }
        else {
            /* New post */
            $output = $Form->display();
        }       
        
        return $output;
    }
    
    // public function custom_submission_form_new_shortcode( $atts ) {
    //     $atts = shortcode_atts( array(
    //         'post_type' => 'post', // 'post', 'recipe'
    //     ), $atts );
    //     extract( $atts );
        
    //     if( !is_user_logged_in() ) {
    //         return '<p class="errorbox">' . __( 'Sorry, only registered users may submit recipes.', 'foodiepro' ) . '</p>';
    //     } 
    //     else {
            
    //         // wp_enqueue_style( 'csf-' . $post_type );
    //         // wp_enqueue_script( 'csf-' . $post_type );   
            
    //         $Form = new CPM_Submission( $post_type );
    //         if( isset( $_POST['submitpost'] ) ) {
    //             return $Form->submit();
    //         } 
    //         else {
    //             $output = $Form->display();
    //         }
    //     }
    // }    
}
