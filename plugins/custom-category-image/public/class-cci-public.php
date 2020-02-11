<?php

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class CCI_Public
{

    public static function shortcode($atts)
    {
        $args = shortcode_atts(array(
                'size'    => 'full',
                'term_id' => null,
                'alt'     => null,
                'onlysrc' => false
        ), $atts);

        return self::get_category_image(array(
            'size'    => $args['size'],
            'term_id' => $args['term_id'],
            'alt'     => $args['alt']
        ), (boolean) $args['onlysrc']);
    }

    public static function get_category_image($atts = array(), $onlysrc = false)
    {
        $params = array_merge(array(
            'size'    => 'full',
            'term_id' => null,
            'alt'     => null,
            'class'   => null
        ), $atts);

        $term_id = $params['term_id'];
        $size    = $params['size'];

        if (!$term_id) {
            $term    = get_queried_object();
            $term_id = $term->term_id;
        }

        if (!$term_id) {
            return;
        }

        $attachment_id   = get_option('categoryimage_'.$term_id);

        $attachment_meta = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $attachment_alt  = trim(strip_tags($attachment_meta));

        $attr = array(
            'alt'=> (is_null($params['alt']) ?  $attachment_alt : $params['alt']),
            'class'=> (is_null($params['class']) ?  '' : $params['class'])
        );

        if ($onlysrc) {
            $src = wp_get_attachment_image_src($attachment_id, $size, false);
            return is_array($src) ? $src[0] : null;
        }

        return wp_get_attachment_image($attachment_id, $size, false, $attr);
    }


}
