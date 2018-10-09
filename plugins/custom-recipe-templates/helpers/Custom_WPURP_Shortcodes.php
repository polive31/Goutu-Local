<?php

class Custom_WPURP_Shortcodes extends WPURP_Premium_Addon {

    const RECIPES_PUBLISH_SLUG = 'publier-recettes';
    const RECIPE_NEW_SLUG = 'nouvelle-recette';
    const RECIPE_EDIT_SLUG = 'modifier-recette';
    const MULTISELECT = array( 'ingredient' => true, 'course' => true, 'cuisine' => false, 'season' => false, 'occasion' => true, 'diet' => true, 'difficult' => false, 'category'=>true, 'post_tag' => true);
    protected static $_PluginDir;  
    protected static $_UploadPath; 
    protected $logged_in;
    protected $taxonomies;
    protected $instructions;
    protected static $UNITS;    

    public function __construct( $name = 'user-submissions' ) {
        parent::__construct( $name );
        
        self::$_PluginDir = plugin_dir_path( dirname( __FILE__ ) );
        $upload_dir = wp_upload_dir();
        self::$_UploadPath = trailingslashit( $upload_dir['basedir'] );

        add_shortcode( 'custom-wpurp-submissions-new-recipe', array( $this, 'new_submission_shortcode' ) );
        add_shortcode( 'custom-wpurp-submissions-current-user-edit', array( $this, 'submissions_current_user_edit_shortcode' ) );
        add_shortcode( 'custom-wpurp-favorites', array( $this, 'favorite_recipes_shortcode' ) );
        

        // Recipe List Shortcode and associated actions
        add_shortcode( 'custom-recipe-submissions-current-user-list', array( $this, 'submissions_current_user_list_shortcode' ) );
        add_action( 'wp_ajax_custom_user_submissions_delete_recipe', array( $this, 'ajax_user_delete_recipe') );
        add_action( 'wp_ajax_nopriv_custom_user_submissions_delete_recipe', array( $this, 'ajax_user_delete_recipe') );

        
        // Misc 
        add_shortcode( 'display-ingredient', array( $this, 'display_ingredient_shortcode' ) );

        // Init actions
        add_action( 'wp', array($this, 'hydrate'));


    }

    public function hydrate() {

        self::$UNITS = array(
            array('g'     , _n('gram','grams',1,'foodiepro')),
            array('kg'    , _n('kilogram', 'kilograms',1, 'foodiepro')),
            array('cl'    , _n('cl','cl',1,'foodiepro')),
            array('dl'     , _n('dl','dl',1,'foodiepro')),
            array('l'     , _n('l','l',1,'foodiepro')),
            array('tbs'   , _n('table spoon','table spoons',1, 'foodiepro')),
            array('tsp'   , _n('tea spoon','tea spoons',1, 'foodiepro')),
            array('stick' , _n('stick','sticks',1,'foodiepro')), //Baton
            array('can'   , _n('can','cans',1, 'foodiepro')), //Boite
            array('bowl'  , _n('bowl','bowls',1, 'foodiepro')), //Bol
            array('bunch' , _n('bunch','bunches',1, 'foodiepro')), //Botte
            array('bouquet'   , _n('bouquet','bouquets',1, 'foodiepro')), //Bouquet
            array('sprig'   , _n('sprig','sprigs',1, 'foodiepro')), //Brin
            array('branch'   , _n('branch','branches',1, 'foodiepro')), //Branche (thym)
            array('bulb'   , _n('bulb','bulbs',1, 'foodiepro')), //Bulbe
            array('cube' , _n('cube','cubes',1, 'foodiepro')), //Cube
            array('finger', _n('finger','fingers',1, 'foodiepro')), //Doigt
            array('sheet' , _n('sheet','sheets',1, 'foodiepro')),  //Feuille
            array('leave' , _n('leave','leaves',1, 'foodiepro')),  //Feuille (plante)
            array('fillet'   , _n('fillet','fillets',1, 'foodiepro')), //Filet (anchois)
            array('clove' , _n('clove','cloves',1,'foodiepro')), // Gousse
            array('knob'   , _n('knob', 'knobs',1,'foodiepro')), //Noix
            array('pinch' , _n('pinch','pinches',1, 'foodiepro')), // Pincée
            array('handful', _n('handful', 'handfuls',1,'foodiepro')), //Poignée
            array('sachet', _n('sachet','sachets',1, 'foodiepro')), //Sachet
            array('cup'   , _n('cup','cups',1, 'foodiepro')), //Tasse
            array('slice'   , _n('slice', 'slices',1,'foodiepro')), //Tranche
            array('glass'   , _n('glass', 'glasses',1,'foodiepro')), //Verre
        );        
    }

    public function new_submission_shortcode() {
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

    public function display_ingredient_shortcode( $options ) {
        $options = shortcode_atts( array(
            'amount' => '', 
            'amount_normalized' => '', 
            'unit' => '',
            'ingredient' => '',
            'notes' => '',
        ), $options );

        return $this->display_ingredient( $options );

    }


    public function display_ingredient( $ingredient ) {
        $out = '';

        $out .= '<span class="recipe-ingredient-quantity-unit"><span class="wpurp-recipe-ingredient-quantity recipe-ingredient-quantity" data-normalized="'.$ingredient['amount_normalized'].'" data-fraction="'.$fraction.'" data-original="'.$ingredient['amount'].'">'.$ingredient['amount'].'</span> <span class="wpurp-recipe-ingredient-unit recipe-ingredient-unit" data-original="'.$ingredient['unit'].'">'.$ingredient['unit'].'</span></span>';

        $taxonomy = get_term_by('name', $ingredient['ingredient'], 'ingredient');
        // $taxonomy_slug = is_object( $taxonomy ) ? $taxonomy->slug : $args['ingredient_name'];
        $taxonomy_slug = $taxonomy->slug;

        $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' );
        $plural = is_array( $plural ) ? false : $plural;
        ////PC::debug( array('Plural array'=>$plural) );
        
        $plural_data = $plural ? ' data-singular="' . esc_attr( $ingredient['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';

        $out .= ' <span class="wpurp-recipe-ingredient-name recipe-ingredient-name"' . $plural_data . '>';

                $ingredient_name = remove_accents( $ingredient['ingredient'] );
                $first_letter = $ingredient_name[0];
                $first_word = strtolower( explode(' ', trim($ingredient_name))[0] );
                
                if ( $ingredient['unit']!='' ) {
                    if ( in_array($first_letter, $vocals) || in_array( $first_word, $exceptions) )
                        $out .= _x('of ','vowel','foodiepro');
                    else 
                        $out .= _x('of ','consonant','foodiepro');                  
                }

        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

        $closing_tag = '';
        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' ) {

            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            } else {
                $custom_link = false;
            }

            if( WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) !== '1' ) {
                if( $custom_link !== false && $custom_link !== '' ) {
                    $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

                    $out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
                    $closing_tag = '</a>';
                } else if( $ingredient_links != 'custom' ) {
                    $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
                    $closing_tag = '</a>';
                }
            }
        }

        // $out .= $plural && ($ingredient['unit']!='' || $ingredient['amount_normalized'] > 1) ? $plural : $ingredient['ingredient'];
        $out .= $plural && ($ingredient['amount_normalized'] > 1) ? $plural : $ingredient['ingredient'];
        $out .= $closing_tag;
        $out .= '</span>';

        if( $ingredient['notes'] != '' ) {
            $out .= ' ';
            $out .= '<span class="wpurp-recipe-ingredient-notes recipe-ingredient-notes">'.$ingredient['notes'].'</span>';
        }

        return $out;
    }

    public function favorite_recipes_shortcode( $options ) {
        $options = shortcode_atts( array(), $options );
        $output = '';
        $user_id = get_current_user_id();

        if( $user_id !== 0 ) {
            $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            $recipes = empty( $favorites ) ? array() : WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if( count( $favorites ) == 0 || count( $recipes ) == 0 ) {
                $output .= '<p class="submitbox">' . __( "You don't have any favorite recipes.", 'wp-ultimate-recipe' ) . '</p>';
            } else {
                // $output .= '<p>' . __('Here is the list of the recipes that you bookmarked.', 'foodiepro') . '</p>';
                $output .= $this->display_recipes( $recipes, false);
            }
        }
        return $output;
    }

   public function submissions_current_user_list_shortcode() {
        $output = '';
        $author = get_current_user_id();

        if( $author !== 0 ) {
            // $output .= 'In Custom User Submission Class !';
            $recipes = WPUltimateRecipe::get()->query()->author( $author )->post_status( array( 'publish', 'private', 'pending', 'draft' ) )->get();

            if( count( $recipes ) !== 0 ) {
                // $output .= '<ul class="wpurp-user-submissions-current-user-edit">';
                $output .= '<p>' . __('Here is the list of the recipes that you created, and their status. You can choose to edit them, change their visibility, or delete them.', 'foodiepro') . '</p>';
                $output .= $this->display_recipes( $recipes, true );
            }
        }
        return $output;
    }  

    public function display_recipes( $recipes, $edit=false ) {
        $output = '';
        $output .= '<table class="custom-recipe-list">';

        $statuses = get_post_statuses();

        foreach ( $recipes as $recipe ) {
            $image_url = $recipe->image_ID() > 0 ? $recipe->image_url( 'mini-thumbnail' ) : WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
 
            $view_url = 'href="' . get_permalink($recipe->ID()) . '" ';    
            $view_title = 'title="' . __('Preview recipe', 'foodiepro') . '" ';
            $edit_url = $edit?'href="' . get_permalink() . self::RECIPE_EDIT_SLUG . '?wpurp-edit-recipe=' . $recipe->ID() . '" ':$view_url;   
            $edit_title = $edit?'title="' . __('Edit recipe', 'foodiepro') . '" ':$view_title;
 
            $item = '<tr class="recipe-list-row">';
            if ($edit) $item .= '<td class="recipe-list-actions">';
            // if ($edit) $item .= '<a ' . $edit_url . $edit_title . '><i class="fa fa-pencil-square-o"></i></a>';
            if ($edit) $item .= '<a ' . $view_url . $view_title . '><i class="fa fa-eye"></i></a>';
            if ($edit) $item .= '</td>';
            $item .= '<td class="recipe-list-thumbnail"><a ' . $edit_url . $edit_title . '><img src="' . $image_url . '"></a></td>';
            $item .= '<td class="recipe-list-title"><a ' . $edit_url . $edit_title . '>' . $recipe->title() . '</a></td>';
            if ($edit) $item .= '<td class="recipe-list-status">' . $statuses[ $recipe->post_status() ] . '</td>';
            if ($edit) $item .= '<td class="recipe-list-actions" title="' . __('Delete recipe', 'foodiepro') . '"><i class="fa fa-trash user-submissions-delete-recipe nodisplay" data-id="' . $recipe->ID() . '" data-title="' . esc_attr( $recipe->title() ) . '"></i></td>';
            $item .= '</tr>';
 
            $output .= apply_filters( 'custom_wpurp_recipe_list_item', $item, $recipe );
        }
        $output .= '</table>';
        return $output;
    }  

    public function ajax_user_delete_recipe() {
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

    public function submissions_current_user_edit_shortcode() {
        $output = '';
        $user_id = get_current_user_id();

        if( isset( $_POST['submitrecipe'] ) ) {            
            $output .= $this->submissions_process();
        } 

        elseif( isset( $_GET['wpurp-edit-recipe'] ) ) {
            $recipe_id = $_GET['wpurp-edit-recipe'];
            $post = get_post( $recipe_id );
            $user = get_userdata( $user_id );

            if( $post->post_author == $user_id ) {
                $output .= '<p class="submitbox">' . __( 'You can edit your recipe here, before submitting it.', 'foodiepro') . '</p>';
                $output .= $this->submissions_form( $recipe_id );
            }
        }        

        return $output;
    }


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

        $recipe = new WPURP_Recipe( $recipe_ID );

        $required_fields = WPUltimateRecipe::option( 'user_submission_required_fields', array() );

        ob_start();

        include( self::$_PluginDir . 'templates/custom_submission_form.php' );

        $form = ob_get_contents();
        ob_end_clean();

        // $output .= self::$_PluginDir . 'templates/custom_submission_form.php';
        // $output .= '<h3>In CUSTOM USER SUBMISSIONS </h3>';
        // $output .= apply_filters( 'custom_user_submissions_form', $form, $recipe );
        $output .= $form;
        
        return $output;
    }

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

            // Save post
            if( $updating ) {
                $post['ID'] = $updating_id;
                $post['post_status'] = $updating_post->post_status;

                wp_update_post( $post );
                $post_id = $updating_id;
            } else {
                $post_id = wp_insert_post( $post, true );
            }

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
                    if ( 'recipe_thumbnail' == $key ) {
                        if( $file['name'] != '' ) {
                            $this->insert_attachment_basic( $key, $post_id, true );
                        }
                    } else {
                }
                        $this->insert_attachment_basic( $key, $post_id, false );
                    }
            }
            update_post_meta( $post_id, 'recipe_instructions', $this->instructions );
            $toto = get_post_meta( $post_id, 'recipe_instructions', true ); 

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

            if ( isset( $_POST['preview'] ) ) {
                
                $output = '';
                $output .= '<div class="recipe-preview">';
                $output .= '<p class="submitbox">';
                // $output .= '<h5>' . __( 'Recipe preview', 'foodiepro' ). '</h5>';
                $output .= __( 'Here is what your recipe will look like once it is published. You can choose to continue editing it, or save it as a draft, or publish it, using the buttons at the bottom of the page.', 'foodiepro');
                $output .= '</p>';
                $output .= '<h4>' . get_the_title( $post_id ) . '</h4>';
                $output .= '[ultimate-recipe id=' . $post_id . ']';
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
                $output .= '<a class="more-link" href=' . $url . '>← ' .  __( 'Back to my published recipes', 'foodiepro' ) . '</a>';
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
                $args = array(
                    'ID' => $post_id,
                    'post_status' => 'pending',
                );
                
                wp_update_post( $args );

                // Restore backuped metadata
                update_post_meta( $post_id, 'recipe_instructions', $meta_backup );

                // Success message
                $successmsg = __( 'Recipe submitted! Thank you, your recipe is now awaiting moderation.', 'foodiepro' );
                $url = do_shortcode('[permalink slug="' . self::RECIPES_PUBLISH_SLUG . '"]');
                $output = '<p class="successbox">' . $successmsg . '</p>';
                $output .= '<p>←' . sprintf( __( 'Back to <a href="%s">my published recipes</a>', 'foodiepro' ), $url ) . '</p>';

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
                $output .= '<p>←' . sprintf( __( 'Back to <a href="%s">my published recipes</a>', 'foodiepro' ), $url ) . '</p>';                

                return $output;

            }     

        }
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
            // $instructions = get_post_meta( $post_id, 'recipe_instructions', true );
            $this->instructions[$number]['image'] = $attach_id;
            // update_post_meta( $post_id, 'recipe_instructions', $instructions );
            // $instructions = get_post_meta( $post_id, 'recipe_instructions', true );
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