<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
class Tooltip {
	
	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	

	public function __construct() {		
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		// Scripts & styles enqueue
		add_action('wp_enqueue_scripts', array($this, 'enqueue_easing_script'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_tooltip_styles'));

		// Shortcodes
		add_shortcode('tooltip', array($this,'output_tooltip_shortcode')); 

	}

	public function enqueue_easing_script() {
        if (! is_single() ) return;
		wp_enqueue_script( 'jquery-easing', self::$PLUGIN_URI . '/vendor/easing/jQuery_Easing.min.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	}

	public function enqueue_tooltip_styles() {
        if (! is_single() ) return;
  		$uri = self::$PLUGIN_URI . '/assets/css/';
  		$path = self::$PLUGIN_PATH . '/assets/css/';
		custom_enqueue_style( 'tooltip', $uri, $path, 'tooltip.css', array(), CHILD_THEME_VERSION );			
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
    public static function get( $content, $vertical, $horizontal, $style='' ) {
    	$uri = self::$PLUGIN_URI . 'assets/img/callout_'. $vertical . '.png';
        $html ='<div class="tooltip-content ' . $vertical . ' ' . $horizontal . ' ' . $style . '">';
        $html.='<div class="wrap">';
        $html.= $content;
        $html.= (strpos($style,'hidden')===false)?'<img class="callout" data-no-lazy="1" src="' . $uri . '">':'';
        $html.='</div>';
        $html.='</div>';

		return $html;
    }


	/* =================================================================*/
	/* = DISPLAY TOOLTIP    
	/* =================================================================*/
    public static function display( $content, $vertical, $horizontal, $style='' ) {
    	echo self::get( $content, $vertical, $horizontal, $style );
    }


}



