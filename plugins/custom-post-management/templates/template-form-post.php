<div class="post-container post-general-container">
    <h4 id="headline-content"><?php _e('Post content', 'foodiepro'); ?><?php if (in_array('post_content', $required_fields)) echo '<span class="required-field   ">*</span>'; ?></h4>
    <table class="post-form" id="post-general-form">
        <tr class="post-general-form-description">
            <!-- <td class="post-general-form-label"><label for="post_description"><?php _e('Description', 'foodiepro'); ?></label></td> -->
            <td class="post-general-form-field">
                <textarea class="post-content" name="post_content" id="post_content" rows="10" placeholder="<?= __('Write your post here.', 'foodiepro'); ?>">
                <?= wp_kses_post($post->post_content); ?>
            </textarea>
            </td>
        </tr>
    </table>
</div>
