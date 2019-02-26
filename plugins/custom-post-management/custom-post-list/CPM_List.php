<?php

class CPM_List {

    private $post_type;

    public function __construct( $type='post' /* recipe */ ) {
        // parent::__construct( $type );
        // add_action( 'wp', array( $this, 'hydrate') );
        $this->post_type=$type;
    }


    public function display( $posts, $edit=false, $title='' ) {
        $output = '';

        $output .= '<h3>' . $title . '</h3>';

        $output .= '<table class="custom-post-list">';

        $statuses = get_post_statuses();
        
        foreach ( $posts as $post ) {
 
            $view_url = 'href="' . get_permalink( $post->ID ) . '" ';
            $view_title = 'title="' . __('Preview post', 'foodiepro') . '" ';

            $item = '<tr class="post-list-row ' . $post->post_status . '">';
            $item .= '<td class="post-list-thumbnail"><a ' . $view_url . $view_title . '><img src="' . get_the_post_thumbnail_url( $post->ID ,'mini-thumbnail') . '"></a></td>';
            $item .= '<td class="post-list-title"><a ' . $view_url . $view_title . '>' . $post->post_title . '</a></td>';
            
            // $favinfo = Custom_post_Favorite::is_favorite_post( $post->ID() );
            // $favlist = $favinfo[1];
            // $favicon = Custom_post_Favorite::get_icon( $favlist );
            
            if ($edit) {

                $edit_url = apply_filters( 'cpm_edit_' . $this->post_type . '_url', get_permalink() . CPM_Assets::get_slug($this->post_type . '_form') . '?edit-' . $this->post_type . '=' . $post->ID);
                $edit_url = $edit?$edit_url:$view_url;

                $edit_title = apply_filters( 'cpm_edit_' . $this->post_type . '_title', __('Edit post', 'foodiepro') );
                $edit_title = $edit?$edit_title:$view_title;
     
                $item .= '<td class="post-list-status">' . $statuses[ $post->post_status ] . '</td>';
                $item .= '<td class="post-list-actions">';
                    $item .= CPM_Assets::get_edit_button( $this->post_type, 'post-edit' );
                
                    $item .= '<div class="post-delete" title="' . __('Delete post', 'foodiepro') . '"><i class="fa fa-trash csf-delete-post nodisplay" data-id="' . $post->ID . '" data-title="' . esc_attr( $post->post_title ) . '"></i></td>';
                    $item .= '</div>';
                $item .= '</td>';
            }
            else {
                // $item .= '<td class="post-list-list" title="' . Custom_post_Favorite::get_field( $favlist, 'label' ) . '">' . $favicon . '</td>';
            }
            $item .= '</tr>';
 
            $output .= apply_filters( 'cpm_post_list_item', $item, $post );
        }
        $output .= '</table>';
        return $output;
    }  


    /* AJAX CALLS 
    -----------------------------------------------------------*/
    public function ajax_user_delete_post() {

        if( ! is_user_logged_in() ) die();

        if(check_ajax_referer( 'custom_user_submissions_list', 'security', false ) ) {
            global $user_ID;

            $post_id = intval( $_POST['post'] );
            $post = get_post( $post_id );

            if( $post->post_type == 'post' && $post->post_author == $user_ID ) {
                wp_delete_post( $post_id );
                wp_send_json_success( 'post deleted !');
            }
            else {
                wp_send_json_error( 'post not deleted');
            }
        }
        else {
            wp_send_json_error( 'Nonce not recognized');
        }

        die();
    }



}