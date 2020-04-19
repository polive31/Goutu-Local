<?php
$out = '';
$previous_group = '';
$first_group = true;

$ingredients=empty($ingredients)?array():$ingredients;

foreach ($ingredients as $ingredient) {

    $term = get_term($ingredient['ingredient_id'], 'ingredient');
    if ($term !== null && !is_wp_error($term)) {
        $ingredient['ingredient'] = $term->name;
    }

    if ($ingredient['group'] != $previous_group || $first_group) {
        $out .= $first_group ? '' : '</ul>';
        $out .= '<ul class="wpurp-recipe-ingredients">';
        $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
        $previous_group = $ingredient['group'];
        $first_group = false;
    }

    $meta = '';

    $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';
    $out .= foodiepro_get_icon('checkbox', 'ingredient-checkbox');
    $ingredient['links'] = 'yes';

    $args=compact('ingredient','target','ratio');
    $out .= CRM_Assets::get_template_part('ingredients', 'ingredient', $args);

    $out .= '</li>';
}

echo $out;
