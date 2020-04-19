<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


// class Custom_Recipe_Favorite extends WPURP_Template_Block {
class CRM_Favorite
{

    private $class_id = 'custom-recipe-favorite tooltip-onclick';
    private static $FAVLISTS = array();
    private static $TOOLTIPS = array();
    private static $FAVSLUG = 'favoris-recettes';

    public function __construct()
    {
        self::$FAVLISTS = array(
            'favorites' => array(
                'meta' => 'wpurp_favorites',
                'label' => __('My favorite recipes', 'crm'),
            ),
            'wishlist' => array(
                'meta' => 'wpurp_favorites_wishlist',
                'label' => __('Recipes in my wishlist', 'crm'),
            ),
            'remove' => array(
                'label' => __('Remove from favorites', 'crm'),
            ),
        );
        self::$TOOLTIPS = array(
            'favorites' => __('In my <a href="%s">favorites</a>', 'crm'),
            'wishlist' => __('In my <a href="%s">wishlist</a>', 'crm'),
            'nofav' => __('Add to my <a href="%s">favorites</a>', 'crm'),
        );
    }

    public function add_list_query_var($vars)
    {
        $vars[] = "list";
        return $vars;
    }

    public function output_button($recipe, $args = array())
    {

        if (!is_user_logged_in()) {
            $link_id = 'id="join_us"';
            $link_url = foodiepro_get_permalink(array('slug' => 'connexion'));
            $onclick = "ga('send','event','join-us','click','recipe-favorite', 0);";
            $favorites_link = foodiepro_get_permalink(array('slug' => 'connexion'));
        } else {
            $link_id = '';
            $link_url = '#';
            $this->class_id .= ' logged-in';
            $onclick = "";
            $favorites_link = do_shortcode('[permalink slug="' . self::$FAVSLUG . '" target="_blank"]');
        }

        $favlist = $this->getfav($recipe->ID());
        if ($favlist != 'nofav') {
            $favorites_link = add_query_arg('list', $favlist, $favorites_link);
        }

        $tooltip = $this->get_tooltip($favlist, $favorites_link);

        ob_start();
?>
        <a href="<?= $link_url; ?>" class="<?= $this->class_id; ?>" id="<?= $favlist; ?>" data-recipe-id="<?= $recipe->ID(); ?>" data-tooltip-id="<?php echo is_user_logged_in() ? '' : 'join_us'; ?>" onClick="<?= $onclick; ?>">
            <?= $this->get_icon($favlist, 'overlayed-icon'); ?>
            <div class="button-caption"><?= __('Cookbook', 'crm'); ?></div>
        </a>
        <?php

        if (is_user_logged_in()) {
            $args = array(
                'content'   => $tooltip,
                'valign'    => 'above',
                'halign'    => 'center',
            );
            Tooltip::display($args);
            $args = array(
                'content'   => $this->output_form($recipe->ID()),
                'valign'    => 'above',
                'halign'    => 'center',
                'action'    => 'click',
                'callout'   => false,
                'class'     => 'favorites-form uppercase',
                'title'     => __('Add to my cookbook', 'crm'),
                'img'       => CHILD_THEME_URL . '/images/popup-icons/cookbook-small.png',
                'imgdir'    => CHILD_THEME_PATH . '/images/popup-icons/'
            );
            Tooltip::display($args);
        }

        $output = ob_get_contents();
        ob_end_clean();

        // return $this->after_output( $output, $recipe, $args );
        return $output;
    }



    public function output_form($recipe_id)
    {

        $isfav = false;

        ob_start();
        ?>
        <div id="favorites_list_form">
            <!-- <p><?= __('Choose a list :', 'crm'); ?></p> -->

            <ul class="favlist">
                <?php
                foreach ($this->get_lists(false) as $list) {
                    if ($this->isfav($recipe_id, $list)) {
                        $class = "isfav";
                        $isfav = true;
                    } else {
                        $class = '';
                    }
                ?>
                    <li class="favlist-item <?= $class; ?>" id="<?= $list; ?>">
                        <?= $this->get_icon('form-' . $list) ?>
                        <span for="<?= $list; ?>"> <?= $this->get_label($list) ?></span>
                    </li>
                <?php
                }
                // Don't display remove item whenever the recipe is not in favorites already
                ?>
                <li class="favlist-item remove <?= $isfav ? '' : 'nodisplay'; ?>" id="remove">
                    <?= $this->get_icon('remove') ?>
                    <span for="remove"> <?= $this->get_label('remove') ?></span>
                </li>

            </ul>

            <p>

            </p>
        </div>
<?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }


    /* DROPDOWN WIDGET FILTER
    -----------------------------------------------------------*/
    public function cpm_list_dropdown_widget_args_cb($args, $page_slug)
    {
        if ($page_slug == CPM_Assets::get_slug('recipe', 'recipe_favorites')) {
            $args=array(
                'title' => __('My lists', 'crm'),
                'queryvar' => 'list',
                'options'   => array(
                    'all'       => array(
                        'label' => __('All cookbook recipes', 'crm'),
                        'description' => '',
                    ),
                    'favorites' => array(
                        'label'         => __('My favorite recipes', 'crm'),
                        'description'   => __('Recipes you have already cooked and appreciate.', 'crm'),
                    ),
                    'wishlist'  => array(
                        'label'         => __('Recipes in my wishlist', 'crm'),
                        'description'   => __('Recipes you are interested in, but didn\'t cook yet', 'crm'),
                    ),
                )
            );
        }
        return $args;
    }

    /********************************************************************************
     ****                           GETTERS                                **********
     ********************************************************************************/

    public static function get_lists($remove = false)
    {
        $favlists = self::$FAVLISTS;
        if (!$remove) {
            unset($favlists['remove']);
        }
        return array_keys($favlists);
    }

    public static function get_label($list)
    {
        if (!isset(self::$FAVLISTS[$list]['label'])) return false;
        return self::$FAVLISTS[$list]['label'];
    }

    public static function get_tooltip($state, $link='')
    {
        if (!isset(self::$TOOLTIPS[$state])) return false;
        return sprintf(self::$TOOLTIPS[$state],$link);
    }

    public static function get_field($list, $field)
    {
        if (isset(self::$FAVLISTS[$list][$field]))
            $val = self::$FAVLISTS[$list][$field];
        else
            $val = false;
        return $val;
    }

    public static function get_meta_name($list)
    {
        if (!isset(self::$FAVLISTS[$list]['meta'])) return false;
        return self::$FAVLISTS[$list]['meta'];
    }

    public static function get_icon($type, $class='')
    {
        switch ($type) {
            case 'remove':
                $html = foodiepro_get_icon('remove', $class);
                break;
            case 'form-favorites':
                $html = foodiepro_get_icon('heart', $class);
                break;
            case 'form-wishlist':
                $html = foodiepro_get_icon('thumbtack', $class);
                break;
            default:
                $html = foodiepro_get_icon('book', $class);
        }
        return $html;
    }

    public static function getfav($recipe_id, $lists = 'all')
    {
        if (!is_user_logged_in()) return 'nofav';

        $user_id = get_current_user_id();
        $state = 'nofav';

        if ($lists == 'all')
            $lists = self::get_lists();
        else
            $lists = array(0 => $lists);

        foreach ($lists as $list) {
            $favorites = get_user_meta($user_id, self::get_meta_name($list), true);
            $favorites = is_array($favorites) ? $favorites : array();
            if (in_array($recipe_id, $favorites)) {
                $state = $list;
            }
        }

        return $state;
    }

    public static function isfav($recipe_id, $lists = 'all')
    {
        if (!is_user_logged_in()) return false;

        $user_id = get_current_user_id();
        $isfav = false;

        if ($lists == 'all')
            $lists = self::get_lists();
        else
            $lists = array(0 => $lists);

        foreach ($lists as $list) {
            $favorites = get_user_meta($user_id, self::get_meta_name($list), true);
            $favorites = is_array($favorites) ? $favorites : array();
            if (in_array($recipe_id, $favorites)) {
                $isfav = true;
            }
        }

        return $isfav;
    }


    /********************************************************************************
     ****                           AJAX CALLBACKS                          **********
     ********************************************************************************/
    public function ajax_favorite_recipe()
    {
        if (!is_user_logged_in()) return false;

        // Force strings translation
        $this->__construct();

        if (check_ajax_referer('custom_favorite_recipe', 'security', false)) {

            $recipe_id = intval($_POST['recipe_id']);
            $user_id = get_current_user_id();
            $response_list = 'nofav';
            $favorites_link = do_shortcode('[permalink slug="' . self::$FAVSLUG . '"]');

            $choice = $_POST['choice'];

            foreach ($this->get_lists() as $list) {
                $favorites = get_user_meta($user_id, $this->get_meta_name($list), true);
                $favorites = is_array($favorites) ? $favorites : array();

                if (($choice == 'remove' || $choice != $list) && in_array($recipe_id, $favorites)) {
                    $key = array_search($recipe_id, $favorites);
                    unset($favorites[$key]);
                } elseif ($choice == $list) {
                    $favorites[] = $recipe_id;
                    $response_list = $list;
                    $favorites_link = add_query_arg('list', $response_list, $favorites_link);
                }
                update_user_meta($user_id, $this->get_meta_name($list), $favorites);
            }
            $response = array(
                            'list'=>$response_list,
                            'tooltip'=> $this->get_tooltip($response_list, $favorites_link)
                        );
            echo json_encode($response);
        }
        die();
    }



    /********************************************************************************
     ****                          SHORTCODE                         **********
     ********************************************************************************/

    public function favorite_recipes_shortcode($atts, $content)
    {
        $atts = shortcode_atts(array(
            // 'post_type' => 'post', // 'post', 'recipe'
        ), $atts);

        if (!is_user_logged_in()) return;
        $user_id = get_current_user_id();

        $PostList = new CPM_List('recipe');

        $lists = get_query_var('list', false);
        if ($lists)
            $lists = array(0 => $lists);
        else
            $lists = self::get_lists();

        $empty = true;
        $output = '';
        foreach ($lists as $list) {
            $favorites = get_user_meta($user_id, self::get_meta_name($list), true);
            $favorites = is_array($favorites) ? $favorites : array();

            $args = array(
                'numberposts' => -1,
                'category' => 0,
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => $favorites,
                'exclude' => array(),
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'recipe',
                'post_status' => array('publish', 'private', 'pending', 'draft'),
                'suppress_filters' => true
            );
            $recipes = get_posts($args);

            // $recipes = empty( $favorites ) ? array() : WPUltimateRecipe::get()->query()->ids( $favorites )->order_by('name')->order('ASC')->get();

            if (count($favorites) > 0) {
                $empty = false;
                $output .= $PostList->display($recipes, false, self::get_label($list));
            }
        }
        if ($empty)
            $output .= '<div>' . __("No recipes found.", 'crm') . '</div>';

        return $output;
    }
}
