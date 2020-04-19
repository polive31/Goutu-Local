<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CRM_Recipe_Shortcodes  {

    public static $id;

    public function __construct() {
        self::$id=0;
    }

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

            $render = new CRM_Template();
            $output = $render->screen( '', $recipe_post );

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
