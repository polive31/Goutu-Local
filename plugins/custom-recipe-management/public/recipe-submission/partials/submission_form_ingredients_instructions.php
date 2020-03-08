<input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce('recipe'); ?>" />
<div class="post-container post-general-container">
    <h4 id="headline-general"><?php _e('General', 'crm'); ?></h4>
    <table class="recipe-form" id="recipe-general-form">
        <tr class="recipe-general-form-description">
            <td class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'crm'); ?><?php if (in_array('recipe_description', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <textarea class="recipe-description" name="recipe_content" id="recipe_description" rows="4" placeholder="<?php echo __('Provide general information about this recipe', 'crm'); ?>"><?php echo $recipe->output_description('form'); ?></textarea>
            </td>
        </tr>
        <tr class="recipe-general-form-servings">
            <td class="recipe-general-form-label"><label for="recipe_servings"><?php _e('Servings', 'crm'); ?><?php if (in_array('recipe_servings', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <input type="text" class="selectonfocus" name="recipe_servings" id="recipe_servings" value="<?php echo esc_attr($recipe->servings()); ?>" placeholder="<?php echo __("Quantity", 'crm'); ?>" />
                <input type="text" name="recipe_servings_type" id="recipe_servings_type" value="<?php echo esc_attr($recipe->servings_type()); ?>" placeholder="<?php echo __("Unit", 'crm'); ?>" />
                <span class="post-guidelines"> <?php _e('(e.g. 2 people, 3 loafs, ...)', 'crm'); ?></span>
            </td>
        </tr>
        <?php if (!empty($recipe->prep_time_text())) { ?>
            <tr class="recipe-general-form-prep-time">
                <td class="recipe-general-form-label"><label for="recipe_prep_time_old"><?php _e('Prep Time (legacy)', 'crm'); ?></label></td>
                <td class="recipe-general-form-field">
                    <span><?php echo esc_attr($recipe->prep_time()); ?></span>
                    <input type="text" name="recipe_prep_time_text" id="recipe_prep_time_text" value="<?php echo esc_attr($recipe->prep_time_text()); ?>" placeholder="<?php echo __("Unit", 'crm'); ?>" />
                    <span class="post-guidelines"> <?php _e('(e.g. 20 minutes, 1-2 hours, ...)', 'crm'); ?></span>
                </td>
            </tr>
        <?php } ?>
        <?php if (!empty($recipe->cook_time_text())) { ?>
            <tr class="recipe-general-form-cook-time">
                <td class="recipe-general-form-label"><label for="recipe_cook_time_old"><?php _e('Cook Time (legacy)', 'crm'); ?></label></td>
                <td class="recipe-general-form-field">
                    <span><?php echo esc_attr($recipe->cook_time()); ?></span>
                    <input type="text" name="recipe_cook_time_text" id="recipe_cook_time_text" value="<?php echo esc_attr($recipe->cook_time_text()); ?>" placeholder="<?php echo __("Unit", 'crm'); ?>" />
                </td>
            </tr>
        <?php } ?>
        <?php if (!empty($recipe->passive_time_text())) { ?>
            <tr class="recipe-general-form-passive-time">
                <td class="recipe-general-form-label"><label for="recipe_passive_time_old"><?php _e('Passive Time (legacy)', 'crm'); ?></label></td>
                <td class="recipe-general-form-field">
                    <span><?php echo esc_attr($recipe->passive_time()); ?></span>
                    <input type="text" name="recipe_passive_time_text" id="recipe_passive_time_text" value="<?php echo esc_attr($recipe->passive_time_text()); ?>" placeholder="<?php echo __("Unit", 'crm'); ?>" />
                </td>
            </tr>
        <?php } ?>
        <tr class="recipe-general-form-prep-time">
            <td class="recipe-general-form-label"><label for="recipe_prep_time"><?php _e('Prep Time', 'crm'); ?><?php if (in_array('recipe_prep_time', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_prep_time_days" id="recipe_prep_time_days" value="<?php echo esc_attr($recipe->get_days('prep')); ?>" min="0" max="99" />
                    <span class="post-guidelines"> <?php _e('days', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_prep_time_hours" id="recipe_prep_time_hours" value="<?php echo esc_attr($recipe->get_hours('prep')); ?>" min="0" max="24" />
                    <span class="post-guidelines"> <?php _e('hours', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_prep_time_minutes" id="recipe_prep_time_minutes" value="<?php echo esc_attr($recipe->get_minutes('prep')); ?>" min="0" max="60" />
                    <span class="post-guidelines"> <?php _e('minutes', 'crm'); ?></span>
                </span>
            </td>
        </tr>
        <tr class="recipe-general-form-cook-time">
            <td class="recipe-general-form-label"><label for="recipe_cook_time"><?php _e('Cook Time', 'crm'); ?><?php if (in_array('recipe_cook_time', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_cook_time_days" id="recipe_cook_time_days" value="<?php echo esc_attr($recipe->get_days('cook')); ?>" min="0" max="99" />
                    <span class="post-guidelines"> <?php _e('days', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_cook_time_hours" id="recipe_cook_time_hours" value="<?php echo esc_attr($recipe->get_hours('cook')); ?>" min="0" max="24" />
                    <span class="post-guidelines"> <?php _e('hours', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_cook_time_minutes" id="recipe_cook_time_minutes" value="<?php echo esc_attr($recipe->get_minutes('cook')); ?>" min="0" max="60" />
                    <span class="post-guidelines"> <?php _e('minutes', 'crm'); ?></span>
                </span>
            </td>
        </tr>
        <tr class="recipe-general-form-passive-time">
            <td class="recipe-general-form-label"><label for="recipe_passive_time"><?php _e('Passive Time', 'crm'); ?><?php if (in_array('recipe_passive_time', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_days" id="recipe_passive_time_days" value="<?php echo esc_attr($recipe->get_days('passive')); ?>" min="0" max="99" />
                    <span class="post-guidelines"> <?php _e('days', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_hours" id="recipe_passive_time_hours" value="<?php echo esc_attr($recipe->get_hours('passive')); ?>" min="0" max="24" />
                    <span class="post-guidelines"> <?php _e('hours', 'crm'); ?></span>
                </span>
                <span class="time-input">
                    <input type="number" class="selectonfocus" name="recipe_passive_time_minutes" id="recipe_passive_time_minutes" value="<?php echo esc_attr($recipe->get_minutes('passive')); ?>" min="0" max="60" />
                    <span class="post-guidelines"> <?php _e('minutes', 'crm'); ?></span>
                </span>
            </td>
        </tr>
        <?php if (!isset($wpurp_user_submission)) { ?>
            <tr>
                <td class="recipe-general-form-label">&nbsp;</td>
                <td class="recipe-general-form-field post-guidelines">
                    <?php _e("Don't forget that you can tag your recipe with <strong>Courses</strong> and <strong>Cuisines</strong> by using the boxes on the right. Use the <strong>featured image</strong> if you want a photo of the finished dish.", 'crm') ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<div class="post-container recipe-ingredients-container" data-units='<?php echo json_encode(CRM_Ingredient::get_units(false)); ?>' data-units-plural='<?php echo json_encode(CRM_Ingredient::get_units(true)); ?>'>
    <h4 id="headline-ingredients"><?php _e('Ingredients', 'crm'); ?><?php if (in_array('recipe_ingredients', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
    <?php $ingredients = $recipe->ingredients(); ?>
    <table class="recipe-form" id="recipe-ingredients">
        <tr class="ingredient-group ingredient-group-first">
            <td class="group" colspan="6">
                <div class="group-container">
                    <span class="header"><?php _e('Ingredients Group', 'crm'); ?></span>
                    <?php
                    $previous_group = '';
                    if (isset($ingredients[0]) && isset($ingredients[0]['group'])) {
                        $previous_group = $ingredients[0]['group'];
                    }
                    ?>
                    <span class="name"><input type="text" placeholder="<?php echo __('eg. For the dough', 'crm'); ?>" class="ingredient-group-label" value="<?php echo esc_attr($previous_group); ?>" /></span>
                </div>
            </td>
            <td class="group mobile-hidden" colspan="1">&nbsp;</td>
        </tr>
        <tbody>
            <tr class="ingredient-group-stub">
                <td class="group center-column delete-button">
                    <?= foodiepro_get_icon('delete', 'ingredient-group-delete','', __('Remove this ingredient group headline', 'crm')); ?>
                </td>
                <td colspan="3" class="group">
                    <div class="group-container">
                        <span class="header"><?php _e('Ingredients Group', 'crm'); ?></span>
                        <span class="name"><input type="text" class="ingredient-group-label" /></span>
                    </div>
                </td>
            </tr>
            <?php
            $i = 0;
            if ($ingredients) {
                foreach ($ingredients as $ingredient) {

                    if (isset($ingredient['ingredient_id'])) {
                        $term = get_term($ingredient['ingredient_id'], 'ingredient');
                        if ($term !== null && !is_wp_error($term)) {
                            $ingredient['ingredient'] = $term->name;
                        }
                    }

                    if (!isset($ingredient['group'])) {
                        $ingredient['group'] = '';
                    }

                    if ($ingredient['group'] != $previous_group) { ?>
                        <tr class="ingredient-group">
                            <td class="group center-column delete-button">
                                <?= foodiepro_get_icon('delete', 'ingredient-group-delete','', __('Remove this ingredient group headline', 'crm')); ?>
                            </td>
                            <td colspan="3" class="group">
                                <div class="group-container">
                                    <span class="header"><?php _e('Ingredients Group', 'crm'); ?></span>
                                    <span class="name"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr($ingredient['group']); ?>" /></span>
                                </div>
                            </td>
                        </tr>
                    <?php
                        $previous_group = $ingredient['group'];
                    }
                    ?>
                    <!-- Existing Ingredient -->
                    <!-- tabindex=-1 is important for the cell to be able to get focus and trigger jQuery events -->
                    <tr class="ingredient saved ui-sortable" id="ingredient_<?php echo $i; ?>" tabindex="-1">
                        <!-- Sort handle -->
                        <td class="sort-handle" title="<?php echo __('Move this ingredient', 'crm'); ?>"> <span class='icon-drag-updown'><i class="fas fa-exchange-alt fa-rotate-90"></i></span> </td>
                        <!-- Ingredient displayed like in published recipe -->
                        <td class="ingredient-preview" colspan="5">
                            <?php
                            echo CRM_Ingredient::display($ingredient);
                            ?>
                        </td>
                        <td class="ingredient-input qty">
                            <input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" value="<?php echo esc_attr($ingredient['amount']); ?>" placeholder="<?php _e('Quantity', 'crm'); ?>" /></td>

                        <td class="ingredient-input unit">
                            <input type="text" name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" value="<?php echo esc_attr($ingredient['unit']); ?>" placeholder="<?php _e('Unit', 'crm'); ?>" />
                        </td>

                        <td class="ingredient-input name">
                            <input type="text" name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" value="<?php echo esc_attr($ingredient['ingredient']); ?>" placeholder="<?php _e('Ingredient', 'crm'); ?>" /></td>
                        <td class="spinner"><?= foodiepro_get_icon('spinner-arrows', 'spinner-ingredients_' . $i, 'ajax-indicator'); ?></td>

                        <td class="ingredient-input notes">
                            <textarea rows="1" col="20" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e('Notes', 'crm'); ?>"><?php echo esc_attr($ingredient['notes']); ?></textarea>
                            <!--  <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" value="<?php echo esc_attr($ingredient['notes']); ?>" placeholder="<?php _e('Notes', 'crm'); ?>" /> -->
                            <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="<?php echo esc_attr($ingredient['group']); ?>" />
                        </td>
                        <td class="delete-button" colspan="1">
                            <?= foodiepro_get_icon('delete', 'ingredients-delete', '', __('Remove this ingredient', 'crm')); ?>
                        </td>
                    </tr>
            <?php
                    $i++;
                }
            }
            ?>
            <!-- New Ingredient (stub) -->
            <!-- tabindex=-1 is important for the cell to be able to get focus and trigger jQuery events -->
            <tr class="ingredient new ui-sortable" id="ingredient_<?php echo $i; ?>" tabindex="-1">
                <td class="sort-handle" title="<?php echo __('Move this ingredient', 'crm'); ?>"><span class='icon-drag-updown'><i class="fas fa-exchange-alt fa-rotate-90"></i></span></td>
                <td class="ingredient-preview" colspan="5">
                    &nbsp;
                </td>
                <!-- Quantity -->
                <td class='ingredient-input qty'>
                    <!-- <span class="mobile-display"><?php _e('Quantity', 'crm'); ?> </span>-->
                    <input type="text" name="recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" placeholder="<?php _e('Quantity', 'crm'); ?>" />
                </td>
                <!-- Unit -->
                <td class='ingredient-input unit'>
                    <!-- <span class="mobile-display"><?php _e('Unit', 'crm'); ?> </span>-->
                    <input type="text" name="recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" placeholder="<?php _e('Unit', 'crm'); ?>" />
                </td>
                <!-- Ingredient Name -->
                <td class='ingredient-input name'>
                    <!-- <span class="mobile-display"><?php _e('Ingredient', 'crm'); ?></span> -->
                    <input type="text" name="recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" placeholder="<?php _e('Ingredient', 'crm'); ?>" /></td>
                <td class="spinner"><?= foodiepro_get_icon('spinner-arrows', 'spinner-ingredients_' . $i, 'ajax-indicator'); ?></td>
                <!-- Ingredient Notes -->
                <td class='ingredient-input notes'>
                    <!-- <span class="mobile-display"><?php _e('Notes', 'crm'); ?></span> -->
                    <textarea rows="1" cols="20" type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" placeholder="<?php _e('Notes', 'crm'); ?>" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>"></textarea>
                    <!--  <input type="text" name="recipe_ingredients[<?php echo $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e('Notes', 'crm'); ?>"  /> -->
                    <input type="hidden" name="recipe_ingredients[<?php echo $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="" />
                </td>
                <!-- Delete button -->
                <td class="delete-button" colspan="1">
                    <?= foodiepro_get_icon('delete', 'ingredients-delete', '', __('Remove this ingredient', 'crm')); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="buttons-box">
        <div class="button" id="ingredients-add-box">
            <a href="#" id="ingredients-add" class="fa-before"><?php _e('Add an ingredient', 'crm'); ?></a>
        </div>
        <div class="button" id="ingredients-add-group-box">
            <a href="#" id="ingredients-add-group" class="fa-before"><?php _e('Add an ingredient group', 'crm'); ?></a>
        </div>
    </div>
    <p class="post-guidelines">
        <?php _e("<strong>Use the TAB key</strong> while adding ingredients, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'crm'); ?>
    </p>
</div>



<div class="post-container recipe-instructions-container">
    <h4 id="headline-instructions"><?php _e('Instructions', 'crm'); ?><?php if (in_array('recipe_instructions', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
    <?php $instructions = $recipe->instructions(); ?>
    <table class="recipe-form" id="recipe-instructions">
        <thead>
            <tr class="instruction-group instruction-group-first">
                <td colspan="4" class="group">
                    <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                    <!-- <span class="name instruction-groups-disabled"><?php echo __('Main Instructions', 'crm') . ' ' . __('(this label is not shown)', 'crm'); ?></span> -->
                    <?php
                    $previous_group = '';
                    if (isset($instructions[0]) && isset($instructions[0]['group'])) {
                        $previous_group = $instructions[0]['group'];
                    }
                    ?>
                    <span class="name"><input type="text" placeholder="<?php echo __('eg. Preparation of the dough', 'crm'); ?>" class="instruction-group-label" value="<?php echo esc_attr($previous_group); ?>" /></span>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr class="instruction-group-stub">
                <td class="group center-column delete-button">
                    <?= foodiepro_get_icon('delete', 'instruction-group-delete','', __('Remove this instruction group headline', 'crm')); ?>
                </td>
                <td colspan="3" class="group">
                    <div class="group-container">
                        <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                        <span class="name"><input type="text" class="instruction-group-label" /></span>
                    </div>
                </td>
            </tr>
            <?php
            $i = 0;

            if ($instructions) {

                foreach ($instructions as $instruction) {
                    if (!isset($instruction['group'])) {
                        $instruction['group'] = '';
                    }

                    if ($instruction['group'] != $previous_group) { ?>
                        <tr class="instruction-group">
                            <td class="group center-column delete-button">
                                <?= foodiepro_get_icon('delete', 'instruction-group-delete','', __('Remove this instruction group headline', 'crm')); ?>
                            </td>
                            <td colspan="3" class="group">
                                <div class="group-container">
                                    <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                                    <span class="name"><input type="text" class="instruction-group-label" value="<?php echo esc_attr($instruction['group']); ?>" /></span>
                                </div>
                            </td>
                        </tr>
                    <?php
                        $previous_group = $instruction['group'];
                    }


                    $has_image = false;
                    if (!isset($instruction['image'])) {
                        // $instruction['image'] = '';
                        // $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
                        //echo '<pre>' . 'instruction image variable : ' . $instruction['image'] . '</pre>';
                    } else {
                        // if( $instruction['image'] ) {
                        $image = wp_get_attachment_image_src($instruction['image'], 'thumbnail');
                        if ($image) {
                            $image = $image[0];
                            $has_image = true;
                        } else {
                            // $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
                        }
                        //echo '<pre>' . "Has image = true !" . '</pre>';
                    }

                    ?>
                    <!-- Existing Instructions Section -->

                    <tr class="instruction ui-sortable" id="recipe_instruction_<?php echo $i; ?>">
                        <td class="sort-handle" title="<?php echo __('Move this instruction', 'crm'); ?>"><span class='icon-drag-updown'><i class="fas fa-exchange-alt fa-rotate-90"></i></span></td>
                        <td class="instruction-content">
                            <div class="instruction-text">
                                <textarea class="recipe-instruction" name="recipe_instructions[<?php echo $i; ?>][description]" rows="2" id="ingredient_description_<?php echo $i; ?>" placeholder="<?php echo __('Enter the instructions for this recipe step', 'crm'); ?>"><?php echo $instruction['description']; ?></textarea>
                                <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?php echo esc_attr($instruction['group']); ?>" />
                            </div>
                            <div class="instruction-buttons">
                                <!-- This input stores the file to be uploaded for the given instruction step -->
                                <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                            </div>
                        </td>
                        <td class="instruction-thumbnail">
                            <div class="instruction-image thumbnail <?php if (!$has_image) { ?>nodisplay<?php }; ?>">
                                <img src="<?php echo $image; ?>" class="post_thumbnail" id="recipe_thumbnail_preview_<?php echo $i; ?>" />
                                <div class="recipe_remove_image_button" id="recipe_thumbnail_remove_<?php echo $i; ?>" title="<?php _e('Remove Image', 'crm') ?>" />
                            </div>
                            <!-- This input stores the attachment handler within the post, for meta save -->
                            <input type="hidden" class="instruction_thumbnail" value="<?= $has_image ? $instruction['image'] : ''; ?>" name="recipe_instructions[<?php echo $i; ?>][image]" /><br />
                        </td>

                        <td class="delete-button" colspan="1">
                            <?= foodiepro_get_icon('delete', 'instructions-delete','', __('Remove this instruction', 'crm')); ?>
                        </td>
                    </tr>
            <?php
                    $i++;
                }
            }

            // $image = WPUltimateRecipe::get()->coreUrl . '/img/image_placeholder.png';
            $image = '';
            ?>
            <!-- New (stub) Instruction Section -->

            <tr class="instruction ui-sortable" id="recipe_instruction_<?php echo $i; ?>">
                <td class="sort-handle" title="<?php echo __('Move this instruction', 'crm'); ?>"><span class='icon-drag-updown'><i class="fas fa-exchange-alt fa-rotate-90"></i></span></td>
                <td class="instruction-content">
                    <div class="instruction-text">
                        <textarea class="recipe-instruction" name="recipe_instructions[<?php echo $i; ?>][description]" rows="2" id="ingredient_description_<?php echo $i; ?>" placeholder="<?php echo __('Enter the instructions for this recipe step', 'crm'); ?>"></textarea>
                        <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="" />
                    </div>

                    <div class="instruction-buttons">
                        <input class="recipe_instructions_image button" type="file" id="recipe_thumbnail_input_<?php echo $i; ?>" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </div>
                </td>
                <td class="instruction-thumbnail">
                    <div class="instruction-image thumbnail nodisplay">
                        <img src="<?php echo $image; ?>" class="post_thumbnail" id="recipe_thumbnail_preview_<?php echo $i; ?>" />
                        <div class="recipe_remove_image_button" id="recipe_thumbnail_remove_<?php echo $i; ?>" title="<?php _e('Remove Image', 'crm') ?>" />
                    </div>
                    <input type="hidden" value="" name="recipe_instructions[<?php echo $i; ?>][image]" /><br />
                </td>
                <td class="delete-button" colspan="1">
                    <?= foodiepro_get_icon('delete', 'instructions-delete','',  __('Remove this instruction', 'crm')); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="buttons-box">
        <div class="button" id="ingredients-add-box">
            <a href="#" id="instructions-add" class="fa-before"><?php _e('Add an instruction', 'crm'); ?></a>
        </div>
        <div class="button" id="ingredients-add-group-box">
            <a href="#" id="instructions-add-group" class="fa-before"><?php _e('Add an instruction group', 'crm'); ?></a>
        </div>
    </div>
    <p class="post-guidelines">
        <?php _e("<strong>Use the TAB key</strong> while adding instructions, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'crm'); ?>
    </p>
</div>

<div class="post-container recipe-notes-container-nojs">
    <h4 id="headline-notes"><?php _e('Recipe Notes', 'crm') ?></h4>
    <p class="post-guidelines"><?php echo __('Provide any additional notes here, for instance side dishes, wine...', 'crm'); ?></p>
    <textarea name="recipe_notes" id="recipe_notes" rows="6" placeholder="<?php echo __('WHAT TO DRINK WITH THIS STRAWBERRY PIE : a sweet white wine', 'crm'); ?>"><?php echo esc_html($recipe->notes()); ?></textarea>
</div>
