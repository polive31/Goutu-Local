<?php

$select_name = $post_type . '_' . $dropdown_args['taxonomy'];

if ($dropdown_args['hierarchical'] == 0) {
    if ($dropdown_args['taxonomy'] == 'post_tag') {
    // New clause "tags_post_type" added to the WP_Query function
    // see term_clauses filter in functions.php
    $dropdown_args['tags_post_type'] = $post_type;
    }
    $dropdown_args['name'] = $select_name;
    wp_dropdown_categories($dropdown_args);
}
else {

    $getparents['orderby'] = $dropdown_args['orderby'];
    $getparents['taxonomy'] = $dropdown_args['taxonomy'];
    $getparents['hierarchical'] = true;
    $getparents['depth'] = 1;
    $getparents['parent'] = 0;
    $parents = get_categories($getparents);
    ?>

    <select lang="fr" name="<?= $select_name; ?>" id="<?= $select_name; ?>" class="postform <?= $dropdown_args['class']; ?>" tabindex="-1">

        <?php if ($dropdown_args['show_option_none'] != '') { ?>

            <option value="" disabled selected><?= $options['labels']['singular_name']; ?></option>
            <option class="" value="-1"><?=  __('none', 'foodiepro'); ?></option>

        <?php }

        foreach ($parents as $parent) {
            $getchildren = $dropdown_args;
            $getchildren['depth'] = 0;
            $getchildren['child_of'] = $parent->term_id;
            $children = get_categories($getchildren);
            ?>

        <optgroup label="<?= $parent->name; ?>">
            <?php foreach ($children as $child) { ?>
                <option class="" value="<?= $child->term_id; ?>"><?= $child->name; ?></option>
            <?php } ?>
        </optgroup>

        <?php } ?>

    </select>

<?php }
