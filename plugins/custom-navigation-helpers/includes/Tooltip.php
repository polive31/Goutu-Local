<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class Tooltip extends CustomNavigationHelpers {
	
	public function __construct() {
		parent::__construct();
		
		// JQuery
		add_action('wp_enqueue_scripts', array($this, 'enqueue_easing_script'));

		// Shortcodes
		add_shortcode('tooltip', array($this,'output_tooltip_shortcode')); 

	}

	public function enqueue_easing_script() {
	  	if ( is_single() ) {	
			$js_uri = self::$PLUGIN_URI . '/vendor/easing/';
			$js_path = self::$PLUGIN_PATH . '/vendor/easing/';
			custom_enqueue_script( 'jquery-easing', $js_uri, $js_path, 'jQuery_Easing.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	  	};
	}



	/* =================================================================*/
	/* = TOOLTIP SHORTCODE    
	/* =================================================================*/

    public function output_tooltip_shortcode( $atts ) {
        // $path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
		$atts = shortcode_atts( array(
			'text' => '', 
			'pos' => 'top',
			), $atts );

		$content = $atts['text']; 
		$position = $atts['pos']; 
        
        return $this->get($content, $position);
    }


	/* =================================================================*/
	/* = RETURN TOOLTIP HTML    
	/* =================================================================*/
    public static function get( $content, $position ) {
    	$uri = self::$PLUGIN_URI . 'assets/img/callout_'. $position . '.png';
        $html ='<div class="tooltip-content">';
        $html.='<div class="wrap">';
        $html.= $content;
        $html.='<img class="callout" data-no-lazy="1" src="' . $uri . '">';
        $html.='</div>';
        $html.='</div>';

		return $html;
    }


	/* =================================================================*/
	/* = DISPLAY TOOLTIP    
	/* =================================================================*/
    public static function display( $content, $position ) {
    	echo self::get( $content, $position);
    }


}



