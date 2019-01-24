<?php

class Custom_Post_Submission {

    const POST_PUBLISH_SLUG = 'publier-recettes';
    const POST_NEW_SLUG = 'nouvelle-recette';
    const POST_EDIT_SLUG = 'modifier-recette';

    protected static $PLUGIN_URI;  
    protected static $PLUGIN_PATH;  
    protected static $_UploadPath; 

    protected static $required_fields; 
    protected static $required_fields_labels; 

    protected static $taxonomies;  
    
    // public function __construct( $name = 'user-submissions' ) {
    public function __construct() {
        self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
        self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );
        $upload_dir = wp_upload_dir();
        self::$_UploadPath = trailingslashit( $upload_dir['basedir'] );

        add_action( 'wp', array( $this, 'hydrate') );

        // Submission form customization
        add_filter( 'wp_dropdown_cats', array($this, 'add_lang_to_select'));
        // add_action('wp_head',array($this,'add_custom_js'));        
       
    }

    public function hydrate() {

        self::$required_fields_labels = array(
                        'post_title_check' => __('Post Title.','foodiepro'),
                        'post_category' => __('Post Category.', 'foodiepro'),
                    );

        self::$required_fields = array_keys( self::$required_fields_labels );

        self::$taxonomies=array(
                    'category' => array(
                        'multiselect' => false,
                        'exclude' => '',
                        'orderby' => 'name',
                        'labels'=>array(
                            'singular_name'=>__( 'Categories', 'foodiepro' ),
                        ),
                    ),         
                    'post_tag' => array(
                        'multiselect' => true,
                        'exclude' => '',
                        'orderby' => 'name',
                        'labels'=>array(
                            'singular_name'=>__( 'Keywords', 'foodiepro' ),
                        ),
                    )
                );
    }


/********************************************************************************
****           USERS SUBMISSION FORM CUSTOMIZATION                          *****
********************************************************************************/


    public function submissions_form( $post_ID = false, $buttons = array('preview', 'draft','publish') ) {

        $output='';
        
        if( !$post_ID ) {
            // Create autosave when submission page viewed
            global $user_ID;
            $post_draft = array(
                'post_status' => 'auto-draft',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => 'post',
                'post_content' => ' ',
            );

            $post_ID = wp_insert_post( $post_draft );
        }

        // $post = new Custom_WPURP_post( $post_ID );


        ob_start();

        include( self::$PLUGIN_PATH . 'templates/custom_post_submission_form.php' );

        $form = ob_get_contents();
        ob_end_clean();

        $output .= $form;
        
        return $output;
    }


 /********************************************************************************
*********************         ACTIONS CALLBACKS       ***************************
********************************************************************************/


    // Set the language for select2 dropdown script
    public function add_lang_to_select($output){
        return str_replace('<select','<select lang="fr"',$output);
    }


/********************************************************************************
***                FUNCTIONS USED IN USER SUBMISSION FORM                     ***
********************************************************************************/

    // Returns list of excluded categories
    public function excluded_terms($tax) {
        $exclude='';    
        if ($tax=='category') {
            $exclude = get_option( 'custom_post_submission_hide_category_terms', array() );
            $exclude = implode( ',', $exclude );
        }
        return $exclude;
    }

/********************************************************************************
****                           AJAX CALLBACKS                          **********
********************************************************************************/

    public function ajax_user_delete_post() {

        if( ! is_user_logged_in() ) die();

        if(check_ajax_referer( 'custom_post_submissions_list', 'security', false ) ) {
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

 
/********************************************************************************
****               SUBMISSION PROCESS MANAGEMENT                       **********
********************************************************************************/

    public function submissions_process() {
        $successmsg = '';

        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) ) {

            wp_verify_nonce( $_POST['submitpost'], 'post_submit' );

            // If guest, interrupt execution
            if( !is_user_logged_in() ) {
                echo __('You must be logged-in to access this page.', 'foodiepro');
                return;
            }

            // Check if updating
            $updating = false;

            $updating_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
            if( $updating_id ) {
                $updating_post = get_post( $updating_id );

                if( $updating_post->post_type == 'post' && $updating_post->post_status == 'auto-draft' ) {
                    $updating = true;
                } elseif( $updating_post->post_type == 'post' && ($updating_post->post_author == get_current_user_id() || current_user_can('administrator') ) ) {
                    $updating = true;
                }
            }


            $title = isset( $_POST['post_title'] ) ? $_POST['post_title'] : '';
            $_POST['post_title_check'] = $title;

            if( !$title ) $title = __( 'Untitled', 'foodiepro' );

            $content = isset( $_POST['content'] ) ? $_POST['content'] : '';

            $post = array(
                'post_title' => $title,
                'post_type' => 'post',
                'post_status' => 'auto-draft',
                'post_content' => $content,
            );

            // Save post
            if( $updating ) {
                $post['ID'] = $updating_id;
                $post['post_status'] = $updating_post->post_status;
                wp_update_post( $post );
                $post_id = $updating_id;
            } else {
                $post_id = wp_insert_post( $post, true );
            }


            // Check categorie and tags as well

            foreach( self::$taxonomies as $taxonomy => $options ) {
                $terms = isset( $_POST['post-'.$taxonomy] ) ? $_POST['post-'.$taxonomy] : false;

                if( $terms ) {
                    if( !is_array( $terms ) ) {
                        $terms = array( intval( $terms ) );
                    } else {
                        $terms = array_map( 'intval', $terms );
                    }

                    wp_set_object_terms( $post_id, $terms, $taxonomy );
                }
            }

            // // Add featured image from media uploader
            // if( isset( $_POST['post_thumbnail'] ) ) {
            //     update_post_meta( $post_id, '_thumbnail_id', $_POST['post_thumbnail'] );
            // }

            // Add all images from basic uploader
            if( $_FILES ) {
                foreach( $_FILES as $key => $file ) {
                    if ( $key == 'post_thumbnail' ) {
                        if( $file['name'] != '' )
                            // featured image thumbnail
                            $this->insert_attachment( $key, $post_id, 'post' );
                    } 
                    else {
                            // Other thumbnails
                        $this->insert_attachment( $key, $post_id, 'instruction' );
                    }
                }
            }

            // Check required fields
            $errors = array();
            foreach( self::$required_fields as $required_field ) {
                if( $required_field != 'post_thumbnail' || !isset( $_FILES['post_thumbnail'] ) ) {
                    if( !isset( $_POST[$required_field] ) || !$_POST[$required_field] || $_POST[$required_field]=='-1'  ) {
                        $errors[] = self::$required_fields_labels[$required_field];
                    }
                } else if( !$_FILES['post_thumbnail']['name'] && get_post_thumbnail_id( $post_id ) == 0 ) {
                    $errors[] = self::$required_fields_labels[$required_field];
                }
            }

            if ( isset( $_POST['preview'] ) ) {
                
                $output = '';
                $output .= '<div class="post-preview">';
                $output .= '<p class="submitbox">';
                // $output .= '<h5>' . __( 'post preview', 'foodiepro' ). '</h5>';
                $output .= __( 'Here is what your post will look like once it is published. You can choose to continue editing it, or save it as a draft, or publish it, using the buttons at the bottom of the page.', 'foodiepro');
                $output .= '</p>';
                $output .= '<h1>' . get_the_title( $post_id ) . '</h1>';
                $output .= '[display-post id=' . $post_id . ']';
                $output .= $this->submissions_form( $post_id, array( 'edit', 'draft', 'publish' ) );
                $output .= '<div>';
                return do_shortcode( $output );
            } 
            
            elseif ( isset( $_POST['edit'] ) ) {
                $output = '';
                $output .= '<p class="submitbox">' . __( 'You can edit your post here, before submitting it.', 'foodiepro') . '</p>';
                $output .= $this->submissions_form( $post_id, array( 'preview', 'draft', 'publish' ) );
                return $output;
            }            

            elseif ( isset( $_POST['draft'] ) ) {
                // Update post status
                $args = array(
                    'ID' => $post_id,
                    'post_status' => 'draft',
                );
                wp_update_post( $args );

                // Display confirmation message                 
                $output = '<p class="submitbox">';
                $output .= __( 'post saved as draft. It will not be visible on the site, but you can edit it at any time and submit it later.', 'foodiepro' );
                $output .= '</p>';
                $url = do_shortcode('[permalink slug="' . self::postS_PUBLISH_SLUG . '"]');
                $output .= sprintf(__('Back to <a href="%s">my published posts</a>','foodiepro'),$url);
                // $output .= '<a class="more-link" href=' . $url . '>← ' .  __( 'Back to my published posts', 'foodiepro' ) . '</a>';
                $output .= $this->submissions_form( $post_id, array( 'preview', 'draft', 'publish' ) );

                // $output .= '<h5>AFTER WP INSERT POST</h5>';
                // $debug_vars = get_post_meta( $post_id, 'post_instructions', true);
                // $output .= '<pre>' . print_r($debug_vars, true) . '</pre>';

                return $output;

            } 

            elseif ( count( $errors ) > 0 ) {
                $output = '';

                if( count( $errors ) > 0 ) {
                    $output .= '<div class="wpurp-errors">';
                    $output .= __( 'Please fill-in those required fields:', 'foodiepro' );
                    $output .= '<ul>';
                    foreach( $errors as $error ) {
                        $output .= '<li>' . $error . '</li>';
                    }
                    $output .= '</ul>';
                    $output .= '</div>';
                }
                $output .= $this->submissions_form( $post_id, array( 'preview', 'draft', 'publish' ) );
                do_action('wp_insert_post', 'wp_insert_post');
                return $output;
            }

            elseif ( isset( $_POST['publish'] ) ) {

                // Protect the metadata added since the last post update, ie the instruction images
                // Reason : otherwise they get deleted in wp_update_post 
                $meta_backup = get_post_meta( $post_id, 'post_instructions', true );
                
                // Update post status
                $status=current_user_can('administrator')?'publish':'pending';
                $args = array(
                    'ID' => $post_id,
                    'post_status' => $status,
                );
                
                wp_update_post( $args );

                // Restore backuped metadata
                update_post_meta( $post_id, 'post_instructions', $meta_backup );

                // Success message
                $successmsg = current_user_can('administrator')?sprintf( __( 'post <a href="%s">published</a>.', 'foodiepro' ), get_permalink($post_id) ):__( 'post submitted! Thank you, your post is now awaiting moderation.', 'foodiepro' );
                $url = do_shortcode('[permalink slug="' . self::POST_PUBLISH_SLUG . '"]');
                $output = '<p class="successbox">' . $successmsg . '</p>';
                $output .= '<p>←' . sprintf( __( '<a href="%s">Back to my posts</a>', 'foodiepro' ), $url ) . '</p>';

                // Send notification email to administrator
                $to = get_option( 'admin_email' );
                if( $to ) {
                    $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );

                    $subject = sprintf( __('New user submission:%s', 'foodiepro'), $title );
                    $message = 'A new post has been submitted on your website.';
                    $message .= "\r\n\r\n";
                    $message .= 'Edit this post: ' . $edit_link;

                    wp_mail( $to, $subject, $message );
                }

                do_action('wp_insert_post', 'wp_insert_post');
                return $output;
            }

            else {

                $output = '';
                $output .= '<p class="submitbox">' . __( 'Unknown action.', 'foodiepro') . '</p>';
                $url = do_shortcode('[permalink slug="' . self::POST_PUBLISH_SLUG . '"]');
                $output .= '<p>←' . sprintf( __( '<a href="%s">Back to my posts</a>', 'foodiepro' ), $url ) . '</p>';

                return $output;

            }     

        }
    }

    public function remove_attachment( $file_handler, $post_id, $img_type = 'post' ) {
        delete_post_meta( $post_id, '_thumbnail_id' );
    }

    public function insert_attachment( $file_handler, $post_id, $img_type = 'post' ) {
        if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
            return;
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attach_id = media_handle_upload( $file_handler, $post_id );

        if ( $img_type == 'post' ) { // Thumbnail image
            update_post_meta( $post_id, '_thumbnail_id', $attach_id );
        }          
        else { // Other attached images
            // TODO
        }

        return $attach_id;
    }


}

new Custom_Post_Submission();