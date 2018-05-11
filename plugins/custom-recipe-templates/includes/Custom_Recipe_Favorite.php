<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_Recipe_Favorite extends WPURP_Template_Block {

    private $class_id='wpurp-recipe-favorite';
    private $logged_in;
    private $link_id;
    private $editorField = 'favoriteRecipe';
	private static $_PluginPath;	
	

    public function __construct( $logged_in, $type = 'recipe-favorite' ) {
        parent::__construct( $type );
        $this->logged_in = $logged_in;
		self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
    }

    public function output( $recipe, $args = array() ) {
        if( !$this->output_block( $recipe, $args ) ) return '';
        
        
        $link_url = '#';
        
        if( !$this->logged_in ) {
            $this->link_id='id="join-us"';
            $favorites_link = '/connexion';
            $link_url = '/connexion';
        } 
        else {
            $this->link_id='';
        	$this->class_id .= ' logged-in';
        	$favorites_link = do_shortcode('[permalink slug="favoris-recettes"]');
        }
        
        $tooltip_in = sprintf(__('In my <a href="%s">favorites</a>','foodiepro'),$favorites_link);
				$tooltip_add = sprintf(__('Add to my <a href="%s">favorites</a>','foodiepro'),$favorites_link);
				
        if( WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) {
        	$this->class_id .= ' is-favorite';
        	$tooltip=$tooltip_in;
        	$tooltip_alt=$tooltip_add;
        }
	   else {
        	$tooltip=$tooltip_add;
        	$tooltip_alt=$tooltip_in;
		}
				
        $tooltip='<div class="toggle">' . $tooltip . '</div>';
        $tooltip_alt='<div class="toggle" style="display:none">' . $tooltip_alt . '</div>';
				
        $output = $this->before_output();
        
        ob_start();
?>

				<a href="<?php echo $link_url;?>" <?php echo $this->link_id;?> class="<?php echo $this->class_id; ?>" data-recipe-id="<?php echo $recipe->ID(); ?>">
				<div class="button-caption"><?php echo __('Favorites','foodiepro'); ?></div>
				</a>
                [tooltip text='<?php echo $tooltip . $tooltip_alt;?>' pos="top"]    
  
                <?php 

        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}