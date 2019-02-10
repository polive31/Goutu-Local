
<?php 

/********************************************************************************
****               SUBMISSION PROCESS MANAGEMENT                       **********
********************************************************************************/

    function submissions_process() {
 
            // // Add featured image from media uploader
            // if( isset( $_POST['recipe_thumbnail'] ) ) {
            //     update_post_meta( $post_id, '_thumbnail_id', $_POST['recipe_thumbnail'] );
            // }

            // Add all images from basic uploader
            $this->instructions = get_post_meta( $post_id, 'recipe_instructions', true ); 
            if( $_FILES ) {
                foreach( $_FILES as $key => $file ) {
                    if ( $key == 'recipe_thumbnail' ) {
                        if( $file['name'] != '' )
                            // Recipe thumbnail
                            $this->insert_attachment( $key, $post_id, 'post' );
                    } 
                    elseif ( $key == 'ingredients_thumbnail' ) {
                        if( $file['name'] != '' ) {
                            // Ingredients overview thumbnail
                            $this->insert_attachment( $key, $post_id, 'ingredients' );
                        }
                    }
                    else {
                            // Instruction step thumbnail
                        $this->insert_attachment( $key, $post_id, 'instruction' );
                    }
                }
            }
            update_post_meta( $post_id, 'recipe_instructions', $this->instructions );

            // Check required fields
            $errors = array();
            // $required_fields = apply_filter('wpurp-required-fields', WPUltimateRecipe::option( 'user_submission_required_fields', array() ) );

            // $required_fields_labels = array();
            // $required_fields_options = wpurp_admin_user_submission_required_fields();
            // foreach( $required_fields_options as $required_fields_option ) {
            //     $label = str_replace( __( 'Custom Fields', 'foodiepro' ) . ': ', '', $required_fields_option['label'] );
            //     $required_fields_labels[$required_fields_option['value']] = $label;
            // }

            foreach( self::$required_fields as $required_field ) {
                if( $required_field != 'recipe_thumbnail' || !isset( $_FILES['recipe_thumbnail'] ) ) {
                    if( !isset( $_POST[$required_field] ) || !$_POST[$required_field] || $_POST[$required_field]=='-1'  ) {
                        $errors[] = self::$required_fields_labels[$required_field];
                    }
                } else if( !$_FILES['recipe_thumbnail']['name'] && get_post_thumbnail_id( $post_id ) == 0 ) {
                    $errors[] = self::$required_fields_labels[$required_field];
                }
            }

            // // Check security question
            // if( WPUltimateRecipe::option( 'user_submissions_use_security_question', '' ) == '1' ) {
            //     if( !isset( $_POST['security-answer'] ) || trim( $_POST['security-answer'] ) !== trim( WPUltimateRecipe::option( 'user_submissions_security_answer', '11' ) ) ) {
            //         $errors[] = __( 'Security Question', 'foodiepro' );
            //     }
            // }

            if ( isset( $_POST['preview'] ) ) {
                
                $output = '';
                $output .= '<div class="recipe-preview">';
                $output .= '<p class="submitbox">';
                // $output .= '<h5>' . __( 'Recipe preview', 'foodiepro' ). '</h5>';
                $output .= __( 'Here is what your recipe will look like once it is published. You can choose to continue editing it, or save it as a draft, or publish it, using the buttons at the bottom of the page.', 'foodiepro');
                $output .= '</p>';
                $output .= '<h1>' . get_the_title( $post_id ) . '</h1>';
                $output .= '[display-recipe id=' . $post_id . ']';
                $output .= $this->submissions_form( $post_id, array( 'edit', 'draft', 'publish' ) );
                $output .= '<div>';
                return do_shortcode( $output );
            } 


            elseif ( isset( $_POST['edit'] ) ) {

                $output = '';
                // $output = '<div class="submitbox">';
                // $output .= '<h5>' . __( 'Recipe edit', 'foodiepro' ). '</h5>';
                $output .= '<p class="submitbox">' . __( 'You can edit your recipe here, before submitting it.', 'foodiepro') . '</p>';
                // $output .= '</div>';
                $output .= $this->submissions_form( $post_id, array( 'preview', 'draft', 'publish' ) );
                return $output;

            }            

            elseif ( isset( $_POST['draft'] ) ) {
                // Update post status
                $output = '';

                // $output .= '<h5>BEFORE WP UPDATE POST</h5>';
                // $debug_vars = get_post_meta( $post_id, 'recipe_instructions', true);
                // $output .= '<pre>' . print_r($debug_vars, true) . '</pre>';
                
                // Protect the metadata added since the last post update, ie the instruction images
                // Reason : otherwise they get deleted in wp_update_post 
                $meta_backup = get_post_meta( $post_id, 'recipe_instructions', true );

                $args = array(
                    'ID' => $post_id,
                    'post_status' => 'draft',
                );
                wp_update_post( $args );

                // Restore backuped metadata
                update_post_meta( $post_id, 'recipe_instructions', $meta_backup );

                // $output .= '<h5>AFTER WP UPDATE POST</h5>';
                // $debug_vars = get_post_meta( $post_id, 'recipe_instructions', true);
                // $output .= '<pre>' . print_r($debug_vars, true) . '</pre>';

                // Display confirmation message                 
                $output .= '<p class="submitbox">';
                $output .= __( 'Recipe saved as draft. It will not be visible on the site, but you can edit it at any time and submit it later.', 'foodiepro' );
                $output .= '</p>';
                $url = do_shortcode('[permalink slug="' . self::RECIPES_PUBLISH_SLUG . '"]');
                $output .= sprintf(__('Back to <a href="%s">my published recipes</a>','foodiepro'),$url);
                // $output .= '<a class="more-link" href=' . $url . '>← ' .  __( 'Back to my published recipes', 'foodiepro' ) . '</a>';
                $output .= $this->submissions_form( $post_id, array( 'preview', 'draft', 'publish' ) );

                // $output .= '<h5>AFTER WP INSERT POST</h5>';
                // $debug_vars = get_post_meta( $post_id, 'recipe_instructions', true);
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
                $meta_backup = get_post_meta( $post_id, 'recipe_instructions', true );
                
                // Update post status
                $status=current_user_can('administrator')?'publish':'pending';
                $args = array(
                    'ID' => $post_id,
                    'post_status' => $status,
                );
                
                wp_update_post( $args );

                // Restore backuped metadata
                update_post_meta( $post_id, 'recipe_instructions', $meta_backup );

                // Success message
                $successmsg = current_user_can('administrator')?sprintf( __( 'Recipe <a href="%s">published</a>.', 'foodiepro' ), get_permalink($post_id) ):__( 'Recipe submitted! Thank you, your recipe is now awaiting moderation.', 'foodiepro' );
                $url = do_shortcode('[permalink slug="' . self::RECIPES_PUBLISH_SLUG . '"]');
                $output = '<p class="successbox">' . $successmsg . '</p>';
                $output .= '<p>←' . sprintf( __( '<a href="%s">Back to my recipes</a>', 'foodiepro' ), $url ) . '</p>';

                // Send notification email to administrator
                $to = get_option( 'admin_email' );
                if( $to ) {
                    $edit_link = admin_url( 'post.php?action=edit&post=' . $post_id );

                    $subject = sprintf( __('New user submission:%s', 'foodiepro'), $title );
                    $message = 'A new recipe has been submitted on your website.';
                    $message .= "\r\n\r\n";
                    $message .= 'Edit this recipe: ' . $edit_link;

                    wp_mail( $to, $subject, $message );
                }

                do_action('wp_insert_post', 'wp_insert_post');
                return $output;
            }

            else {

                $output = '';
                $output .= '<p class="submitbox">' . __( 'Unknown action.', 'foodiepro') . '</p>';
                $url = do_shortcode('[permalink slug="' . self::RECIPES_PUBLISH_SLUG . '"]');
                $output .= '<p>←' . sprintf( __( '<a href="%s">Back to my recipes</a>', 'foodiepro' ), $url ) . '</p>';

                return $output;

            }     

        }