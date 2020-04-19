<?php
    $post_id=$post->ID;
    $args=compact('post_id','state','post_type');
    CPM_Assets::echo_template_part('form', 'intro', $args);
?>
<div id="custom_post_submission_form" class="postbox">
    <form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">

        <input type="hidden" id="submit_post_id" name="submit_post_id" value="<?= $post->ID; ?>" />

        <?php // IMPORTANT : DO NOT USE post_type for input name as it is a reserved name and will cause an error 404 on submit
        ?>
        <input type="hidden" id="submit_post_type" name="submit_post_type" value="<?= $post_type; ?>" />
        <input type="hidden" id="submit_post_status" name="submit_post_status" value="<?= get_post_status($post->ID) ?>" />

        <span style="opacity:0" id="submission_form_meta_info"></span>

        <div class="post-container post-title-container">
            <p>
                <h4 id="headline-title"><?= CPM_Assets::get_label($post_type, 'title'); ?><?php if (in_array($post_type . '_title', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
                <input type="text" id="<?= $post_type; ?>_title" value="<?= isset($_POST['post_title']) ? $_POST['post_title'] : esc_html($post->post_title);  ?>" size="20" name="post_title" />
            </p>
        </div>
        <div class="post-container post-image-container">
            <?php
            // $image_url = CPM_Assets::get_post_image_url( $post );
            $image_url = get_the_post_thumbnail_url($post->ID, 'thumbnail');;
            ?>
            <h4 id="headline-image"><?php _e('Featured image', 'foodiepro'); ?><?php if (in_array($post_type . '_image_attachment', $required_fields)) echo '<span class="required-field">*</span>'; ?></h4>
            <p class="post-guidelines"><?= CPM_Assets::get_label($post_type, 'featured_image'); ?></p>
            <div class="post-image thumbnail <?php if (!$image_url) { ?>nopic<?php }; ?>">
                <span class="spinner"><?= foodiepro_get_icon('spinner-dots'); ?></span>
                <img src="<?= $image_url; ?>" class="post_thumbnail skip-lazy" id="post_thumbnail_preview_featured" />
                <div class="post_remove_image_button" id="post_thumbnail_remove" title="<?php _e('Remove Image', 'foodiepro') ?>" />
            </div>
        </div>
        <input type="hidden" class="" id="<?= $post_type; ?>_image_attachment" value="<?= get_post_thumbnail_id($post->ID); ?>" name="<?= $post_type; ?>_image_attachment">
        <div class="post-image input">
            <input class="post_image_thumbnail button" type="file" id="post_thumbnail_input_featured" value="" size="50" name="<?= $post_type; ?>_thumbnail_featured" />
        </div>
</div>

<div class="post-container post-tags-container">
    <h4 id="headline-tags"><?php _e('Post Tags', 'foodiepro') ?></h4>
    <div class="taxonomy-select-spinner"><?= foodiepro_get_icon('spinner-dots'); ?></div>
    <div class="taxonomy-select-boxes nodisplay">
        <?php
        $post_id = $post->ID;
        $args = compact('post_id', 'required_fields', 'post_type');
        CPM_Assets::echo_template_part('form', 'taxonomies', $args);
        ?>
    </div>
</div>

<?php
/* Possibility of adding a specific post type section there */
echo apply_filters('cpm_' . $post_type . '_section', $post, $required_fields);
?>

<div id="post-form-buttons">
    <?php
    /* Add submission buttons */
    $buttons = array('preview', 'draft', 'publish');
    $args = compact('buttons', 'post_id');
    $buttons_html = CPM_Assets::get_template_part('form', 'buttons', $args);
    echo apply_filters('cpm_' . $post_type . '_submission_buttons', $buttons_html);
    ?>
    <input type="hidden" name="action" value="post" />
    <?= wp_nonce_field($post_type . '_submit', 'submit' . $post_type, true, false); ?>
</div>

</form>
</div>

<?php
