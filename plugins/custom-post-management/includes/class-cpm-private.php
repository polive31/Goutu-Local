<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CPM_Private
{
    /**
     * foodiepro_404_error_queryvar
     *
     * @param  mixed $qvars
     * @return void
     */
    public static function custom_404_error_queryvar($qvars)
    {
        $qvars[] = 'error404';
        return $qvars;
    }

    /**
     * Redirect towards register page on private page if not logged-in
     *
     * @return void
     */
    public static function redirect_private_content()
    {
        if (is_admin() || current_user_can('administrator')) return;

        global $wp_query, $wpdb;
        if (is_404()) {
            $url = foodiepro_get_permalink(array('slug' => 'page-not-found'));
            $current_query = $wpdb->get_row($wp_query->request);
            if (isset($current_query->post_status)) {
                $type = isset($current_query->post_type)? $current_query->post_type:'post';
                $url = add_query_arg('error404', $type . '-' . $current_query->post_status, $url);
                wp_redirect($url);
            }
            else
                wp_redirect( get_home_url() );
            exit;
        }
    }


    /**
     * Shortcode for customized 404 page
     *
     * @return void
     */
    public static function custom_404_page()
    {
        $error = get_query_var( 'error404' );

        $name='404';
        // if ( wp_is_mobile() ) {
        //     $name.='-vertical';
        // }
        $name.='.jpg';

        $url = CPM_Assets::plugin_uri() . 'assets/img/404';
        $dir = CPM_Assets::plugin_path() . 'assets/img/404';

        $default_msg = sprintf(__('Go to the <a href="%s">home page</a> and discover Goûtu.org from here !', 'foodiepro'), foodiepro_get_permalink( array('wp'=>'home') ) );

        /* Rendering starts */
        $html = foodiepro_get_picture(array(
            'src'   => trailingslashit($url) . $name,
            'dir'   => $dir,
            'filter_max_width' => 1024,
            'lazy' => false,
            // $filter_ext = array('jpg', 'jpeg', 'png');
            'class' => 'alignnone',
        ));
        $html .= '<br>';

        if ( foodiepro_contains( $error, '-') ) {
            $match=preg_match('/(\w+)\-(\w+)/', $error, $matches);
            $post_type = ($match === 1 && in_array($matches[1], CPM_Assets::get_post_types() ) ) ? $matches[1] : 'post';
            $post_error = $match === 1 ? $matches[2] : false;

            $connexion_url=foodiepro_get_permalink( array('wp'=> 'login'));
            $discover_url=foodiepro_get_permalink( array('slug'=>'connexion'));

            $html .= CPM_Assets::get_label($post_type, 'error404_' . $post_error );
            $html .= '<br>';
            if ( !is_user_logged_in() ) {
                $html .= sprintf(__('<a href="%s">Log-in</a> to access private posts. Not a member ? <a href="%s">Check out</a> all what you can do on Goûtu.org !', 'foodiepro'), $connexion_url, $discover_url);
                $html .= '<br>';
            }
            elseif ($post_error=='friends' ) {
                $members_url=foodiepro_get_permalink(array('community'=>'members'));
                $html .= sprintf(__('<a href="%s">Make new friends</a> to get access to their private posts.', 'foodiepro'), $members_url);
                $html .= '<br>';
            }
            elseif ($post_error == 'group') {
                $groups_url = foodiepro_get_permalink(array('community'=>'groups'));
                $html .= sprintf(__('<a href="%s">Apply to this group</a> in order to get access to their private posts.', 'foodiepro'), $groups_url);
                $html .= '<br>';
            }
            else
                $html .= $default_msg;
        }
        else {
            $html .= __('The page you are trying to open does not exist, sorry', 'foodiepro');
            $html .= '<br>';
            $html .= $default_msg;
        }

        return $html;
    }


}
