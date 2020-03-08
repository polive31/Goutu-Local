<?php


// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CRM_Recipe_Post_Type {

    public const SLUG='recette';
    const EP_RECIPE = 524288; // 2^19


    public function register_recipe_post_type()
    {
        $name = __( 'Recipes', 'crm' );
        $singular = __( 'Recipe', 'crm' );

        $taxonomies = array( '' );

        $taxonomies = array( 'category', 'post_tag' );
        $has_archive = true;

        $args = array(
            'labels' => array(
                'name'                  => $name,
                'singular_name'         => $singular,
                'add_new'               => __( 'Add New', 'crm' ),
                'add_new_item'          => __( 'Add New', 'crm' ) . ' ' . $singular,
                'edit'                  => __( 'Edit', 'crm' ),
                'edit_item'             => __( 'Edit', 'crm' ) . ' ' . $singular,
                'new_item'              => __( 'New', 'crm' ) . ' ' . $singular,
                'view'                  => __( 'View', 'crm' ),
                'view_item'             => __( 'View', 'crm' ) . ' ' . $singular,
                'search_items'          => __( 'Search', 'crm' ) . ' ' . $name,
                'not_found'             => __( 'No', 'crm' ) . ' ' . $name . ' ' . __( 'found.', 'crm' ),
                'not_found_in_trash'    => __( 'No', 'crm' ) . ' ' . $name . ' ' . __( 'found in trash.', 'crm' ),
                'parent'                => __( 'Parent', 'crm' ) . ' ' . $singular,
            ),
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'comments', 'excerpt', 'author', 'revisions', 'publicize', 'shortlinks', 'genesis-simple-sidebars' ),
            'yarpp_support' => true,
            'taxonomies'    => $taxonomies,
            'menu_icon'     => 'dashicons-products',
            'has_archive'   => $has_archive,
            'rewrite'       => array(
                'slug'      => self::SLUG,
                'ep_mask'   => self::EP_RECIPE,
            ),
            'show_in_rest'  => true,
        );

        register_post_type( 'recipe', $args );
    }

    public function recipe_post_class( $classes )
    {
        if ( get_post_type() == 'recipe' )
        {
            $classes[] = 'post';
            $classes[] = 'type-post';
        }

        return $classes;
    }

    /*
     * Remove the slug from published recipe post permalinks.
     */
    public function remove_recipe_slug( $post_link, $post, $leavename ) {

        // if(WPUltimateRecipe::option( 'remove_recipe_slug', '0' ) == '1' ) {
        //     if ('recipe' != $post->post_type || 'publish' != $post->post_status ) {
        //         return $post_link;
        //     }

        //     $post_link = str_replace( '/' . self::SLUG . '/', '/', $post_link );
        // }

        return $post_link;
    }

    /*
     * Some hackery to have WordPress match postname to any of our public post types
     * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
     * Typically core only accounts for posts and pages where the slug is /post-name/
     */
    public function remove_recipe_slug_in_parse_request( $query ) {
        // if(WPUltimateRecipe::option( 'remove_recipe_slug', '0' ) == '1' ) {
        //     if ( 'subscriptions' === $query->get('name') ) return; // Fix for Mailpoet.
        //     if ( !$query->is_main_query() ) return;
        //     if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        //         return;
        //     }

        //     if ( !empty( $query->query['name'] ) ) {
        //         $query->set( 'post_type', array( 'post', 'recipe', 'page' ) );
        //     }
        // }
    }
}
