<?php
$html='';
if (in_array('preview', $buttons)) {
    $url = get_preview_post_link($post_id);
    $html .= '<a href="' . $url . '" target="_blank" class="black-button">' . __('Preview', 'foodiepro') . '</a>';
}
if (in_array('draft', $buttons))
    $html .= '<input type="submit" value="' .  __('Draft', 'foodiepro') . '" id="draft" name="draft" />';
if (in_array('publish', $buttons))
    $html .= '<input type="submit" value="' . __('Publish', 'foodiepro') . '" id="publish" name="publish" />';

echo $html;
