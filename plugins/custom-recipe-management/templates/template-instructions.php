<?php

$out = '';
$previous_group = '';

$out .= '<ul class="wpurp-recipe-instruction-container">';
$first_group = true;

$instructions = empty($instructions)?array():$instructions;

for ($i = 0; $i < count($instructions); $i++) {

    $instruction = $instructions[$i];
    $first_inst = false;

    if ($instruction['group'] != $previous_group) { /* Entering new instruction group */
        $first_inst = true;
        $out .= $first_group ? '' : '</ul>';
        $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
        $out .= '<ul class="wpurp-recipe-instructions">';
        $previous_group = $instruction['group'];
        $first_group = false;
    }

    $style = $first_inst ? ' li-first' : '';
    $style .= !isset($instructions[$i + 1]) || $instruction['group'] != $instructions[$i + 1]['group'] ? ' li-last' : '';

    $out .= '<li class="wpurp-recipe-instruction ' . $style . '" id="' . CRM_Assets::RECIPE_STEP_ID_ROOT . $i . '">';

    if ($target != 'print') {
        $bullet = CRM_Assets::get_template_part('instructions', 'bullet', array('id'=>$i));
    }
    else
        $bullet='';
    $out .= '<span class="recipe-instruction-text">' . $bullet . $instruction['description'] . '</span>';

    if (!empty($instruction['image']) && ($target == "screen")) {
        $thumb = wp_get_attachment_image_src($instruction['image'], 'thumbnail');
        $thumb_url = $thumb['0'];

        $full_img = wp_get_attachment_image_src($instruction['image'], 'full');
        $full_img_url = $full_img['0'];

        $title_tag = esc_attr(get_the_title($instruction['image']));
        $alt_tag = esc_attr(get_post_meta($instruction['image'], '_wp_attachment_image_alt', true));

        $out .= '<div class="instruction-step-image">';
        $out .= '<a href="' . $full_img_url . '" id="lightbox" title="' . $title_tag . '">';
        $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
        $out .= '</a></div>';
    }

    $out .= '</li>';
}
$out .= '</ul>';

echo do_shortcode($out);

?>
