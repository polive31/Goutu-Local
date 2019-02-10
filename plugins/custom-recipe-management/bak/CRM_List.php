<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_List {

    public function __construct() {
    }


/********************************************************************************
****                   RECIPE LIST FUNCTIONS                           **********
********************************************************************************/

    public function display_recipes( $recipes, $edit=false, $title='' ) {
        $output = '';

        $output .= '<h3>' . $title . '</h3>';

        $output .= '<table class="custom-recipe-list">';

        $statuses = get_post_statuses();
        
        foreach ( $recipes as $recipe ) {
            $image_url = $recipe->image_ID() > 0 ? $recipe->image_url( 'mini-thumbnail' ) : WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
 
            $view_url = 'href="' . get_permalink($recipe->ID()) . '" ';    
            $view_title = 'title="' . __('Preview recipe', 'foodiepro') . '" ';
            // $edit_url = $edit?'href="' . get_permalink() . self::RECIPE_EDIT_SLUG . '?wpurp-edit-recipe=' . $recipe->ID() . '" ':$view_url;   
            $edit_url = $edit?'href="' . get_permalink() . self::RECIPE_EDIT_SLUG . '?wpurp-edit-recipe=' . $recipe->ID() . '" ':$view_url;   
            $edit_title = $edit?'title="' . __('Edit recipe', 'foodiepro') . '" ':$view_title;
 
            $item = '<tr class="recipe-list-row ' . $recipe->post_status() . '">';
            // $item .= '<td class="recipe-list-thumbnail"><a ' . $edit_url . $edit_title . '><img src="' . $image_url . '"></a></td>';
            $item .= '<td class="recipe-list-thumbnail"><a ' . $view_url . $view_title . '><img src="' . $image_url . '"></a></td>';
            // $item .= '<td class="recipe-list-title"><a ' . $edit_url . $edit_title . '>' . $recipe->title() . '</a></td>';
            $item .= '<td class="recipe-list-title"><a ' . $view_url . $view_title . '>' . $recipe->title() . '</a></td>';

            $favinfo = Custom_Recipe_Favorite::is_favorite_recipe( $recipe->ID() );
            $favlist = $favinfo[1];
            $favicon = Custom_Recipe_Favorite::get_icon( $favlist );
            

            if ($edit) {
                $item .= '<td class="recipe-list-status">' . $statuses[ $recipe->post_status() ] . '</td>';
                $item .= '<td class="recipe-list-actions">';
                    $item .= '<div class="recipe-edit" title="' . __('Edit recipe', 'foodiepro') . '">';
                    $item .= '<a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a>';
                    // $item .= '<a ' . $view_url . $view_title . '><i class="fa fa-eye"></i></a>';
                    $item .= '</div>';
                
                    $item .= '<div class="recipe-delete" title="' . __('Delete recipe', 'foodiepro') . '"><i class="fa fa-trash user-submissions-delete-recipe nodisplay" data-id="' . $recipe->ID() . '" data-title="' . esc_attr( $recipe->title() ) . '"></i></td>';
                    $item .= '</div>';
                $item .= '</td>';
            }
     else {
     $item .= '<td class="recipe-list-list" title="' . Custom_Recipe_Favorite::get_field( $favlist, 'label' ) . '">' . $favicon . '</td>';
     }
            $item .= '</tr>';
 
            $output .= apply_filters( 'custom_wpurp_recipe_list_item', $item, $recipe );
        }
        $output .= '</table>';
        return $output;
    }  




/********************************************************************************
****                           AJAX CALLBACKS                          **********
********************************************************************************/

    public function ajax_user_delete_recipe() {

        if( ! is_user_logged_in() ) die();

        if(check_ajax_referer( 'custom_user_submissions_list', 'security', false ) ) {
            global $user_ID;

            $recipe_id = intval( $_POST['recipe'] );
            $recipe = get_post( $recipe_id );

            if( $recipe->post_type == 'recipe' && $recipe->post_author == $user_ID ) {
                wp_delete_post( $recipe_id );
                wp_send_json_success( 'Recipe deleted !');
            }
            else {
                wp_send_json_error( 'Recipe not deleted');
            }
        }
        else {
            wp_send_json_error( 'Nonce not recognized');
        }

        die();
    }


}
