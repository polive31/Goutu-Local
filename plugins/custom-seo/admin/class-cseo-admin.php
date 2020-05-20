<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CSEO_Admin {

    /**
     * If yoast metadata description is set, then copy it to the post excerpt on save
     *
     * @param  mixed $data
     * @param  mixed $postarr
     * @return void
     */
    public function set_post_excerpt($data, $postarr)
    {
        if (!is_admin()) return $data;
        $yoast_excerpt = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
        if (empty($yoast_excerpt)) return $data;

        $data['post_excerpt'] = $yoast_excerpt;
        return $data;
    }


}
