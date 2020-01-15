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

        if ( count($posts) == 0 ) {
            $output = CPM_Assets::get_label( $this->post_type, 'noposts' );
            return $output;
        }

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
                $item .= '<td class="post-list-status">' . $statuses[ $post->post_status ] . '</td>';
                $item .= '<td class="post-list-actions">';
                    $item .= CPM_Assets::get_edit_button( $post, $this->post_type, 'post-edit' );
                    $item .= CPM_Assets::get_delete_button( $post, $this->post_type, 'post-delete' );
                $item .= '</td>';
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

        if(check_ajax_referer( 'custom_posts_list', 'security', false ) ) {
            global $user_ID;

            $post_id = intval( $_POST['post'] );
            $post = get_post( $post_id );

            if( $post->post_author == $user_ID ) {
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
