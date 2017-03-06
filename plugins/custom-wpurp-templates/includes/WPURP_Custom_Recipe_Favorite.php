<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPURP_Custom_Recipe_Favorite extends WPURP_Template_Block {

    public $class='wpurp-recipe-favorite';
    public $editorField = 'favoriteRecipe';

    public function __construct( $type = 'recipe-favorite' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        if( !is_user_logged_in() || !WPUltimateRecipe::is_addon_active( 'favorite-recipes' ) ) return '';

        $title_in =__('In my favorites','foodiepro');
				$title_add =__('Add to my favorites','foodiepro');
				
        if( WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) {
        	$this->class = $this->class . ' is-favorite';
        	$title=$title_in;
        	$title_alt=$title_add;
        }
				else {
        	$title=$title_add;
        	$title_alt=$title_in;
				}
				
        $output = $this->before_output();
        ob_start();
?>
				<a href="#" class="<?php echo $this->class; ?>" title="<?php echo $title?>" data-title-alt="<?php echo $title_alt; ?>"" data-recipe-id="<?php echo $recipe->ID(); ?>">
				<div class="button-caption"><?php echo __('Favorites','foodiepro'); ?></div>
				</a>

<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}