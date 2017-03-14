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
					$menu_link = '/connexion';
        }

				else {
					$classes[] = 'logged-in';
					$menu_link = '/mon-goutu/menus';
				}
        $tooltip_in=sprintf(__('In my <a href="%s">shopping list</a>','foodiepro'),$menu_link);
        $tooltip_add=sprintf(__('Add to my <a href="%s">shopping list</a>','foodiepro'),$menu_link);
					
        $shopping_list_recipes = array();
        if( isset( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) ) {
            $shopping_list_recipes = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) );
        }

        $in_shopping_list = in_array( $recipe->ID(), $shopping_list_recipes );

        if( $in_shopping_list ) {
        	$classes[] = 'in-shopping-list';
        	$tooltip=$tooltip_in;
        	$tooltip_alt=$tooltip_add;
        }
        else {
        	$tooltip=$tooltip_add;
        	$tooltip_alt=$tooltip_in;
        }
        $this->classes = $classes;
        $output = $this->before_output();
			
        $tooltip='<div>' . $tooltip . '</div>';
        $tooltip_alt='<div style="display:none">' . $tooltip_alt . '</div>';
        
        ob_start();?>
        
				<a href="#"<?php echo $this->style(); ?> id="<?php echo $link_id;?>" data-recipe-id="<?php echo $recipe->ID(); ?>">
				<div class="button-caption"><?php echo __('Add to Shopping List','foodiepro'); ?></div>
				</a>
				<?php echo Custom_Recipe_Templates::output_tooltip($tooltip.$tooltip_alt,'top');?>
				<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}