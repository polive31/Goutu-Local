<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CSD_Assets
{
    const FIELD_DELIMITER = '%';
    private static $SCHEMAS;
    private static $PLUGIN_URI;

    public static function hydrate() {
        self::$PLUGIN_URI = plugin_dir_url( __DIR__ );

        $logo_url = trailingslashit(self::$PLUGIN_URI) . 'assets/img/logo-google.png';
        $logo_width = 235;

        self::$SCHEMAS = array(
            // 'archive'   => array(
            //     '<location>'      => array(
            //         'archive'       => '',
            //         'search'        => '',
            //         'tag'           => '',
            //     ),
            //     '@context'          => 'https://schema.org',
            //     '@type'             => 'ItemList',
            //     'itemListElement'   => array(
            //         '<repeat>'   => '',
            //         '@type'      => 'ListItem',
            //         'position'   => '%item-pos%',
            //         'url'        => '%item-url%',
            //     ),
            // ),
            'archive'   => array(
                '<location>'      => array(
                    'archive'       => '',
                    'search'        => '',
                    'tag'           => '',
                ),
                '@context'          => 'https://schema.org',
                '@type'             => 'ItemList',
                'itemListElement'   => '%items%',
                ),

            'post'      => array(
                '<location>'      => array(
                    'singular'  => 'post',
                ),
                '@context'      => 'http://schema.org/',
                '@type'         => '%type%',
                'mainEntityOfPage'  => array(
                    '@type'     => 'WebPage',
                    '@id'       => '%url%',
                ),
                'headline'      => '%title%',
                'publisher'     => array(
                    '@type'     => 'Organization',
                    'name'      => "Goûtu.org",
                    'logo'      => array(
                        '@type' => 'ImageObject',
                        'url'   => $logo_url,
                        'width' => $logo_width,
                    ),
                ),
                'author'        => array(
                    '@type'     => 'Person',
                    'name'      => '%author%',
                ),
                'description'   => '%description%',
                'datePublished' => '%date-published%',
                'dateModified'  => '%date-modified%',
                'image'         => '%thumbnail%',
                'keywords'      => '%tags%',
            ),

            'recipe'    => array(
                '<location>'      => array(
                    'singular'   => 'recipe',
                ),
                '@type'             => '%type%',
                '@context'          => 'http://schema.org/',
                'mainEntityOfPage'  => array(
                    '@type'         => 'WebPage',
                    '@id'           => '%url%',
                ),
                'name'              => '%title%',
                'publisher'         => array(
                    '@type'         => 'Organization',
                    'name'          => "Goûtu.org",
                    'logo'          => array(
                        '@type'     => 'ImageObject',
                        'url'       => $logo_url,
                        'width'     => $logo_width,
                    ),
                ),
                'author'            => array(
                    '@type'         => 'Person',
                    'name'          => '%author%',
                ),
                'description'       => '%description%',
                'datePublished'     => '%date-published%',
                'dateModified'      => '%date-modified%',
                'image'             => '%thumbnail%',
                'recipeYield'       => '%servings%',
                'recipeCategory'    => '%course%',
                'recipeCuisine'     => '%cuisine%',
                'suitableForDiet'   => '%diet%',
                'aggregateRating'   => array(
                    '@type'         => 'AggregateRating',
                    'ratingValue'   => '%rating-value%',
                    'ratingCount'   => '%rating-count%',
                ),
                'prepTime'          => '%preptime%',
                'cookTime'          => '%cooktime%',
                'totalTime'         => '%totaltime%',
                'recipeIngredient'  => '%ingredients%',
                'recipeInstructions'=> '%instructions%',
                'keywords'          => '%tags%',
            ),
        );
    }

    public static function get_schema( $template ) {
        $schema = false;
        if (isset(self::$SCHEMAS[$template]))
            $schema = self::$SCHEMAS[$template];
        return $schema;
    }


}
