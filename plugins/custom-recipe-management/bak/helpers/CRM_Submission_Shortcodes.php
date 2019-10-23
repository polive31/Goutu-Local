<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// class Custom_Recipe_Submission_Shortcodes extends WPURP_Premium_Addon {
class CRM_Submission_Shortcodes extends CRM_Recipe_Submission {

    
    public function new_submission_shortcode() {
        if( !is_user_logged_in() ) {
            return '<p class="errorbox">' . __( 'Sorry, only registered users may submit recipes.', 'foodiepro' ) . '</p>';
        } else {
            if( isset( $_POST['submitrecipe'] ) ) {
                return $this->submissions_process();
            } else {
                return $this->submissions_form();
            }
        }
    }


    public function submissions_current_user_edit_shortcode() {
        $output = '';
        $user_id = get_current_user_id();
        
        if( isset( $_POST['submitrecipe'] ) ) {            
            $output .= $this->submissions_process();
        } 
        
        elseif( isset( $_GET['wpurp-edit-recipe'] ) ) {
            $recipe_id = $_GET['wpurp-edit-recipe'];
            $post = get_post( $recipe_id );
            $user = get_userdata( $user_id );
            
            if( $post->post_author == $user_id || current_user_can('administrator') ) {
                $output .= '<p class="submitbox">' . __( 'You can edit your recipe here, before submitting it.', 'foodiepro') . '</p>';
                $output .= $this->submissions_form( $recipe_id );
            }
        }        
        
        return $output;
    }
    

}