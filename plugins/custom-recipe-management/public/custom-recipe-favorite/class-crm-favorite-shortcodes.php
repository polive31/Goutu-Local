<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// class Custom_Recipe_Favorite extends WPURP_Template_Block {
class CRM_Favorite_Shortcodes {


    public function favorite_recipes_shortcode( $atts, $content ) {
        $atts = shortcode_atts( array(
			// 'post_type' => 'post', // 'post', 'recipe'
		), $atts );

        if( !is_user_logged_in() ) return;
        $user_id = get_current_user_id();

        $PostList = new CPM_List( 'recipe' );

        $lists = get_query_var( 'list', false );
        if ($lists)
            $lists = array( 0 => $lists );
        else
            $lists = CRM_Favorite::get_lists();
            
        $empty=true;
        $output='';
        foreach ($lists as $list) {
            $favorites = get_user_meta( $user_id, CRM_Favorite::get_meta_name($list), true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            $args = array(
                'numberposts' => -1,
                'category' => 0, 
                'orderby' => 'date',
                'order' => 'DESC', 
                'include' => $favorites,
                'exclude' => array(), 
                'meta_key' => '',
                'meta_value' =>'', 
                'post_type' => 'recipe',
                'post_status' => array( 'publish', 'private', 'pending', 'draft' ),
                'suppress_filters' => true
            );
            $recipes = get_posts( $args );

            // $recipes = empty( $favorites ) ? array() : WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if( count( $favorites ) > 0 ) {
                $empty=false;
                $output .= $PostList->display( $recipes, false, CRM_Favorite::get_label($list) );
            }
        }
        if ($empty)
            $output .= '<div class="submitbox">' . __( "No recipes found.", 'foodiepro' ) . '</p>';
        
        return $output;
    }    



}