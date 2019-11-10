<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Submission {

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
    }



/********************************************************************************
****           RECIPE SUBMISSION FORM CUSTOMIZATION                          *****
********************************************************************************/
    public function add_recipe_specific_section( $form, $post, $required_fields=array() ) {
        $recipe = new CRM_Recipe( $post->ID );
        $wpurp_user_submission = true;

        ob_start();
        include( self::$_PluginDir . 'custom-recipe-submission/partials/submission_form_ingredients_instructions.php' );
        $form .= ob_get_contents();
        ob_end_clean();

        return $form;
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
***                CALLBACKS FOR RECIPE SUBMISSION                            ***
********************************************************************************/
    public function recipe_submission_main( $post_id ) {
        // Save all extended recipe meta and set time fields (the standard WPURP meta
        // are already saved thanks to a callback in WPURP plugin)
        $recipe=new CRM_Recipe( $post_id );
        $recipe->save();

        // Save ingredients & instruction images
        // (main image is already saved as part of CPM_Submission->submit() function )
        $this->instructions = get_post_meta( $post_id, 'recipe_instructions', true );

        if( $_FILES ) {
            foreach( $_FILES as $key => $file ) {
                if ( $file['name'] != '' ) {
                    $this->insert_attachment( $key, $post_id );
                }
            }
        }
        update_post_meta( $post_id, 'recipe_instructions', $this->instructions );
    }

    public function insert_attachment( $file_handler, $post_id ) {
        if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
            return;
        }

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        $attach_id = media_handle_upload( $file_handler, $post_id );

        if ( $file_handler == 'recipe_thumbnail' ) { // Featured Recipe image
            set_post_thumbnail( $post_id, $attach_id );
        }
        elseif ( $file_handler == 'ingredients_thumbnail' ) { // Ingredients image
            update_post_meta( $post_id, '_ingredients_thumbnail_id', $attach_id );
        }
        else { // Instructions image
            $number = explode( '_', $file_handler );
            $number = $number[2];
            /* Post meta update for instructions is handled in WPURP/core/helpers/recipe_save.php */
            $this->instructions[$number]['image'] = strval($attach_id);
        }

        return $attach_id;
    }


/********************************************************************************
****                           AJAX CALLBACKS                          **********
********************************************************************************/
    public function ajax_remove_instruction_image() {
        $check = check_ajax_referer( 'custom_recipe_submission_form', 'security', false );
        $post_id = intval( $_POST['postid'] );
        $thumb_id = intval( $_POST['thumbid'] );

        if ($thumb_id==0) {
            $result = delete_post_thumbnail( $post_id );
        }
        else {
            $instructions = get_post_meta( $post_id, 'recipe_instructions', true );
            if ( isset($instructions[$thumb_id]['image']) ) {
                delete_post_meta($post_id, '_instructions_thumbnail_id', $instructions[$thumb_id]['image']);
                unset($instructions[$thumb_id]['image']);
            }
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
        $ingredient_preview = CRM_Ingredient::display( $args );
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
        // $suggestions[] = addslashes($term->name);
            $suggestions[] = htmlspecialchars($term->name);

        echo json_encode($suggestions); //encode into JSON format and output

        die(); //stop "0" from being output
    }


}
