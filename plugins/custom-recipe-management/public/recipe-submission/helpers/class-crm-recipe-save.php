<?php

class CRM_Recipe_Save extends CRM_Recipe {

    // private $recipe;
    private $post;

    public function __construct($post) {
        parent::__construct($post);
        $this->post = $post;
    }


    /**
     * Main function allowing to save all recipe specific post meta
     *
     * @return void
     */
    public function save_recipe_meta()
    {
        $this->convert_durations_before_save();

        $fields = $this->fields();
        foreach ( $fields as $field )
        {
            $old = $this->get( $field );

            $new = null;
            if (isset($_POST[$field]) ) {
                if ( $this->format($field) == 'scalar' ) {
                    if ( $this->input_type($field) == 'text' )
                        $new = sanitize_text_field( $_POST[$field] );
                    elseif ( $this->input_type($field) == 'url' )
                        $new = esc_url( $_POST[$field], array('https') );
                    else
                        $new = wp_kses_post( $_POST[$field] );
                }
                else {
                    // array fields are processed in dedicated functions below
                    $new = $_POST[$field];
                }
            }

            // Field specific adjustments
            if( isset( $new ) && $field == 'recipe_ingredients' )
                $new = $this->process_ingredients($new);
            elseif( isset( $new ) && $field == 'recipe_instructions' )
                $new = $this->process_instructions($old, $new);
            elseif( isset( $new ) && $field == 'recipe_servings' )
                $this->set( 'recipe_servings_normalized', $this->normalize_servings($new));

            // Update or delete meta data if changed
            if ( isset( $new ) && $new != $old )
            {
                // Generic save instruction for "standard" fields
                $this->set( $field, $new );
                if( $field == 'recipe_ingredients' ) {
                    $notice = '<strong>' . $this->post->post_title . ':</strong> <a href="'.admin_url( 'edit.php?post_type=recipe&page=wpurp_nutritional_information&limit_by_recipe=' . $this->ID() ).'">'. __( 'Update the Nutritional Information', 'wp-ultimate-recipe') .'</a>';
                    CRM_Notices::add_admin_notice( $notice );
                }
            }
            elseif ( $new == '' && $old )
                $this->delete( $field, $old);

        }
    }


    /* SAVE FIELD FUNCTIONS
    -----------------------------------------------------------------*/

    /**
     * process_instructions
     *
     * @param  mixed $old
     * @param  mixed $new
     * @return void
     */
    public function process_instructions($old, $new)
    {
        $non_empty_instructions = array();
        foreach ($new as $instruction) {

            $description = isset($instruction['description'])?trim($instruction['description']):'';
            $image = isset($instruction['image'])?trim($instruction['image']):'';
            $video = isset($instruction['video'])?trim($instruction['video']):'';

            if ( !empty( $description ) || !empty( $image ) || !empty( $video ) ) {
                $instruction['group']= sanitize_text_field($instruction['group']);
                $instruction['description']=sanitize_textarea_field($instruction['description']);
                $instruction['video']=esc_url($instruction['video'], array('https'));
                $non_empty_instructions[] = $instruction;
            }
        }
        return $non_empty_instructions;
    }

    /**
     * Ingredients names are saved as terms since they belong to the "ingredient" taxonomy
     * BUT ingredients data for the recipe (name, amount, unit, notes) are also saved as post meta
     *
     * @param  mixed $new
     * @return void
     */
    public function process_ingredients($new)
    {
        $non_empty_ingredients = array();
        foreach ($new as $ingredient) {
            if (trim($ingredient['ingredient']) != '') {
                $term = term_exists($ingredient['ingredient'], 'ingredient');
                $term_id = empty($term)?false:intval($term['term_id']);
                $ingredient['ingredient_id'] = $term_id;
                $ingredient['amount_normalized'] = CRM_Ingredient::normalize_amount($ingredient['amount']);
                $ingredient['group']= sanitize_text_field($ingredient['group']);
                $ingredient['unit']=sanitize_text_field($ingredient['unit']);
                $ingredient['notes']=foodiepro_esc($ingredient['notes']);
                $non_empty_ingredients[] = $ingredient;
            }
        }
        return $non_empty_ingredients;
    }


    /**
     * Add ingredients as terms in the "ingredient" taxonomy
     * Attaches those terms to the current recipe post being saved
     *
     * @return void
     */
    public function save_ingredient_terms() {
        $terms=array();
        $ingredients = $this->get( 'recipe_ingredients' );
        foreach ($ingredients as $ingredient) {
            $term = term_exists($ingredient['ingredient'], 'ingredient');
            if ( empty($term) ) {
                $term = wp_insert_term(sanitize_text_field($ingredient['ingredient']), 'ingredient');
            }
            if (is_wp_error($term)) {
                if (isset($term->error_data['term_exists'])) {
                    $term_id = intval($term->error_data['term_exists']);
                } else {
                    var_dump($term);
                }
            } else {
                $term_id = intval($term['term_id']);
            }
            $terms[] = $term_id;
        }
        wp_set_post_terms($this->ID(), $terms, 'ingredient');
    }

    /**
     * Updates $_POST inputs 'recipe_cook_time', 'recipe_prep_time', and 'recipe_passive_time'
     * based on the days, hours, minutes input fields
     *
     * @return void
     */
    public function convert_durations_before_save()
    {
        $types = array('prep', 'cook', 'passive');
        foreach ($types as $type) {
            $field = "recipe_{$type}_time";
            $days = isset($_POST["{$field}_days"]) ? (int) $_POST["{$field}_days"] : 0;
            $hours = isset($_POST["{$field}_hours"]) ? (int) $_POST["{$field}_hours"] : 0;
            $minutes = isset($_POST["{$field}_minutes"]) ? (int) $_POST["{$field}_minutes"] : 0;
            $time = $this->time($days, $hours, $minutes);
            if ( !empty($time) ) {
                $_POST[$field] = $time;
            }
        }
    }


    /* HELPERS
    -------------------------------------------------------------------------------------*/

    /**
     * Get normalized servings
     */
    public function normalize_servings( $servings )
    {
        $amount = CRM_Ingredient::normalize_amount( $servings );

        if( $amount == 0 ) {
            $amount = 4;
        }

        return $amount;
    }




}
