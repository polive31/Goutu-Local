<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_ShoppingList extends WPURP_Template_Block {

	private $logged_in;

    public function __construct( $logged_in, $type = 'recipe-add-to-shopping-list' )
    {
      parent::__construct( $type );
  		$this->logged_in = $logged_in;
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';

        $link_id='';
	      $classes = array();

        if( !$this->logged_in ) {
        	$link_id='id="join-us"';
					$menu_link =  foodiepro_get_permalink(array('slug' => 'connexion'));
        }
		else {
			$classes[] = 'logged-in';
			// $menu_link = do_shortcode('[permalink slug="liste-courses"]');
			$menu_link = foodiepro_get_permalink(array('slug'=>'liste-courses'));
		}
        $tooltip_in=sprintf(__('In my <a href="%s">shopping list</a>','crm'),$menu_link);
        $tooltip_add=sprintf(__('Add to my <a href="%s">shopping list</a>','crm'),$menu_link);

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

        $tooltip='<div class="toggle">' . $tooltip . '</div>';
        $tooltip_alt='<div class="toggle" style="display:none">' . $tooltip_alt . '</div>';

        ob_start();?>

				<a href="#"<?php echo $this->style(); ?> <?php echo $link_id;?> data-recipe-id="<?php echo $recipe->ID(); ?>">
				<div class="button-caption"><?php echo __('Add to Shopping List','crm'); ?></div>
				</a>
                [tooltip text='<?php echo $tooltip . $tooltip_alt;?>' pos="top"]

                <?php

        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}
