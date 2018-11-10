<?php

class Custom_Recipe extends WPURP_Recipe {

    private $extfields = array(
        'recipe_prep_time_days',
        'recipe_prep_time_hours',
        'recipe_prep_time_minutes',
        'recipe_cook_time_days',
        'recipe_cook_time_hours',
        'recipe_cook_time_minutes',
        'recipe_passive_time_days',
        'recipe_passive_time_hours',
        'recipe_passive_time_minutes',                
    );    

    public function __construct( $post )
    {
        parent::__construct( $post );
    }

    public function extfields()
    {
        return $this->extfields;
    }

    public function prep_time_days() {
        return $this->meta( 'recipe_prep_time_days' );
    }
    public function prep_time_hours() {
        return $this->meta( 'recipe_prep_time_hours' );
    }    
    public function prep_time_minutes() {
        return $this->meta( 'recipe_prep_time_minutes' );
    }
    public function cook_time_days() {
        return $this->meta( 'recipe_cook_time_days' );
    }
    public function cook_time_hours() {
        return $this->meta( 'recipe_cook_time_hours' );
    }    
    public function cook_time_minutes() {
        return $this->meta( 'recipe_cook_time_minutes' );
    }
    public function passive_time_days() {
        return $this->meta( 'recipe_passive_time_days' );
    }
    public function passive_time_hours() {
        return $this->meta( 'recipe_passive_time_hours' );
    }    
    public function passive_time_minutes() {
        return $this->meta( 'recipe_passive_time_minutes' );
    }        
}