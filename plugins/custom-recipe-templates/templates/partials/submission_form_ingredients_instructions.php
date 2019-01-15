<?php
// Recipe should never be null. Construct just allows easy access to WPURP_Recipe functions in IDE.
if( is_null( $recipe ) ) $recipe = new WPURP_Recipe(0);
if( !isset( $required_fields ) ) $required_fields = array();

?>

<input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce( 'recipe' ); ?>" />
<div class="recipe-container recipe-general-container">
    <h4 id="headline-general"><?php _e( 'General', 'foodiepro' ); ?></h4>
    <table class="recipe-form" id="recipe-general-form">
        <tr class="recipe-general-form-description">
            <td class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'foodiepro' ); ?><?php if( in_array( 'recipe_description', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
               <textarea class="recipe-description" name="content" id="recipe_description" rows="4" placeholder="<?php echo __('Provide general information about this recipe', 'foodiepro');?>"><?php echo $recipe->output_description('form'); ?></textarea>
            </td>
        </tr>
        <tr class="recipe-general-form-servings">
            <td class="recipe-general-form-label"><label for="recipe_servings"><?php _e( 'Servings', 'foodiepro' ); ?><?php if( in_array( 'recipe_servings', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" class="selectonfocus" name="recipe_servings" id="recipe_servings" value="<?php echo esc_attr( $recipe->servings() ); ?>" placeholder="<?php echo __("Quantity",'foodiepro');?>" />
                <input type="text" name="recipe_servings_type" id="recipe_servings_type" value="<?php echo esc_attr( $recipe->servings_type() ); ?>" placeholder="<?php echo __("Unit",'foodiepro');?>" />
                <span class="recipe-general-form-notes"> <?php _e( '(e.g. 2 people, 3 loafs, ...)', 'foodiepro' ); ?></span>
            </td>
        </tr>
        <?php if ( !empty( $recipe->prep_time_text() ) ) {?>
        <tr class="recipe-general-form-prep-time">
            <td class="recipe-general-form-label"><label for="recipe_prep_time_old"><?php _e( 'Prep Time (legacy)', 'foodiepro' ); ?></label></td>
            <td class="recipe-general-form-field">
                <span><?php echo esc_attr( $recipe->prep_time() ); ?></span>
                <input type="text" name="recipe_prep_time_text" id="recipe_prep_time_text" value="<?php echo esc_attr( $recipe->prep_time_text() ); ?>" placeholder="<?php echo __("Unit",'foodiepro');?>" />
                <span class="recipe-general-form-notes"> <?php _e( '(e.g. 20 minutes, 1-2 hours, ...)', 'foodiepro' ); ?></span>
            </td>
        </tr>
        <?php } ?>
        <?php if ( !empty( $recipe->cook_time_text() ) ) {?>
        <tr class="recipe-general-form-cook-time">
            <td class="recipe-general-form-label"><label for="recipe_cook_time_old"><?php _e( 'Cook Time (legacy)', 'foodiepro' ); ?></label></td>
            <td class="recipe-general-form-field">
                <span><?php echo esc_attr( $recipe->cook_time() ); ?></span>
                <input type="text" name="recipe_cook_time_text" id="recipe_cook_time_text" value="<?php echo esc_attr( $recipe->cook_time_text() ); ?>" placeholder="<?php echo __("Unit",'foodiepro');?>" />
            </td>
        </tr>
        <?php } ?>
        <?php if ( !empty( $recipe->passive_time_text() ) ) {?>
        <tr class="recipe-general-form-passive-time">
            <td class="recipe-general-form-label"><label for="recipe_passive_time_old"><?php _e( 'Passive Time (legacy)', 'foodiepro' ); ?></label></td>
            <td class="recipe-general-form-field">
                <span><?php echo esc_attr( $recipe->passive_time() ); ?></span>
                <input type="text" name="recipe_passive_time_text" id="recipe_passive_time_text" value="<?php echo esc_attr( $recipe->passive_time_text() ); ?>" placeholder="<?php echo __("Unit",'foodiepro');?>" />
            </td>
        </tr>
        <?php } ?>
        <tr class="recipe-general-form-prep-time">
            <td class="recipe-general-form-label"><label for="recipe_prep_time"><?php _e( 'Prep Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_prep_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_prep_time_days" id="recipe_prep_time_days" value="<?php echo esc_attr( $recipe->get_days( 'prep' ) ); ?>" min="0" max="99" />
                    <span class="recipe-general-form-notes"> <?php _e( 'days', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number"class="selectonfocus" name="recipe_prep_time_hours" id="recipe_prep_time_hours" value="<?php echo esc_attr( $recipe->get_hours( 'prep' ) ); ?>" min="0" max="24" />
                    <span class="recipe-general-form-notes"> <?php _e( 'hours', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_prep_time_minutes" id="recipe_prep_time_minutes" value="<?php echo esc_attr( $recipe->get_minutes( 'prep' ) ); ?>" min="0" max="60"  />
                    <span class="recipe-general-form-notes"> <?php _e( 'minutes', 'foodiepro' ); ?></span>
                </span>
            </td>
        </tr>        
         <tr class="recipe-general-form-cook-time">
            <td class="recipe-general-form-label"><label for="recipe_cook_time"><?php _e( 'Cook Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_cook_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number"class="selectonfocus" name="recipe_cook_time_days" id="recipe_cook_time_days" value="<?php echo esc_attr( $recipe->get_days( 'cook' ) ); ?>" min="0" max="99" />
                    <span class="recipe-general-form-notes"> <?php _e( 'days', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_cook_time_hours" id="recipe_cook_time_hours" value="<?php echo esc_attr( $recipe->get_hours( 'cook' ) ); ?>" min="0" max="24" />
                    <span class="recipe-general-form-notes"> <?php _e( 'hours', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_cook_time_minutes" id="recipe_cook_time_minutes" value="<?php echo esc_attr( $recipe->get_minutes( 'cook' ) ); ?>" min="0" max="60"  />
                    <span class="recipe-general-form-notes"> <?php _e( 'minutes', 'foodiepro' ); ?></span>
                </span>
            </td>
        </tr>  
         <tr class="recipe-general-form-passive-time">
            <td class="recipe-general-form-label"><label for="recipe_passive_time"><?php _e( 'Passive Time', 'foodiepro' ); ?><?php if( in_array( 'recipe_passive_time', $required_fields ) ) echo '<span class="wpurp-required">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_days" id="recipe_passive_time_days" value="<?php echo esc_attr( $recipe->get_days( 'passive' ) ); ?>" min="0" max="99" />
                    <span class="recipe-general-form-notes"> <?php _e( 'days', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_hours" id="recipe_passive_time_hours" value="<?php echo esc_attr( $recipe->get_hours( 'passive' ) ); ?>" min="0" max="24" />
                    <span class="recipe-general-form-notes"> <?php _e( 'hours', 'foodiepro' ); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_minutes" id="recipe_passive_time_minutes" value="<?php echo esc_attr( $recipe->get_minutes( 'passive' ) ); ?>" min="0" max="60"  />
                    <span class="recipe-general-form-notes"> <?php _e( 'minutes', 'foodiepro' ); ?></span>
                </span>
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

<div class="recipe-container recipe-ingredients-container" data-units='<?php echo json_encode(Custom_WPURP_Ingredient::get_units(false));?>'  data-units-plural='<?php echo json_encode(Custom_WPURP_Ingredient::get_units(true));?>'>
    <h4 id="headline-ingredients"><?php _e( 'Ingredients', 'foodiepro' ); ?></h4>
    <?php $ingredients = $recipe->ingredients(); ?>
    <table class="recipe-form" id="recipe-ingredients">
        <tr class="ingredient-group ingredient-group-first">
                <td class="group" colspan="6">
                    <div class="group-container">
                        <span class="header"><?php _e( 'Ingredients Group', 'foodiepro' ); ?></span>
                        <?php
                        $previous_group = '';
                        if( isset( $ingredients[0] ) && isset( $ingredients[0]['group'] ) ) {
                            $previous_group = $ingredients[0]['group'];
                        }
                        ?>
                        <span class="name"><input type="text" placeholder="<?php echo __('eg. For the dough', 'foodiepro');?>" class="ingredient-group-label" value="<?php echo esc_attr( $previous_group ); ?>" /></span>
                    </div>
                  </td>
                  <td class="group mobile-hidden" colspan="1">&nbsp;</td>
        </tr>
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
        if( $ingredients ) {
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
                <!-- tabindex=-1 is important for the cell to be able to get focus and trigger jQuery events -->
                <tr class="ingredient saved ui-sortable" id="ingredient_<?php echo $i;?>" tabindex="-1">
                    <!-- Sort handle -->
                    <td class="sort-handle" title="<?php echo __('Move this ingredient', 'foodiepro');?>"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16" > </td>
                    <!-- Ingredient displayed like in published recipe -->
                    <td class="ingredient-preview" colspan="5">
                    <?php
                        echo Custom_WPURP_Ingredient::display($ingredient);
                    ?>
                    </td>
                    <td class="ingredient-input qty">
                        <input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['amount'] ); ?>"  placeholder="<?php _e( 'Quantity', 'foodiepro' ); ?>"  /></td>
                    
                    <td class="ingredient-input unit">
                        <input type="text" name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['unit'] ); ?>" placeholder="<?php _e( 'Unit', 'foodiepro' );?>" />
                    </td>
                    
                    <td class="ingredient-input name">
                        <input type="text"   name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['ingredient'] ); ?>" placeholder="<?php _e( 'Ingredient', 'foodiepro' ); ?>"  /></td><td class="spinner"><i id="spinner-ingredients_<?php echo $i; ?>" class="ajax-indicator fa fa-refresh fa-spin"></i></td>
                    
                    <td class="ingredient-input notes">
                        <textarea rows="1" col="20" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e( 'Notes', 'foodiepro' ); ?>"><?php echo esc_attr( $ingredient['notes'] ); ?></textarea> 
                       <!--  <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['notes'] ); ?>" placeholder="<?php _e( 'Notes', 'foodiepro' ); ?>" /> -->
                        <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="<?php echo esc_attr( $ingredient['group'] ); ?>" />
                    </td>
                    <td class="delete-button" colspan="1"><span class="ingredients-delete" title="<?php echo __('Remove this ingredient', 'foodiepro');?>" >&nbsp;</span>
                    </td>
                </tr>
                <?php
                $i++;
            }

        }
        ?>
        <!-- New Ingredient (stub) -->
        <!-- tabindex=-1 is important for the cell to be able to get focus and trigger jQuery events -->
        <tr class="ingredient new ui-sortable" id="ingredient_<?php echo $i;?>" tabindex="-1">
            <td class="sort-handle" title="<?php echo __('Move this ingredient', 'foodiepro');?>"><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16" ></td>
            <td class="ingredient-preview" colspan="5">
                &nbsp;
            </td>
            <!-- Quantity -->
            <td class='ingredient-input qty'><!-- <span class="mobile-display"><?php _e( 'Quantity', 'foodiepro' ); ?> </span>-->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" placeholder="<?php _e( 'Quantity', 'foodiepro' ); ?>" />
            </td>
            <!-- Unit -->
            <td class='ingredient-input unit'><!-- <span class="mobile-display"><?php _e( 'Unit', 'foodiepro' ); ?> </span>-->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" placeholder="<?php _e( 'Unit', 'foodiepro' ); ?>" />
            </td>
            <!-- Ingredient Name -->
            <td class='ingredient-input name'><!-- <span class="mobile-display"><?php _e( 'Ingredient', 'foodiepro' ); ?></span> -->
                <input type="text" name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" placeholder="<?php _e( 'Ingredient', 'foodiepro' ); ?>" /></td><td class="spinner"><i id="spinner-ingredients_<?php echo $i; ?>" class="ajax-indicator fa fa-refresh fa-spin"></i>
            </td>
            <!-- Ingredient Notes -->
            <td class='ingredient-input notes'>
                <!-- <span class="mobile-display"><?php _e( 'Notes', 'foodiepro' ); ?></span> -->
                <textarea rows="1" cols="20" type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" placeholder="<?php _e( 'Notes', 'foodiepro' );?>" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>"></textarea>
               <!--  <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e( 'Notes', 'foodiepro' ); ?>"  /> -->
                <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="" />
            </td>
            <!-- Delete button -->
            <td class="delete-button" colspan="1"><span class="ingredients-delete" title="<?php echo __('Remove this ingredient', 'foodiepro');?>" >&nbsp;</span></td>
        </tr>
        </tbody>
    </table>
    <div class="buttons-box">
        <div class="button" id="ingredients-add-box">
            <a href="#" id="ingredients-add"><?php _e( 'Add an ingredient', 'foodiepro' ); ?></a>
        </div>
        <div  class="button" id="ingredients-add-group-box">
            <a href="#" id="ingredients-add-group"><?php _e( 'Add an ingredient group', 'foodiepro' ); ?></a>
        </div>
    </div>
    <div class="recipe-form-notes">
        <?php _e( "<strong>Use the TAB key</strong> while adding ingredients, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
    </div>
</div>



<div class="recipe-container recipe-instructions-container">
    <h4 id="headline-instructions"><?php _e( 'Instructions', 'foodiepro' ); ?></h4>
    <?php $instructions = $recipe->instructions(); ?>
    <table class="recipe-form" id="recipe-instructions">
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

                
            $has_image = false;
            if( !isset( $instruction['image'] ) ) {
                // $instruction['image'] = '';
                $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
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
                    }
                    //echo '<pre>' . "Has image = true !" . '</pre>';
                }
                
                ?> 
            <!-- Existing Instructions Section -->
            
            <tr class="instruction ui-sortable" id="recipe_instruction_<?php echo $i; ?>">
                <td class="sort-handle" title="<?php echo __('Move this instruction', 'foodiepro');?>"><span><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16" ></span></td>
                <td class="instruction-content">
                    <div class="instruction-text">
                        <textarea class="recipe-instruction" name="recipe_instructions[<?php echo $i; ?>][description]" rows="2" id="ingredient_description_<?php echo $i; ?>"  placeholder="<?php echo __('Enter the instructions for this recipe step', 'foodiepro');?>" ><?php echo $instruction['description']; ?></textarea>
                        <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?php echo esc_attr( $instruction['group'] ); ?>" />
                    </div>
                    <div class="instruction-buttons">
                        <!-- This input stores the file to be uploaded for the given instruction step -->
                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </div>
                </td>
                <td>
                    <div class="instruction-image thumbnail <?php if( !$has_image ) { ?>nodisplay<?php };?>">
                        <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" id="recipe_thumbnail_preview_<?php echo $i; ?>" />
                        <div class="recipe_remove_image_button" id="recipe_thumbnail_remove_<?php echo $i; ?>" title="<?php _e( 'Remove Image', 'foodiepro' ) ?>" /></div>
                        <!-- This input stores the attachment handler within the post, for meta save -->
                        <input type="hidden" value="<?php echo $instruction['image']; ?>" name="recipe_instructions[<?php echo $i; ?>][image]" /><br/>
                      
                    </div>
                </td>

                <td class="delete-button" colspan="1"><span class="instructions-delete" title="<?php echo __('Remove this instruction', 'foodiepro');?>">&nbsp;</span></td>
            </tr>
            <?php
            $i++;
        }

    }

    $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
    ?>
            <!-- New (stub) Instruction Section -->

            <tr class="instruction ui-sortable" id="recipe_instruction_<?php echo $i; ?>">
                <td class="sort-handle" title="<?php echo __('Move this instruction', 'foodiepro');?>"><span><img src="<?php echo WPUltimateRecipe::get()->coreUrl; ?>/img/arrows.png" width="18" height="16" /></span></td>
                <td class="instruction-content">
                    <div class="instruction-text">
                        <textarea class="recipe-instruction" name="recipe_instructions[<?php echo $i; ?>][description]" rows="2" id="ingredient_description_<?php echo $i; ?>" placeholder="<?php echo __('Enter the instructions for this recipe step', 'foodiepro');?>" ></textarea>
                        <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="" />
                    </div>

                    <div class="instruction-buttons">
                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </div>
                </td>
                <td>                    
                    <div class="instruction-image thumbnail nodisplay">
                        <img src="<?php echo $image; ?>" class="recipe_instructions_thumbnail" id="recipe_thumbnail_preview_<?php echo $i; ?>" />
                        <div class="recipe_remove_image_button" id="recipe_thumbnail_remove_<?php echo $i; ?>" title="<?php _e( 'Remove Image', 'foodiepro' ) ?>" /></div>
                        <input type="hidden" value="" name="recipe_instructions[<?php echo $i; ?>][image]" /><br/>
                    </div>
                </td>
                <td class="delete-button" colspan="1"><span class="instructions-delete" title="<?php echo __('Remove this instruction', 'foodiepro');?>">&nbsp;</span></td>
            </tr>
        </tbody>
    </table>

    <div class="buttons-box">
        <div class="button" id="ingredients-add-box">
            <a href="#" id="instructions-add"><?php _e( 'Add an instruction', 'foodiepro' ); ?></a>
        </div>
        <div class="button" id="ingredients-add-group-box">
            <a href="#" id="instructions-add-group"><?php _e( 'Add an instruction group', 'foodiepro' ); ?></a>
        </div>
    </div>
    <div class="recipe-form-notes">
        <?php _e( "<strong>Use the TAB key</strong> while adding instructions, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'foodiepro' ); ?>
    </div>
</div>

<div class="recipe-container recipe-notes-container-nojs">
    <h4 id="headline-notes"><?php _e( 'Recipe notes', 'foodiepro' ) ?></h4>
		<textarea name="recipe_notes" id="recipe_notes" rows="6" placeholder="<?php echo __('Provide any additional notes here... E.g. side dishes, wine to drink with...','foodiepro');?>"><?php echo esc_html( $recipe->notes() ); ?></textarea>
</div>
<!-- <?php
$custom_fields_addon = WPUltimateRecipe::addon( 'custom-fields' );
if( $custom_fields_addon && ( !isset( $wpurp_user_submission ) || WPUltimateRecipe::option( 'recipe_fields_in_user_submission', '1' ) == '1' ) )
{
    $custom_fields = $custom_fields_addon->get_custom_fields();
    $custom_fields_in_user_submission = WPUltimateRecipe::option( 'recipe_fields_user_submission', array_keys( $custom_fields ) );

    if( count( $custom_fields ) > 0 ) {
?>
<div class="recipe-container recipe-custom-fields-container">
    <h4 id="headline-custom"><?php _e( 'Custom Fields', 'foodiepro' ) ?></h4>
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
</div> -->
<?php }
} ?>
