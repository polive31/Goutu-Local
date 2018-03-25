<?php

class Custom_User_Submissions_Shortcodes extends WPURP_Premium_Addon {

    const RECIPES_PUBLISH_SLUG = 'publier-recettes';
    const RECIPE_NEW_SLUG = 'nouvelle-recette';
    const RECIPE_EDIT_SLUG = 'modifier-recette';
    protected static $_PluginDir;  
    protected $logged_in;

    public function __construct( $name = 'user-submissions' ) {
        parent::__construct( $name );
        
        self::$_PluginDir = plugin_dir_path( dirname( __FILE__ ) );

        // add_action( 'init', array( $this, 'assets' ) );
        // add_action( 'wp_enqueue_scripts', array( $this, 'scripts_for_image_upload' ), -10 );
        // add_action( 'init', array( $this, 'allow_logged_in_uploads' ) );

        // add_action( 'wp_ajax_query-attachments', array( $this, 'ajax_restrict_media' ), 1 );
        // add_action( 'wp_ajax_nopriv_query-attachments', array( $this, 'ajax_restrict_media' ), 1 );
        // add_action( 'wp_ajax_user_submissions_delete_recipe', array( $this, 'ajax_user_submissions_delete_recipe' ) );
        // add_action( 'wp_ajax_nopriv_user_submissions_delete_recipe', array( $this, 'ajax_user_submissions_delete_recipe' ) );

        // Ajax uploader
        add_action( 'afu_after_upload_done', array( $this, 'update_recipe_fields' ) );

        add_shortcode( 'custom_submissions', array( $this, 'submissions_shortcode' ) ); // For backwards compatibility
        add_shortcode( 'custom-recipe-submissions', array( $this, 'submissions_shortcode' ) );
        add_shortcode( 'custom-recipe-submissions-current-user-edit', array( $this, 'submissions_current_user_edit_shortcode' ) );
    }

    // public function assets() {
    //     WPUltimateRecipe::get()->helper( 'assets' )->add(
    //         array(
    //             'file' => $this->addonPath . '/css/public.css',
    //             'premium' => true,
    //             'public' => true,
    //             'shortcode' => array( 'wpurp_submissions', 'ultimate-recipe-submissions' ),
    //             //'setting' => array( 'user_submission_css', '1' ),
    //         ),
    //         array(
    //             'file' => $this->addonPath . '/css/public_base.css',
    //             'premium' => true,
    //             'public' => true,
    //             'shortcode' => array( 'wpurp_submissions', 'ultimate-recipe-submissions' ),
    //         ),
    //         array(
    //             'file' => '/js/recipe_form.js',
    //             'public' => true,
    //             'shortcode' => array( 'wpurp_submissions', 'ultimate-recipe-submissions' ),
    //             'deps' => array(
    //                 'jquery',
    //                 'jquery-ui-sortable',
    //                 'suggest',
    //             ),
    //             'data' => array(
    //                 'name' => 'wpurp_recipe_form',
    //                 'coreUrl' => WPUltimateRecipe::get()->coreUrl,
    //             )
    //         )
    //     );
    // }

    // public function allow_logged_in_uploads() {
    //     if( is_user_logged_in() && !current_user_can('upload_files') && WPUltimateRecipe::option( 'user_submission_enable', 'guests' ) != 'off' && WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) == '1' ) {
    //         $user = wp_get_current_user();
    //         $user->add_cap('upload_files');
    //     }
    // }

    // public function scripts_for_image_upload() {

    //     if( current_user_can( 'upload_files' ) && WPUltimateRecipe::option( 'user_submission_enable', 'guests' ) != 'off' && WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) == '1' && WPUltimateRecipe::get()->helper( 'assets' )->check_for_shortcode( array( 'wpurp_submissions', 'ultimate-recipe-submissions' ) ) )
    //     {
    //         if( function_exists( 'wp_enqueue_media' ) ) {
    //             wp_enqueue_media();
    //         } else {
    //             wp_enqueue_style( 'thickbox' );
    //             wp_enqueue_script( 'media-upload' );
    //             wp_enqueue_script( 'thickbox' );
    //         }
    //     }
    // }

    // public function ajax_restrict_media()
    // {
    //     if( WPUltimateRecipe::option( 'user_submission_restrict_media_access', '1' ) == '1' && !current_user_can( 'edit_others_posts' ) ) {
    //         exit;
    //     }
    // }

    public function submissions_shortcode() {
        if( !is_user_logged_in() ) {
            return '<p class="errorbox">' . __( 'Sorry, only registered users may submit recipes.', 'foodiepro' ) . '</p>';
        } else {
            if( isset( $_POST['submitrecipe'] ) ) {
                return $this->submissions_process();
            } else {
                return $this->submissions_form();
            }
        }
    }

    public function submissions_current_user_edit_shortcode() {
        $output = '';
        $author = get_current_user_id();


        if( isset( $_POST['submitrecipe'] ) ) {
            $output .= $this->submissions_process();
        } elseif( isset( $_GET['wpurp-edit-recipe'] ) ) {
            $recipe_id = $_GET['wpurp-edit-recipe'];
            $post = get_post( $recipe_id );

            if( $post->post_author == $author ) {
                // $output .='<h3>CUSTOM SUBMISSION FORM</h3>';
                $output .= $this->submissions_form( $recipe_id );
            }
        }        

        elseif( $author !== 0 ) {
            // $output .= 'In Custom User Submission Class !';
            $recipes = WPUltimateRecipe::get()->query()->author( $author )->post_status( array( 'publish', 'private', 'pending' ) )->get();

            if( count( $recipes ) !== 0 ) {
                $output .= '<ul class="wpurp-user-submissions-current-user-edit">';
                foreach ( $recipes as $recipe ) {
                    $item = '<li>';
                    if( WPUltimateRecipe::option( 'user_submission_delete_button', '0') == '1' ) {
                        $item .= '<i class="fa fa-trash user-submissions-delete-recipe" data-id="' . $recipe->ID() . '" data-title="' . esc_attr( $recipe->title() ) . '"></i>';
                    }
                    $url = get_permalink() . self::RECIPE_EDIT_SLUG;    
                    $item .= '<a href="' . $url . '?wpurp-edit-recipe=' . $recipe->ID() . '">' . $recipe->title() . '</a>';
                    $item .= '</li>';
                    $output .= apply_filters( 'wpurp_user_submissions_current_user_edit_item', $item, $recipe );
                }
                $output .= '</ul>';
            }
        }

        return $output;
    }

    public function ajax_user_submissions_delete_recipe()
    {
        if(check_ajax_referer( 'wpurp_user_submissions', 'security', false ) )
        {
            global $user_ID;

            $recipe_id = intval( $_POST['recipe'] );
            $recipe = get_post( $recipe_id );

            if( WPUltimateRecipe::option( 'user_submission_delete_button', '0') == '1' && $recipe->post_type == 'recipe' && $recipe->post_author == $user_ID ) {
                wp_delete_post( $recipe_id );
            }
        }

        die();
    }

    public function submissions_form( $recipe_ID = false ) {

        $output='';
        
        if( !$recipe_ID ) {
            // Create autosave when submission page viewed
            global $user_ID;
            $recipe_draft = array(
                'post_status' => 'auto-draft',
                'post_date' => date('Y-m-d H:i:s'),
                'post_author' => $user_ID,
                'post_type' => 'recipe',
                'post_content' => ' ',
            );

            $recipe_ID = wp_insert_post( $recipe_draft );
        }

        $recipe = new WPURP_Recipe( $recipe_ID );

        ob_start();
        include( self::$_PluginDir . 'templates/custom_submission_form.php' );
        $form = ob_get_contents();
        ob_end_clean();

        // $output .= self::$_PluginDir . 'templates/custom_submission_form.php';
        // $output .= '<h3>In CUSTOM USER SUBMISSIONS </h3>';
        $output .= apply_filters( 'custom_user_submissions_form', $form, $recipe );
        // $output .= $form;
        
        return $output;
    }

    public function update_recipe_fields() {
        return;
    }

    public function submissions_process() {
        $successmsg = '';

        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) ) {

            wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' );

            // Check if updating
            $updating = false;

            $updating_id = isset( $_POST['recipe_id'] ) ? intval( $_POST['recipe_id'] ) : false;
            if( $updating_id ) {
                $updating_post = get_post( $updating_id );

                if( $updating_post->post_type == 'recipe' && $updating_post->post_status == 'auto-draft' ) {
                    $updating = true;
                } elseif( $updating_post->post_type == 'recipe' && $updating_post->post_author == get_current_user_id() ) {
                    $updating = true;
                }
            }


            $title = isset( $_POST['recipe_title'] ) ? $_POST['recipe_title'] : '';
            $_POST['recipe_title_check'] = $title;

            if( !$title ) $title = __( 'Untitled', 'foodiepro' );

            $post = array(
                'post_title' => $title,
                'post_type'	=> 'recipe',
                'post_status' => 'auto-draft',
            );

            // If guest, use default author
            if( !is_user_logged_in() ) {
                $default_author = intval( WPUltimateRecipe::option( 'user_submission_default_user', '0' ) );

                if( $default_author ) {
                    $post['post_author'] = $default_author;
                }
            }

            // Check ingredients
            if( WPUltimateRecipe::option( 'user_submission_ingredient_list', '0' ) == '1' ) {
                $ingredients = $_POST['recipe_ingredients'];
                $correct_ingredients = array();

                foreach( $ingredients as $ingredient ) {
                    if( term_exists( $ingredient['ingredient'], 'ingredient' ) ) {
                        $correct_ingredients[] = $ingredient;
                    }
                }

                $_POST['recipe_ingredients'] = $correct_ingredients;
            }

            // Save post
            if( $updating ) {
                $post['ID'] = $updating_id;
                $post['post_status'] = $updating_post->post_status;

                wp_update_post( $post );
                $post_id = $updating_id;
            } else {
                $post_id = wp_insert_post( $post, true );
            }

            // Add terms
            $taxonomies = WPUltimateRecipe::get()->tags();
            unset($taxonomies['ingredient']);

            // Check categorie and tags as well
            $taxonomies['category'] = true;
            $taxonomies['post_tag'] = true;

            foreach( $taxonomies as $taxonomy => $options ) {
                $terms = isset( $_POST['recipe-'.$taxonomy] ) ? $_POST['recipe-'.$taxonomy] : false;

                if ( 'category' == $taxonomy ) {
                    $default_category = intval( WPUltimateRecipe::option( 'user_submission_default_category', 0 ) );

                    if ( $default_category ) {
                        if ( ! $terms ) {
                            $terms = $default_category;
                        } else {
                            if ( is_array( $terms ) ) {
                                $terms[] = $default_category;
                            } else {
                                $terms = array( $terms, $default_category );
                            }
                        }
                    }
                }
                if( $terms ) {
                    if( !is_array( $terms ) ) {
                        $terms = array( intval( $terms ) );
                    } else {
                        $terms = array_map( 'intval', $terms );
                    }

                    wp_set_object_terms( $post_id, $terms, $taxonomy );
                }
            }

            // If guest, add author name
            if( !is_user_logged_in() ) {
                if( $_POST['recipe-author'] != '' ) {
                    $authorname = $_POST['recipe-author'];
                } else {
                    $authorname = __( 'Anonymous', 'foodiepro' );
                }
                update_post_meta( $post_id, 'recipe-author', $authorname );
            } else {
                // Prevent issue with required field
                $_POST['recipe-author'] = 'OK';
            }

            // Add featured image from media uploader
            if( isset( $_POST['recipe_thumbnail'] ) ) {
                update_post_meta( $post_id, '_thumbnail_id', $_POST['recipe_thumbnail'] );
            }

            // Add all images from basic uploader
            if( $_FILES ) {
                foreach( $_FILES as $key => $file ) {
                    if ( 'recipe_thumbnail' == $key ) {
                        if( $file['name'] != '' ) {
                            $this->insert_attachment_basic( $key, $post_id, true );
                        }
                    } else {
                        // $this->insert_attachment_basic( $key, $post_id, false );
                    }
                }
            }

            // Check required fields
            $errors = array();
            $required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() );

            $required_fields_labels = array();
            $required_fields_options = wpurp_admin_user_submission_required_fields();
            foreach( $required_fields_options as $required_fields_option ) {
                $label = str_replace( __( 'Custom Fields', 'foodiepro' ) . ': ', '', $required_fields_option['label'] );
                $required_fields_labels[$required_fields_option['value']] = $label;
            }

            foreach( $required_fields as $required_field ) {
                if( $required_field != 'recipe_thumbnail' || !isset( $_FILES['recipe_thumbnail'] ) ) {
                    if( !isset( $_POST[$required_field] ) || !$_POST[$required_field] ) {
                        $errors[] = $required_fields_labels[$required_field];
                    }
                } else if( !$_FILES['recipe_thumbnail']['name'] && get_post_thumbnail_id( $post_id ) == 0 ) {
                    $errors[] = $required_fields_labels[$required_field];
                }
            }

            // // Check security question
            // if( WPUltimateRecipe::option( 'user_submissions_use_security_question', '' ) == '1' ) {
            //     if( !isset( $_POST['security-answer'] ) || trim( $_POST['security-answer'] ) !== trim( WPUltimateRecipe::option( 'user_submissions_security_answer', '11' ) ) ) {
            //         $errors[] = __( 'Security Question', 'foodiepro' );
            //     }
            // }

            if( count( $errors ) > 0 || isset( $_POST['preview'] ) ) {
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

                if( isset( $_POST['preview'] ) ) {
                    $output .= '<h4>' . __( 'Preview', 'foodiepro' ). '</h4>';
                    $output .= '[ultimate-recipe id=' . $post_id . ']';
                    $output .= '<br/><br/>';
                }

                $output .= $this->submissions_form( $post_id );

                return do_shortcode( $output );
            } else {
                // Update post status
                $args = array(
                    'ID' => $post_id,
                    'post_status' => 'pending',
                );

                // Check approval rules
                $auto_approve = WPUltimateRecipe::option( 'user_submission_approve', 'off' );

                if( $auto_approve == 'guests' ) {
                    $args['post_status'] = 'publish';
                } elseif( $auto_approve == 'registered' && is_user_logged_in() ) {
                    $args['post_status'] = 'publish';
                }

                $auto_approve_users = WPUltimateRecipe::option( 'user_submission_approve_users', array() );
                $auto_approve_users = array_map( 'intval', $auto_approve_users );
                if( in_array( get_current_user_id(), $auto_approve_users ) ) {
                    $args['post_status'] = 'publish';
                }

                $auto_approve_role = trim( WPUltimateRecipe::option( 'user_submissions_approve_role', '' ) );
                if( $auto_approve_role !== '' && current_user_can( $auto_approve_role ) ) {
                    $args['post_status'] = 'publish';
                }

                wp_update_post( $args );

                // Success message
                $successmsg = WPUltimateRecipe::option( 'user_submission_submitted_text', __( 'Recipe submitted! Thank you, your recipe is now awaiting moderation.', 'foodiepro' ) );

                // Send notification email to administrator
                if( WPUltimateRecipe::option('user_submission_email_admin', '0' ) == '1' ) {
                    $to = get_option( 'admin_email' );

                    if( $to ) {
                        $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );

                        $subject = 'New user submission: ' . $title;
                        $message = 'A new recipe has been submitted on your website.';
                        $message .= "\r\n\r\n";
                        $message .= 'Edit this recipe: ' . $edit_link;

                        wp_mail( $to, $subject, $message );
                    }
                }
            }
        }

        do_action('wp_insert_post', 'wp_insert_post');
        return '<p class="successbox">' . $successmsg . '</p>';
    }

    public function insert_attachment_basic( $file_handler, $post_id, $setthumb = false ) {
        if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
            return;
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attach_id = media_handle_upload( $file_handler, $post_id );

        if( true == $setthumb ) { // Thumbnail image
            update_post_meta( $post_id, '_thumbnail_id', $attach_id );
        } else { // Instructions image
            $number = explode( '_', $file_handler );
            $number = $number[2];
            $instructions = get_post_meta( $post_id, 'recipe_instructions', true );
            $instructions[$number]['image'] = $attach_id;
            update_post_meta( $post_id, 'recipe_instructions', $instructions );
            $instructions = get_post_meta( $post_id, 'recipe_instructions', true );
        }

        return $attach_id;
    }

    public function add_helper_buttons( $form, $recipe ) {
        
        // HTML for Helper Buttons 
        ob_start();
        ?>
        <div class="wpurp-user-submissions button-area">
        
        <!-- Recipe Timer Button -->
        <input class="user-submissions-button" id="add-timer" type="button" value="<?php _e('Format as Duration','foodiepro'); ?>" />
        <input class="user-submissions-button" id="add-ingredient" type="button" value="<?php _e('Format as Ingredient','foodiepro'); ?>" />
        
        <script type="text/javascript">
        jQuery(document).ready(function() { 
            console.log("In Buttons Script !");
            jQuery('.user-submissions-button').on('click', function() {
                console.log("Button Click Detected !");
                buttonType = 'timer';
                if (buttonType=="timer") {
                console.log("Add Recipe Timer");
                }
                else 
                console.log("Add Ingredient");
        });
    });
        </script>

        </div>
        <?php
      $html = ob_get_contents();
      ob_end_clean();

        $html .= $form;
        
        return $html;
    }


}

//WPUltimateRecipe::loaded_addon( 'user-submissions', new WPURP_User_Submissions() );