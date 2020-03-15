<?php

/**
 *
 * .
 *
 * Description.
 *
 * @since Version 3 digits
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


// <script type="application/ld+json">
// {
//   "@context":"https://schema.org",
//   "@type":"ItemList",
//   "itemListElement":[
//     {
//       "@type":"ListItem",
//       "position":1,
//       "url":"http://example.com/coffee_cake.html"
//     },
//     {
//       "@type":"ListItem",
//       "position":2,
//       "url":"http://example.com/apple_pie.html"
//     },
//     {
//       "@type":"ListItem",
//       "position":3,
//       "url":"http://example.com/blueberry-pie.html"
//     }
//   ]
// }
// </script>

class CNH_Structured_Data
{

    private $count = 1;

    private $metadata = array(
        '@context'  => 'https://schema.org',
        '@type'     => 'ItemList',
        'itemListElement' => array(),
    );

    public function populate_entry_metadata()
    {
        if (is_archive() || is_search() || is_tag()) {
            $element = array();
            $element['@type'] = 'ListItem';
            $element['position'] = $this->count++;
            $element['url'] = get_permalink();
            $this->metadata['itemListElement'][] = $element;
        }
    }

    public function output_metadata()
    {
        if ( is_archive() || is_search() || is_tag() ) {
            $json = json_encode($this->metadata, JSON_UNESCAPED_SLASHES);
            ?>
            <script type="application/ld+json">
            <?= $json; ?>
            </script>
            <?php
        }
    }
}
