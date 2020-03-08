<?php

class CRM_Ingredient_Metadata {

    public function __construct()
    {
        new WPURP_Taxonomy_Metadata( 'ingredient', array(
            'plural' => array(
                'label'       => __( 'Plural', 'crm' ),
                'desc'        => __( 'Optional plural version of this ingredient.', 'crm' ),
                'placeholder' => '',
            ),
            'wpurp_link' => array(
                'label'       => __( 'Link', 'crm' ),
                'desc'        => __( 'Send your visitors to a specific link when clicking on an ingredient.', 'crm' ),
                'placeholder' => 'http://www.example.com',
            ),
            'nofollow_link' => array(
                'label'       => __( 'Nofollow link', 'crm' ),
                'desc'        => __( 'Always use nofollow for this ingredient.', 'crm' ),
                'type'        => 'checkbox',
            ),
            'hide_link' => array(
                'label'       => __( 'Hide link', 'crm' ),
                'desc'        => __( "Don't use a link in the ingredients list for this ingredient.", 'crm' ),
                'type'        => 'checkbox',
            ),
            'group' => array(
                'label'       => __( 'Group', 'crm' ),
                'desc'        => __( 'Use this to group ingredients in the shopping list.', 'crm' ),
                'placeholder' => __( 'Vegetables', 'crm' ),
            ),
        ) );

        add_filter( 'manage_edit-ingredient_columns', array( $this, 'add_metadata_column_to_ingredients' ) );
        add_action( 'manage_ingredient_custom_column', array( $this, 'add_metadata_column_content' ), 10, 3 );
    }

    public function add_metadata_column_to_ingredients($columns)
    {
        $columns['plural'] = __( 'Plural', 'crm' );
        $columns['link'] = __( 'Link', 'crm' );
        return $columns;
    }

    public function add_metadata_column_content($content, $column_name, $term_id)
    {
        $term = get_term( $term_id, 'ingredient' );

        if( $column_name == 'link' ) {
            $custom_link = WPURP_Taxonomy_Metadata::get( 'ingredient', $term->slug, 'link' );

            if( $custom_link ) {
                echo $custom_link;
            }
        } else if( $column_name == 'plural' ) {
            $plural = WPURP_Taxonomy_Metadata::get( 'ingredient', $term->slug, 'plural' );

            if( $plural ) {
                echo $plural;
            }
        }

    }
}
