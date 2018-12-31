<?php

class Custom_WPURP_Recipe_Submission {

    const RECIPES_PUBLISH_SLUG = 'publier-recettes';
    const RECIPE_NEW_SLUG = 'nouvelle-recette';
    const RECIPE_EDIT_SLUG = 'modifier-recette';

    protected static $_PluginDir;  
    protected static $_PluginUrl;  
    protected static $_UploadPath; 

    protected static $required_fields; 
    protected static $required_fields_labels; 
    
    // public function __construct( $name = 'user-submissions' ) {
    public function __construct() {
        self::$_PluginDir = plugin_dir_path( dirname( __FILE__ ) );
        self::$_PluginUrl = plugin_dir_url( dirname( __FILE__ ) );
        $upload_dir = wp_upload_dir();
        self::$_UploadPath = trailingslashit( $upload_dir['basedir'] );

        add_action( 'wp', array( $this, 'hydrate') );

        // Recipe headline filter
        add_filter( 'genesis_post_info', array($this, 'add_recipe_edit_button'), 20); // Important : priority must be above 15 since post meta is customized with priority 15 (see custom post templates)

        // Submission form customization
        add_filter( 'wp_dropdown_cats', array($this, 'add_lang_to_select'));
        // add_action('wp_head',array($this,'add_custom_js'));        

        // Ajax callbacks
        add_action( 'wp_ajax_custom_user_submissions_delete_recipe', array( $this, 'ajax_user_delete_recipe') );
        add_action( 'wp_ajax_nopriv_custom_user_submissions_delete_recipe', array( $this, 'ajax_user_delete_recipe') );

        // Ajax callbacks for ingredient preview 
        add_action( 'wp_ajax_ingredient_preview', array( $this, 'ajax_ingredient_preview'));
        add_action( 'wp_ajax_nopriv_ingredient_preview', array( $this, 'ajax_ingredient_preview'));         

        /* Ajax Callbacks for Autocomplete jquery plugin  */
        add_action('wp_ajax_nopriv_get_tax_terms', array($this, 'ajax_custom_get_tax_terms'));
        add_action('wp_ajax_get_tax_terms', array($this, 'ajax_custom_get_tax_terms'));        
    }

    public function hydrate() {
        self::$required_fields_labels = array(
                        'recipe_title_check' => __('Recipe title.','foodiepro'),
                        'recipe_servings' => __('Number of servings.', 'foodiepro'),
                        'recipe_prep_time' => __('Preparation time.', 'foodiepro'),
                        'recipe-course' => __('Recipe course.', 'foodiepro'),
                        'recipe-difficult' => __('Recipe difficulty.', 'foodiepro'),
                    );
        self::$required_fields = array_keys( self::$required_fields_labels );
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
****           USERS SUBMISSION FORM CUSTOMIZATION                          *****
********************************************************************************/


    public function submissions_form( $recipe_ID = false, $buttons = array('preview', 'draft','publish') ) {

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

        $recipe = new Custom_WPURP_Recipe( $recipe_ID );

        // $required_fields = self::REQUIRED_FIELDS;
        // $required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() );

        ob_start();

        include( self::$_PluginDir . 'templates/custom_submission_form.php' );

        $form = ob_get_contents();
        ob_end_clean();

        $output .= $form;
        
        return $output;
    }


    public function add_button_bar( $form, $recipe ) {
        
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


/********************************************************************************
*********************         ACTIONS CALLBACKS       ***************************
********************************************************************************/

    //* Customize the entry meta in the entry header (requires HTML5 theme support)
    public function add_recipe_edit_button($post_info) {

        if ( !is_singular('recipe') ) return;

        global $post;
        $current_user = wp_get_current_user();
        if ($post->post_author == $current_user->ID || current_user_can('administrator')) { 

            $edit_url = 'href="' . get_permalink() . self::RECIPE_EDIT_SLUG . '?wpurp-edit-recipe=' . $post->ID . '" ';
            $edit_title = 'title="' . __('Edit recipe', 'foodiepro') . '" ';

            $post_info .= '<span class="edit-button"><a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a></span>';    
        }
        return $post_info;
    }    


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
            $exclude = WPUltimateRecipe::option( 'user_submission_hide_category_terms', array() );
            $exclude = implode( ',', $exclude );
        }
        return $exclude;
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

    public function ajax_ingredient_preview() {
        if( ! check_ajax_referer( 'preview_ingredient', 'security', false ) ) {
            wp_send_json_error( array('msg' => 'Nonce not recognized'));
            die();
        }
        if (! is_user_logged_in()) {
            wp_send_json_error( array('msg' => 'User not logged-in'));
            die();
        }
        // if( isset($_POST['ingredient_id'] ) ) {
        //     $id= $_POST['ingredient_id'];
        //     // echo $id;
        // }
        // else {
        //     wp_send_json_error( array('msg' => 'No ingredient id provided'));
        //     die();
        // }
        if ( empty($_POST['ingredient']) ) {
             wp_send_json_error( array('msg' => 'No ingredient name provided'));
            die();          
        }
        $args=array(
            'amount' => '',
            'unit'  => '',
            'ingredient' => '',
            'notes' => ''
        );
        foreach ($args as $key => $value ) {
            if( isset( $_POST[$key] ) )            
                $args[$key] = $_POST[$key];
        }
        $args['links']='no';
        $ingredient_preview = Custom_WPURP_Ingredient::display( $args );
        wp_send_json_success( array('msg' => $ingredient_preview) );
        die();
    }

    public function ajax_custom_get_tax_terms() {
        // global $wpdb; //get access to the WordPress database object variable

        if ( !is_user_logged_in() ) die();
        if ( ! isset( $_GET['tax'] ) ) die();
        if ( ! isset( $_GET['keys'] ) ) die();
        
        $taxonomy = $_GET['tax'];
        $keys = $_GET['keys'];
        // $plural = isset($_GET['plural'])?$_GET['plural']:false;

        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'name__like' => $keys,
            'hide_empty' => false,
        ) );

        //copy the terms to a simple array
        $suggestions = array();
        foreach( $terms as $term )
            $suggestions[] = addslashes($term->name);
            
        echo json_encode($suggestions); //encode into JSON format and output
     
        die(); //stop "0" from being output     
    }



/********************************************************************************
****               SUBMISSION PROCESS MANAGEMENT                       **********
********************************************************************************/

    public function submissions_process() {
        $successmsg = '';

        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) ) {

            wp_verify_nonce( $_POST['submitrecipe'], 'recipe_submit' );

            // If guest, interrupt execution
            if( !is_user_logged_in() ) {
                echo __('You must be logged-in to access this page.', 'foodiepro');
                return;
            }

            // Check if updating
            $updating = false;

            $updating_id = isset( $_POST['recipe_id'] ) ? intval( $_POST['recipe_id'] ) : false;
            if( $updating_id ) {
                $updating_post = get_post( $updating_id );

                if( $updating_post->post_type == 'recipe' && $updating_post->post_status == 'auto-draft' ) {
                    $updating = true;
                } elseif( $updating_post->post_type == 'recipe' && ($updating_post->post_author == get_current_user_id() || current_user_can('administrator') ) ) {
                    $updating = true;
                }
            }


            $title = isset( $_POST['recipe_title'] ) ? $_POST['recipe_title'] : '';
            $_POST['recipe_title_check'] = $title;

            if( !$title ) $title = __( 'Untitled', 'foodiepro' );

            $content = isset( $_POST['content'] ) ? $_POST['content'] : '';

            $post = array(
                'post_title' => $title,
                'post_type' => 'recipe',
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

            // Save all extended recipe meta and set time fields (the standard WPURP meta
            // are already saved thanks to a callback in WPURP plugin)
            $recipe=new Custom_WPURP_Recipe( $post_id );
            $recipe->save();

            // $meta = get_post_meta( $post );

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

            // // Add featured image from media uploader
            // if( isset( $_POST['recipe_thumbnail'] ) ) {
            //     update_post_meta( $post_id, '_thumbnail_id', $_POST['recipe_thumbnail'] );
            // }

            // Add all images from basic uploader
            $this->instructions = get_post_meta( $post_id, 'recipe_instructions', true ); 
            if( $_FILES ) {
                foreach( $_FILES as $key => $file ) {
                    if ( $key == 'recipe_thumbnail' ) {
                        if( $file['name'] != '' ) {
                            // Recipe thumbnail
                            $this->insert_attachment_basic( $key, $post_id, 'post' );
                        }
                    } 
                    elseif ( $key == 'ingredients_thumbnail' ) {
                        if( $file['name'] != '' ) {
                            // Ingredients overview thumbnail
                            $this->insert_attachment_basic( $key, $post_id, 'ingredients' );
                        }
                    }
                    else {
                            // Instruction step thumbnail
                        $this->insert_attachment_basic( $key, $post_id, 'instruction' );
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
                $successmsg = current_user_can('administrator')?__( 'Recipe published.', 'foodiepro' ):__( 'Recipe submitted! Thank you, your recipe is now awaiting moderation.', 'foodiepro' );
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
    }


    public function insert_attachment_basic( $file_handler, $post_id, $img_type = 'post' ) {
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
        elseif ( $img_type == 'ingredients' ) { // Ingredients image
            update_post_meta( $post_id, '_ingredients_thumbnail_id', $attach_id );
        }            
        else { // Instructions image
            $number = explode( '_', $file_handler );
            $number = $number[2];
            $this->instructions[$number]['image'] = $attach_id;
        }

        return $attach_id;
    }


}

new Custom_WPURP_Recipe_Submission();