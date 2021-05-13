<?php


if ($newgroup) { ?>
    <tr class="instruction-group">
        <td class="group center-column delete-button">
            <?= foodiepro_get_icon('delete', 'instruction-group-delete', '', __('Remove this instruction group headline', 'crm')); ?>
        </td>
        <td colspan="3" class="group">
            <div class="group-container">
                <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                <span class="name"><input type="text" class="instruction-group-label" value="<?php echo esc_attr($instruction['group']); ?>" /></span>
            </div>
        </td>
    </tr>
<?php
}

$has_image = false;
$image = '';
if (!empty($instruction['image'])) {
    $image = wp_get_attachment_image_src($instruction['image'], 'thumbnail');
    if ($image) {
        $image = $image[0];
        $has_image = true;
    }
}

if (!isset($instruction['group'])) {
    $instruction['group'] = '';
}

?>

<tr class="instruction ui-sortable <?php if (!($instruction['visible'])) { ?>nodisplay<?php }; ?>" id="recipe_instruction_<?php echo $i; ?>">

    <td class="sort-handle ui-sortable-handle" title="<?php echo __('Move this instruction', 'crm'); ?>">
        <span class='icon-drag-updown'>
            <?= foodiepro_get_icon('drag-updown'); ?>
        </span>
    </td>
    <td class="instruction-content">
        <div class="instruction-text">
            <textarea class="recipe-instruction" name="recipe_instructions[<?= $i; ?>][description]" rows="2" id="recipe_instruction_<?= $i; ?>" placeholder="<?= __('Enter the instructions for this recipe step', 'crm'); ?>"><?= $instruction['description']; ?></textarea>
            <input type="hidden" name="recipe_instructions[<?php echo $i; ?>][group]" class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?= esc_attr($instruction['group']); ?>" />
        </div>
        <div class="instruction-buttons">
            <!-- This input stores the file to be uploaded for the given instruction step -->
            <div class="instruction-button">
                <label>Image</label>
                <input class="post_image_thumbnail" type="file" id="post_thumbnail_input_<?= $i; ?>" value="" size="50" name="<?= CRM_Assets::RECIPE_THUMB_INPUT . '_' . $i; ?>" />
            </div>
            <div class="instruction-button">
                <label>Vidéo</label>

                <input class="recipe-instruction-video" type="text" id="recipe_instruction_video_<?= $i; ?>" placeholder="Lien vers la vidéo Youtube" size="50" name="recipe_instructions[<?= $i; ?>][video]" value="<?= $instruction['video']; ?>" />
            </div>
        </div>
    </td>
    <td class="instruction-thumbnail">
        <div class="instruction-image thumbnail <?php if (!$has_image) { ?>nopic<?php }; ?>">
            <span class="spinner"><?= foodiepro_get_icon('spinner-dots'); ?></span>
            <img src="<?php echo $image; ?>" class="post_thumbnail skip-lazy" id="<?= 'post_thumbnail_preview_' . $i; ?>" />
            <div class="post_remove_image_button" id="post_thumbnail_remove_<?php echo $i; ?>" title="<?php _e('Remove Image', 'crm') ?>" />
        </div>
        <!-- This input stores the attachment handler within the post, for meta save -->
        <input type="hidden" class="instruction_thumbnail" id="recipe_image_attachment_<?php echo $i; ?>" value="<?= $has_image ? $instruction['image'] : ''; ?>" name="recipe_instructions[<?php echo $i; ?>][image]" /><br />
    </td>

    <td class="delete-button" colspan="1">
        <?= foodiepro_get_icon('delete', 'instructions-delete', '', __('Remove this instruction', 'crm')); ?>
    </td>


</tr>

<?php
