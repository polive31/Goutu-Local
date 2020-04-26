<?php

$ratio=isset($ratio)?$ratio:1;
$parts = CRM_Ingredient::get_ingredient_parts($ingredient, $ratio);

// MARKUP
$out = '<span class="recipe-ingredient-quantity-unit">';
    $out .= '<span class="recipe-ingredient-quantity" data-normalized="' . $parts['amount_normalized'] . '" data-fraction="' . $parts['fraction'] . '" data-original="' . $ingredient['amount'] . '">';
        if ( $target == 'print' )
            $out .= $parts['amount']?$parts['amount']:'';// Takes actual servings into account ($ratio)
        else
            $out .= $ingredient['amount'];
        $out .= '</span>';
    $out .= '<span class="recipe-ingredient-unit" data-original="' . $parts['unit_singular'] . '" data-plural="' . $parts['unit_plural'] . '">';
        $out .= ' ' . $parts['unit'];
        $out .= '</span>';
    $out .= '</span>';
$out .= ' ' . $parts['of'];
$plural_data = $parts['plural'] ? ' data-singular="' . esc_attr($ingredient['ingredient']) . '" data-plural="' . esc_attr($parts['plural']) . '"' : '';
$out .= '<span class="recipe-ingredient-name"' . $plural_data . '>';

$closing_tag = '';
$hide_link = WPURP_Taxonomy_MetaData::get('ingredient', $parts['tax'], 'hide_link') == '1';
if ( !empty($parts['tax']) && empty($hide_link) && $target != 'print') {
    $out .= '<a href="' . get_term_link($parts['tax'], 'ingredient') . '">';
    $closing_tag = '</a>';
}

$out .= '<span id="ingredient_name_root">';
    $out .= $parts['ingredient'];
    $out .= '</span>';
$out .= $closing_tag;
$out .= '</span>';

// INGREDIENT "NOTES"
if (!empty($ingredient['notes'])) {
    $out .= ' <span class="recipe-ingredient-notes">' . do_shortcode($ingredient['notes']) . '</span>';
}

echo $out;
