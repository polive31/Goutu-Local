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



/* SUBMISSION FORM
--------------------------------------------------------------------------------------*/
    public function add_button_bar( $form, $recipe ) {

        // HTML for Helper Buttons
        ob_start();
        ?>
        <div class="wpurp-user-submissions button-area">

        <!-- Recipe Timer Button -->
        <input class="user-submissions-button" id="add-timer" type="button" value="<?php _e('Format as Duration','crm'); ?>" />
        <input class="user-submissions-button" id="add-ingredient" type="button" value="<?php _e('Format as Ingredient','crm'); ?>" />

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

/* CPM CALLBACKS
--------------------------------------------------------------------------------------*/
    public function cpm_recipe_section_cb($post, $required_fields = array())
    {
        $recipe = new CRM_Recipe($post->ID);
        /* Ingredient group is an hidden input
        For debugging purposes it can be made visible by setting the following variables */
        $group_input_class = ''; // Debug value='debug', Production value=''
        $group_input_type = 'hidden'; // Debug value='text', Production value='hidden'

        $args=compact( 'recipe', 'group_input_class', 'group_input_type', 'required_fields' );

        $form = CRM_Assets::get_template_part( 'form', false, $args );

        return $form;
    }


    /**
     * cpm_recipe_submission_main_cb
     *CPM standard post data are already saved in the main CPM Submission class :
     * * post title
     * * post content
     * * post thumbnail
     * * post taxonomies (tags for post, for recipe : course, season, cuisine, diet )
     *
     * @param  mixed $post_id
     * @return void
     */
    public function save_recipe_meta($post_id)
    {
        $post = get_post($post_id);
        $recipe = new CRM_Recipe_Save($post);
        $recipe->save_recipe_meta();
    }

/* AJAX CALLBACKS
--------------------------------------------------------------------------------------*/
    /**
     * ajax_upload_recipe_image
     *
     * Takes care of attaching the image to the recipe post
     * It doesn't treat the "recipe_instruction" metadata
     * which will be handled at post submission
     *
     * @return void
     */
    public function ajax_upload_recipe_image()
    {
        if (!check_ajax_referer('custom_post_submission_form', 'security', false)) {
            echo('Nonce not recognized');
            die();
        }
        $post_id = intval($_POST['postId']);

        $alt = get_the_title($post_id);
        if (empty($alt) && isset($_POST['imageAlt'])) {
            $alt = $_POST['imageAlt'];
        };

        $featured = $_POST['thumbId']=='featured';

        if (!$featured) {
            $thumb_id = intval($_POST['thumbId']);
            $alt .= ' - Instruction ' . $thumb_id;
        }

        $attach_id = CPM_Save::insert_attachment('file', $post_id, $alt);
        if (!$attach_id) {
            echo('Image upload failed : insert attachment');
            die();
        }


        if( $featured ) {
            $result = set_post_thumbnail($post_id, $attach_id);
            if (!$result) {
                echo ('Image upload failed : set post thumbnail');
                die();
            }
        }
        else {
            /* It is necessary to update the instruction meta now, in case the form
                is refreshed prior to an autosave or submission */
            $result = CRM_Recipe::add_instruction_image( $post_id, $thumb_id, $attach_id);
            if (!$result) {
                echo ('Image upload failed : update instruction#' . $thumb_id . ' meta');
                die();
            }
        }

        $size=$featured?'square-thumbnail':'thumbnail';
        $image = wp_get_attachment_image_src($attach_id, $size);
        if ($image) {
            $response=array(
                'src'   => $image[0],
                'attachId' => $attach_id,
            );
            wp_send_json_success($response);
        }

        echo('Image upload failed : get attachment image');
        die();
    }


    /**
     * ajax_remove_instruction_image
     *
     * Takes care of detaching the image from the recipe post
     * It doesn't treat the "recipe_instruction" metadata
     * which will be handled at post submission
     *
     * @return void
     */
    public function ajax_remove_instruction_image()
    {
        if (!check_ajax_referer('custom_post_submission_form', 'security', false)) {
            echo ('Nonce not recognized');
            die();
        }

        $featured = $_POST['thumbId'] == 'featured';
        $post_id = intval($_POST['postId']);

        if ($featured) {
            $attach_id = get_post_thumbnail_id($post_id);
            if (empty($attach_id)) {
                echo ('Image remove failed : get post thumbnail id not found');
                die();
            }
        }
        else {
            $thumb_id = intval($_POST['thumbId']);
            $attach_id = intval($_POST['attachId']);
            /* It is necessary to update the instruction meta now, in case the form
                is refreshed prior to an autosave or submission */
            $result = CRM_Recipe::delete_instruction_image( $post_id, $thumb_id);
            if (!$result) {
                echo ('Image remove failed : update instruction#' . $thumb_id . ' meta');
                die();
            }
        }


        $result = wp_delete_attachment($attach_id);
        if (empty($result)) {
            echo ('Image remove failed : delete attachment');
            die();
        }

        wp_send_json_success($result);
    }


    /**
     * ajax_custom_get_tax_terms
     *
     * Callback for autocomplete ajax utility
     *
     * @return void
     */
    public function ajax_custom_get_tax_terms() {
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
            $suggestions[] = htmlspecialchars($term->name);
        // $suggestions[] = addslashes($term->name);

        echo json_encode($suggestions); //encode into JSON format and output
        die(); //stop "0" from being output
    }


}
