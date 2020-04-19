<?php


if ($newgroup) { ?>
    <tr class="ingredient-group">
        <td class="group center-column delete-button">
            <?= foodiepro_get_icon('delete', 'ingredient-group-delete', '', __('Remove this ingredient group headline', 'crm')); ?>
        </td>
        <td colspan="3" class="group">
            <div class="group-container">
                <span class="header"><?php _e('Ingredients Group', 'crm'); ?></span>
                <span class="name"><input type="text" class="ingredient-group-label" value="<?php echo esc_attr($ingredient['group']); ?>" /></span>
            </div>
        </td>
    </tr>
<?php

}

?>
<!-- Existing Ingredient -->
<!-- tabindex=-1 is important for the cell to be able to get focus and trigger jQuery events -->
<tr class="ingredient <?= ($saved)?'saved':'new'; ?> ui-sortable" id="ingredient_<?= $i; ?>" tabindex="-1">
    <!-- Sort handle -->
    <td class="sort-handle" title="<?php echo __('Move this ingredient', 'crm'); ?>">
        <input class="ingredients_group <?= $group_input_class; ?>" type="<?= $group_input_type; ?>" name="recipe_ingredients[<?= $i; ?>][group]" class="ingredients_group" id="ingredient_group_<?= $i; ?>" value="<?php echo esc_attr($ingredient['group']); ?>" />
        <span class='icon-drag-updown'><?= foodiepro_get_icon('drag-updown'); ?></span>
    </td>
    <!-- Ingredient displayed like in published recipe -->
    <td class="ingredient-preview" colspan="5">
        <?php
        // echo CRM_Ingredient::display($ingredient);
        $target='screen';
        $args = compact('ingredient', 'target');
        CRM_Assets::echo_template_part('ingredients', 'ingredient', $args);
        ?>
    </td>
    <td class="ingredient-input qty">
        <input type="text" name="recipe_ingredients[<?= $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?= $i; ?>" value="<?php echo esc_attr($ingredient['amount']); ?>" placeholder="<?php _e('Quantity', 'crm'); ?>" /></td>

    <td class="ingredient-input unit">
        <input type="text" name="recipe_ingredients[<?= $i; ?>][unit]" class="ingredients_unit" id="ingredients_unit_<?= $i; ?>" value="<?php echo esc_attr($ingredient['unit']); ?>" placeholder="<?php _e('Unit', 'crm'); ?>" />
    </td>

    <td class="ingredient-input name">
        <input type="text" name="recipe_ingredients[<?= $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?= $i; ?>" value="<?php echo esc_attr($ingredient['ingredient']); ?>" placeholder="<?php _e('Ingredient', 'crm'); ?>" /></td>
    <td class="spinner"><?= foodiepro_get_icon('spinner-arrows', 'spinner-ingredients_' . $i, 'ajax-indicator'); ?></td>

    <td class="ingredient-input notes">
        <textarea rows="1" col="20" name="recipe_ingredients[<?= $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?= $i; ?>" placeholder="<?php _e('Notes', 'crm'); ?>"><?php echo esc_attr($ingredient['notes']); ?></textarea>
        <!--  <input type="text" name="recipe_ingredients[<?= $i; ?>][notes]" class="ingredients_notes" id="ingredient_notes_<?= $i; ?>" value="<?php echo esc_attr($ingredient['notes']); ?>" placeholder="<?php _e('Notes', 'crm'); ?>" /> -->
    </td>
    <td class="delete-button" colspan="1">
        <?= foodiepro_get_icon('delete', 'ingredients-delete', '', __('Remove this ingredient', 'crm')); ?>
    </td>
</tr>

<?php
