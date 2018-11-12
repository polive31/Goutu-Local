<?php

class Custom_Recipe extends WPURP_Recipe {

    // private $extfields = array(
    //     'recipe_prep_time_days',
    //     'recipe_prep_time_hours',
    //     'recipe_prep_time_minutes',
    //     'recipe_cook_time_days',
    //     'recipe_cook_time_hours',
    //     'recipe_cook_time_minutes',
    //     'recipe_passive_time_days',
    //     'recipe_passive_time_hours',
    //     'recipe_passive_time_minutes',                
    // );    

    private $title;

    public function __construct( $post )
    {
        parent::__construct( $post );
        $this->title = array(
            'prep' => __('Preparation','foodiepro'),
            'cook' => __('Cooking','foodiepro'),
            'passive' => __('Wait','foodiepro'),
        );       
    }

    // Description output in json meta
    // In this case, we output the description based on either scheme : description meta 
    // or as part of the post's content (new scheme)
    public function description_meta() {
        $description = '';
        if ( !empty($this->description()) )
            $description = $this->description();
        else {
            $post = get_post( $this->ID() );
            $content = $post?$post->post_content:'';
            $description = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content));
        }
        return $description;
    }

    // Description output in recipe display
    // In this case, we don't output the description whenever it is already saved 
    // within the post's content (new scheme)
    // however if the target is the submission form, then the content is output
    public function output_description( $target='post' ) {
        $description = '';
        $post = get_post( $this->ID() );
        $content = $post?$post->post_content:'';
        $content = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content));
        if ( empty($content) )
            $description = $this->description();
        elseif ( $target=='form' ) {
            $description = $content;
        }
        return esc_html($description);
    }

    public function extfields() {
        return $this->extfields;
    }

    public function get_days( $type ) {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta( $meta );
        return floor($minutes/24/60);
    }

    public function get_hours( $type ) {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta( $meta );
        return floor($minutes%1440/60);
    }    

    public function get_minutes( $type ) {
        $meta = "recipe_{$type}_time";
        $minutes = $this->meta( $meta );
        return $minutes%60;
    }

    public function get_time( $days, $hours, $minutes ) {
        return $days*24*60+$hours*60+$minutes;
    }

    public function get_time_meta( $type ) {
        $meta = false;

        $get_legacy_time_text = "{$type}_time_text";
        if ( !empty($this->$get_legacy_time_text()) ) {
            $get_legacy_time_meta = "{$type}_time_meta";
            $meta = $this->$get_legacy_time_meta();
        }
        else {
            $meta_name = "recipe_{$type}_time";
            $minutes = $this->meta( $meta_name );        

            $days = floor($minutes / 1440);
            $minutes = $minutes % 1440;

            $hours = floor($minutes / 60);
            $minutes = $minutes % 60;    

            $meta = sprintf('P%dDT%dH%dM', $days, $hours, $minutes);
        }
        return $meta;
    }    

    public function output_time( $type ) {

        $html = '';

        $get_legacy_time_text = "{$type}_time_text";
        $legacy_time_text = $this->$get_legacy_time_text();

        if ( !empty( $legacy_time_text ) )  {
            $get_legacy_time = "{$type}_time";
            $legacy_time = $this->$get_legacy_time();
            $html .= $legacy_time . ' ' . $legacy_time_text;
        }
        else {
            if ( !empty($this->get_days($type)) ) {
                $html .= sprintf(_n('%s day ', '%s days ', $this->get_days($type), 'foodiepro'), $this->get_days($type));
            }
            if ( !empty($this->get_hours($type)) ) {
                $html .= sprintf(_n('%s hour ', '%s hours ', $this->get_hours($type), 'foodiepro'), $this->get_hours($type));
                $html .= empty($this->get_minutes($type))?'':sprintf('%02d', $this->get_minutes($type));
            } 
            else {                
                $html .= empty($this->get_minutes($type))?'':sprintf(_n('%s minute','%s minutes',$this->get_minutes($type),'foodiepro'),$this->get_minutes($type));
            }
            if ( empty($html) ) return '';
        }

        $html = '<div class="label-container" id="' . $type . '"><div class="recipe-label">' . $this->title[$type] . '</div>' . $html;
        $html .= '</div>';
        return $html;
    }


}