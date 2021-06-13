<?php

/**
 * Class Custom_Post_Submission
 *
 *
 * Provides functions to generate front end submission form and submission process
 * Can be adapted via parameters and hooks, to any post type
 *
 * @author  Pascal Olive
 *
 * @since 1.0
 *
 * @param array $args {
 *   Parameters for class constructor. Allows to adapt the submission services to a given Custom Post Type
 *   @param  string  $type   Post type. Default 'post'. Accepts 'recipe', or any other existing CPT.
 * }
 *
 */


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CPM_Save
{

    /**
     *
     * Can be called either from AJAX or during form submission
     * Gets a post ID if new post
     * Determines the proper post state based on current state & action
     * Saves the post core data (title, content)
     *
     * @param  mixed $type
     * @param  mixed $_POST
     * Array('post_id', '$type_title', '$type_content')
     * @return void
     */
    public static function save_post( $action )
    {

    //    The following array is going to be populated
    //    all through this function
    //     $post = array(
    //         'ID'            => '',
    //         'post_author'   => '',
    //         'post_type'     => '',
    //         'post_status'   => '',
    //         'post_title'    => '',
    //         'post_content'  => '',
    //     );

        /* Get post ID & update status */
        $id = isset($_POST['submit_post_id']) ? intval($_POST['submit_post_id']) : false;
        $updated_post = get_post($id);
        $new = empty($updated_post);
        $post['ID']=$new?0:$id;

        /* Check if authorized user */
        if (!$new) {
            if ($updated_post->post_type != $_POST['submit_post_type']) return false;
            if ($updated_post->post_author != get_current_user_id() && !current_user_can('administrator')) return false;
        }

        /* Set post type  */
        $post['post_type'] = $_POST['submit_post_type'];

        /* Set post author (don't change on existing posts) */
        if ($new)
            $post['post_author'] = get_current_user_id();
        else
            $post['post_author'] = $updated_post->post_author;

        /* Set post status */
        $status='restored';
        if ($action=='autosave') {
            if ( !in_array($updated_post->post_status,array('auto-draft','restored')) ) {
                /* Autosave disabled for pending or published posts
                ... since it would make their state change without reason */
                echo 'Autosave disabled except for new & restored posts.';
                die();
            }
            elseif ($updated_post->post_status == 'draft' && current_user_can('edit_posts') )
                $status='draft';
        }
        elseif ( ( $action=='draft'|| $action=='publish') && current_user_can('edit_posts')) {
            // Post cannot be set to pending yet because required fields verification must happen later
            $status='draft';
        }
        $post['post_status']=$status;

        /* Get post title */
        $title = empty($_POST['post_title']) ? __('Untitled', 'foodiepro') : $_POST['post_title'];
        $title = sanitize_text_field($title);
        $post['post_title']=$title;

        /* Update post slug after title */
        $post['post_name']= sanitize_title($title);

        /* Get post content */
        $content = isset($_POST['post_content']) ? $_POST['post_content'] : '';
        $post['post_content']=wp_kses_post($content);
        // NOTA : foodiepro_esc prevents proper table formatting.... missing some attributes probably
        // $post['post_content']=foodiepro_esc($content);

        /* Get post video link */
        $video_url = isset($_POST['post_video']) ? $_POST['post_video'] : '';
        $video_url = esc_url($video_url, array('https'));

        /* Save post : although insert_post can update an existing post, it will also modify
            the creation date, which is not wanted */
        if ($new) {
            $post_id = wp_insert_post($post, true);
            $post_meta_id = add_post_meta( $post_id, 'post_video', $video_url, true );
        }
        else {
            $post_id = wp_update_post($post, true);
            $post_meta_id = update_post_meta( $post_id, 'post_video', $video_url );
        }

        return $post_id;
    }


    /* IMAGE
    -----------------------------------------------------------------*/
    public static function insert_attachment($file_handler, $post_id, $alt)
    {
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
            return false;;
        }

        if (!isset($_FILES['file']))
            return false;

        $file = $_FILES['file'];

        if ( empty($file['name'] ))
            return false;

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $post_data = array(
            'post_title'     => $alt,
        );

        $attach_id = media_handle_upload($file_handler, $post_id, $post_data);
        update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);

        return $attach_id;
    }


    /* TAXONOMY TERMS
    -----------------------------------------------------------------*/
    public static function save_tax_terms( $post_type, $post_id ) {
        if (!is_int($post_id)) return false;
        $taxonomies = CPM_Assets::get_taxonomies($post_type);
        $result = true;
        foreach ($taxonomies as $taxonomy => $options) {
            $terms = isset($_POST[$post_type . '_' . $taxonomy]) ? $_POST[$post_type . '_' . $taxonomy] : false;
            if ($terms) {
                if (!is_array($terms)) {
                    $terms = array(intval($terms));
                } else {
                    $terms = array_map('intval', $terms);
                }
            } else
                $terms = null;
            $term_ids = wp_set_object_terms($post_id, $terms, $taxonomy);
            $result = !is_wp_error($term_ids) && $result;
        }
        return $result;
    }



}
