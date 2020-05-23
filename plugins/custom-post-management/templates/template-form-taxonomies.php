<?php

$taxonomies = CPM_Assets::get_taxonomies($post_type);
$dropdowns = array();

// Generate dropdown markup for each taxonomy (course, cuisine, difficulty, diet...)
// -----------------------------------------------------------
foreach ($taxonomies as $taxonomy => $options) {
    $dropdown_args['taxonomy'] = $taxonomy;
    $dropdown_args['hide_empty'] = false;
    $dropdown_args['class'] = "postform $taxonomy";
    $dropdown_args['class'] .= $options['multiselect'] ? ' multiselect' : '';
    $dropdown_args['show_option_none'] = $options['multiselect'] ? '' : $options['labels']['singular_name'];
    $dropdown_args['hierarchical'] = isset($options['hierarchical']) ? $options['hierarchical'] : false;
    $dropdown_args['exclude'] = isset($options['exclude']) ? $options['exclude'] : '';
    $dropdown_args['exclude_tree'] = isset($options['exclude_tree']) ? $options['exclude_tree'] : '';
    $dropdown_args['orderby'] = $options['orderby'];
    // $args['child_of'] = $options['child_of'];


    $args=compact( 'dropdown_args', 'options', 'post_type');
    $dropdowns[$taxonomy] = array(
        'label' => $options['labels']['singular_name'],
        // Generates dropdown with groups headers in case of hierarchical taxonomies
        'markup' => CPM_Assets::get_template_part('form-taxonomies','dropdown', $args),
    );
}

// Echoes all dropdowns that were previously built
// -----------------------------------------------------------

?>

<table>

    <?php
    foreach ($dropdowns as $taxonomy => $dropdown) {
        // Multiselect
        if ($taxonomies[$taxonomy]['multiselect']) {
            preg_match("/<select[^>]+>/i", $dropdown['markup'], $dropdown_match);
            if (isset($dropdown_match[0])) {
                $select_multiple = preg_replace("/name='([^']+)/i", "$0[]' data-placeholder='" . $dropdown['label'] . "' multiple='multiple", $dropdown_match[0]);
                $dropdown['markup'] = str_ireplace($dropdown_match[0], $select_multiple, $dropdown['markup']);
            }
        }
        // Mark existing post terms as Selected in the dropdown
        $terms = wp_get_post_terms($post_id, $taxonomy, array('fields' => 'ids'));
        foreach ($terms as $term_id) {
            $dropdown['markup'] = str_replace(' value="' . $term_id . '"', ' value="' . $term_id . '" selected="selected"', $dropdown['markup']);
        }
    ?>

        <tr class="post-general-form-<?= $taxonomy; ?>">
            <td class="post-general-form-label">
                <label for="<?= $taxonomy; ?>">
                    <?php
                    echo $taxonomies[$taxonomy]['labels']['singular_name'];
                    if (in_array($post_type . '_' . $taxonomy, $required_fields))
                        echo '<span class="required-field">*</span>';
                    ?>
                </label>
            </td>
            <td class="post-general-form-field">
                <?= $dropdown['markup']; ?>
            </td>
        </tr>

    <?php } //foreach ?>

</table>

<?php
