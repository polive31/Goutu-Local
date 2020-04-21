<input type="hidden" name="recipe_meta_box_nonce" value="<?php echo wp_create_nonce('recipe'); ?>" />
<div class="post-container post-general-container">
    <h4 id="headline-general"><?php _e('General', 'crm'); ?></h4>
    <table class="recipe-form" id="recipe-general-form">
        <tr class="recipe-general-form-description">
            <td class="recipe-general-form-label"><label for="recipe_description"><?php _e('Description', 'crm'); ?><?php if (in_array('recipe_content', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
            <td class="recipe-general-form-field">
                <textarea class="recipe-description" name="post_content" id="post_content" rows="4" placeholder="<?php echo __('Provide general information about this recipe', 'crm'); ?>"><?= $recipe->legacy()?($recipe->description()):($recipe->post_content()); ?></textarea>
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

        <?php

        // LEGACY TIME DISPLAY
        if (!empty($recipe->prep_time_text())) { ?>
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


        <?php }

        $time = 'prep';
        $args = compact('time', 'required_fields', 'recipe');
        CRM_Assets::echo_template_part('form', 'time', $args);

        $time = 'cook';
        $args = compact('time', 'required_fields', 'recipe');
        CRM_Assets::echo_template_part('form', 'time', $args);

        $time = 'passive';
        $args = compact('time', 'required_fields', 'recipe');
        CRM_Assets::echo_template_part('form', 'time', $args);

        ?>

    </table>
</div>

<div class="post-container recipe-ingredients-container" data-units='<?php echo json_encode(CRM_Ingredient::get_units(false)); ?>' data-units-plural='<?php echo json_encode(CRM_Ingredient::get_units(true)); ?>'>
    <h4 id="headline-ingredients"><?php _e('Ingredients', 'crm'); ?><?php if (in_array('recipe_ingredients', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
    <?php $ingredients = $recipe->ingredients(); ?>
    <?php
    $args = compact('ingredients', 'group_input_class', 'group_input_type');
    CRM_Assets::echo_template_part('form', 'ingredients', $args);
    ?>
    <div class="buttons-box">
        <div class="button add-item-box" id="ingredients-add">
            <?= foodiepro_get_icon('plus-circle') . __('Add an ingredient', 'crm'); ?>
        </div>
        <div class="button add-item-box" id="ingredients-add-group">
            <?= foodiepro_get_icon('plus-circle') . __('Add an ingredient group', 'crm'); ?>
        </div>
    </div>
    <p class="post-guidelines">
        <?php _e("<strong>Use the TAB key</strong> while adding ingredients, it will automatically create new fields. <strong>Don't worry about empty lines</strong>, these will be ignored.", 'crm'); ?>
    </p>
</div>



<div class="post-container recipe-instructions-container">
    <h4 id="headline-instructions"><?php _e('Instructions', 'crm'); ?><?php if (in_array('recipe_instructions', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
    <?php $instructions = $recipe->instructions(); ?>
    <?php
    $args = compact('instructions');
    CRM_Assets::echo_template_part('form', 'instructions', $args);
    ?>
    <div class="buttons-box">
        <div class="button add-item-box" id="instructions-add">
            <?= foodiepro_get_icon('plus-circle') . __('Add an instruction', 'crm'); ?>
        </div>
        <div class="button add-item-box" id="instructions-add-group">
            <?= foodiepro_get_icon('plus-circle') . __('Add an instruction group', 'crm'); ?>
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

<?php
