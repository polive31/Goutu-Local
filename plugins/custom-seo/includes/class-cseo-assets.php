<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CSEO_Assets {

    private const CORNERSTONE = array(
        'post'      => array(
            'category'  => 'trucs-et-astuces',
        ),
        'recipe'    => array(
            'country'   => 'monde',
            'region'    => 'regions',
            'diet'      => 'regimes',
            'season'    => 'saisons',
            'occasion'  => 'occasions',
            'course'    => 'plats',
        )
    );

    private static $france_id;

    public static function hydrate() {
        $term = get_term_by( 'slug', 'france', 'cuisine');
        self::$france_id=$term->term_id;
    }

    public static function get_cornerstone_url( $post ) {
        if (!is_a( $post, 'WP_Post' )) return false;
        if (!isset(self::CORNERSTONE[$post->post_type])) return false;

        foreach (self::CORNERSTONE[$post->post_type] as $tax => $slug) {
            if ($tax=='country') {
                $terms=wp_get_post_terms( $post->ID, 'cuisine');
                if (!empty($terms) && $terms[0]->parent != self::$france_id && $terms[0]->slug!='france' ) {
                    return $slug;
                }
            }
            elseif ($tax=='region') {
                $terms=wp_get_post_terms( $post->ID, 'cuisine');
                if (!empty($terms) && ( $terms[0]->parent == self::$france_id ||  $terms[0]->slug=='france') ) {
                    return $slug;
                }
            }
            elseif (has_term('', $tax)) {
                return $slug;
            }
        }

        return false;
    }

}
