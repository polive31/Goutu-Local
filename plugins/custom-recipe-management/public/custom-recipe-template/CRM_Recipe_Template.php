<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Provides a custom template for WPURP recipes using
    the corresponding WPURP filters
*/

class CRM_Recipe_Template {

	protected static $_PluginPath;
	protected static $_PluginUri;
	// public static $logged_in;

	private $post_ID;

	public function __construct() {
		self::$_PluginUri = plugin_dir_url( dirname( __FILE__ ) );
		self::$_PluginPath = plugin_dir_path( dirname( __FILE__ ) );
	}


    public function print_recipe( $content, $recipe ) {
        $post_ID = get_the_ID();

        ob_start();
        include( self::$_PluginUri . 'custom-recipe-template/partials/custom-recipe-print-template.php' );
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    public function display_recipe( $content, $recipe ) {

        if ( isset($recipe->ID ) )
            $recipe = new CRM_Recipe( $recipe->ID );
        else
            $recipe = new CRM_Recipe( $recipe->ID() );


        $imgID = $recipe->featured_image();
        $imgAlt = get_post_meta($imgID,'_wp_attachment_image_alt', true);
        if (empty($imgAlt))
            // $imgAlt=sprintf(__('Recipe of %s', 'foodiepro'), $recipe->title());
            $imgAlt=$recipe->title();

        ob_start();

        // Output JSON+LD metadata & rich snippets
        echo $this->json_ld_meta_output($recipe,'');

        include( self::$_PluginPath . 'custom-recipe-template/partials/custom-recipe-template.php' );

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    public function json_ld_meta_output( $recipe, $args ) {
        $Custom_Metadata = new Custom_Recipe_Metadata();
        // $metadata = in_array( WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ), array( 'json', 'json-inline' ) ) ? $Custom_Metadata->get_metadata( $recipe ) : '';
        $metadata = $Custom_Metadata->get_metadata( $recipe );

        ob_start();
        echo $metadata;
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function custom_ingredients_list( $recipe, $args ) {
        $out = '';
        $previous_group = '';
        $first_group = true;
        //$out .= '<ul class="wpurp-recipe-ingredients">';

        foreach( $recipe->ingredients() as $ingredient ) {

            if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
                $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
                if ( $term !== null && !is_wp_error( $term ) ) {
                    $ingredient['ingredient'] = $term->name;
                }
            }

            if( $ingredient['group'] != $previous_group || $first_group ) { //removed isset($ingredient['group'] ) &&
                $out .= $first_group ? '' : '</ul>';
                $out .= '<ul class="wpurp-recipe-ingredients">';
                $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
                $previous_group = $ingredient['group'];
                $first_group = false;
            }

            $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeIngredient"' : '';

            $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';

            // $out .= '<input type="checkbox" name="ingredient-check">&nbsp;</input>';
            $out .= '<span class="fa-before" id="checkbox">&nbsp;</span>';

            $ingredient['links'] = 'yes';
            $out .= CRM_Ingredient::display( $ingredient );

            $out .= '</li>';
        }
        //$out .= '</ul>';
        ob_start();
        ?>
        <script>
			jQuery(document).ready(function(){
			// console.log('Inline ingredient checkbox');
				jQuery(document).on('click', '.wpurp-recipe-ingredient  #checkbox', function(e) {
					// console.log('Click on ingredient checkbox detected !');
					e.preventDefault();
					e.stopPropagation();
					jQuery(this).toggleClass('clicked');
				});
			});
        </script>
        <?php
        $out .= ob_get_contents();
        ob_end_clean();

        return $out;
    }

    public function custom_instructions_list( $recipe, $args ) {
        $out = '';
        $previous_group = '';
        $instructions = $recipe->instructions();

        $out .= '<ul class="wpurp-recipe-instruction-container">';
        $first_group = true;

        for( $i = 0; $i < count($instructions); $i++ ) {

            $instruction = $instructions[$i];
            $first_inst = false;

            if( $instruction['group'] != $previous_group ) { /* Entering new instruction group */
                $first_inst = true;
                $out .= $first_group ? '' : '</ul>';
                $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
                $out .= '<ul class="wpurp-recipe-instructions">';
                $previous_group = $instruction['group'];
                $first_group = false;
            }

            $style = $first_inst ? ' li-first' : '';
            $style .= !isset( $instructions[$i+1] ) || $instruction['group'] != $instructions[$i+1]['group'] ? ' li-last' : '';

            $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeInstructions"' : '';

            $out .= '<li class="wpurp-recipe-instruction ' . $style . '" id="wpurp_recipe_instruction' . $i . '">';
            //$out .= '<div' . $meta . '>'.$instruction['description'].'</div>';


			// $out .= $instruction['description'];
			$out .= $this->get_bullet($i) . '</span><span class="recipe-instruction-text">' . $instruction['description'] . '</span>';
			// $out .= '<span class="recipe-instruction-bullet">' . ($i+1) . '</span>[tts]' . $instruction['description'] . '[/tts]';


            if( !empty($instruction['image']) ) {
                $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
                $thumb_url = $thumb['0'];

                $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
                $full_img_url = $full_img['0'];

                $title_tag = WPUltimateRecipe::option( 'recipe_instruction_images_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $instruction['image'] ) ) : esc_attr( $instruction['description'] );
                $alt_tag = WPUltimateRecipe::option( 'recipe_instruction_images_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $instruction['image'], '_wp_attachment_image_alt', true ) ) : esc_attr( $instruction['description'] );

                if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) {
                    $out .= '<div class="instruction-step-image">';
                    $out .= '<a href="' . $full_img_url . '" id="lightbox" title="' . $title_tag . '">';
                    $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
                    $out .= '</a></div>';
                } else {
                    $out .= '<div class="instruction-step-image"><img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/></div>';
                }
            }

            $out .= '</li>';
        }
            $out .= '</ul>';

        return $out;
	}

	public function get_bullet( $id ) {
		ob_start();
		?>
		<div class="recipe-instruction-bullet" title="<?= __('Read this step aloud','foodiepro'); ?>" id="recipe-instruction-bullet<?= $id; ?>">
			<?= $id+1;?>
			<div id="r1" class="ring"></div>
			<div id="r2" class="ring"></div>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
    }


/* Method 2 functions (BETA)
-----------------------------------------------------------------------------------*/

    public function disable_wpurp_rendering()
    {
        return true;
    }


    public function display_recipe_from_scratch($content)
    {

        $api_request = defined('REST_REQUEST');
        if ( !$api_request && !is_feed() && !in_the_loop() || !is_main_query() ) {
            return $content;
        }

        if (get_post_type() == 'recipe' ) {

            remove_filter('the_content', array($this, 'display_recipe_from_scratch'), 10);

            $recipe = new WPURP_Recipe(get_post());

            if (!post_password_required() && (is_single() || WPUltimateRecipe::option('recipe_archive_display', 'full') == 'full' || (is_feed() && WPUltimateRecipe::option('recipe_rss_feed_display', 'full') == 'full'))) {
                $taxonomies = WPUltimateRecipe::get()->tags();
                unset($taxonomies['ingredient']);

                $type = is_feed() ? 'feed' : 'recipe';

                if (!is_single() && WPUltimateRecipe::option('recipe_archive_use_custom_template', '0') == '1') {
                    $template = WPUltimateRecipe::option('recipe_archive_recipe_template', '70');
                } else {
                    $template = 'default';
                }

                // $recipe_box = apply_filters('wpurp_output_recipe', $recipe->output_string($type, $template), $recipe);
                $recipe_box = $this->display_recipe('', $recipe);

                if (strpos($content, '[recipe]') !== false) {
                    $content = str_replace('[recipe]', $recipe_box, $content);
                } else if (preg_match("/<!--\s*nextpage.*-->/", $recipe->post_content(), $out)) {
                    // Add metadata if there is a 'nextpage' tag and there wasn't a '[recipe]' tag on this specific page
                    $content .= $recipe->output_string('metadata');
                } else if (is_single() || !preg_match("/<!--\s*more.*-->/", $recipe->post_content(), $out)) {
                    // Add recipe box to the end of single pages or excerpts (unless there's a 'more' tag
                    $content .= $recipe_box;
                }
            } else {
                $content = str_replace('[recipe]', '', $content); // Remove shortcode from excerpt
                $content = $this->excerpt_filter($content);
            }

            // Remove searchable part
            $content = preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content);

            add_filter('the_content', array($this, 'display_recipe_from_scratch'), 10);
        }

        return $content;
    }

    /* CALLBACKS
	-----------------------------------------------------------------------------------*/
    public function fetch_gallery_images($attachments, $post_id)
    {
        $attachment_ids = get_post_meta($post_id, '_post_image_gallery', true);
        $attachments = array();
        if (!empty($attachment_ids)) {
            foreach ($attachment_ids as $id) {
                if (get_post_status($id) == 'publish') {
                    $attachments[$id] = get_post($id);
                };
            }
        }
        return $attachments;
    }

    public function tag_uploaded_images($media_ids, $success, $post_id)
    {
        if (!(in_array(get_post_type($post_id), array('post', 'recipe'))) || !$success) return;
        $existing_ids = get_post_meta($post_id, '_post_image_gallery', true);

        //Set attachment title if exists
        foreach ($media_ids as $media_id) {
            $title = get_post_meta($media_id, 'fu_title', true);
            if (!empty($title)) {
                $media_post = array(
                    'ID'           => $media_id,
                    'post_title'   => $title,
                );
                wp_update_post($media_post);
            }
        }

        if (!empty($existing_ids)) {
            $media_ids = array_merge($existing_ids, $media_ids);
        }
        if (!empty($media_ids)) {
            update_post_meta($post_id, '_post_image_gallery', $media_ids, 0);
        }
    }

    public function excerpt_filter($content)
    {
        $ignore_query = !in_the_loop() || !is_main_query();
        if (apply_filters('wpurp_recipe_content_loop_check', $ignore_query)) {
            return $content;
        }

        if (get_post_type() == 'recipe') {
            remove_filter('get_the_excerpt', array($this, 'excerpt_filter'), 10);

            $recipe = new WPURP_Recipe(get_post());
            $excerpt = $recipe->excerpt();

            $post_content = $recipe->post_content();
            $post_content = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $post_content));

            if ($post_content == '' && empty($excerpt)) {
                $content = $recipe->description();
            } else if ($content == '') {
                $content = get_the_excerpt();
            }

            $content = apply_filters('wpurp_output_recipe_excerpt', $content, $recipe);

            add_filter('get_the_excerpt', array($this, 'excerpt_filter'), 10);
        }

        return $content;
    }

}
