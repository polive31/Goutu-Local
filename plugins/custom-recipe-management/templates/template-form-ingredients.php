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
                <?= foodiepro_get_icon('delete', 'ingredient-group-delete', '', __('Remove this ingredient group headline', 'crm')); ?>
            </td>
            <td colspan="3" class="group">
                <div class="group-container">
                    <span class="header"><?php _e('Ingredients Group', 'crm'); ?></span>
                    <span class="name"><input type="text" class="ingredient-group-label" /></span>
                </div>
            </td>
        </tr>
        <?php

        $ingredients[] = CRM_Recipe::get_ingredient_item();
        $i = 0;
        foreach ($ingredients as $ingredient) {

            if (isset($ingredient['ingredient_id'])) {
                $term = get_term($ingredient['ingredient_id'], 'ingredient');
                if ($term !== null && !is_wp_error($term)) {
                    $ingredient['ingredient'] = $term->name;
                }
            }
            $ingredient['group'] = isset($ingredient['group']) ? $ingredient['group'] : '';
            if ($ingredient['group'] != $previous_group) {
                $previous_group = $ingredient['group'];
                $newgroup = true;
            } else
                $newgroup = false;

            $saved = $i < count($ingredients) - 1;
            $args = compact('i', 'ingredient', 'group_input_class', 'group_input_type', 'newgroup', 'saved');
            CRM_Assets::echo_template_part('form-ingredients', 'ingredient', $args);
            $i++;
        }

        ?>

    </tbody>
</table>
<?php
