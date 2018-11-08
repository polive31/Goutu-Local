<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_WPURP_Ingredient {

	public static $MONTHS;
    public static $UNITS = array(); 

    private $ingredient_meta;

    CONST VOWELS = array('a','e','i','o','u');
    CONST EXCEPTIONS = array('huile','herbes');
    CONST UNITS_LIST = array(
        array('g'     , 'g'),
        array('kg'    , 'kg'),
        array('ml'    , 'ml'),
        array('cl'    , 'cl'),
        array('dl'     , 'dl'),
        array('l'     , 'l'),
        array('cuillère à café'   , 'cuillerées à café'),
        array('cuillère à soupe'   , 'cuillerées à soupe'),
        array('baton' , 'batons'), //Baton
        array('boîte'   , 'boîtes'), //Boite
        array('bol'   , 'bols'), //Bols
        array('botte'   , 'bottes'), //Bottes
        array('bouquet'   , 'bouquets'), //Bouquet
        array('brin'   , 'brins'), //Brin
        array('branche'   , 'branches'), //Branche (thym)
        array('bulbe'   , 'bulbes'), //Bulbe
        array('cube' , 'cubes'), //Cube
        array('doigt', 'doigts'), //Doigt
        array('feuille' , 'feuilles'),  //Feuille
        array('filet'   , 'filets'), //Filet (anchois)
        array('gousse' , 'gousses'), // Gousse
        array('noix'   , 'noix'), //Noix
        array('pavé' , 'pavés'), // Pincée
        array('pincée' , 'pincées'), // Pincée
        array('poignée', 'poignées'), //Poignée
        array('sachet', 'sachets'), //Sachet
        array('tasse'   , 'tasses'), //Tasse
        array('tranche'   , 'tranches'), //Tranche
        array('verre'   , 'verres'), //Verre
    );      	

	public function __construct() {
		// parent::__construct();
		add_action( 'init', array($this, 'hydrate' ));
		add_action( 'ingredient_add_form_fields', array($this, 'taxonomy_add_months_field'), 10, 2 );
		add_action( 'ingredient_add_form_fields', array($this, 'taxonomy_add_plural_field'), 10, 2 );
		add_action( 'ingredient_edit_form_fields', array($this, 'taxonomy_edit_fields'), 10, 2 );
		// add_action( 'ingredient_edit_form_fields', array($this, 'taxonomy_edit_plural_field'), 10, 2 );
		add_action( 'edited_ingredient', array($this, 'save_meta'), 10, 2 );  
		add_action( 'create_ingredient', array($this, 'save_meta'), 10, 2 );
		// Shortcode
		add_shortcode( 'display-ingredient', array( $this, 'display_ingredient_shortcode' ) );		
	}

	public function hydrate() {
		self::$MONTHS = array(
			__('January','foodiepro'),
			__('February','foodiepro'),
			__('March','foodiepro'),
			__('April','foodiepro'),
			__('May','foodiepro'),
			__('June','foodiepro'),
			__('July','foodiepro'),
			__('August','foodiepro'),
			__('September','foodiepro'),
			__('October','foodiepro'),
			__('November','foodiepro'),
			__('December','foodiepro')
		);
        // foreach (self::UNITS_LIST as $unit) {
        //     self::$UNITS[sanitize_title($unit[0])]=array($unit[0],$unit[1]);
        // }		
	}

    public function display_ingredient_shortcode( $options ) {
        $options = shortcode_atts( array(
            'amount' => '', 
            'amount_normalized' => '', 
            'unit' => '',
            'ingredient' => '',
            'notes' => '',
            'links' => 'yes',
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

    public static function is_initial_vowel( $expression ) {  
        if (empty($expression)) return false;
        $name = remove_accents( $expression );
        $first_letter = $name[0];
        $first_word = strtolower( explode(' ', trim($name))[0] );
        return ( in_array($first_letter, self::VOWELS) || in_array( $first_word, self::EXCEPTIONS) );
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
        if ($taxonomy) {
	        // $taxonomy_slug = is_object( $taxonomy ) ? $taxonomy->slug : $args['ingredient_name'];
	        $taxonomy_slug = $taxonomy->slug;
	        $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' );
        // $isplural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'isplural' );
	        $plural = is_array( $plural ) ? false : $plural;
	        $plural = is_array( $plural ) ? false : $plural;
	        ////PC::debug( array('Plural array'=>$plural) );  
        }

        $plural_data = $plural ? ' data-singular="' . esc_attr( $args['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';
        $out .= ' <span class="wpurp-recipe-ingredient-name recipe-ingredient-name"' . $plural_data . '>';

        // echo '<pre>' . print_r("is plural : " . $isplural) . '</pre>';

        // INGREDIENT "OF"
        if ($unit != '') {
	        if ( self::is_initial_vowel($args['ingredient']) )
	            $out .= _x('of ','vowel','foodiepro');
	        else 
	            $out .= _x('of ','consonant','foodiepro');                  
        }

        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

        $closing_tag = '';
		
		$hide_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) == '1';

        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' && !$hide_link) {

            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            } else {
                $custom_link = false;
            }

            // if( isset($args['links']) &&  ($args['links'] == 'yes') ) {
            if( $custom_link !== false && $custom_link !== '' ) {
                $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

            	$out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
            	$closing_tag = '</a>';

            } else if( $ingredient_links != 'custom' ) {
                $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
                $closing_tag = '</a>';
            }
            // }   
        }

        // $out .= $plural && ($ingredient['unit']!='' || $ingredient['amount_normalized'] > 1) ? $plural : $ingredient['ingredient'];
        $out .= ($plural && ($amount_normalized > 1 || $unit != '' || (empty($amount_normalized) && empty($unit)) )) ? $plural : $args['ingredient'];
        $out .= $closing_tag;
        $out .= '</span>';

        // INGREDIENT "NOTES"
        if( $args['notes'] != '' ) {
            $out .= ' ';
            $out .= '<span class="wpurp-recipe-ingredient-notes recipe-ingredient-notes">'.$args['notes'].'</span>';
        }

        return $out;
    }    	

	public function taxonomy_edit_fields($term) {
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$ingredient_meta = get_option( "taxonomy_$t_id" ); 
		$this->taxonomy_edit_months_field($term, $ingredient_meta);
		$this->taxonomy_edit_isplural_field($term, $ingredient_meta);
	}

	// Edit term page
	public function taxonomy_edit_isplural_field($term, $ingredient_meta) {
		// put the term ID into a variable
		// echo '<pre>' . print_r($ingredient_meta) . '<br></pre>';
	 	?>
	 	<tr class="form-field">
		<th scope="row" valign="top">
		<label for="wpurp_taxonomy_metadata_ingredientisplural"><?php echo __('Always plural ?','foodiepro');?></label>
		<td>	
			<?php
				// echo '<pre>' . print_r(self::$MONTHS) . '</pre>';
				// echo '<pre>' . print_r($month) . '<br></pre>';
				$checked = isset($ingredient_meta['isplural']);
			?>
			<div class="form-field">
				<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[isplural]" id="wpurp_taxonomy_metadata_ingredientisplural" title="always_plural" <?php echo $checked?"checked":"";?>  >
			</div>
			<p class="description"><?php _e( 'Check whenever this ingredient should always displayed in its plural form','foodiepro' ); ?></p>
		</td>
		</th>
		</tr>

		<?php
	}	

	// Edit term page
	public function taxonomy_edit_months_field($term, $ingredient_meta) {

	 	?>
	 	<tr class="form-field">
		<th scope="row" valign="top">
		<label for="wpurp_taxonomy_metadata_ingredient_months"><?php echo __('Months','foodiepro');?></label>
		<td>
			<table>
				<tr>		
				<?php
				$i=1;
				foreach (self::$MONTHS as $month) {	
				// echo '<pre>' . print_r(self::$MONTHS) . '</pre>';
					// echo '<pre>' . print_r($month) . '<br></pre>';
					$checked = isset($ingredient_meta['month'][$i]);
					?>
					<td>
					<div class="form-field">
						<label for="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" title="<?php echo $month;?>"><?php echo $month[0]; ?></label>
						<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" id="wpurp_taxonomy_metadata_ingredientmonth<?php echo $i;?>" title="<?php echo $month;?>" <?php echo $checked?"checked":"";?>  >
					</div>
					</td>
					<?php
					$i++;
				}?>
				</tr>
			</table>
			<p class="description"><?php _e( 'Check the months when this ingredient is available','foodiepro' ); ?></p>
		</td>
		</th>
		</tr>

		<?php
	}


	// Add term page
	public function taxonomy_add_plural_field() {
		// this will add the custom meta field to the add new term page
		?>
		<label for="wpurp_taxonomy_metadata_ingredientisplural"><?php echo __('Always plural ?','foodiepro');?></label>
		<div class="form-ingredient-plural">
			<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[isplural]" id="wpurp_taxonomy_metadata_ingredientisplural" value="available" title="always_plural" >
		</div>
		<p class="description"><?php _e( 'Check whenever this ingredient should always displayed in its plural form','foodiepro' ); ?></p>
		<?php
	}

	// Add term page
	public function taxonomy_add_months_field() {
		// this will add the custom meta field to the add new term page
		?>
		<label for="wpurp_ctm_ingredient_month"><?php echo __('Months','foodiepro');?></label>
		<!-- <table> -->
		<!-- <tr> -->
		<?php
		$i=1;
		foreach (self::$MONTHS as $month) {	
		// echo '<pre>' . print_r(self::$MONTHS) . '</pre>';
			// echo '<pre>' . print_r($month) . '<br></pre>';
			?>
			<!-- <td> -->
			<div class="form-ingredient-month">
				<label for="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" title="<?php echo $month;?>"><?php echo $month[0]; ?></label>
				<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" id="wpurp_taxonomy_metadata_ingredientmonth<?php echo $i;?>" value="available" title="<?php echo $month;?>" >
			</div>
			<!-- </td> -->
			<?php
			$i++;
		}?>
		<!-- </tr> -->
		<!-- </table> -->
		<p class="description"><?php _e( 'Check the months when this ingredient is available','foodiepro' ); ?></p>
		<?php
	}


	// Get taxonomy field.
    public function get_meta( $term ) {
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		return get_option( "taxonomy_$t_id" ); 
    }

	// Save extra taxonomy fields callback function.
	public function save_meta( $term_id ) {
		if ( isset( $_POST['wpurp_taxonomy_metadata_ingredient'] ) ) {
			// $t_id = $term_id;
			$this->ingredient_meta = get_option( "taxonomy_$term_id" );

			$this->update_month();
			$this->update_isplural();

			// Save the option array.
			update_option( "taxonomy_$term_id", $this->ingredient_meta );
		}
	}  

	public function update_month() {
		$i=1;
		foreach ( self::$MONTHS as $month ) {
			if ( isset ( $_POST['wpurp_taxonomy_metadata_ingredient']['month'][$i] ) ) 
				$this->ingredient_meta['month'][$i] = $_POST['wpurp_taxonomy_metadata_ingredient']['month'][$i];
			elseif ( isset($this->ingredient_meta['month'][$i]) )
				unset($this->ingredient_meta['month'][$i]);
			$i++;
		}
	}

	public function update_isplural() {
		if ( isset ( $_POST['wpurp_taxonomy_metadata_ingredient']['isplural'] ) ) 
			$this->ingredient_meta['isplural'] = $_POST['wpurp_taxonomy_metadata_ingredient']['isplural'];
		elseif ( isset($this->ingredient_meta['isplural']) )
			unset($this->ingredient_meta['isplural']);
	}


}
