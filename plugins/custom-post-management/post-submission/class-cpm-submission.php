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

class CPM_Submission
{
    private $post_type;
    public function __construct($type)
    {
        $this->post_type = $type;
    }

    /********************************************************************************
     *********************         ACTIONS CALLBACKS       ***************************
     ********************************************************************************/
    // Set the language for select2 dropdown script
    public function add_lang_to_select($output)
    {
        return str_replace('<select', '<select lang="fr"', $output);
    }

    /* HOOKS */
    public static function allow_empty_submission_form($empty, $post_data)
    {
        if ($post_data['post_status'] == 'auto-draft') {
            $empty = false;
        }
        return $empty;
    }

    /********************************************************************************
     *********************     MAIN SUBMISSION FORM OUTPUT      *********************
     ********************************************************************************/

    public function display($post_ID = false, $state = 'new')
    {
        if (!$post_ID) {
            // Create autosave when submission page viewed
            global $user_ID;
            $post_draft = array(
                'post_status' => 'auto-draft',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => $this->post_type,
                'post_title' => '',
                'post_content' => '',
            );
            $post_ID = wp_insert_post($post_draft);
        }

        // Variables for form template display
        $required = CPM_Assets::get_required($this->post_type);
        $required_fields = array_keys($required);
        $post = get_post($post_ID);
        $post_type=$this->post_type;

        $args=compact('state','post','required_fields','post_type');
        $output=CPM_Assets::get_template_part('form', false, $args);

        return $output;
    }



    /********************************************************************************
     *********************         SPECIFIC POST CALLBACKS       ********************
     ********************************************************************************/

    public function add_post_specific_section($post, $required_fields)
    {
        $args=compact('post','required_fields');
        $form = CPM_Assets::get_template_part('form','post',$args);

        return $form;
    }


    /********************************************************************************
     ****               SUBMISSION PROCESS MANAGEMENT                       **********
     ********************************************************************************/
    public function submit( $origin='submit' )
    {

        $successmsg = '';
        $post_type = $this->post_type;

        if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action'])) {

            $valid = wp_verify_nonce($_POST['submit' . $post_type], $post_type . '_submit');

            if (!$valid || !is_user_logged_in() || !current_user_can('publish_posts')) {
                // if (!$valid || !current_user_can('publish_posts')) {
                    echo __('You are not authorized to access this page.', 'foodiepro');
                    return;
            }

            if (isset($_POST['draft']))
                $action='draft';
            elseif (isset($_POST['publish']))
                $action='publish';
            elseif ($origin == 'autosave')
                $action = 'autosave';


            if ( in_array($action, array('draft', 'publish')) ) {
                /* Stop autosave calls to fire as soon the form is being submitted */
                remove_action( 'wp_ajax_' . $post_type . '_autosave', array($this, 'ajax_post_autosave_cb') );
            }

            // Create or update the post and get a post_id & missing required fields
            $post_id = CPM_Save::save_post( $action );

            if (!$post_id ) {
                if ($action='autosave') {
                    echo 'Post autosave failed : save post';
                    die();
                }
                else
                    trigger_error('Post submission failed : save post');
            }

            // Save taxonomy terms
            $result = CPM_Save::save_tax_terms($post_type, $post_id);
            if (!$result) {
                if ($action = 'autosave') {
                    echo 'Post autosave failed : save taxonomy terms';
                    die();
                } else
                    trigger_error('Post submission failed : save taxonomy terms');
            }

            // Allow custom actions depending on post type
            do_action('cpm_' . $post_type . '_submission_main', $post_id);


            /* Display following post submission */
            if ($action=='draft') {
                $state = 'draft';
                $args=compact('post_id','post_type','state');
                $output = CPM_Assets::get_template_part('form','intro', $args);
                $output .= $this->js_alert_disable();
                return $output;
            }

            elseif ($action=='publish') {

                /* Check required fields and exit if some are missing  */
                $errors = self::check_required( $_POST['submit_post_type'] );
                if ( empty($errors) ) {
                    if (current_user_can('administrator'))
                        $result = self::update_post_status($post_id, 'publish');
                    elseif (current_user_can('publish_posts'))
                        $result = self::update_post_status($post_id, 'pending');
                }
                else {
                    $post_type=$post_type;
                    $args=compact('post_type','errors');
                    $output = CPM_Assets::get_template_part('form','missing',$args);
                    $output .= $this->display($post_id, 'none');
                    // do_action('wp_insert_post', 'wp_insert_post');
                    return $output;
                }

                // If no errors, display success message
                if (current_user_can('administrator'))
                    $successmsg = sprintf(CPM_Assets::get_label($post_type, 'publish-admin'), get_permalink($post_id));
                else
                    $successmsg = CPM_Assets::get_label($post_type, 'publish-user');

                $url = foodiepro_get_permalink(array('slug' => CPM_Assets::get_slug($post_type, $post_type . '_' . 'list')));
                $output = '<p class="successbox">' . $successmsg . '</p>';
                $output .= '<span class="post-nav-link">' . sprintf(CPM_Assets::get_label($post_type, 'back'), $url) . '</span>';
                $output .= $this->js_alert_disable();

                do_action('wp_insert_post', 'wp_insert_post');
                return $output;
            }

            elseif ($action == 'autosave' ) {
                $date_modified=sprintf(__('Saved automatically on %s at %s', 'foodiepro'), get_the_modified_date('d/m/Y', $post_id), get_the_modified_time('H\hi', $post_id));
                wp_send_json_success( array(
                    'post_id' => $post_id,
                    'modified' => $date_modified,
                    'status' => get_post_status( $post_id )
                ));
            }

            else {
                $output = '';
                $output .= '<p class="submitbox">' . __('Unknown action.', 'foodiepro') . '</p>';
                $url = foodiepro_get_permalink(array('slug' => CPM_Assets::get_slug($post_type, $post_type . '_' . 'list')));
                $output .= '<span class="post-nav-link">' . sprintf(CPM_Assets::get_label($post_type, 'back'), $url) . '</span>';

                return $output;
            }


        }
    }


    /**
     * Updates post status
     *
     * @param  mixed $post_id
     * @param  mixed $status
     * @return void
     */
    public static function update_post_status( $post_id, $status ) {
        $result = wp_update_post(array(
            'ID'            =>  $post_id,
            'post_status'   =>  $status
        ));
        return $result;
    }


    /**
     * Check required fields
     *
     * @param  mixed $post_type
     * @return void
     */
    public static function check_required($post_type)
    {
        // Check required fields
        $missing = array();
        $required = CPM_Assets::get_required($post_type);
        foreach ($required as $field => $label) {
            if (!isset($_POST[$field])) {
                $missing[] = $label;
            } elseif (empty($_POST[$field]) || !$_POST[$field] || $_POST[$field] == '-1') {
                $missing[] = $label;
            } elseif (is_array($_POST[$field]) && count($_POST[$field]) == 1) {
                if (!implode($_POST[$field][0])) {
                    $missing[] = $label;
                }
            }
        }
        return $missing;
    }



    //Can be called either from AJAX or during form submission
    /**
     * save_post
     *
     * @param  mixed $type
     * @param  mixed $_POST
     * Array('post_id', '$type_title', '$type_content')
     * @return void
     */
    public static function save_post()
    {
        // Check if updating
        $updating = false;
        $type = $_POST['submit_post_type'];
        $post_status = 'auto-draft';
        $updating_id = isset($_POST['submit_post_id']) ? intval($_POST['submit_post_id']) : false;
        if ($updating_id) {
            $updating_post = get_post($updating_id);
            if ($updating_post->post_type == $type && $updating_post->post_status == 'auto-draft') {
                $post_status = 'restored';
                $updating = true;
            } elseif ($updating_post->post_type == $type && ($updating_post->post_author == get_current_user_id() || current_user_can('administrator'))) {
                $post_status = $updating_post->post_status;
                $updating = true;
            }
        }

        $title = isset($_POST['post_title']) ? $_POST['post_title'] : '';
        if (!$title) {
            $title = __('Untitled', 'foodiepro');
        } else {
            $title = sanitize_text_field($title);
        }
        $content = isset($_POST['post_content']) ? $_POST['post_content'] : '';

        $post = array(
            'post_title' => $title,
            'post_type' => $type,
            'post_status' => $post_status,
            'post_content' => $content,
        );

        // Save post
        if ($updating) {
            $post['ID'] = $updating_id;
            $post_id = $updating_id;
            wp_update_post($post);
        } else {
            $post['post_author'] = get_current_user_id();
            $post_id = wp_insert_post($post, true);
        }
        clean_post_cache($post_id);
        return $post_id;
    }

    public function js_alert_disable()
    {
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
                console.log("Detected already submitted form, disabling alert !");
                formSubmitting = true;
            });
        </script>
<?php
    }




/********************************************************************************
 *********************         AJAX CALLBACKS       ***************************
********************************************************************************/

    public function ajax_post_autosave_cb()
    {
        if (!check_ajax_referer('custom_' . $this->post_type . '_autosave', 'security', false)) {
            echo('Nonce not recognized');
            die();
        }

        if (empty($_POST['post_data'])) {
            echo('No post data provided');
            die();
        }
        parse_str($_POST['post_data'], $params);
        if (is_array($params)) {
            $_POST= $params;
        }
        else {
            echo('Post data is not an array');
            die();
        }

        $post_id = $this->submit( 'autosave' );

        $date_saved = sprintf(__('Autosaved on %s at %s', 'foodiepro'), date('d/m/Y'), date('H\hi'));

        $response = array(
            'postId'    => $post_id,
            'dateSaved' => $date_saved
        );
        wp_send_json_success($response);
    }


    public function ajax_upload_post_image()
    {
        if (!check_ajax_referer('custom_post_submission_form', 'security', false)) {
            echo('Nonce not recognized');
            die();
        }
        if ($_POST['thumbId'] != 'featured') return;

        $post_id = intval($_POST['postId']);
        $alt = get_the_title($post_id);
        if (empty($alt) && isset($_POST['imageAlt'])) {
            $alt = $_POST['imageAlt'];
        };

        $attach_id = CPM_Save::insert_attachment('file', $post_id, $alt);
        if (!$attach_id) {
            echo ('Image upload failed : insert attachment');
            die();
        }
        $result = set_post_thumbnail($post_id, $attach_id);
        if (!$result) {
            echo ('Image upload failed : set post thumbnail');
            die();
        }

        if ($result) {
            $image = wp_get_attachment_image_src( $attach_id, 'thumbnail');
            if ($image) {
                $response = array(
                    'src'   => $image[0],
                    'attachId' => $attach_id,
                );
                wp_send_json_success($response);
            }
        }
        echo ('Image upload failed : get attachment image');
        die();
    }


    public function ajax_remove_featured_image()
    {
        if (!check_ajax_referer('custom_post_submission_form', 'security', false)) {
            echo('Nonce not recognized');
            die();
        }

        if ($_POST['thumbId'] != 'featured') {
            echo ('Image remove failed : not the featured image');
            die();
        }

        $post_id = intval($_POST['postId']);
        $attach_id = get_post_thumbnail_id( $post_id );
        if (empty($attach_id)) {
            echo ('Image remove failed : attachment not found');
            die();
        }

        $result = wp_delete_attachment($attach_id);
        if (empty($result)) {
            echo ('Image remove failed : delete attachment');
            die();
        }

        wp_send_json_success($result);
    }


    public function ajax_tinymce_upload_image()
    {
        if (!check_ajax_referer('custom_post_editor', 'security', false)) {
            echo ('Nonce not recognized');
            die();
        }

        $post_id = intval($_POST['postid']);
        reset($_FILES);
        $temp = current($_FILES);
        $file = key($_FILES);

        if (!is_uploaded_file($temp['tmp_name'])) {
            // Notify editor that the upload failed
            header("HTTP/1.1 500 Server Error");
            echo('HTTP/1.1 500 Server Error');
            die();
        }

        // Sanitize input
        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            header("HTTP/1.1 400 Invalid file name.");
            echo('HTTP/1.1 400 Invalid file name.');
            die();
        }

        // Verify extension
        if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
            header("HTTP/1.1 400 Invalid extension.");
            echo('HTTP/1.1 400 Invalid extension.');
            die();
        }

        //require the needed files
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $attach_id = media_handle_upload($file, $post_id);

        // Respond to the successful upload with JSON.
        $location = wp_get_attachment_url($attach_id);
        $response = array(
            'id'    => $attach_id,
            'location' => $location,
        );

        wp_send_json_success($response);
    }
}
