<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class CPM_Output_Shortcodes {

    public function new_post_button($atts)
    {
        $atts = shortcode_atts(array(
            'post_type' => 'post', // 'post', 'recipe'
            'text' => '',
        ), $atts);

        $post_type = $atts['post_type'];
        $text = CPM_Assets::get_label($post_type, 'new_button');
        $text = esc_html($text);

        $out = '<div>';
        $out .= foodiepro_get_permalink(array(
            'slug' => CPM_Assets::get_slug($post_type, $post_type . '_form'),
            'class' => "black-button",
            'text' => $text,
        ));
        $out .= '</div>';

        return $out;
    }

    public function custom_post_list_shortcode($atts, $content)
    {
        if (!is_user_logged_in()) return;

        $atts = shortcode_atts(array(
            'post_type' => 'post', // 'post', 'recipe'
            'empty' => '', // 'true'
        ), $atts);

        extract($atts);
        $content = esc_html($content);
        $empty=($empty==='true');
        $List = new CPM_List($post_type);

        $author = get_current_user_id();

        $status = esc_html(get_query_var('status', false));
        if ($status)
            $statuses = CPM_Assets::get_statuses($post_type, $status, false );
        else
            $statuses = CPM_Assets::get_statuses($post_type, 'registered', false);

        // Output posts grouped by status
        $output = '';
        foreach ($statuses as $post_status=> $data) {
            $args = array(
                'author' => $author,
                'numberposts' => -1,
                'category' => 0,
                'orderby' => 'modified',
                'order' => 'DESC',
                'include' => array(),
                'exclude' => array(),
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => $post_type,
                'post_status' => $post_status,
                'suppress_filters' => true
            );
            remove_filter( 'pre_get_posts', 'CPM_Post_Status::remove_restored_from_archives');
            $posts = get_posts($args);
            add_filter( 'pre_get_posts', 'CPM_Post_Status::remove_restored_from_archives');
            if ($empty || !empty($posts) || count($statuses)==1 ) {
                $output .= $List->display($posts, true, $data['label'], $data['description']);
            }
        }
        return $output;
    }

}
