<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CAH_Post_Metabox
{
    public function post_excerpt_meta_box()
    {
        add_meta_box(
            'post_excerpt',
            __('Post Excerpt', 'foodiepro'),
            array($this, 'post_excerpt_meta_box_callback')
        );
    }
    public function post_excerpt_meta_box_callback()
    {
        the_excerpt();
    }

}
