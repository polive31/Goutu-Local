<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Recipe_Add_To_Shopping_List extends WPURP_Template_Block {

    public function __construct( $type = 'recipe-add-to-shopping-list' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        
        $link_id='';
	      $classes = array();
        
        if( !is_user_logged_in() ) {
        	$link_id='join_us';
        } 
				else {
					$classes[] = 'logged-in';
	        $shopping_list_recipes = array();
	        if( isset( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) ) {
	            $shopping_list_recipes = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) );
	        }

	        $in_shopping_list = in_array( $recipe->ID(), $shopping_list_recipes );

	        $title_in=__('In my shopping list', 'foodiepro');
	        $title_add=__('Add to my shopping list', 'foodiepro');
	        if( $in_shopping_list ) {
	        	$classes[] = 'in-shopping-list';
	        	$title=$title_in;
	        	$title_alt=$title_add;
	        }
	        else {
	        	$title=$title_add;
	        	$title_alt=$title_in;
	        }

	        /*$tooltip_text = __('Add to Shopping List', 'wp-ultimate-recipe');
	        $tooltip_alt_text = __('This recipe is in your Shopping List', 'wp-ultimate-recipe');
	        if( $tooltip_text && $tooltip_alt_text ) $classes[] = 'recipe-tooltip';

	        if( $in_shopping_list ) {
	            $tooltip_text_backup = $tooltip_text;
	            $tooltip_text = $tooltip_alt_text;
	            $tooltip_alt_text = $tooltip_text_backup;
	        }*/

	        $this->classes = $classes;
	        $output = $this->before_output();
				}
        ob_start();?>
        
				<a href="#"<?php echo $this->style(); ?> title="<?php echo $title;?>" id="<?php echo $link_id;?>" data-recipe-id="<?php echo $recipe->ID(); ?>" data-title-alt="<?php echo $title_alt; ?>">
				<div class="button-caption"><?php echo __('Add to Shopping List','foodiepro'); ?></div>
				</a>
				<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}