<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CPM_Like
{
    private $post_type;

    const LIKED_POST_TYPES=array(
        'post',
        'recipe',
        'menu'
    );

    const LIKING_USERS_META= 'liking_users';
    const LIKE_COUNT_META= 'like_count';

    public function __construct($type = 'post')
    {
        $this->post_type = $type;
    }

    /* DISPLAY
    ----------------------------------------------------------------------*/
    /**
     * Generate markup for like button in post
     *
     * @param  mixed $vertical
     * @param  mixed $horizontal
     * @return void
     */
    public function get_button($vertical = 'above', $horizontal = 'center')
    {
        $tooltip_like = CPM_Assets::get_label( $this->post_type, 'tooltip_like');
        $tooltip_dislike = CPM_Assets::get_label( $this->post_type, 'tooltip_dislike');

        $post_id = get_the_id();

        if ($this->is_liked_post($post_id)) {
            $slug = 'liked';
            $link_class = 'is-liked';
            $tooltip = $tooltip_dislike;
            $tooltip_alt = $tooltip_like;
        } else {
            $slug = 'like';
            $link_class = '';
            $tooltip = $tooltip_like;
            $tooltip_alt = $tooltip_dislike;
        }

        $tooltip = '<div class="toggle">' . $tooltip . '</div>';
        $tooltip_alt = '<div class="toggle" style="display:none">' . $tooltip_alt . '</div>';

        $link_id = '';
        $link_url = '#';

        ob_start();
        ?>
        <a href="<?= $link_url; ?>" id="<?= $link_id; ?>" class="<?= $link_class; ?>" data-post-id="<?= $post_id; ?>">
            <?= foodiepro_get_icon($slug); ?>
            <span class="button-caption">
                <?= $this->get_like_caption( $this->like_count($post_id), $this->post_type); ?>
            </span>
        </a>

        <?php
        $args = array(
            'content'   => $tooltip . $tooltip_alt,
            'valign'    => $vertical,
            'halign'    => $horizontal,
        );
        Tooltip::display($args);

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Display like button in a post
     *
     * @param  mixed $vertical
     * @param  mixed $horizontal
     * @return void
     */
    public function display($vertical = 'above', $horizontal = 'left')
    {
        echo $this->get_button($vertical, $horizontal);
    }



    /**
     * get_like_count
     *
     * @param  mixed $post
     * @param  mixed $class
     * @param  mixed $icon
     * @param  mixed $tag
     * @return void
     */
    public static function get_like_count( $post='current', $class='', $icon=true, $tag='span' ) {
        if ($post == 'current')
            $post_id = get_the_id();
        else
            $post_id = intval($post);

        $count =self::like_count($post_id);

        if ($icon) {
            $html = '<div class="likes"><a title="' . sprintf(_n('%s like', '%s likes', $count, 'foodiepro'), $count) . '"><span class="like-count ' . $class . ' ' . ($count == 0 ? 'nolike' : '') . '"><i class="far fa-thumbs-up"></i>' . $count . '</span></a></div>';
        } else {
            $html = sprintf(_n('%s like', '%s likes', $count, 'foodiepro'), $count);
            $html = '<' . $tag . ' class="like-count ' . $class . '">' . $html . '</' . $tag . '>';
        }

        return $html;
    }



    /* CALLBACKS
    ----------------------------------------------------------------------*/

    /**
     * New post submission callback
     * Add ratings default value (required for proper sorting in archives)
     *
     * @param  mixed $post_ID
     * @return void
     */
    public static function add_default_like_count($post_ID)
    {
        if (!wp_is_post_revision($post_ID) && (in_array(get_post_type(), self::LIKED_POST_TYPES))) {
            update_post_meta($post_ID, self::LIKE_COUNT_META, '0');
        }
    }


    /**
     * AJAX callback for post like action
     *
     * @return void
     */
    public function ajax_like_post()
    {
        if (!check_ajax_referer( 'custom_post_like', 'security', false)) {
            echo ('Nonce not recognized');
            die();
        }

        $post_id = $_POST['post_id'];
        $liking_users = get_post_meta($post_id, self::LIKING_USERS_META, true);
        $liking_users = is_array($liking_users) ? $liking_users : array();

        $from_id = is_user_logged_in() ? get_current_user_id() : self::get_user_ip();

        if (in_array($from_id, $liking_users)) {
            $key = array_search($from_id, $liking_users);
            unset($liking_users[$key]);
            do_action('cpm_post_dislike', $from_id, $post_id);
        } else {
            $liking_users[] = $from_id;
            do_action('cpm_post_like', $from_id, $post_id);
            $post = get_post($post_id);
            if (is_user_logged_in()) {
                do_action('csi_send_notification', 'like', $from_id, $post->post_author, $post_id);
            }
        }

        $count = count($liking_users);
        $caption = $this->get_like_caption($count, get_post_type($post_id));

        $response = update_post_meta($post_id, self::LIKING_USERS_META, $liking_users);
        $response = $response && update_post_meta($post_id, self::LIKE_COUNT_META, $count);

        if ( !$response ) {
            echo ('Post meta update failed.');
            die();
        }

        do_action('cpm_after_post_like', $from_id, $post_id);

        wp_send_json_success($caption);
    }

    /* HELPERS
    ----------------------------------------------------------------------*/
    /**
     * Returns post like text
     *
     * @param  mixed $count
     * @param  mixed $post_type
     * @return string $html
     */
    public function get_like_caption($count, $post_type)
    {
        $html = '';
        $suffix=$count>1?'n':$count;
        $html = sprintf( CPM_Assets::get_label( $post_type, 'like' . $suffix), $count);
        return $html;
    }


    /**
     * is_liked_post
     *
     * @param  mixed $post_id
     * @return void
     */
    public static function is_liked_post($post_id)
    {
        $user_id = is_user_logged_in() ? get_current_user_id() : self::get_user_ip();

        $liking_users = get_post_meta($post_id, self::LIKING_USERS_META, true);
        $liking_users = is_array($liking_users) ? $liking_users : array();

        return in_array($user_id, $liking_users);
    }

    /**
     * like_count
     *
     * @param  mixed $post_id
     * @return void
     */
    public static function like_count($post_id)
    {
        if (is_null($post_id)) return false;

        $liking_users = get_post_meta($post_id, self::LIKING_USERS_META, true);
        $liking_users = is_array($liking_users) ? $liking_users : array();

        return count($liking_users);
    }


    /**
     * Get the user ip (from WP Beginner)
     *
     * @return string $ip
     */
    public static function get_user_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


/* ARCHIVE
-----------------------------------------------------------*/
    public function sort_entries_by_like_count($query)
    {
        $order = get_query_var('orderby', false);

        if (($order == 'like-count')) {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', self::LIKE_COUNT_META);
            $query->set('order', 'DESC');
        }
    }

}
