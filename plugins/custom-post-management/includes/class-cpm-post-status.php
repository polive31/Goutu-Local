<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CPM_Post_Status
{
    private $custom_status;

    public function hydrate() {
        $label = _x('Restored', 'post', 'foodiepro');
        $this->custom_status = array(
            'slug'  => 'restored',
            'label' => $label,
            'description' => $label,
        );
    }

    public function add_post_status_queryvar($vars) {
        $vars[] .= 'status';
        return $vars;
    }

    // Register Custom Post Status
    public function register_restored_post_status()
    {
        register_post_status(
            $this->get_field('slug'),
            array(
                'label' => $this->get_field('label'),
                'public' => true,
                'exclude_from_search' => false,
                'show_in_admin_all_list' => true,
                'show_in_admin_status_list' => true,
                'label_count' => _n_noop($this->get_field('label') . '<span class="count">(%s)</span>', $this->get_field('label') . '<span class="count">(%s)</span>'),
            )
        );
    }

    public static function remove_restored_from_archives( $query ) {
        if ( ($query->is_search || $query->is_archive) && !is_admin() ) {
            $query->set( 'post_status', array('publish') );
        }
        return $query;
    }

    // Display Custom Post Status Option in Post Edit
    public function display_status_option_in_post_edit()
    {
        global $post;

        $selected = '';
        $label = '';
        if (isset($post->post_type) && in_array($post->post_type, CPM_Assets::get_post_types())) {
            $selected = selected($post->post_status, $this->get_field('slug'), false);
?>
            <script>
                jQuery(document).ready(function() {
                    jQuery("select#post_status").append("<option value=\"<?= $this->get_field('slug'); ?>\" <?= $selected ?>><?= $this->get_field('label'); ?></option>");
                    jQuery(".misc-pub-section label").append("<span id=\"post-status-display\"><?= $this->get_field('label'); ?></span>");
                });
            </script>
        <?php
        }
    }


    public function display_status_option_in_post_quick_edit()
    {
        global $post;
        if (in_array($post->post_type, CPM_Assets::get_post_types())) {
        ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery('select[name=\"_status\"]').append('<option value=\"<?= $this->get_field('slug'); ?>\"><?= $this->get_field('label'); ?></option>');
                });
            </script>"
<?php
        }
    }

    public function display_status_in_post_grid($status)
    {
        global $post;

        if (!isset($post->post_type)) return $status;

        if (in_array($post->post_type, CPM_Assets::get_post_types())) {
            if (get_query_var('post_status') != $this->get_field('slug')) { // not for pages with all posts of this status
                if ($post->post_status == $this->get_field('slug')) {
                    $status=array($this->get_field('label'));
                }
            }
        }
        return $status;
    }

/* ------------------------------------------------
    GETTERS
--------------------------------------------------*/
    public function get_field($field)
    {
        if (!isset($this->custom_status[$field])) return false;
        return $this->custom_status[$field];
    }

}
