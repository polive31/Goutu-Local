<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CSD_Meta
{
    private $template;
    private $schema;
    protected static $instance = array();

    public function __construct( $template ) {
        $this->template = $template;
        $this->schema = CSD_Assets::get_schema($this->template);
        if (!$this->schema) {
            CSD_Assets::hydrate();
        }
        $this->schema = CSD_Assets::get_schema($this->template);
    }

    public static function get_instance( $template )
    {
        if ( empty(self::$instance[$template]) ) {
            self::$instance[$template] = new self($template);
        }
        return self::$instance[$template];
    }

    public function render()
    {
        $meta = array();
        $meta = apply_filters("csd_enqueue_{$this->template}_meta", $meta);
        if (empty($meta)) return;

        $meta = $this->sanitize_metadata($meta);
        $html = '<script type="application/ld+json">' . json_encode($meta) . '</script>';
        echo $html;
    }

    public function set(&$meta, $data, $schema=false) {
        if ( !$this->is_output_here() ) return;
        /* If $schema false, then use the class property (means that we are called by the filter callback
            and not in a recursive function call) */
        $schema = $schema?$schema:$this->schema;
        /* If $schema still unavailable, then it might not have been initialized properly
            try and retrieve it from CSD_Assets */
        $schema = $schema?$schema:CSD_Assets::get_schema($this->template);

        foreach ($schema as $property => $value) {
            if ( $this->is_field($value)) {
                $field = $this->get_field($value);
                if ( isset($data[$field]) )
                    $meta[$property]= $data[$field];
                else {
                    $err_msg = sprintf( '%s field missing from supplied data.', print_r($field, true) );
                    trigger_error( $err_msg, E_USER_NOTICE);
                    $meta[$property]=null;
                }
            }
            elseif ( !$this->is_tag($property) ) {
                if ( is_array($value) ) {
                    // Explore lower level of the schema
                    $meta[$property]=array();
                    $this->set( $meta[$property], $data, $schema[$property] );
                }
                elseif ( $value && !empty($value) )
                    // Only non-empty values will  be published
                    $meta[$property] = $value;
            }
        }
    }

    private function is_tag( $property ) {
        $istag = false;
        if ( strpos( $property, '<')!== false ) {
            $istag=true;
        }
        return $istag;
    }

    private function get_tag( $property )
    {
        $tag = trim($property, '<>');
        return $tag;
    }

    private function is_field($value) {
        if (is_array($value)) return false;
        $isfield = ( strpos($value, CSD_Assets::FIELD_DELIMITER)!== false);
        return $isfield;
    }

    private function get_field($value) {
        $field = trim($value, CSD_Assets::FIELD_DELIMITER );
        return $field;
    }

    public function is_output_here()
    {
        $output = false;

        if ($this->schema && isset($this->schema['<location>']))
            $conditions = $this->schema['<location>'];

        foreach ($conditions as $condition => $values) {
            if ($condition == 'singular')
                $thismet = is_singular($values);
            elseif ($condition == 'single')
                $thismet = is_single($values);
            elseif ($condition == 'archive')
                $thismet = is_archive($values);
            elseif ($condition == 'search')
                $thismet = is_search();
            elseif ($condition == 'tag')
                $thismet = is_tag($values);
            elseif ($condition == 'page')
                $thismet = is_page($values);
            $output = $output || $thismet;
        }
        return $output;
    }


    private function sanitize_metadata($metadata)
    {
        $sanitized = array();
        if (is_array($metadata)) {
            foreach ($metadata as $key => $value) {
                $sanitized[$key] = $this->sanitize_metadata($value);
            }
        } else
            $sanitized = strip_shortcodes(wp_strip_all_tags($metadata));
        return $sanitized;
    }

}
