<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CRM_Taxonomies {

    /**
     * Register recipe taxonomies
     */
    public function register() {

        $tax_list = CRM_Assets::get_taxonomies();

        foreach($tax_list as $name => $data) {

            $options = $this->get_tax_reg_options($name, $data);

            register_taxonomy(
                $name,
                'recipe',
                $options
            );
            // register_taxonomy_for_object_type( $name, 'recipe' );

            /* Create a metadata instance except for ingredients who have already their instatiation in the CRM_Ingredient_Metadata class */
            if ('ingredient' != $name) {
                new WPURP_Taxonomy_MetaData($name, array(
                    'wpurp_link' => array(
                        'label'       => __('Link', 'crm'),
                        'desc'        => __('Send your visitors to a specific link when clicking on an term.', 'crm'),
                        'placeholder' => 'http://www.example.com',
                    ),
                ));
            }

        }
    }

    /**
     * Add taxonomy to array
     */
    private function get_tax_reg_options($tag, $nicenames)
    {
        $name = sanitize_text_field($nicenames[0] );
        $singular = sanitize_text_field($nicenames[1] );

        $name_lower = strtolower($name);
        $singular_lower = strtolower($singular);

        $arr = array(
            'labels' => array(
                'name'                       => $name,
                'singular_name'              => $singular,
                'search_items'               => __( 'Search', 'crm' ) . ' ' . $name,
                'popular_items'              => __( 'Popular', 'crm' ) . ' ' . $name,
                'all_items'                  => __( 'All', 'crm' ) . ' ' . $name,
                'edit_item'                  => __( 'Edit', 'crm' ) . ' ' . $singular,
                'update_item'                => __( 'Update', 'crm' ) . ' ' . $singular,
                'add_new_item'               => __( 'Add New', 'crm' ) . ' ' . $singular,
                'new_item_name'              => __( 'New', 'crm' ) . ' ' . $singular . ' ' . __( 'Name', 'crm' ),
                'separate_items_with_commas' => __( 'Separate ', 'crm') . $name_lower . __(' with commas', 'crm' ),
                'add_or_remove_items'        => __( 'Add or remove', 'crm' ) . ' ' . $name_lower,
                'choose_from_most_used'      => __( 'Choose from the most used', 'crm' ) . ' ' . $name_lower,
                'not_found'                  => __( 'No', 'crm' ) . ' ' . $name_lower . ' ' . __( 'found.', 'crm' ),
                'menu_name'                  => $name
            ),
            'description'   => 'A short description for ' . $name,
            'public'        => true, // determines show_ui, show_tagcloud, show_in_menu,show_in_nav_menus
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => array(
                'slug' => sanitize_title( $this->remove_accents($nicenames[1]) ),
            ),
        );

        if ( 'ingredient' !== $tag ) {
            $arr['show_admin_column'] = true;
        }

        return $arr;
    }

    public function remove_accents($word)
    {
        $trans = array(
            "é" => "e",
            "è" => "e",
            "ê" => "e",
            "â" => "a",
            "à" => "a",
            "ô" => "o",
            "'" => " ",
        );
        $word = strtr($word, $trans);
        return $word;
    }


}
