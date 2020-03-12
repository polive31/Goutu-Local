<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class CAM_Tax_Columns
{
    public function add_archive_headline_column( $columns ) {
        $columns['archive-headline'] = __( 'Archive Headline' );
        return $columns;
    }


    public function populate_archive_headline_column($out, $name, $term_id)
    {
        if ($name=='archive-headline') {
            $out = get_term_meta($term_id, 'headline', true);
            echo $out;
        }
    }

}
