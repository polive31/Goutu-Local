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


class CNH_Archive_Meta
{
    private $count = 1;
    private $items=array();


    public function add_entry_to_items()
    {
        if (is_archive() || is_search() || is_tag() ) {
            $item = array();
            $item['@type'] = 'ListItem';
            $item['position'] = $this->count++;
            $item['url'] = get_permalink();
            $this->items['items'][] = $item;
        }
    }

    public function enqueue_archive_meta($meta)
    {
        $Archive_Meta = CSD_Meta::get_instance('archive');
        if (!$Archive_Meta->is_output_here()) return;

        // Prepare data array
        $data = $this->items;

        $Archive_Meta->set($meta, $data);
        return $meta;
    }

}
