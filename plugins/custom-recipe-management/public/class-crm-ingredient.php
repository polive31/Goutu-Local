<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Ingredient {

    public static $UNITS = array(); 

    const UNITS_LIST = array(
        array('g'     		, 'g'),
        array('kg'    		, 'kg'),
        array('ml'    		, 'ml'),
        array('cl'    		, 'cl'),
        array('dl'    		, 'dl'),
        array('l'     		, 'l'),
        array('cuillère à café'   , 'cuillerées à café'),
        array('cuillère à soupe'  , 'cuillerées à soupe'),
        array('bâton' 		, 'bâtons'), 
        array('boîte'   	, 'boîtes'), 
        array('bol'   		, 'bols'), 
        array('botte'   	, 'bottes'),
        array('bouquet'   	, 'bouquets'),
        array('brin'   		, 'brins'), 
        array('branche'   	, 'branches'),
        array('bulbe'   	, 'bulbes'), 
        array('cube' 		, 'cubes'), 
        array('capsule' 	, 'capsules'), 
        array('doigt'		, 'doigts'),
        array('feuille' 	, 'feuilles'),
        array('filet'   	, 'filets'),
        array('goutte'		, 'gouttes'), 
        array('gousse' 		, 'gousses'),
        array('noix'   		, 'noix'), 
        array('pavé' 		, 'pavés'),
        array('pincée' 		, 'pincées'),
        array('poignée'		, 'poignées'), 
        array('sachet'		, 'sachets'), 
        array('tasse'   	, 'tasses'),
        array('tige'   		, 'tiges'),
        array('tubercule'   , 'tubercules'),
        array('tranche'   	, 'tranches'), 
        array('verre'   	, 'verres'), 		
    );      	



	/* INGREDIENT */
	
    public function display_ingredient_shortcode( $options ) {
        $options = shortcode_atts( array(
            'amount' => '', 
            'amount_normalized' => '', 
            'unit' => '',
            'ingredient' => '',
            'notes' => '',
            'links' => 'no',
        ), $options );

        return self::display( $options );
    }


    public static function get_units( $plural ) {
        $column=$plural?1:0;
        return array_column(self::UNITS_LIST,$column);
        // echo '<pre>' . print_r( self::$UNITS ) . '</pre>'; 
    }

    public static function output_unit( $name, $amount ) {
        $index =  array_search( $name, array_column(self::UNITS_LIST,0));
        $plural = $index?self::UNITS_LIST[$index][1]:$name;
        return ($amount > 1)?$plural:$name;
    }

    public static function display( $args ) {
        if ( empty($args['ingredient']) ) return false;
        $out = '';
        
        // amount
        $fraction = strpos($args['amount'], '/') === false ? false : true;
        $amount_normalized=isset($args['amount_normalized'])?$args['amount_normalized']:$args['amount'];

        // UNIT
        $unit = self::output_unit($args['unit'], $amount_normalized);

        // OUTPUT FIRST PART 
        $out .= '<span class="recipe-ingredient-quantity-unit"><span class="wpurp-recipe-ingredient-quantity recipe-ingredient-quantity" data-normalized="'. $amount_normalized .'" data-fraction="'.$fraction.'" data-original="'.$args['amount'].'">'.$args['amount'].' </span>';
        $out .= '<span class="wpurp-recipe-ingredient-unit recipe-ingredient-unit" data-original="'. $unit .'">'.$unit.'</span></span>';


        // INGREDIENT TAXONOMY TERM DATA
        $taxonomy = get_term_by('name', $args['ingredient'], 'ingredient');
        
        $plural=false;
        // $isplural=false;

		$taxonomy_slug = ($taxonomy && is_object( $taxonomy )) ? $taxonomy->slug : false;
		$plural = $taxonomy_slug?WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' ):'';
		$plural = is_array( $plural ) ? false : $plural;


        $plural_data = $plural ? ' data-singular="' . esc_attr( $args['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';
        $out .= ' <span class="wpurp-recipe-ingredient-name recipe-ingredient-name"' . $plural_data . '>';

        // INGREDIENT "OF"
        if ($unit != '') {
	        if ( initial_is_vowel($args['ingredient']) )
	            $out .= _x('of ','vowel','foodiepro');
	        else 
	            $out .= _x('of ','consonant','foodiepro');                  
        }

        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

        $closing_tag = '';
		

		$hide_link = $taxonomy_slug? WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) == '1':true;

        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' && !$hide_link) {

            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            } else {
                $custom_link = false;
            }

            if( isset($args['links']) &&  ($args['links'] == 'yes') ) {
	            if( $custom_link !== false && $custom_link !== '' ) {
	                $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

	            	$out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
	            	$closing_tag = '</a>';

	            } else if( $ingredient_links != 'custom' ) {
	                $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
	                $closing_tag = '</a>';
	            }
            }   
        }

        $out .= ($plural && self::is_plural( $amount_normalized, $unit)) ? $plural : $args['ingredient'];
        $out .= $closing_tag;
        $out .= '</span>';

        // INGREDIENT "NOTES"
        if ( ! empty($args['notes']) )  {
            $out .= ' <span class="wpurp-recipe-ingredient-notes recipe-ingredient-notes">'.$args['notes'].'</span>';
        }

        return $out;
    }    	

    public static function is_plural( $amount, $unit ) {
    	$plural = $amount > 1 || $unit != '' || (empty($amount) && empty($unit));
    	return $plural;
    }



}