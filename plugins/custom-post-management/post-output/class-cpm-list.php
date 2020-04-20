<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CPM_List {

    private $post_type;

    public function __construct( $type='post' /* recipe */ ) {
        // parent::__construct( $type );
        // add_action( 'wp', array( $this, 'hydrate') );
        $this->post_type=$type;
    }


    /**
     * display
     *
     * @param  mixed $posts
     *  posts to be displayed as a list
     * @param  mixed $edit
     * display an edit button on each post ?
     * @param  mixed $headline
     * headline to be displayed before the posts list
     * @param  mixed $description
     * description of the current list, to be displayed under the headline
     * @return void
     */
    public function display( $posts, $edit=false, $headline='', $description='' ) {

        $output = '<h3>' . $headline . '</h3>';
        $output .= '<p>' . $description . '</p>';
        if ( count($posts) == 0 ) {
            $output .= CPM_Assets::get_label( $this->post_type, 'noposts' );
            return $output;
        }

        $output .= '<table class="custom-post-list">';

        foreach ( $posts as $post ) {
            $view_url = 'href="' . get_permalink( $post->ID ) . '" ';
            $view_title = 'title="' . __('Preview post', 'foodiepro') . '" ';
            $date_modified = sprintf(__('Modified on %s at %s', 'foodiepro'), get_the_modified_date('d/m/Y', $post->ID), get_the_modified_time('H\hi', $post->ID));
            $image = get_the_post_thumbnail_url($post->ID, 'mini-thumbnail');
            if (empty($image))
                $image = CPM_Assets::get_fallback_img_url( $this->post_type, 'small' );

            $item = '<tr class="post-list-row ' . $post->post_status . '">';
            $item .= '<td class="post-list-thumbnail"><a ' . $view_url . $view_title . '><img src="' . $image . '"></a></td>';
            $item .= '<td class="post-list-title"><a ' . $view_url . $view_title . '>' . get_the_title($post) . '</a></td>';

            if ($edit) {
                $item .= '<td class="post-list-date"><span>' . $date_modified . '</span></td>';
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
