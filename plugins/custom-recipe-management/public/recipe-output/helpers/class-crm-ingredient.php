<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Ingredient {

    const UNITS_LIST = array(
        array('g'               , 'g'),
        array('kg'              , 'kg'),
        array('ml'              , 'ml'),
        array('cl'              , 'cl'),
        array('dl'              , 'dl'),
        array('litre'           , 'litres'),
        array('cuillère à café' , 'cuillerées à café'),
        array('cuillère à soupe', 'cuillerées à soupe'),
        array('bol'   		    , 'bols'),
        array('boîte'   	    , 'boîtes'),
        array('botte'   	    , 'bottes'),
        array('bouquet'   	    , 'bouquets'),
        array('branche'   	    , 'branches'),
        array('brin'   		    , 'brins'),
        array('bulbe'   	    , 'bulbes'),
        array('cube' 		    , 'cubes'),
        array('capsule' 	    , 'capsules'),
        array('doigt'		    , 'doigts'),
        array('étoile'		    , 'étoiles'),
        array('feuille' 	    , 'feuilles'),
        array('filet'   	    , 'filets'),
        array('goutte'		    , 'gouttes'),
        array('gousse' 		    , 'gousses'),
        array('noix'   		    , 'noix'),
        array('pavé' 		    , 'pavés'),
        array('pincée' 		    , 'pincées'),
        array('poignée'		    , 'poignées'),
        array('sachet'		    , 'sachets'),
        array('tasse'   	    , 'tasses'),
        array('tige'   		    , 'tiges'),
        array('tubercule'       , 'tubercules'),
        array('tranche'   	    , 'tranches'),
        array('bâton' 		    , 'bâtons'),
        array('verre'   	    , 'verres'),
    );


	/* =================================================================*/
	/* = SHORTCODES
	/* =================================================================*/

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

    public function display_ingredient_months_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => false,
            'currentmonth' => false, // displays an arrow showing the current month
        ), $atts );

        if ($atts['id']) {
            $ingredient_id = $atts['id'];
        }
        elseif (is_tax('ingredient')) {
            $ingredient_id = get_queried_object()->slug;
        }
        else
            return false;

        $ingredient_months = get_term_meta($ingredient_id, 'month', true );
        if ( empty($ingredient_months) ) return '';

        $html = '<h2>' . __('Harvest Period','crm') . '</h2>';
        $html .= '<table class="ingredient-months">';
        $html .= '<tr>';
        $i=1;

        $months = CRM_Ingredient_Month::$MONTHS;
        foreach (  $months as $month ) {
            $available = in_array( $i, $ingredient_months )?'available':'';
            $html .= '<td class="' . $available . '" title="' . $month . '">' . $month[0] . '</td>';
            $i++;
        }
        $html .= '</tr>';
        $html .= '</table>';
        return $html;
    }

	/* =================================================================*/
	/* = HELPERS
	/* =================================================================*/

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

    public static function display( $args, $target='screen' ) {
        if ( empty($args['ingredient']) ) return false;
        $out = '';

        // amount
        $fraction = strpos($args['amount'], '/') === false ? false : true;
        $amount_normalized=isset($args['amount_normalized'])?$args['amount_normalized']:$args['amount'];

        // UNIT
        $unit = self::output_unit($args['unit'], $amount_normalized);

        // OUTPUT FIRST PART
        $out .= '<span class="recipe-ingredient-quantity-unit"><span class="recipe-ingredient-quantity" data-normalized="'. $amount_normalized .'" data-fraction="'.$fraction.'" data-original="'.$args['amount'].'">'.$args['amount'].' </span>';
        $out .= '<span class="recipe-ingredient-unit" data-original="'. $unit .'">'.$unit.'</span></span>';


        // INGREDIENT TAXONOMY TERM DATA
        $taxonomy = get_term_by('name', $args['ingredient'], 'ingredient');

        $plural=false;
        // $isplural=false;

        $taxonomy_slug = ($taxonomy && is_object( $taxonomy )) ? $taxonomy->slug : false;
		$plural = $taxonomy_slug?WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' ):'';
		$plural = is_array( $plural ) ? false : $plural;


        $plural_data = $plural ? ' data-singular="' . esc_attr( $args['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';
        $out .= ' <span class="recipe-ingredient-name"' . $plural_data . '>';

        // INGREDIENT "OF"
        if ($unit != '') {
	        if ( initial_is_vowel($args['ingredient']) )
	            $out .= _x('of ','vowel','crm');
	        else
	            $out .= _x('of ','consonant','crm');
        }

        $closing_tag = '';

        $hide_link_meta =  WPURP_Taxonomy_MetaData::get('ingredient', $taxonomy_slug, 'hide_link') == '1';
		$hide_link = $taxonomy_slug?$hide_link_meta:true;

        if ( !empty( $taxonomy ) && !$hide_link && !$target='print') {
            $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            if( isset($args['links']) &&  ($args['links'] == 'yes') ) {
	            if( $custom_link !== false && $custom_link !== '' ) {
	                $nofollow = '';
	            	$out .= '<a href="' . $custom_link . '" class="custom-ingredient-link" target="_blank"' . $nofollow . '>';
	            	$closing_tag = '</a>';
	            } else {
	                $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
	                $closing_tag = '</a>';
	            }
            }
        }

        $out .= '<span id="ingredient_name_root">';
        $out .= ($plural && self::is_plural( $amount_normalized, $unit)) ? $plural : $args['ingredient'];
        $out .= '</span>';
        $out .= $closing_tag;
        $out .= '</span>';

        // INGREDIENT "NOTES"
        if ( ! empty($args['notes']) )  {
            $out .= ' <span class="recipe-ingredient-notes">'.$args['notes'].'</span>';
        }

        return $out;
    }

    public static function is_plural( $amount, $unit ) {
    	$plural = $amount > 1 || $unit != '' || (empty($amount) && empty($unit));
    	return $plural;
    }


	/* =================================================================*/
	/* = CALLBACKS
	/* =================================================================*/
    public function query_current_month_ingredients( $args, $instance) {
        $args['meta_query'] = array(
                'key'     => 'mykey',     // Adjust to your needs!
                'value'   => 'myvalue',   // Adjust to your needs!
                'compare' => '=',         // Default
        );
    }

}
