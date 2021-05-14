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
<div class="post-container post-general-container">
    <h4 id="headline-content"><?php _e('Post video', 'foodiepro'); ?><?php if (in_array('post_video', $required_fields)) echo '<span class="required-field   ">*</span>'; ?></h4>
    <input class="post-video" type="text" id="post_video" placeholder="<?= __('Link to the online video (Youtube, ...). Must begin with https://...', 'crm'); ?>" size="50" name="post_video" value="<?= get_post_meta($post->ID, 'post_video', true); ?>" />
</div>
