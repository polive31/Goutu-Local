<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// class Custom_Recipe_Submission_Shortcodes extends WPURP_Premium_Addon {
class CRM_List_Shortcodes extends CRM_List {
    protected $taxonomies;
    protected $instructions;

    public function __construct() {
        // parent::__construct();

    }

   public function submissions_current_user_list_shortcode() {
        $output = '';
        $author = get_current_user_id();

        wp_enqueue_style( 'custom-post-list' );

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

    public function favorite_recipes_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            ), $atts );
        $output = '';

        if( !is_user_logged_in() ) return;
        $user_id = get_current_user_id();

        wp_enqueue_style( 'custom-post-list' );

        $Favs = new Custom_recipe_Favorite();

        $lists = get_query_var( 'list', false );
        if (!$lists) {
            // $lists = Custom_recipe_Favorite::get_lists();
            $lists = $Favs->get_lists();
        }
        else
            $lists = array( 0 =>$lists );

        $empty=true;
        foreach ($lists as $list) {
            $favorites = get_user_meta( $user_id, $Favs->get_meta_name($list), true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            $recipes = empty( $favorites ) ? array() : WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if( count( $favorites ) > 0 ) {
                $empty=false;
                $output .= $this->display_recipes( $recipes, false, $Favs->get_label($list) );
            }
        }
        if ($empty)
            $output .= '<div class="submitbox">' . __( "No recipes found.", 'foodiepro' ) . '</p>';

        return $output;
    }


}
