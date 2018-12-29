<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// class Custom_Recipe_Favorite extends WPURP_Template_Block {
class Custom_Recipe_Favorite extends Custom_WPURP_Templates {

    private $class_id='custom-recipe-favorite tooltip-target';
    private $logged_in;
    private $link_id;
    private $editorField = 'favoriteRecipe';

    // public function __construct( $logged_in, $type = 'recipe-favorite' ) {
    public function __construct() {
        // parent::__construct( $type );
        // self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
                // Ajax
        add_action( 'wp_ajax_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );
    }


    public function output( $recipe, $args = array() ) {
        // if( !$this->output_block( $recipe, $args ) ) return '';
        
        $link_url = '#';
        
        if( !is_user_logged_in() ) {
            $this->link_id='id="join-us"';
            $favorites_link = '/connexion';
            $link_url = '/connexion';
            // $onclick = "_gaq.push(['_trackEvent’, 'join-us', 'click’, 'recipe-rate, '0’]);";
            $onclick = "ga('send','event','join-us','click','recipe-favorite', 0);";
        } 
        else {
            $this->link_id='';
        	$this->class_id .= ' logged-in';
        	$favorites_link = do_shortcode('[permalink slug="favoris-recettes"]');
            $onclick = "";
        }
        
        $tooltip_in = sprintf(__('In my <a href="%s">favorites</a>','foodiepro'), $favorites_link);
        $tooltip_add = sprintf(__('Add to my <a href="%s">favorites</a>','foodiepro'), $favorites_link);

        if( $this->is_favorite_recipe( $recipe->ID() ) ) {
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
				
        // $output = $this->before_output();
        
        ob_start();
        ?>
            <a href="<?= $link_url;?>" <?= $this->link_id;?> class="<?= $this->class_id; ?>" data-recipe-id="<?= $recipe->ID(); ?>" onClick="<?= $onclick; ?>" >
            <div class="button-caption"><?= __('Favorites','foodiepro'); ?></div>
            </a>
        <?php 

        Tooltip::display($tooltip . $tooltip_alt, 'above', 'center');    
        Tooltip::display($this->add_popup(), 'above', 'center', 'click' );    
        $output = ob_get_contents();
        ob_end_clean();

        // return $this->after_output( $output, $recipe, $args );
        return $output;
    }

    
    public static function is_favorite_recipe( $recipe_id ) {
        $user_id = get_current_user_id();

        $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
        $favorites = is_array( $favorites ) ? $favorites : array();

        return in_array( $recipe_id, $favorites );
    }

    public function ajax_favorite_recipe() {
        if( !is_user_logged_in() ) return false;

        if(check_ajax_referer( 'custom_favorite_recipe', 'security', false ) ) {
            $recipe_id = intval( $_POST['recipe_id'] );
            $user_id = get_current_user_id();

            $type=$_POST['contact']?$_POST['contact']:'';

            $favorites = get_user_meta( $user_id, 'wpurp_favorites', true );
            $favorites = is_array( $favorites ) ? $favorites : array();

            if( in_array( $recipe_id, $favorites ) ) {
                $key = array_search( $recipe_id, $favorites );
                unset( $favorites[$key ] );
            } else {
                $favorites[] = $recipe_id;
            }

            update_user_meta( $user_id, 'wpurp_favorites', $favorites );
        }

        die();
    }

    public function add_popup() {
        
        ob_start();
        ?>
        <form>
        <p><?= __('Choose a list :','foodiepro'); ?></p>
        <p>
            <input type="radio" id="favorites" name="contact" value="favorites">
            <label for="favorites"><?= __('<strong>My favorite recipes</strong>','foodiepro'); ?></label>
        </p>
        <p>
            <input type="radio" id="wishlist" name="contact" value="wishlist" checked>
            <label for="wishlist"><?= __('<strong>Recipes wish list</strong>','foodiepro'); ?></label>
        </p>
        <p>
            <!-- <button type="submit"><?= __('Add','foodiepro'); ?></button> -->
            <button onClick='addToFavorites(this)'><?= __('Add','foodiepro'); ?></button>
            <button class="alignright cancel" onClick='addToFavoritesCancel(this);'><?= __('Cancel','foodiepro'); ?></button>
        </p>
        </form>
        <?php
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }


}

new Custom_Recipe_Favorite();