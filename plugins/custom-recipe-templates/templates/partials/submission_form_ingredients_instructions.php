<?php
// Recipe should never be null. Construct just allows easy access to WPURP_Recipe functions in IDE.
if( is_null( $recipe ) ) $recipe = new WPURP_Recipe(0);
if( !isset( $required_fields ) ) $required_fields = array();
?>

<script>

    function autoSuggestIngredient(id) {
        console.log('In autoSuggestIngredient');
        <?php if( WPUltimateRecipe::option( 'disable_ingredient_autocomplete', '' ) !== '1' ) { ?>
        // jQuery('#' + id).suggest("<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=" + type);
        // console.log("<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=" + type);
        tax='ingredient';
        thisInput=jQuery('#' + id);
        term=thisInput.val();
        // term1=jQuery(this).val();
        console.log(term);
        // console.log(term1);
        // console.log('#' + id);
                spinnerHTML = jQuery('#spinner-' + id).html();
                console.log( '#spinner-' + id + ' : ' + spinnerHTML);
                jQuery('#spinner-' + id).show();
        jQuery('#' + id).autoComplete({
            minChars: 3,
            source: function(term, response) {
                try { xhr.abort(); } catch(e){}
                xhr = jQuery.ajax({
                    dataType: 'json',
                    url: '/wp-admin/admin-ajax.php',
                    data: 'action=get_tax_terms&tax='+tax+'&keys='+term,
                    success: function(data) {
                        response(data);
                    },
                    complete: function() {
                        jQuery('#spinner-' + id).hide();
                    }
                });
            }

        });
        <?php } ?>
    }

    jQuery(document).ready(function() {
        jQuery(".recipe-instructions-container").on("change", "input.recipe_instructions_image", function() { 
            var changedSelectId = jQuery(this).attr("id");
            var Id = changedSelectId.match(/\d+/);
            // console.log( "Changement sur l'input..." + Id );
            PreviewImage(Id);
        });
    });

    function PreviewImage(id="") {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("recipe_thumbnail_input_" + id).files[0]);
        oFReader.onload = function (oFREvent) {
            document.getElementById("instruction_thumbnail_preview_" + id ).src = oFREvent.target.result;
        };
    };
</script>
<input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce( 'recipe' ); ?>" />
<div class="recipe-general-container">
    <h4><?php _e( 'General', 'foodiepro' ); ?></h4>
    <table class="recipe-general-form">
    <?php if( !isset( $wpurp_user_submission ) ) { ?>
        <tr class="recipe-general-form-title">
            <td class="recipe-general-form-label"><label for="recipe_title"><?php _e( 'Title', 'foodiepro' ); ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" name="recipe_title" id="recipe_title" value="<?php echo esc_attr( $recipe->title() ); ?>" />
                <span class="recipe-general-form-notes"> <?php _e( '(leave blank to use post title)', 'foodiepro' ) ?></span>
            </td>
        </tr>
<!--         <?php if( WPUltimateRecipe::option( 'recipe_alternate_image', '1' ) == '1' ) { ?>
        <tr class="recipe-general-form-alternate-image">
            <td class="recipe-general-form-label"><label for="recipe_alternate_image"><?php _e( 'Image', 'foodiepro' ); ?></label></td>
            <td class="recipe-general-form-field">
                <input type="hidden" name="recipe_alternate_image" id="recipe_alternate_image" value="<?php echo $recipe->alternate_image(); ?>" />
                <input class="recipe_alternate_image_add button <?php if( $recipe->alternate_image() ) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Alternate Image', 'foodiepro' ); ?>" />
                <input class="recipe_alternate_image_remove button<?php if( !$recipe->alternate_image() ) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e('Remove Alternate Image', 'foodiepro' ); ?>" />

                <br/><img src="<?php echo $recipe->alternate_image() ? $recipe->image_url( 'thumbnail' ) : WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png'; ?>" class="recipe_alternate_image" />
                <span class="recipe-general-form-notes"> <?php _e( '(leave blank to use featured image)', 'foodiepro' ); ?></span>
            </td>
        </tr>
        <?php } ?> -->
    <?php } ?>
        <tr class="recipe-general-form-description">
            <td class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'foodiepro' ); ?><?php if( in_array( 'recipe_description', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <textarea name="recipe_description" id="recipe_description" rows="4"><?php echo esc_html( $recipe->description() ); ?></textarea>
            </td>
        </tr>
        <tr class="recipe-general-form-servings">
            <td class="recipe-general-form-label"><label for="recipe_servings"><?php _e( 'Servings', 'foodiepro' ); ?><?php if( in_array( 'recipe_servings', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" name="recipe_servings" id="recipe_servings" value="<?php echo esc_attr( $recipe->servings() ); ?>" />
                <input type="text" name="recipe_servings_type" id="recipe_servings_type" value="<?php echo esc_attr( $recipe->servings_type() ); ?>" />
                <span class="recipe-general-form-notes"> <?php _e( '(e.g. 2 people, 3 loafs, ...)', 'foodiepro' ); ?></span>
            </td>
        </tr>
        <tr class="recipe-general-form-prep-time">
            <td class="recipe-general-form-label"><label for="recipe_prep_time"><?php _e( 'Prep Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_prep_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" name="recipe_prep_time" id="recipe_prep_time" value="<?php echo esc_attr( $recipe->prep_time() ); ?>" />
                <input type="text" name="recipe_prep_time_text" id="recipe_prep_time_text" value="<?php echo esc_attr( $recipe->prep_time_text() ); ?>" />
                <span class="recipe-general-form-notes"> <?php _e( '(e.g. 20 minutes, 1-2 hours, ...)', 'foodiepro' ); ?></span>
            </td>
        </tr>
        <tr class="recipe-general-form-cook-time">
            <td class="recipe-general-form-label"><label for="recipe_cook_time"><?php _e( 'Cook Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_cook_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" name="recipe_cook_time" id="recipe_cook_time" value="<?php echo esc_attr( $recipe->cook_time() ); ?>" />
                <input type="text" name="recipe_cook_time_text" id="recipe_cook_time_text" value="<?php echo esc_attr( $recipe->cook_time_text() ); ?>" />
            </td>
        </tr>
        <tr class="recipe-general-form-passive-time">
            <td class="recipe-general-form-label"><label for="recipe_passive_time"><?php _e( 'Passive Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_passive_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" name="recipe_passive_time" id="recipe_passive_time" value="<?php echo esc_attr( $recipe->passive_time() ); ?>" />
                <input type="text" name="recipe_passive_time_text" id="recipe_passive_time_text" value="<?php echo esc_attr( $recipe->passive_time_text() ); ?>" />
            </td>
        </tr>
    <?php if( !isset( $wpurp_user_submission ) ) { ?>
        <tr>
            <td class="recipe-general-form-label">&nbsp;</td>
            <td class="recipe-general-form-field recipe-form-notes">
                <?php _e( "Don't forget that you can tag your recipe with <strong>Courses</strong> and <strong>Cuisines</strong> by using the boxes on the right. Use the <strong>featured image</strong> if you want a photo of the finished dish.", 'foodiepro' ) ?>
            </td>
        </tr>
    <?php } ?>
    </table>
</div>

<div class="recipe-ingredients-container">
    <h4><?php _e( 'Ingredients', 'foodiepro' ); ?></h4>
    <?php $ingredients = $recipe->ingredients(); ?>
    <table id="recipe-ingredients">
        <thead>
        <tr class="ingredient-group ingredient-group-first">
	          	<td class="group" colspan="5">
					<div class="group-container">
		            	<span class="header"><?php _e( 'Ingredients Group', 'foodiepro' ); ?></span>
		                <!-- <span class="name ingredient-groups-disabled"><?php echo __( 'Main Ingredients', 'foodiepro' ) . ' ' . __( '(this label is not shown)', 'foodiepro' ); ?></span> -->
		                <?php
		                $previous_group = '';
		                if( isset( $ingredients[0] ) && isset( $ingredients[0]['group'] ) ) {
		                    $previous_group = $ingredients[0]['group'];
		                }
		                ?>
                        <!-- <span class="name ingredient-groups-enabled"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr( $previous_group ); ?>" /></span> -->
		                <span class="name"><input type="text" placeholder="<?php echo __('eg. For the dough', 'foodiepro');?>" class="ingredient-group-label" value="<?php echo esc_attr( $previous_group ); ?>" /></span>
					</div>
		          </td>
		          <td class="group mobile-hidden" colspan="1">&nbsp;</td>
        </tr>
<!--         <tr class="ingredient-field-header">
            <td>&nbsp;</td>
            <td><?php _e( 'Quantity', 'foodiepro' ); ?></td>
            <td><?php _e( 'Unit', 'foodiepro' ); ?></td>
            <td><?php _e( 'Ingredient', 'foodiepro' );?><span class="wpurp-required">* </span><?php _e( '(singular)', 'foodiepro' ); ?></td>
            <td><?php _e( 'Notes', 'foodiepro' ); ?></td>
        </tr> -->
        </thead>
        <tbody>
        <tr class="ingredient-group-stub">
            <td colspan="6" class="group">
            	<div class="group-container">
            		<span class="header"><?php _e( 'Ingredients Group', 'foodiepro' ); ?></span>
            		<span class="name"><input type="text" class="ingredient-group-label" /></span>
            	</div>
            	<div class="group center-column delete-button"><span class="ingredient-group-delete">&nbsp;</span></div>
            </td>
        </tr>
        <?php
        $i = 0;
        if( $ingredients )
        {
            foreach( $ingredients as $ingredient ) {

                if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
                    $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
                    if ( $term !== null && !is_wp_error( $term ) ) {
                        $ingredient['ingredient'] = $term->name;
                    }
                }

                if( !isset( $ingredient['group'] ) ) {
                    $ingredient['group'] = '';
                }

                if( $ingredient['group'] != $previous_group ) { ?>
                    <tr class="ingredient-group">
                        <td colspan="6" class="group">
                        	<div class="group-container">
                        		<span class="header"><?php _e( 'Ingredients Group', 'foodiepro' ); ?></span>
                        		<span class="name" colspan="2"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr( $ingredient['group'] ); ?>" /></span>
                        	</div>
                        	<div class="group center-column delete-button">
                        		<span class="ingredient-group-delete">&nbsp;</span>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $previous_group = $ingredient['group'];
                }
                ?>
                <!-- Existing Ingredient -->
                <tr class="ingredient">
                    <!-- Sort handle -->
                    <td class="sort-handle"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></td>
                    <!-- Quantity -->
                    <td id="qty"><!-- <span class="mobile-display"><?php _e( 'Quantity', 'foodiepro' ); ?></span> --><input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['amount'] ); ?>" /></td>
                    <!-- Unit -->
                    <td id="unit"><!-- <span class="mobile-display"><?php _e( 'Unit', 'foodiepro' ); ?></span> --><input type="text"   name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['unit'] ); ?>" /></td>
                    <!-- Name -->
                    <td id="name"><!-- <span class="mobile-display"><?php _e( 'Ingredients', 'foodiepro' ); ?></span> --><input type="text"   name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" onkeyup="autoSuggestIngredient('ingredients_<?php echo $i; ?>');" value="<?php echo esc_attr( $ingredient['ingredient'] ); ?>" /></td><td><i id="spinner-ingredients_<?php echo $i; ?>" class="spinner fa fa-refresh fa-spin"></i></td>
                    <!-- Notes -->
                    <td id="notes">
                            <!-- <span class="mobile-display"><?php _e( 'Notes', 'foodiepro' ); ?></span> -->
                        <!--<textarea rows="1" col="20" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>"><?php echo esc_attr( $ingredient['notes'] ); ?></textarea> -->
                        <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['notes'] ); ?>" />
                        <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['group'] ); ?>" />
                    </td>
                    <td class="delete-button" colspan="1"><span class="ingredients-delete">&nbsp;</span></td>
                </tr>
                <?php
                $i++;
            }

        }
        ?>
        <!-- New Ingredient (stub) -->
        <tr class="ingredient">
            <td class="sort-handle"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></td>
            <!-- Quantity -->
            <td><!-- <span class="mobile-display"><?php _e( 'Quantity', 'foodiepro' ); ?> </span>-->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" placeholder="<?php _e( 'Quantity', 'foodiepro' ); ?>" />
            </td>
            <!-- Unit -->
            <td><!-- <span class="mobile-display"><?php _e( 'Unit', 'foodiepro' ); ?> </span>-->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" placeholder="<?php _e( 'Unit', 'foodiepro' ); ?>" />
            </td>
            <!-- Ingredient Name -->
            <td><!-- <span class="mobile-display"><?php _e( 'Ingredient', 'foodiepro' ); ?></span> -->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" onkeyup="autoSuggestIngredient('ingredients_<?php echo $i; ?>');" placeholder="<?php _e( 'Ingredient', 'foodiepro' ); ?>" /></td><td><i id="spinner-ingredients_<?php echo $i; ?>" class="spinner fa fa-refresh fa-spin"></i>
            </td>
            <!-- Ingredient Notes -->
            <td>
                <!-- <span class="mobile-display"><?php _e( 'Notes', 'foodiepro' ); ?></span> -->
                <!-- <textarea rows="1" cols="20" type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" <?php if($i == 0) { echo 'placeholder="' . __( 'extra virgin', 'foodiepro' ) . '"'; }?> class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>"></textarea> -->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e( 'Notes', 'foodiepro' ); ?>"  />
                <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="" />
            </td>
            <!-- Delete button -->
            <td class="delete-button" colspan="1"><span class="ingredients-delete">&nbsp;</span></td>
        </tr>
        </tbody>
    </table>
    <div class="button" id="ingredients-add-box">
        <a href="#" id="ingredients-add"><?php _e( 'Add an ingredient', 'foodiepro' ); ?></a>
    </div>
    <div  class="button" id="ingredients-add-group-box">
        <a href="#" id="ingredients-add-group"><?php _e( 'Add an ingredient group', 'foodiepro' ); ?></a>
    </div>
    <div class="recipe-form-notes">
        <?php _e( "<strong>Use the TAB key</strong> while adding ingredients, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
    </div>
</div>

<div class="recipe-instructions-container">
    <h4><?php _e( 'Instructions', 'foodiepro' ); ?></h4>
    <?php $instructions = $recipe->instructions(); ?>
    <table id="recipe-instructions">
        <thead>
        <tr class="instruction-group instruction-group-first">
			<td colspan="3" class="group">
          		<span class="header"><?php _e( 'Instructions Group', 'foodiepro' ); ?></span>
				<!-- <span class="name instruction-groups-disabled"><?php echo __( 'Main Instructions', 'foodiepro' ) . ' ' . __( '(this label is not shown)', 'foodiepro' ); ?></span> -->
                <?php
                $previous_group = '';
                if( isset( $instructions[0] ) && isset( $instructions[0]['group'] ) ) {
                    $previous_group = $instructions[0]['group'];
                }
                ?>
                <span class="name"><input type="text" placeholder="<?php echo __('eg. Preparation of the dough','foodiepro'); ?>" class="instruction-group-label" value="<?php echo esc_attr( $previous_group ); ?>"/></span>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr class="instruction-group-stub">
		        <td colspan="3" class="group">
		        	<div class="group-container">
		            <span class="header"><?php _e( 'Instructions Group', 'foodiepro' ); ?></span>
		            <span class="name"><input type="text" class="instruction-group-label" /></span>
		        	</div>
            	<div class="group center-column delete-button"><span class="instruction-group-delete">&nbsp;</span></div>
		        </td>
        </tr>
    <?php
    $i = 0;

    if( $instructions ) {

        foreach( $instructions as $instruction ) {
            if( !isset( $instruction['group'] ) ) {
                $instruction['group'] = '';
            }

            if( $instruction['group'] != $previous_group )
                { ?>
                    <tr class="instruction-group">
                        <td colspan="3" class="group">
                        	<div class="group-container">
                            <span class="header"><?php _e( 'Instructions Group', 'foodiepro' ); ?></span>
                            <span class="name"><input type="text" class="instruction-group-label" value="<?php echo esc_attr( $instruction['group'] ); ?>"/></span>
                        	</div>
                        	<div class="group center-column delete-button"><span class="instruction-group-delete">&nbsp;</span></div>
                        </td>
                    </tr>
        <?php
                    $previous_group = $instruction['group'];
                }

            if( !isset( $instruction['image'] ) ) {
                // $instruction['image'] = '';
                $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
                $has_image = false;
                //echo '<pre>' . 'instruction image variable : ' . $instruction['image'] . '</pre>';
            }

            else {
            // if( $instruction['image'] ) {
                $image = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
                if ( $image ) {
                    $image = $image[0];
                    $has_image = true;
                }
                else {
                    $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
                    $has_image = false;
                }
                //echo '<pre>' . "Has image = true !" . '</pre>';
            }
            // else {
            //     // $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
            //     $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
            //     $has_image = false;
            //     //echo '<pre>' . "Has image = false" . '</pre>';
            // }
        ?> 
            <!-- Existing Instructions Section -->

            <tr class="instruction">
                <td  class="sort-handle"><span><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16"></span></td>
                <td>
                    <div class="instruction-text">
                        <textarea name="recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"><?php echo $instruction['description']; ?></textarea>
                        <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?php echo esc_attr( $instruction['group'] ); ?>" />
                    </div>

                    <div class="instruction-buttons">
                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </div>
                </td>
                <td>
                    <div class="instruction-image">
                    <?php if ( isset( $wpurp_user_submission ) && ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) ) { ?>
                        <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" id="instruction_thumbnail_preview_<?php echo $i; ?>" />
                        <?php if( $has_image ) { ?>
                        <input type="hidden" value="<?php echo $instruction['image']; ?>" name="recipe_instructions[<?php echo $i; ?>][image]" /><br/>
                        <?php } ?>
                    <?php } else { ?>
                        <input name="recipe_instructions[<?php echo $i; ?>][image]" class="recipe_instructions_image" type="hidden" value="<?php echo $instruction['image']; ?>" />
                        <input class="recipe_instructions_add_image button<?php if($has_image) { echo ' wpurp-hide'; } ?>" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e( 'Add Image', 'wp-ultimate-recipe' ) ?>" />
                        <input class="recipe_instructions_remove_image button<?php if(!$has_image) { echo ' wpurp-hide'; } ?>" type="button" value="<?php _e( 'Remove Image', 'wp-ultimate-recipe' ) ?>" />
                        <br /><img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" />
                    <?php } ?>           
                    </div>
                </td>

                <td class="delete-button" colspan="1"><span class="instructions-delete">&nbsp;</span></td>
            </tr>
            <?php
            $i++;
        }

    }

    $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
    ?>
            <!-- New (stub) Instruction Section -->

            <tr class="instruction">
                <td class="sort-handle"><span><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16" /></span></td>
                <td>

                    <div class="instruction-text">
                        <textarea name="recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"></textarea>
                        <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="" />
                    </div>

                    <div class="instruction-buttons">
                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </div>
                </td>
                <td>                    
                    <div class="instruction-image">
                    <?php if ( isset( $wpurp_user_submission ) && ( !current_user_can( 'upload_files' ) || WPUltimateRecipe::option( 'user_submission_use_media_manager', '1' ) != '1' ) ) { ?>
                        <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" id="instruction_thumbnail_preview_<?php echo $i; ?>" />
                    <?php } else { ?>
                        <input name="recipe_instructions[<?php echo $i; ?>][image]" class="recipe_instructions_image" type="hidden" value="" />
                        <input class="recipe_instructions_add_image button" rel="<?php echo $recipe->ID(); ?>" type="button" value="<?php _e('Add Image', 'wp-ultimate-recipe' ) ?>" />
                        <input class="recipe_instructions_remove_image button wpurp-hide" type="button" value="<?php _e( 'Remove Image', 'wp-ultimate-recipe' ) ?>" />
                        <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" />
                    <?php } ?>
                     </div>
                </td>
                <td class="delete-button" colspan="1"><span class="instructions-delete">&nbsp;</span></td>
            </tr>
        </tbody>
    </table>

    <div class="button" id="ingredients-add-box">
        <a href="#" id="instructions-add"><?php _e( 'Add an instruction', 'foodiepro' ); ?></a>
    </div>
    <div class="button" id="ingredients-add-group-box">
        <a href="#" id="instructions-add-group"><?php _e( 'Add an instruction group', 'foodiepro' ); ?></a>
    </div>
    <div class="recipe-form-notes">
        <?php _e( "<strong>Use the TAB key</strong> while adding instructions, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
    </div>
</div>

<div class="recipe-notes-container-nojs">
    <h4><?php _e( 'Recipe notes', 'foodiepro' ) ?></h4>
		<textarea name="recipe_notes" id="recipe_notes" rows="6"><?php echo esc_html( $recipe->notes() ); ?></textarea>
</div>
<?php
$custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
if( $custom_fields_addon && ( !isset( $wpurp_user_submission ) || WPUltimateRecipe::option( 'recipe_fields_in_user_submission', '1' ) == '1' ) )
{
    $custom_fields = $custom_fields_addon->get_custom_fields();
    $custom_fields_in_user_submission = WPUltimateRecipe::option( 'recipe_fields_user_submission', array_keys( $custom_fields ) );

    if( count( $custom_fields ) > 0 ) {
?>
<div class="recipe-custom-fields-container">
    <h4><?php _e( 'Custom Fields', 'foodiepro' ) ?></h4>
    <table class="recipe-general-form">
        <?php foreach( $custom_fields as $key => $custom_field ) {
            if( isset( $wpurp_user_submission ) && !in_array( $key, $custom_fields_in_user_submission ) ) continue;
            ?>
            <tr>
                <td class="recipe-general-form-label"><label for="<?php echo $key; ?>"><?php echo $custom_field['name']; ?><?php if( in_array( $key, $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
                <td class="recipe-general-form-field">
                    <textarea name="<?php echo $key; ?>" id="<?php echo $key; ?>" rows="1"><?php echo $recipe->custom_field( $key ); ?></textarea>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php }
} ?>
