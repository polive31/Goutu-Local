<?php

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class CCI_Admin
{

    protected $taxonomies;


    public function show_admin_notice()
    {
        if (get_option('wpcustom_notice', false)  !== false) {
            return;
        }

        update_option('wpcustom_notice', 1);

        ___template('admin-notice');
    }

    public function manage_category_columns($columns)
    {
        return array_merge($columns, array('image' => __('Image', 'wpcustom-category-image')));
    }

    public function manage_category_columns_fields($deprecated, $column_name, $term_id)
    {
        if ($column_name == 'image' && $this->has_image($term_id)) {
            echo CCI_Public::get_category_image(array(
                'term_id' => $term_id,
                'size'    => 'thumbnail',
            ));
        }
    }

    public function admin_init()
    {
        $this->taxonomies = get_taxonomies();

        add_filter('manage_edit-category_columns', array($this, 'manage_category_columns'));
        add_filter('manage_category_custom_column', array($this, 'manage_category_columns_fields'), 10, 3);

        foreach ((array) $this->taxonomies as $taxonomy) {
            $this->add_custom_column_fields($taxonomy);
        }
    }

    public function add_custom_column_fields($taxonomy)
    {
        add_action("{$taxonomy}_add_form_fields", array($this, 'add_taxonomy_field'));
        add_action("{$taxonomy}_edit_form_fields", array($this, 'edit_taxonomy_field'));

        // Add custom columns to custom taxonomies
        add_filter("manage_edit-{$taxonomy}_columns", array($this, 'manage_category_columns'));
        add_filter("manage_{$taxonomy}_custom_column", array($this, 'manage_category_columns_fields'), 10, 3);
    }

    /**
     * Enqueue assets into admin
     */
    public function admin_enqueue_assets($hook)
    {
        if ($hook != 'edit-tags.php' && $hook != 'term.php') {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_script('category-image-js', $this->asset_url('/js/categoryimage.js'), array('jquery'), '1.0.0', true );

        wp_localize_script('category-image-js', 'CategoryImage', array(
            'wp_version' => WPCCI_WP_VERSION,
                'label'      => array(
                    'title'  => __('Choose Category Image', 'wpcustom-category-image'),
                    'button' => __('Choose Image', 'wpcustom-category-image')
                )
            )
        );

        wp_enqueue_style('category-image-css', $this->asset_url('/css/categoryimage.css'));
    }

    private function asset_url($file)
    {
        return plugins_url('assets/' . $file, dirname(__FILE__));
    }

    public function save_image($term_id)
    {
        // Ignore quick edit
        if (isset($_POST['action']) && $_POST['action'] == 'inline-save-tax') {
            return;
        }

        $attachment_id = isset($_POST['categoryimage_attachment']) ? (int) $_POST['categoryimage_attachment'] : null;

        if (! is_null($attachment_id) && $attachment_id > 0 && !empty($attachment_id)) {
            update_option('categoryimage_'.$term_id, $attachment_id);
            return;
        }

        delete_option('categoryimage_'.$term_id);
    }

    public function get_attachment_id($term_id)
    {
        return get_option('categoryimage_'.$term_id);
    }

    public function has_image($term_id)
    {
        return ($this->get_attachment_id($term_id) !== false);
    }

    public function add_taxonomy_field($taxonomy)
    {
        echo $this->taxonomy_field('add-form-option-image', $taxonomy);
    }

    public function edit_taxonomy_field($taxonomy)
    {
        echo $this->taxonomy_field('edit-form-option-image', $taxonomy);
    }

    public function taxonomy_field($template, $taxonomy)
    {
        $params = array(
            'label'  => array(
                'image'        => __('Image', 'wpcustom-category-image'),
                'upload_image' => __('Upload/Edit Image', 'wpcustom-category-image'),
                'remove_image' => __('Remove image', 'wpcustom-category-image')
            ),
            'categoryimage_attachment' => null
        );


        if (isset($taxonomy->term_id) && $this->has_image($taxonomy->term_id)) {
            $image = CCI_Public::get_category_image(array(
                'term_id' => $taxonomy->term_id
            ), true);

            $attachment_id = $this->get_attachment_id($taxonomy->term_id);

            $params = array_replace_recursive($params, array(
                'categoryimage_image'      => $image,
                'categoryimage_attachment' => $attachment_id,
            ));
        }

        return ___template($template, $params, false);
    }
}
