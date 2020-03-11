<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CSN_Like
{
    // private $logged_in;
    private $post_type;

    const LIKED_POST_TYPES=array(
        'post',
        'recipe',
        'menu'
    );

    public function __construct($type = 'post')
    {
        $this->post_type = $type;
    }

    /* POST TYPE-DEPENDENT FUNCTIONS
    ----------------------------------------------------------------------*/
    public function get_html($vertical = 'above', $horizontal = 'center')
    {
        $post_id = get_the_id();
        $link_class = 'social-like-post';
        $slug = 'like';
        $link_id = '';
        $link_url = '#';
        $output = '';

        // if( is_user_logged_in() ) {
            //     $link_class .= ' logged-in';
            // }
            // else {
                //     $link_id='join-us';
                //     $link_url = '/connexion';
                // }

                if ($this->post_type == 'recipe') {
                    $tooltip_like = __('I cooked and liked this recipe', 'foodiepro');
                    $tooltip_dislike = __('Do not like this recipe anymore', 'foodiepro');
                } else {
                    $tooltip_like = __('Like this post', 'foodiepro');
                    $tooltip_dislike = __('Do not like this post anymore', 'foodiepro');
                }

                if ($this->is_liked_post($post_id)) {
                    $slug = 'liked';
                    $link_class .= ' is-liked';
                    $tooltip = $tooltip_dislike;
                    $tooltip_alt = $tooltip_like;
                } else {
                    $tooltip = $tooltip_like;
                    $tooltip_alt = $tooltip_dislike;
                }

                $tooltip = '<div class="toggle">' . $tooltip . '</div>';
                $tooltip_alt = '<div class="toggle" style="display:none">' . $tooltip_alt . '</div>';

                // $output = $this->before_output();

                ob_start();
                $ga = "ga('send','event','like','click','" . $this->post_type . "', 0)";
                // echo $ga;
                $ga = WP_MINIFY ? $ga : '';
                // echo $ga;
                ?>
                <a href="<?php echo $link_url; ?>" onClick="<?= $ga; ?>" id="<?php echo $link_id; ?>" class="<?php echo $link_class; ?>" data-post-id="<?php echo $post_id; ?>">
                <?= foodiepro_get_icon($slug); ?>
                <div class="button-caption">
                <?php
                $count_likes = $this->like_count($post_id);
                $this->display_like($count_likes, $this->post_type);
                ?>
                </div>
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

                // $output = $this->after_output( $output );
                return $output;
            }

            public function display($vertical = 'above', $horizontal = 'left')
            {
                echo $this->get_html($vertical, $horizontal);
            }



            /* AJAX CALLBACK
            ----------------------------------------------------------------------*/

            public function ajax_like_post()
            {
                if (check_ajax_referer('custom_post_like', 'security', false)) {
                    // echo 'Ajax security OK';
                    $post_id = $_POST['post_id'];
                    $liking_users = get_post_meta($post_id, 'liking_users', true);
                    $liking_users = is_array($liking_users) ? $liking_users : array();

                    $user_id = is_user_logged_in() ? get_current_user_id() : self::get_user_ip();

                    if (in_array($user_id, $liking_users)) {
                        $key = array_search($user_id, $liking_users);
                        unset($liking_users[$key]);
                        // echo '\n Removed user ID from post liking_users meta';
                        // echo print_r( $liking_users );
                        do_action('post_dislike', $user_id, $post_id);
                    } else {
                        $liking_users[] = $user_id;
                        // echo '\n Added user ID to post liking_users meta';
                        do_action('post_like', $user_id, $post_id);
                    }

                    $count = count($liking_users);
                    $this->display_like($count, get_post_type($post_id));
                    // echo sprintf(__('%s cooked', 'foodiepro'), $count);
                    update_post_meta($post_id, 'liking_users', $liking_users);
                    update_post_meta($post_id, 'like_count', $count);
                    do_action('csn_after_post_like', $user_id, $post_id);
                } else {
                    // echo __('Please refresh the page before','foodiepro');
                }
                die();
            }

            /* HELPERS
            ----------------------------------------------------------------------*/
            public function display_like($count, $post_type)
            {
                if ($count == 0) {
                    if ($post_type == 'recipe')
                    echo __('I cooked it', 'foodiepro');
                    else
                    echo __('I like', 'foodiepro');
                } else {
                    if ($post_type == 'recipe')
                    echo sprintf(_n('%s cooked', '%s cooked', $count, 'foodiepro'), $count);
                    else
                    echo sprintf(_n('%s like', '%s likes', $count, 'foodiepro'), $count);
                }
            }

            public function display_debug_information()
            {
                $userid = get_current_user_id();
                $postid = get_the_id();
                // $usermeta = get_user_meta( $userid, 'liked_posts', true );
                $postmeta = get_post_meta($postid, 'liking_users', true);
                echo '<pre>' . 'Current user : ' . $userid . '</pre>';
                // echo '<pre>' . print_r( $usermeta) . '</pre>';
                echo '<pre>' . print_r($postmeta) . '</pre>';
            }

            public function enqueue_scripts()
            {
            if (!is_single()) return;
            // wp_enqueue_script( 'custom-post-like', self::$PLUGIN_URI . '/assets/js/social-like-post.js', array( 'jquery' ), CHILD_THEME_VERSION, false );
            custom_enqueue_script(
                'custom-post-like',
                'assets/js/social-like-post.js',
                CSN_Assets::plugin_url(),
                CSN_Assets::plugin_path(),
                array('jquery'),
                CHILD_THEME_VERSION,
                false
            );
            wp_localize_script(
                'custom-post-like',
                'custom_post_like',
                array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('custom_post_like')
                    )
                );
            }

            public static function is_liked_post($post_id)
            {
                $user_id = is_user_logged_in() ? get_current_user_id() : self::get_user_ip();

                $liking_users = get_post_meta($post_id, 'liking_users', true);
                $liking_users = is_array($liking_users) ? $liking_users : array();

                return in_array($user_id, $liking_users);
            }

            public static function like_count($post_id)
            {
                if (is_null($post_id)) return false;

                $liking_users = get_post_meta($post_id, 'liking_users', true);
                $liking_users = is_array($liking_users) ? $liking_users : array();

                return count($liking_users);
            }

            /* Get the user ip (from WP Beginner)
            -------------------------------------------------------------*/
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
                return apply_filters('wpb_get_ip', $ip);
            }

            /* SHORTCODE
            ----------------------------------------------------------------------*/
            public function like_count_shortcode($atts)
            {
                $a = shortcode_atts(array(
                    'post'     => 'current', // defaults to current post otherwise post id given in this attribute
                    'tag'     => 'span',
                    'class' => 'like-count',
                    'icon'     => true,
                ), $atts);

                if ($a['post'] == 'current')
                $post_id = get_the_id();
                else
                $post_id = intval($a['post']);

                $count = $this->like_count($post_id);

                if ($a['icon']) {
                    $html = '<span class="like-count ' . ($count == 0 ? 'nolike' : '') . '" title="' . sprintf(_n('%s like', '%s likes', $count, 'foodiepro'), $count) . '"><i class="far fa-thumbs-up"></i>' . $count . '</span>';
                } else {
                    $html = sprintf(_n('%s like', '%s likes', $count, 'foodiepro'), $count);
                    $html = '<' . $a['tag'] . ' class="' . $a['class'] . '">' . $html . '</' . $a['tag'] . '>';
                }

                return $html;
            }

        /* New post submission callback
        /* Add ratings default value (required for proper sorting in archives)
        -------------------------------------------------------------*/
        public static function add_default_like_count($post_ID)
        {
            if (!wp_is_post_revision($post_ID) && (in_array(get_post_type(), self::LIKED_POST_TYPES) )) {
                update_post_meta($post_ID, 'like_count', '0');
            }
        }

        /* ===========================================================================
        /* ===================         ARCHIVE              =======================
        /* =========================================================================== */

        public function sort_entries_by_like_count($query)
        {
            // Select any archive. For custom post type use: is_post_type_archive( $post_type )
            //if (is_archive() || is_search() ): => ne pas utiliser car r�sultats de recherche non relevants

            $order = get_query_var('orderby', false);

            if (($order == 'like-count')) {
                $query->set('orderby', 'meta_value_num');
                $query->set('meta_key', 'like_count');
                $query->set('order', 'DESC');
            }
        }

}
