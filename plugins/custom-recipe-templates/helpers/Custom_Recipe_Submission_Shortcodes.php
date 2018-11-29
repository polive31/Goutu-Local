<?php

// class Custom_Recipe_Submission_Shortcodes extends WPURP_Premium_Addon {
class Custom_Recipe_Submission_Shortcodes extends Custom_WPURP_Recipe_Submission {
    protected $taxonomies;
    protected $instructions;

    public function __construct() {
        // parent::__construct();

        // Submission pages shortcodes
        add_shortcode( 'custom-wpurp-submissions-new-recipe', array( $this, 'new_submission_shortcode' ) );
        add_shortcode( 'custom-wpurp-submissions-current-user-edit', array( $this, 'submissions_current_user_edit_shortcode' ) );
        
        // Created & Favorite recipes lists shortcodes
        add_shortcode( 'custom-recipe-submissions-current-user-list', array( $this, 'submissions_current_user_list_shortcode' ) );
        add_shortcode( 'custom-wpurp-favorites', array( $this, 'favorite_recipes_shortcode' ) );     
    }

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

   public function submissions_current_user_list_shortcode() {
        $output = '';
        $author = get_current_user_id();

        if( $author !== 0 ) {
            // $output .= 'In Custom User Submission Class !';
            $recipes = WPUltimateRecipe::get()->query()->author( $author )->post_status( array( 'publish', 'private', 'pending', 'draft' ) )->get();

            if( count( $recipes ) !== 0 ) {
                // $output .= '<ul class="wpurp-user-submissions-current-user-edit">';
                $output .= '<p>' . __('Here is the list of the recipes that you created, and their status. You can choose to edit them, change their visibility, or delete them.', 'foodiepro') . '</p>';
                $output .= $this->display_recipes( $recipes, true );
            }
        }
        return $output;
    }  

    public function favorite_recipes_shortcode( $options ) {
        $options = shortcode_atts( array(), $options );
        $output = '';
        $user_id = get_current_user_id();

        if( $user_id !== 0 ) {
            $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            $recipes = empty( $favorites ) ? array() : WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if( count( $favorites ) == 0 || count( $recipes ) == 0 ) {
                $output .= '<p class="submitbox">' . __( "You don't have any favorite recipes.", 'wp-ultimate-recipe' ) . '</p>';
            } else {
                // $output .= '<p>' . __('Here is the list of the recipes that you bookmarked.', 'foodiepro') . '</p>';
                $output .= $this->display_recipes( $recipes, false);
            }
        }
        return $output;
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
