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

    private static $FAVLISTS = array();

    // public function __construct( $logged_in, $type = 'recipe-favorite' ) {
    public function __construct() {
        // parent::__construct( $type );
        // self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
                // Ajax
        add_action( 'wp_ajax_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );

        self::$FAVLISTS=array(
            'favorites' => array(
                'meta' => 'wpurp_favorites',
                'icon' => '<i class="fa fa-heart"></i>',
                'label' => __('<strong>My favorite recipes</strong>','foodiepro'),
            ),
            'wishlist' => array(
                'meta' => 'wpurp_favorites_wishlist',
                'icon' => '<i class="fa fa-thumb-tack"></i>',
                'label' => __('<strong>Recipes wish list</strong>','foodiepro'),
            ),
        );
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

        $isfav = $this->is_favorite_recipe( $recipe->ID() );
        if( $isfav[0] ) {
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
            <?php echo $this->get_toolbar_icon('favorites-' . $isfav[1] ); ?>
            <div class="button-caption"><?= __('Favorites','foodiepro'); ?></div>
            </a>
        <?php 

        Tooltip::display($tooltip . $tooltip_alt, 'above', 'center');    
        Tooltip::display($this->add_popup( $recipe->ID() ), 'above', 'center', 'click', false, null, 'form' );    
        $output = ob_get_contents();
        ob_end_clean();

        // return $this->after_output( $output, $recipe, $args );
        return $output;
    }

    public function ajax_favorite_recipe() {
        if( !is_user_logged_in() ) return 'user not logged-in';

        echo 'reached ajax_favorite_recipe() !';
        
        if (check_ajax_referer( 'custom_favorite_recipe', 'security', false ) ) {
            
            $recipe_id = intval( $_POST['recipe_id'] );
            $user_id = get_current_user_id();
            
            $choice=$_POST['choice'];
            
            foreach ($this->get_lists() as $list) {
                $favorites = get_user_meta( $user_id, $this->get_meta_name($list), true );
                $favorites = is_array( $favorites ) ? $favorites : array();
                
                if ( ($choice=='remove' || $choice != $list) && in_array( $recipe_id, $favorites ) ) {
                    $key = array_search( $recipe_id, $favorites );
                    unset( $favorites[$key ] );
                } 
                elseif ( $choice==$list ) {
                    $favorites[] = $recipe_id;
                }
                
                update_user_meta( $user_id, $this->get_meta_name($list), $favorites );
            }
            echo 'User favorite recipes metadata update completed';
        }
        else {
            echo 'Security check failed';
        }
        die();
    }
    
    public function add_popup( $recipe_id ) {
        
        $fav=$this->is_favorite_recipe( $recipe_id );
        
        ob_start();
        ?>
        <div id="favorite-form">
        <p><?= __('Choose a list :','foodiepro'); ?></p>
        <?php 
        $isfav=false;
        foreach ( $this->get_lists() as $list) {
            $fav=$this->is_favorite_recipe( $recipe_id, $list );
            $isfav=$isfav || $fav[0];
            ?>
            <p>
                <input type="radio" id="<?= $list; ?>" name="favlist" value="<?= $list; ?>" <?= $fav[0]?'checked':'' ?>>
                <label for="<?= $list; ?>"> <?= $this->get_label( $list ) ?></label>
                <span class="favorite-icon"><?= $this->get_icon( $list ) ?></span>
            </p>
            <?php 
        } 
        if ( $isfav ) {
        ?>
            <p>
                <input type="radio" id="remove" name="favlist" value="remove">
                <label for="remove"> <?= __('Remove from favorites','foodiepro'); ?></label>
                <span class="favorite-icon">✘</span>
            </p>        
        <?php 
        } ?>
        <p>
            <button onClick='addToFavoritesUpdate(this)'><?= __('OK','foodiepro'); ?></button>
            <button class="alignright cancel" onClick='addToFavoritesCancel(this);'><?= __('Cancel','foodiepro'); ?></button>
        </p>
        </div>
        <?php
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function get_lists() {
        return array_keys(self::$FAVLISTS);
    }

    public static function get_label( $list ) {
        return self::$FAVLISTS[$list]['label'];
    }

    public static function get_meta_name( $list ) {
        return self::$FAVLISTS[$list]['meta'];
    }  
    
    public static function get_icon( $list ) {
        return self::$FAVLISTS[$list]['icon'];
    }

    public static function get_toolbar_icon( $type ) {
        switch ($type) {
            case 'favorites-no' :
                // $html = '<span class="fa-stack">';
                $html = '<i class="fa fa-bookmark-o"></i>';
                // $html = '<i class="fa fa-bookmark-o fa-stack-1x"></i>';
                // $html .= '<i class="fa fa-heart fa-exp fa-stack-1x"></i>';
                // $html .= '</span>';   
                break;
            case 'favorites-favorites' :
                // $html = 'favorites-favorites';
                $html = '<span class="fa-narrow fa-stack">';
                $html .= '<i class="fa fa-bookmark fa-stack-1x"></i>';
                $html .= '<i class="fa fa-heart fa-exp fa-stack-1x"></i>';
                $html .= '</span>';                
                break;
            case 'favorites-wishlist' :
                // $html = 'favorites-wishlist';
                $html = '<span class="fa-narrow fa-stack">';
                $html .= '<i class="fa fa-bookmark fa-stack-1x"></i>';
                // $html .= '<i class="fa fa-clock fa-exp fa-stack-1x"></i>';
                $html .= '<i class="fa fa-thumb-tack fa-exp fa-stack-1x"></i>';
                $html .= '</span>';              
                break;         
        }
        return $html;
    }    
    
    public static function is_favorite_recipe( $recipe_id, $lists='all' ) {
        $user_id = get_current_user_id();
        $is_fav=false;
        $fav_list='no';

        if ($lists=='all') 
            $lists = self::get_lists();
        else
            $lists = array( 0 => $lists);

        foreach ($lists as $list)  {
            $favorites = get_user_meta( $user_id, self::get_meta_name( $list ), true );
            $favorites = is_array( $favorites ) ? $favorites : array();
            if ( in_array( $recipe_id, $favorites ) ) {
                $is_fav= true;
                $fav_list = $list;
            }
        }  

        return array( $is_fav, $fav_list );
    }    


}

new Custom_Recipe_Favorite();