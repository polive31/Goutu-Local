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
            $out .= '<span class="checkbox">&nbsp;</span>';

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
				jQuery(document).on('click', '.wpurp-recipe-ingredient .checkbox', function(e) {
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
    


    
	
}

