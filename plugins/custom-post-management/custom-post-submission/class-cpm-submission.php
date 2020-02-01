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
class CPM_Submission
{

    private static $PLUGIN_URI;
    private static $PLUGIN_PATH;
    private static $UPLOAD_PATH;

    private $post_type;


    public function __construct($type)
    {
        self::$PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
        self::$PLUGIN_URI = plugin_dir_url(dirname(__FILE__));

        $this->post_type = $type;

        // self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
        // self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
        $upload_dir = wp_upload_dir();
        self::$UPLOAD_PATH = trailingslashit($upload_dir['basedir']);
    }


    /********************************************************************************
     *********************         ACTIONS CALLBACKS       ***************************
     ********************************************************************************/
    // Set the language for select2 dropdown script
    public function add_lang_to_select($output)
    {
        return str_replace('<select', '<select lang="fr"', $output);
    }


    /********************************************************************************
     *********************         OUTPUT FUNCTIONS      ***************************
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
                'post_content' => ' ',
            );
            $post_ID = wp_insert_post($post_draft);
        }

        $post = get_post($post_ID);

        $output = $this->get_intro_text($state);

        $required = CPM_Assets::get_required($this->post_type);
        $required_fields = array_keys($required);
        /* Standard submission form with title, image & taxonomies */

        $output .= '<div id="custom_post_submission_form" class="postbox">';
        $output .= '<form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">';

        ob_start();
        include(self::$PLUGIN_PATH . 'custom-post-submission/partials/custom_post_submission_form.php');
        $output .= ob_get_contents();
        ob_end_clean();

        /* Possibility of adding a specific post type section there */
        $output = apply_filters('cpm_' . $this->post_type . '_section', $output, $post, $required_fields);

        /* Add submission buttons */
        $buttons = array('preview', 'draft', 'publish');
        $buttons_html = $this->get_buttons($buttons, $post_ID);
        $output .= apply_filters('cpm_' . $this->post_type . '_submission_buttons', $buttons_html);

        $output .= '</form>';
        $output .= '</div>';

        return $output;
    }

    public function get_intro_text($state)
    {

        /* Intro text output */
        switch ($state) {
            case 'new':
                $output = '<p class="submitbox">';
                $output .= CPM_Assets::get_label($this->post_type, 'new1');
                $output .= '<br>';
                $output .= CPM_Assets::get_label($this->post_type, 'new2');
                $output .= '</p>';
                break;
            case 'edit':
                $output = '<p class="submitbox">';
                $output .= CPM_Assets::get_label($this->post_type, 'edit1');
                $output .= '<br>';
                $output .= CPM_Assets::get_label($this->post_type, 'edit2');
                $output .= '</p>';
                break;
            case 'draft':
                $output = '<p class="submitbox">';
                $output .= CPM_Assets::get_label($this->post_type, 'draft1');
                $output .= '<br>';
                $output .= CPM_Assets::get_label($this->post_type, 'draft2');
                $output .= '</p>';
                $url = do_shortcode('[permalink slug="' . CPM_Assets::get_slug($this->post_type . '_list') . '"]');
                $output .= '<p>';
                $output .= '<span class="post-nav-link">' . sprintf(CPM_Assets::get_label($this->post_type, 'back'), $url) . '</span>';
                $output .= '</p>';
                break;
            default:
                $output = '';
        }

        return $output;
    }


    public function display_taxonomies($post_ID, $required_fields)
    {

        $taxonomies = CPM_Assets::get_taxonomies($this->post_type);
        $dropdowns = array();

        // General dropdown arguments
        $args = array(
            'echo' => 0,
            'orderby' => 'description',
            'hide_empty' => 0,
        );

        // Generate dropdown markup for each taxonomy (course, cuisine, difficulty, diet...)
        // -----------------------------------------------------------
        foreach ($taxonomies as $taxonomy => $options) {
            $args['taxonomy'] = $taxonomy;
            $args['class'] = "postform $taxonomy";
            $args['class'] .= $options['multiselect'] ? 'multiselect' : '';
            $args['show_option_none'] = $options['multiselect'] ? '' : $options['labels']['singular_name'];
            $args['hierarchical'] = isset($options['hierarchical']) ? $options['hierarchical'] : false;
            $args['exclude'] = isset($options['exclude']) ? $options['exclude'] : '';
            $args['exclude_tree'] = isset($options['exclude_tree']) ? $options['exclude_tree'] : '';
            $args['orderby'] = $options['orderby'];
            // $args['child_of'] = $options['child_of'];

            $dropdowns[$taxonomy] = array(
                'label' => $options['labels']['singular_name'],
                // Generates dropdown with groups headers in case of hierarchical taxonomies
                'markup' => self::get_dropdown($args, $options, $this->post_type),
            );
        }

        // Echoes all dropdowns that were previously built
        // -----------------------------------------------------------
        $html = '<table>';
        foreach ($dropdowns as $taxonomy => $dropdown) {

            // Multiselect
            if ($taxonomies[$taxonomy]['multiselect']) {
                preg_match("/<select[^>]+>/i", $dropdown['markup'], $dropdown_match);
                if (isset($dropdown_match[0])) {
                    $select_multiple = preg_replace("/name='([^']+)/i", "$0[]' data-placeholder='" . $dropdown['label'] . "' multiple='multiple", $dropdown_match[0]);
                    $dropdown['markup'] = str_ireplace($dropdown_match[0], $select_multiple, $dropdown['markup']);
                }
            }

            // Mark existing post terms as Selected in the dropdown
            $terms = wp_get_post_terms($post_ID, $taxonomy, array('fields' => 'ids'));
            foreach ($terms as $term_id) {
                $dropdown['markup'] = str_replace(' value="' . $term_id . '"', ' value="' . $term_id . '" selected="selected"', $dropdown['markup']);
            }

            ob_start();
            ?>

                <tr class="post-general-form-<?= $taxonomy; ?>">
                    <td class="post-general-form-label">
                        <label for="<?= $taxonomy; ?>">
                            <?php
                                        echo $taxonomies[$taxonomy]['labels']['singular_name'];
                                        if (in_array($this->post_type . '_' . $taxonomy, $required_fields))
                                            echo '<span class="required-field">*</span>';
                                        ?>
                        </label>
                    </td>
                    <td class="post-general-form-field">
                        <?= $dropdown['markup']; ?>
                    </td>
                </tr>

            <?php
                        $html .= ob_get_contents();
                        ob_end_clean();
                    }
                    $html .= '</table>';
                    echo $html;
    }

                public static function get_dropdown($args, $options, $post_type)
                {
                    // $args = array( 'taxonomy' => 'course');
                    // This function generates a select dropdown list with option groups whenever
                    // the argument hierarchical is true, otherwise it renders the standard wp_dropdown_categories output

                    $select_name = $post_type . '_' . $args['taxonomy'];

                    if ($args['hierarchical'] == 0) {
                        if ($args['taxonomy'] == 'post_tag') {
                            // New clause "tags_post_type" added to the WP_Query function
                            // see req_clauses filter above
                            $args['tags_post_type'] = $post_type;
                        }
                        $args['name'] = $select_name;
                        $html = wp_dropdown_categories($args);
                        return $html;
                    }


                    $getparents['orderby'] = $args['orderby'];
                    $getparents['taxonomy'] = $args['taxonomy'];
                    $getparents['hierarchical'] = true;
                    $getparents['depth'] = 1;
                    $getparents['parent'] = 0;
                    $parents = get_categories($getparents);

                    $html = '<select lang="fr" name="' . $select_name . '"  id="' . $select_name . '" class="postform ' . $args['class'] . '" tabindex="-1">';
                    // echo '<pre>' . print_r( $terms ) . '</pre>';
                    if ($args['show_option_none'] != '') {
                        $html .= '<option value="" disabled selected>' . $options['labels']['singular_name'] . '</option>';
                        $html .= '<option class="" value="-1">' . __('none', 'foodiepro') . '</option>';
                    }

                    foreach ($parents as $parent) {
                        $getchildren = $args;
                        $getchildren['depth'] = 0;
                        $getchildren['child_of'] = $parent->term_id;

                        $children = get_categories($getchildren);

                        $html .= '<optgroup label="' . $parent->name . '">';
                        foreach ($children as $child) {
                            $html .= '<option class="" value="' . $child->term_id . '">' . $child->name . '</option>';
                        }
                        $html .= '</optgroup>';
                    }

                    $html .= '</select>';
                    return $html;
                }


            /********************************************************************************
             ********************* OUTPUT  FUNCTIONS      ***************************
                ********************************************************************************/

            public function get_buttons($buttons, $post_ID)
            {
                $html = '<div id="post-form-buttons">';
                if (in_array('preview', $buttons)) {
                    $url = get_preview_post_link($post_ID);
                    $html .= '<a href="' . $url . '" class="black-button">' . __('Preview', 'foodiepro') . '</a>';
                }
                if (in_array('draft', $buttons)) {
                    $html .= '<input type="submit" value="' . __('Draft', 'foodiepro') . '" id="draft" name="draft" />';
                }
                if (in_array('publish', $buttons)) {
                    $html .= '<input type="submit" value="' . __('Publish', 'foodiepro') . '" id="publish" name="publish" />';
                }
                $html .= '</div>';
                $html .= '<input type="hidden" name="action" value="post" />';
                $html .= wp_nonce_field($this->post_type . '_submit', 'submit' . $this->post_type);

                return $html;
            }

            /********************************************************************************
             ****               SUBMISSION PROCESS MANAGEMENT                       **********
             ********************************************************************************/
            public function submit()
            {
                $successmsg = '';

                if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action'])) {

                    $valid = wp_verify_nonce($_POST['submit' . $this->post_type], $this->post_type . '_submit');

                    // If guest, interrupt execution
                    if (!$valid || !is_user_logged_in() || !current_user_can('publish_posts')) {
                        echo __('You are not authorized to access this page.', 'foodiepro');
                        return;
                    }

                    // Check if updating
                    $updating = false;

                    $updating_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : false;
                    if ($updating_id) {
                        $updating_post = get_post($updating_id);

                        if ($updating_post->post_type == $this->post_type && $updating_post->post_status == 'auto-draft') {
                            $updating = true;
                        } elseif ($updating_post->post_type == $this->post_type && ($updating_post->post_author == get_current_user_id() || current_user_can('administrator'))) {
                            $updating = true;
                        }
                    }

                    $title = isset($_POST[$this->post_type . '_title']) ? $_POST[$this->post_type . '_title'] : '';
                    if (!$title) {
                        $title = __('Untitled', 'foodiepro');
                    }
                    else {
                        $title = sanitize_text_field($title);
                    }
                    $content = isset($_POST[$this->post_type . '_content']) ? $_POST[$this->post_type . '_content'] : '';

                    $post = array(
                        'post_title' => $title,
                        'post_type' => $this->post_type,
                        'post_status' => 'auto-draft',
                        'post_content' => $content,
                    );

                    // Save post
                    if ($updating) {
                        $post['ID'] = $updating_id;
                        $post['post_status'] = $updating_post->post_status;
                        wp_update_post($post);
                        $post_id = $updating_id;
                    } else {
                        $post_id = wp_insert_post($post, true);
                    }

                    // Add featured image
                    $key = $this->post_type . '_thumbnail';
                    if (isset($_FILES[$key])) {
                        $file = $_FILES[$key];
                        if ($file['name'] != '')
                            $this->save_featured_image($key, $post_id);
                    }

                    do_action('cpm_' . $this->post_type . '_submission_main', $post_id);

                    // Check categorie and tags as well
                    $taxonomies = CPM_Assets::get_taxonomies($this->post_type);
                    foreach ($taxonomies as $taxonomy => $options) {
                        $terms = isset($_POST[$this->post_type . '_' . $taxonomy]) ? $_POST[$this->post_type . '_' . $taxonomy] : false;
                        if ($terms) {
                            if (!is_array($terms)) {
                                $terms = array(intval($terms));
                            } else {
                                $terms = array_map('intval', $terms);
                            }
                        } else
                            $terms = null;
                        wp_set_object_terms($post_id, $terms, $taxonomy);
                    }


                    // Check required fields
                    $errors = array();
                    $required = CPM_Assets::get_required($this->post_type);
                    foreach ($required as $field => $label) {
                        if ($field != $this->post_type . '_thumbnail' || !isset($_FILES[$this->post_type . '_thumbnail'])) {
                            if (!isset($_POST[$field])) {
                                $errors[] = $label;
                            } elseif (empty($_POST[$field]) || !$_POST[$field] || $_POST[$field] == '-1') {
                                $errors[] = $label;
                            } elseif (is_array($_POST[$field]) && count($_POST[$field]) == 1) {
                                if (!implode($_POST[$field][0])) {
                                    $errors[] = $label;
                                }
                            }
                        } elseif (!$_FILES[$this->post_type . '_thumbnail']['name'] && get_post_thumbnail_id($post_id) == 0) {
                            $errors[] = $label;
                        }
                    }

                    /* POST actions */
                    if (isset($_POST['edit'])) {
                        $output = '';
                        $output .= '<p class="submitbox">' . CPM_Assets::get_label($this->post_type, 'edit1') . '</p>';
                        $output .= $this->submit($post_id, array('preview', 'draft', 'publish'));
                        return $output;
                    } elseif (isset($_POST['draft'])) {
                        // Update post status
                        // Do not use wp_update_post which erases some recently added metadata
                        global $wpdb;
                        $wpdb->update($wpdb->posts, array('post_status' => 'draft'), array('ID' => $post_id));
                        clean_post_cache($post_id);
                        // $output = $this->display( $post_id, 'draft' );
                        // $output = '';
                        $output = $this->get_intro_text('draft');
                        $output .= $this->js_alert_disable();
                        return $output;
                    } elseif (count($errors) > 0) {
                        $output = '';

                        if (count($errors) > 0) {
                            $output .= '<div class="errorbox">';
                            $output .= CPM_Assets::get_label($this->post_type, 'required');
                            $output .= '<ul>';
                            foreach ($errors as $error) {
                                $output .= '<li>' . $error . '</li>';
                            }
                            $output .= '</ul>';
                            $output .= '</div>';
                        }
                        $output .= $this->display($post_id, 'none');
                        do_action('wp_insert_post', 'wp_insert_post');
                        return $output;
                    } elseif (isset($_POST['publish'])) {
                        // Update post status
                        // Do not use wp_update_post which erases some recently added metadata
                        $status = current_user_can('administrator') ? 'publish' : 'pending';
                        global $wpdb;
                        $wpdb->update($wpdb->posts, array('post_status' => $status), array('ID' => $post_id));
                        clean_post_cache($post_id);

                        // Success message
                        if (current_user_can('administrator'))
                        $successmsg = sprintf(CPM_Assets::get_label($this->post_type, 'publish-admin'), get_permalink($post_id));
                        else
                        $successmsg = CPM_Assets::get_label($this->post_type, 'publish-user');

                        $url = do_shortcode('[permalink slug="' . CPM_Assets::get_slug($this->post_type . '_' . 'list') . '"]');
                        $output = '<p class="successbox">' . $successmsg . '</p>';
                        $output .= '<span class="post-nav-link">' . sprintf(CPM_Assets::get_label($this->post_type, 'back'), $url) . '</span>';
                        $output .= $this->js_alert_disable();

                        // Send notification email to administrator
                        $to = get_option('admin_email');
                        if ($to) {
                            $edit_link = admin_url('post.php?action=edit&post=' . $post_id);

                            $subject = sprintf(__('New user submission:%s', 'foodiepro'), $title);
                            $message = 'A new ' . $this->post_type . ' has been submitted on your website.';
                            $message .= "\r\n\r\n";
                            $message .= 'Edit this post: ' . $edit_link;

                            wp_mail($to, $subject, $message);
                        }

                        do_action('wp_insert_post', 'wp_insert_post');
                        return $output;
                    } else {
                        $output = '';
                        $output .= '<p class="submitbox">' . __('Unknown action.', 'foodiepro') . '</p>';
                        $url = do_shortcode('[permalink slug="' . CPM_Assets::get_slug('list') . '"]');
                        $output .= '<span class="post-nav-link">' . sprintf(CPM_Assets::get_label($this->post_type, 'back'), $url) . '</span>';

                        return $output;
                    }
                }
            }

            public function js_alert_disable()
            {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        console.log( "Detected already submitted form, disabling alert !");
                        formSubmitting = true;
                    });
                </script>
                <?php
            }


            public function save_featured_image($file_handler, $post_id)
            {
                if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
                    return;
                }

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attach_id = media_handle_upload($file_handler, $post_id);

                // update_post_meta( $post_id, '_thumbnail_id', $attach_id );
                set_post_thumbnail($post_id, $attach_id);

                return $attach_id;
            }


            /********************************************************************************
             *********************         AJAX CALLBACKS       ***************************
             ********************************************************************************/

            public function ajax_remove_featured_image()
            {
                $check = check_ajax_referer('custom_submission_form', 'security', false);
                $post_id = intval($_POST['postid']);
                $result = delete_post_thumbnail($post_id);
                die();
            }

            public function ajax_tinymce_upload_image()
            {
                $check = check_ajax_referer('custom_submission_form', 'security', false);

                $post_id = intval($_POST['postid']);

                reset($_FILES);
                $temp = current($_FILES);
                $file = key($_FILES);

                if (!is_uploaded_file($temp['tmp_name'])) {
                    // Notify editor that the upload failed
                    header("HTTP/1.1 500 Server Error");
                    return;
                }

                // Sanitize input
                if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
                    header("HTTP/1.1 400 Invalid file name.");
                    return;
                }

                // Verify extension
                if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
                    header("HTTP/1.1 400 Invalid extension.");
                    return;
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

                echo json_encode($response);

                die();
            }

            /********************************************************************************
             *********************         SPECIFIC POST HOOKS       *************************
             ********************************************************************************/

            public function add_post_specific_section($form, $post, $required_fields)
            {

                ob_start();
                ?>
            <input type="hidden" name="post_meta_box_nonce" value="<?= wp_create_nonce('post'); ?>" />
            <div class="post-container post-general-container">
                <h4 id="headline-content"><?php _e('Post content', 'foodiepro'); ?><?php if (in_array('post_content', $required_fields)) echo '<span class="required-field   ">*</span>'; ?></h4>
                <table class="post-form" id="post-general-form">
                    <tr class="post-general-form-description">
                        <!-- <td class="post-general-form-label"><label for="post_description"><?php _e('Description', 'foodiepro'); ?></label></td> -->
                        <td class="post-general-form-field">
                            <textarea class="post-content" name="post_content" id="post_content" rows="10" placeholder="<?= __('Write your post here.', 'foodiepro'); ?>">
                        <?= $post->post_content; ?>
                    </textarea>
                        </td>
                    </tr>
                </table>
            </div>

    <?php
            $form .= ob_get_contents();
            ob_end_clean();
            return $form;
        }
    }
