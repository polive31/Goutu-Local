<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


class CRM_Output
{

    protected static $_PluginPath;
    protected static $_PluginUri;
    // public static $logged_in;

    private $post_ID;
    private $recipe;

    public function __construct()
    {
        self::$_PluginUri = plugin_dir_url(dirname(__DIR__));
        self::$_PluginPath = plugin_dir_path(dirname(__DIR__));

        define('EP_RECIPE', 524288); // 2^19
    }

    /* PRINT OUTPUT FUNCTIONS
    -------------------------------------------------------------------------*/

    /**
     * Defines a URL rewrite endpoint for "virtual" recipe print pages
     *
     * @return void
     */
    public function endpoint()
    {
        add_rewrite_endpoint( CRM_Assets::keyword(), EP_RECIPE);
    }

    /**
     * Callback for 'template_redirect' hook, allowing to route the output to the desired template
     *
     * @return void
     */
    public function redirect()
    {
        $print = get_query_var( CRM_Assets::keyword(), false);
        if ($print !== false) {
            $post = get_post();
            $recipe = new CRM_Recipe($post);
            $this->print_recipe($recipe, $print);
            exit();
        }
    }

    /**
     * Output recipe according to print template
     *
     * @param  mixed $recipe
     * @param  mixed $parameters
     * @return void
     */
    public function print_recipe($recipe, $parameters)
    {
        // Get Serving Size
        preg_match("/[0-9\.,]+/", $parameters, $servings);
        $servings = empty($servings) ? 0.0 : floatval(str_replace(',', '.', $servings[0]));

        if ($servings < 1) {
            $servings = $recipe->servings_normalized();
        }

        if (isset($recipe->ID))
            $recipe = new CRM_Recipe($recipe->ID);
        else
            $recipe = new CRM_Recipe($recipe->ID());

        $js_uri = self::$_PluginUri . 'assets/js/print_page.js';
        $stylesheet_uri = self::$_PluginUri . 'assets/css/custom-recipe-print.css';

        $args=compact('recipe','js_uri','stylesheet_uri','servings');
        CRM_Assets::echo_template_part( 'print', false, $args);

    }



    /* SCREEN OUTPUT FUNCTIONS
    -------------------------------------------------------------------------*/
    /**
     * Output recipe according to screen template
     *
     * @return void
     */
    public function screen( $post=null )
    {
        $recipe = new CRM_Recipe( $post );
        $args = compact('recipe');
        CRM_Assets::echo_template_part('screen', false, $args);
    }


    /* CALLBACKS
	-----------------------------------------------------------------------------------*/
    /**
     * Replace post content with recipe custom post type content
     *
     * @return void
     */
    public function do_recipe_content() {
        if ( !is_singular('recipe') ) return;
        remove_action('genesis_entry_content',      'genesis_do_post_content');
        add_action('genesis_entry_content',         array($this, 'screen'));
    }

    /**
     * fetch_gallery_images
     *
     * @param  mixed $attachments
     * @param  mixed $post_id
     * @return void
     */
    public function fetch_gallery_images($attachments, $post_id)
    {
        $attachment_ids = get_post_meta($post_id, '_post_image_gallery', true);
        $attachments = array();
        if (!empty($attachment_ids)) {
            foreach ($attachment_ids as $id) {
                if (get_post_status($id) == 'publish') {
                    $attachments[$id] = get_post($id);
                };
            }
        }
        return $attachments;
    }

    /**
     * tag_uploaded_images
     *
     * @param  mixed $media_ids
     * @param  mixed $success
     * @param  mixed $post_id
     * @return void
     */
    public function tag_uploaded_images($media_ids, $success, $post_id)
    {
        if (!(in_array(get_post_type($post_id), array('post', 'recipe'))) || !$success) return;
        $existing_ids = get_post_meta($post_id, '_post_image_gallery', true);

        //Set attachment title if exists
        foreach ($media_ids as $media_id) {
            $title = get_post_meta($media_id, 'fu_title', true);
            if (!empty($title)) {
                $media_post = array(
                    'ID'           => $media_id,
                    'post_title'   => $title,
                );
                wp_update_post($media_post);
            }
        }

        if (!empty($existing_ids)) {
            $media_ids = array_merge($existing_ids, $media_ids);
        }
        if (!empty($media_ids)) {
            update_post_meta($post_id, '_post_image_gallery', $media_ids, 0);
        }
    }


    /* GETTERS
     --------------------------------------------------------------------------------------------*/

    public function get_icon($icon, $class = 'svg-icon', $id = '', $tag = 'div')
    {
        $img_path = self::$_PluginPath . 'assets/img/icons/';
        $html = file_get_contents($img_path . $icon . '.svg');

        if ($html === false) return '';
        $html = '<' . $tag . ' class="' . $class . '" id="' . $id . '">' . $html . '</' . $tag . '>';
        return $html;
    }


    /* HELPERS
     --------------------------------------------------------------------------------------------*/
    /**
     * This function is used in print template, to remove image tags from the output
     *
     * @param  mixed $content
     * @return void
     */
    public static function stripout_images($content)
    {
        $content = preg_replace("/<img[^>]+\>/i", " ", $content);
        $content = str_replace(']]>', ']]>', $content);
        return $content;
    }


    /* SHORTCODES
     --------------------------------------------------------------------------------------------*/

    public function add_tts_button( $atts, $content = "" ) {

        if (!function_exists('RV_clean_text')) return $content;

        static $button_id = 0;
        $button_id++;

        $cleantext = RV_clean_text($content);
        extract(shortcode_atts(array(
            'voice'          => 'French Female',
            'title'          => __('Read out loud','crm'),
            'buttontext'     => '',
            'buttonposition' => 'before'
        ), $atts));

        $parameters = RV_extract_extra_parameters($atts);

        // $speakerIcon = "&#128266;";
        $speakerIcon = '<i class="fa fa-play" aria-hidden="true"></i>';
        // The script can be multiline, but the button should be in a single line, otherwise it can mess up a user's layout.
        $button = '<div id="bb'.$button_id.'" type="button" value="Play" class="responsivevoice-button" title="' . $title . '">'.$speakerIcon.' '.$buttontext.'</div>
            <script>
                bb' . $button_id . '.onclick = function(){
                    if(responsiveVoice.isPlaying()){
                        responsiveVoice.cancel();
                    }else{
                        responsiveVoice.speak("' . $cleantext . '", "' . $voice . '"' . $parameters . ');
                    }
                };
            </script>
        ';

        if ($buttonposition == 'after')
            return do_shortcode($content) . $button;
        else
            return $button . do_shortcode($content);
    }

    public function recipe_shortcode( $options ) {
        $options = shortcode_atts( array(
            'id' => 'random', // If no ID given, show a random recipe
            'template' => 'default'
        ), $options );

        $recipe_post = null;

        if( $options['id'] == 'random' ) {
            $posts = get_posts(array(
                'post_type' => WPURP_POST_TYPE,
                'posts_per_page' => 1,
                'orderby' => 'rand',
            ));

            $recipe_post = isset( $posts[0] ) ? $posts[0] : null;
        } elseif( $options['id'] == 'latest' ) {
            $posts = get_posts(array(
                'post_type' => WPURP_POST_TYPE,
                'posts_per_page' => 1,
            ));

            $recipe_post = isset( $posts[0] ) ? $posts[0] : null;
        } else {
            $recipe_post = get_post( intval( $options['id'] ) );
        }

        if( !is_null( $recipe_post ) && $recipe_post->post_type == 'recipe' && ( !is_feed() )) {

            if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
                $type = 'amp';
                $template = null;
            } else {
                $type = is_feed() ? 'feed' : 'recipe';
                $template = is_feed() ? null : $options['template'];
            }

            $output = $this->screen( $recipe_post );

        }
        else
        {
            $output = '';
        }

        return do_shortcode( $output );
    }

    public function timer_shortcode($atts, $content)
    {
        $atts = shortcode_atts(array(
            'seconds' => '0',
            'minutes' => '0',
            'hours' => '0',
        ), $atts);

        $seconds = intval($atts['seconds']);
        $minutes = intval($atts['minutes']);
        $hours = intval($atts['hours']);

        $seconds = $seconds + (60 * $minutes) + (60 * 60 * $hours);

        if ($seconds > 0) {
            $timer = '<a href="#" class="wpurp-timer-link" title="' . __('Click to Start Timer', 'crm') . '">';
            $timer .= '<span class="wpurp-timer" data-seconds="' . esc_attr($seconds) . '">';
            $timer .= $content;
            $timer .= '</span></a>';

            return $timer;
        } else {
            return $content;
        }
    }

}
