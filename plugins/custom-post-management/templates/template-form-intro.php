<?php


switch ($state) {
    case 'new': ?>
        <p class="submitbox">
            <?= CPM_Assets::get_label($post_type, 'new1'); ?>
            <br>
            <?= CPM_Assets::get_label($post_type, 'new2'); ?>
        </p>
    <?php
    break;

    case 'edit': ?>
        <p class="submitbox">
            <?= CPM_Assets::get_label($post_type, 'edit1'); ?>
            <br>
            <?= CPM_Assets::get_label($post_type, 'edit2'); ?>
        </p>
    <?php
    break;

    case 'draft': ?>
        <p class="submitbox">
            <?= sprintf(CPM_Assets::get_label($post_type, 'draft1'), get_permalink($post_id)); ?>
            <br>
            <?= CPM_Assets::get_label($post_type, 'draft2'); ?>
        </p>
        <?php
        $url = foodiepro_get_permalink(array(
            'slug' => CPM_Assets::get_slug($post_type, $post_type . '_list')
        ));
        ?>
        <p>
            <span class="post-nav-link"><?= sprintf(CPM_Assets::get_label($post_type, 'back'), $url); ?></span>
        </p>
        <?php
    break;

    default:
        echo '';

}
