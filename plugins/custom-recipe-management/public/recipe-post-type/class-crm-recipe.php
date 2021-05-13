<?php

class CRM_Recipe {

    const META_FIELDS = array(
        'recipe_servings'      => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_servings_type' => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_prep_time'     => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_cook_time'     => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_passive_time'  => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_prep_time_text'     => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_cook_time_text'     => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_passive_time_text'  => array(
            'storage'  => 'scalar',
            'input'  => 'text',
        ),
        'recipe_ingredients'   => array(
            'storage'  => 'array',
            'input'  => 'text',
        ),
        'recipe_instructions'  => array(
            'storage'  => 'array',
            'input'  => 'textarea',
        ),
        'recipe_notes'         => array(
            'storage'   => 'scalar',
            'input'  => 'textarea',
        ),
    );

    const TEMPLATE = array(
        'ingredient'  => array(
            'amount'        => '',
            'unit'          => '',
            'ingredient'    => '',
            'notes'         => '',
            'group'         => '',
        ),
        'instruction'  => array(
            'description'   => '',
            'group'         => '',
            'image'         => '',
            'video'         => '',
        )
    );

    private $post;
    private $meta;
    private static $instanceID;

    public function __construct($post=null)
    {

        // Get associated post
        if ( empty($post) || is_numeric($post) )  {
            $this->post=get_post( $post );
        } else if( is_object( $post ) && $post instanceof WP_Post ) {
            $this->post = $post;
        } else {
            throw new InvalidArgumentException( 'invalid Recipe instantiation.' );
        }

        // Get metadata
        $this->meta = get_post_custom( $this->post->ID );
    }

    public function is_present( $field )
    {
        $nutrition_field = '';
        if( substr( $field, 0, 16 ) == 'recipe_nutrition' ) {
            $nutrition_field = substr( $field, 17 );
            $field = 'recipe_nutrition';
        }

        switch( $field ) {
            case 'recipe_image':
                return $this->image_ID();

            case 'recipe_featured_image':
                return get_post_thumbnail_id( $this->ID() ) != '';

            case 'recipe_ingredients':
                return $this->has_ingredients();

            case 'recipe_instructions':
                return $this->has_instructions();

            case 'recipe_post_content':
                return trim( $this->post_content() ) != '';

            case 'recipe_nutrition':
                $val = $this->nutritional( $nutrition_field );
                return isset( $val ) && trim( $val ) != '';

            default:
                $val = $this->meta($field);
                return isset( $val ) && trim( $val ) != '';
        }
    }


    /* FIELD GETTERS & SETTERS
    -----------------------------------------------------------------*/
    public function fields()
    {
        return array_keys(self::META_FIELDS);
    }

    public function format( $field ) {
        if (isset (self::META_FIELDS[$field]['storage']))
            return self::META_FIELDS[$field]['storage'];
        return false;
    }

    public function input_type( $field ) {
        if (isset (self::META_FIELDS[$field]['input']))
            return self::META_FIELDS[$field]['input'];
        return false;
    }


    public function get( $field ) {
        if ( $this->format($field)=='scalar' )
            return $this->meta($field);
        elseif ($this->format($field) == 'array' )
            return @unserialize( $this->meta($field) );
        return false;
    }

    public function meta( $field )
    {
        if( isset( $this->meta[$field] ) ) {
            return $this->meta[$field][0];
        }
        return null;
    }

    public function set( $field, $value )
    {
        update_post_meta($this->ID(), $field, $value);
    }

    public function delete( $field, $value )
    {
        delete_post_meta($this->ID(), $field, $value);
    }




    /* TIME
    -----------------------------------------------------------------------*/
    private function minutes2dhm($minutes)
    {
        $days = floor($minutes / 1440);
        $minutes = $minutes % 1440;

        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;

        $dhm = sprintf('P%dDT%dH%dM', $days, $hours, $minutes);
        return $dhm;
    }


    /* Legacy time getters */
    public function passive_time() {return $this->meta('recipe_passive_time');}
    public function prep_time(){return $this->meta('recipe_prep_time');}
    public function cook_time(){return $this->meta('recipe_cook_time');}
    public function passive_time_text(){return $this->meta('recipe_passive_time_text');}
    public function prep_time_text(){return $this->meta('recipe_prep_time_text');}
    public function cook_time_text() {return $this->meta('recipe_cook_time_text');}

    public function prep_time_meta()
    {
        $meta = false;
        $amount = esc_attr($this->prep_time());
        $unit = strtolower($this->prep_time_text());
        $meta = $this->get_time_meta_string($amount, $unit);
        return $meta;
    }

    public function cook_time_meta()
    {
        $meta = false;
        $amount = esc_attr($this->cook_time());
        $unit = strtolower($this->cook_time_text());
        $meta = $this->get_time_meta_string($amount, $unit);
        return $meta;
    }

    public function get_time_meta_string($amount, $unit)
    {
        $meta = false;
        $amount = floatval($amount);
        if (
            strtolower($unit) == strtolower(__('minute', 'wp-ultimate-recipe'))
            || strtolower($unit) == strtolower(__('minutes', 'wp-ultimate-recipe'))
            || strtolower($unit) == 'min'
            || strtolower($unit) == 'mins'
        ) {
            $meta = 'PT' . $amount . 'M';
        } elseif (
            strtolower($unit) == strtolower(__('hour', 'wp-ultimate-recipe'))
            || strtolower($unit) == strtolower(__('hours', 'wp-ultimate-recipe'))
            || strtolower($unit) == 'hr'
            || strtolower($unit) == 'hrs'
        ) {
            $meta = 'PT' . $amount . 'H';
        }
        return $meta;
    }


    /* INGREDIENTS
    --------------------------------------------------------*/
    public static function get_ingredient_item()
    {
        return self::TEMPLATE['ingredient'];
    }

    public function has_ingredients()
    {
        $ingredients = $this->ingredients();
        return !empty($ingredients);
    }

    public function ingredients()
    {
        $ingredients = @unserialize( $this->meta( 'recipe_ingredients' ) );

        // Try to fix serialize offset issues
        if( $ingredients === false ) {
            $ingredients = unserialize( preg_replace_callback ( '!s:(\d+):"(.*?)";!', array( $this, 'regex_replace_serialize' ), $this->meta( 'recipe_ingredients' ) ) );
        }

        // return apply_filters( 'wpurp_recipe_field_ingredients', $ingredients, $this );
        return $ingredients;
    }

    public function ingredient_count() {
        $count=(is_array($this->ingredients()))?count($this->ingredients()):0;
        return $count;
    }

    /* INSTRUCTIONS
    ---------------------------------------------------------------------*/
    public static function get_instruction_item($visible=true) {
        $instruction=self::TEMPLATE['instruction'];
        $instruction['visible']=$visible;
        return $instruction;
    }

    public function has_instructions()
    {
        $instructions = $this->instructions();
        return !empty($instructions);
    }

    public function instructions()
    {
        $instructions = @unserialize( $this->meta( 'recipe_instructions' ) );

        // Try to fix serialize offset issues
        if( $instructions === false ) {
            $instructions = unserialize( preg_replace_callback ( '!s:(\d+):"(.*?)";!', array( $this, 'regex_replace_serialize' ), $this->meta( 'recipe_instructions' ) ) );
        }

        // return apply_filters( 'wpurp_recipe_field_instructions', $instructions, $this );
        return $instructions;
    }

    /**
     * Avoids constructing the whole CRM_Recipe class just for updating/adding one image
     *
     * @param  mixed $index
     * @param  mixed $attach_id
     * @return void
     */
    public static function add_instruction_image($post_id, $index, $attach_id)
    {
        $instructions = get_post_meta($post_id, 'recipe_instructions', true);
        if (empty($instructions)) {
            $instructions=array();
        }
        if (!isset($instructions[$index])) {
            $instructions[$index] = self::TEMPLATE['instruction'];
        };
        $instructions[$index]['image'] = $attach_id;
        $result = update_post_meta($post_id, 'recipe_instructions', $instructions);
        // clean_post_cache($post_id); // seems to only work with the term cache, not the meta ???

        return $result;
    }

    /**
     * Avoids constructing the whole CRM_Recipe class just for removing one image
     *
     * @param  mixed $index
     * @param  mixed $attach_id
     * @return void
     */
    public static function delete_instruction_image($post_id, $index)
    {
        $instructions = get_post_meta($post_id, 'recipe_instructions', true);
        if (!isset($instructions[$index])) return false;
        $instructions[$index]['image'] = '';
        $result = update_post_meta($post_id, 'recipe_instructions', $instructions);
        // clean_post_cache($post_id); // seems to only work with the term cache, not the meta ???

        return $result;
    }


    /* IMAGE
    -----------------------------------------------------------------*/
    public function featured_image()
    {
        return get_post_thumbnail_id($this->ID());
    }

    public function featured_image_url($type)
    {
        $thumb = wp_get_attachment_image_src($this->featured_image(), $type);
        return $thumb['0'];
    }

    public function ID()
    {
        return $this->post->ID;
    }

    public function image_url($type)
    {
        $thumb = wp_get_attachment_image_src($this->image_ID(), $type);
        return $thumb['0'];
    }

    public function image_ID()
    {
        // if( WPUltimateRecipe::option( 'recipe_alternate_image', '1' ) == '1' ) {
        //     $image_id = $this->alternate_image() ? $this->alternate_image() : $this->featured_image();
        // } else {
        $image_id = $this->featured_image();
        // }
        return $image_id;
    }

    public function alternate_image()
    {
        return $this->meta('recipe_alternate_image');
    }

    public function alternate_image_url($type)
    {
        $thumb = wp_get_attachment_image_src($this->alternate_image(), $type);
        return $thumb ? $thumb['0'] : '';
    }



    /* MISC GETTERS
    ----------------------------------------------------------------------- */

    public function regex_replace_serialize( $match )
    {
        return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    }

    public function link()
    {
        return get_permalink( $this->ID() );
    }

    public function link_print()
    {
        $link = $this->link();
        $link = trailingslashit($link) . CRM_Assets::keyword() . '/' . $this->servings_normalized();
        return $link;
    }

    public function notes()
    {
        return $this->meta( 'recipe_notes' );
    }

    public function nutritional( $field = false )
    {
        $nutritional = apply_filters( 'wpurp_recipe_field_nutritional', unserialize( $this->meta( 'recipe_nutritional' ) ), $this );

        if( $field ) {
            $nutritional = isset( $nutritional[$field] ) ? $nutritional[$field] : '';
        }

        return $nutritional;
    }

    public function legacy()
    {
        return empty($this->post->post_content);
    }

    public function post_content()
    {
        return $this->post->post_content;
    }

    public function description()
    {
        return $this->meta('recipe_description');
    }

    public function post_status()
    {
        return $this->post->post_status;
    }

    public function author()
    {
        $author_id = $this->post->post_author;

        if ($author_id == 0) {
            return $this->meta('recipe-author');
        } else {
            $author = get_userdata($this->post->post_author);

            return $author->data->display_name;
        }
    }

    public function date()
    {
        return $this->post->post_date;
    }


    public function excerpt()
    {
        return $this->post->post_excerpt;
    }


    public function servings()
    {
        return $this->meta( 'recipe_servings' );
    }

    public function servings_normalized()
    {
        return $this->meta( 'recipe_servings_normalized' );
    }

    public function servings_type()
    {
        return $this->meta( 'recipe_servings_type' );
    }

    // public function template()
    // {
    //     $template = $this->meta( 'recipe_custom_template' );
    //     return is_null( $template ) ? 'default' : $template;
    // }

    public function terms()
    {
        return unserialize( $this->meta( 'recipe_terms' ) );
    }

    public function terms_with_parents()
    {
        return unserialize( $this->meta( 'recipe_terms_with_parents' ) );
    }

    public function title()
    {
        if ( $this->meta( 'recipe_title' ) ) {
            return $this->meta( 'recipe_title' );
        } else {
            return $this->post->post_title;
        }
    }

    public function has_video()
    {
        return $this->video_id() ? true : false;
    }

    public function video_id()
    {
        $video_id = $this->meta( 'recipe_video_id' );
        return $video_id ? $video_id : '';
    }

    public function video_thumb( $show_image = false )
    {
        $video_thumb = $this->meta( 'recipe_video_thumb' );

        if ( $show_image ) {
            return $video_thumb ? '<img src="' . $video_thumb . '">' : '';
        } else {
            return $video_thumb ? $video_thumb : '';
        }
    }

    public function video_data() {
		return wp_get_attachment_metadata( $this->video_id() );
    }

    public function video_url() {
		return wp_get_attachment_url( $this->video_id() );
    }

    public function video_thumb_url( $size = 'thumbnail' ) {
		$image_id = get_post_thumbnail_id( $this->video_id() );
		$thumb = wp_get_attachment_image_src( $image_id, $size );
        $image_url = $thumb && isset( $thumb[0] ) ? $thumb[0] : '';

		return $image_url;
	}

    public function video()
    {
        if ( ! $this->has_video() ) {
            return '';
        }

        $video_data = $this->video_data();
        $output = '[video';
        $output .= ' width="' . $video_data['width'] . '"';
        $output .= ' height="' . $video_data['height'] . '"';

        $format = isset( $video_data['fileformat'] ) && $video_data['fileformat'] ? $video_data['fileformat'] : 'mp4';
        $output .= ' ' . $format . '="' . $this->video_url() . '"';

        $thumb_size = array( $video_data['width'], $video_data['height'] );
        $thumb_url = $this->video_thumb_url( $thumb_size );

        if ( $thumb_url ) {
            $output .= ' poster="' . $thumb_url . '"';
        }

        $output .= '][/video]';

        return do_shortcode( $output );
    }

    public function video_metadata() {
		$metadata = false;

		if ( $this->video_id() ) {
			$attachment = get_post( $this->video_id() );
			$video_data = $this->video_data();

			$image_sizes = array(
				$this->video_thumb_url( 'full' ),
			);
			$image_sizes = array_values( array_unique( $image_sizes ) );

			$metadata = array(
				'name' => $attachment->post_title,
				'description' => $attachment->post_content,
				'thumbnailUrl' => $image_sizes,
				'contentUrl' => $this->video_url(),
				'uploadDate' => date( 'c', strtotime( $attachment->post_date ) ),
				'duration' => 'PT' . $video_data['length'] . 'S',
			);
		}

		return $metadata;
	}

    /* PUBLIC Functions
    -------------------------------------------------------------*/
    public function days($type)
    {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta($meta);
        return floor((int) $minutes / 24 / 60);
    }

    public function hours($type)
    {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta($meta);
        return floor((int) $minutes % 1440 / 60);
    }

    public function minutes($type)
    {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta($meta);
        return (int) $minutes % 60;
    }


    /**
     * User by CRM Recipe Meta class to generate JSON+LD recipe structured data
     *
     * @param  mixed $type
     * @return void
     */
    public function get_time_meta($type)
    {
        $meta = false;

        $get_legacy_time_text = "{$type}_time_text";
        if (!empty($this->$get_legacy_time_text())) {
            $get_legacy_time_meta = "{$type}_time_meta";
            $meta = $this->$get_legacy_time_meta();
        } else {
            $meta_name = "recipe_{$type}_time";
            $minutes = $this->meta($meta_name);
            if ($minutes)
                $meta = $this->minutes2dhm($minutes);
        }
        return $meta;
    }

    /**
     * User by CRM Recipe Meta class to generate JSON+LD recipe structured data
     *
     * @return void
     */
    public function get_total_time_meta()
    {
        $meta = false;
        $cooktime = (int) $this->meta('recipe_cook_time');
        $preptime = (int) $this->meta('recipe_prep_time');
        $passivetime = (int) $this->meta('recipe_passive_time');

        // Total time in minutes
        $totaltime = $cooktime + $preptime + $passivetime;
        if ($totaltime) return '';
        $meta = $this->minutes2dhm($totaltime);
        return $meta;
    }


    /**
     * time
     *
     * @param  mixed $days
     * @param  mixed $hours
     * @param  mixed $minutes
     * @return void
     */
    public function time($days, $hours, $minutes)
    {
        return $days * 24 * 60 + $hours * 60 + $minutes;
    }

    /**
     * Public function for outputting recipe durations in recipe screen & print templates
     *
     * @param  mixed $time
     * @return void
     */
    public function output_time($time)
    {
        $html = '';

        $get_legacy_time_text = "{$time}_time_text";
        $legacy_time_text = $this->$get_legacy_time_text();

        if (!empty($legacy_time_text)) {
            $get_legacy_time = "{$time}_time";
            $legacy_time = $this->$get_legacy_time();
            $html = $legacy_time . ' ' . $legacy_time_text;
        } else {
            if (!empty($this->days($time))) {
                $html = sprintf(_n('%s day ', '%s days ', $this->days($time), 'crm'), $this->days($time));
            }
            if (!empty($this->hours($time))) {
                $html .= sprintf(_n('%s hour ', '%s hours ', $this->hours($time), 'crm'), $this->hours($time));
                $html .= empty($this->minutes($time)) ? '' : sprintf('%02d', $this->minutes($time));
            } else {
                $html .= empty($this->minutes($time)) ? '' : sprintf(_n('%s minute', '%s minutes', $this->minutes($time), 'crm'), $this->minutes($time));
            }
        }

        if (empty($html)) $html = false;
        return $html;
    }

}
