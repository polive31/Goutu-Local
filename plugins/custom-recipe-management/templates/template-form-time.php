<tr class="recipe-general-form-<?= $time; ?>-time">
    <td class="recipe-general-form-label"><label for="recipe_<?= $time; ?>_time"><?= CPM_Assets::get_label('recipe', $time . '_time'); ?><?php if (in_array('recipe_' . $time . '_time', $required_fields)) echo '<span class="required-field">*</span>'; ?></label></td>
    <td class="recipe-general-form-field">
        <span class="time-input">
            <input type="number" class="selectonfocus" name="recipe_<?= $time; ?>_time_days" id="recipe_<?= $time; ?>_time_days" value="<?= esc_attr($recipe->days($time)); ?>" min="0" max="99" />
            <span class="post-guidelines"> <?php _e('days', 'crm'); ?></span>
        </span>
        <span class="time-input">
            <input type="number" class="selectonfocus" name="recipe_<?= $time; ?>_time_hours" id="recipe_<?= $time; ?>_time_hours" value="<?= esc_attr($recipe->hours($time)); ?>" min="0" max="24" />
            <span class="post-guidelines"> <?php _e('hours', 'crm'); ?></span>
        </span>
        <span class="time-input">
            <input type="number" class="selectonfocus" name="recipe_<?= $time; ?>_time_minutes" id="recipe_<?= $time; ?>_time_minutes" value="<?= esc_attr($recipe->minutes($time)); ?>" min="0" max="60" />
            <span class="post-guidelines"> <?php _e('minutes', 'crm'); ?></span>
        </span>
    </td>
</tr>

<?php
