<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class CPM_Shortcodes {

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
            'class' => "button",
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




    /**
     * get_post_count
     *
     * @param  mixed $atts
     * @return void
     */
    public static function get_post_count( $atts ) {
        //Let's not loose time if user doesn't have the rights
        if( !current_user_can('editor') && !current_user_can('administrator') ) return;

        $atts = shortcode_atts( array(
            'status' => 'pending', //draft, publish, auto-draft, private, separated by " "
            'type' => 'post', //recipe
        ), $atts );

        $post_type=$atts['type'];
        $status=$atts['status'];

        $count = wp_count_posts($post_type );
        if (isset($count->$status)) {
            $html = ($count->$status>0)?'<span class="post-count-indicator">('.$count->$status.')</span>':'';
        }

        return $html;
    }


    /**
     * custom_submission_form_shortcode
     *
     * @param  mixed $atts
     * @return void
     */
    public function custom_submission_form_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'post_type' => 'post', // 'post', 'recipe'
        ), $atts);
        extract($atts);

        if (!is_user_logged_in()) {
            return '<p class="errorbox">' . __('Sorry, only registered users may submit recipes.', 'foodiepro') . '</p>';
        }

        $output = '';
        $user_id = get_current_user_id();

        $Form = new CPM_Submission($post_type);

        // Check that the nonce input field is set
        if (isset($_POST['submit' . $post_type])) {
            /* Submit post */
            $output .= $Form->submit();
        } elseif (isset($_GET['edit-' . $post_type])) {
            /* Edit post */
            $post_id = $_GET['edit-' . $post_type];
            $post = get_post($post_id);
            $user = get_userdata($user_id);

            if ($post->post_author == $user_id || current_user_can('administrator')) {
                $output .= $Form->display($post_id, 'edit');
            }
        } else {
            /* New post */
            $output = $Form->display();
        }

        return $output;
    }


}
