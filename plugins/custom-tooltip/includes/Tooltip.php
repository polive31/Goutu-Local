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

		// Scripts & styles enqueue, with fallback in case class is created after scripts enqueue
		// add_action('wp_enqueue_scripts', array($this, 'enqueue_easing_script'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_tooltip_assets') );

		// Shortcodes
		add_shortcode('tooltip', array($this,'output_tooltip_shortcode')); 

	}

	public function enqueue_easing_script() {
        if (! is_single() ) return;
		wp_enqueue_script( 'jquery-easing', self::$PLUGIN_URI . '/vendor/easing/jQuery_Easing.min.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	}

	public function enqueue_tooltip_assets() {
        if (! is_single() ) return;
		  
		$uri = self::$PLUGIN_URI . '/assets/css/';
  		$path = self::$PLUGIN_PATH . '/assets/css/';
		custom_enqueue_style( 'tooltip', $uri, $path, 'tooltip.css', array(), CHILD_THEME_VERSION );			
		  
		$uri = self::$PLUGIN_URI . '/assets/js/';
  		$path = self::$PLUGIN_PATH . '/assets/js/';
		custom_enqueue_script( 'tooltip', $uri, $path, 'tooltip.js', array(), CHILD_THEME_VERSION, true );			
	}	



	/* =================================================================*/
	/* = TOOLTIP SHORTCODE    
	/* =================================================================*/

    public function output_tooltip_shortcode( $atts ) {
        // $path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
		$atts = shortcode_atts( array(
			'content' => '', 
			'halign' => 'middle',
			'valign' => 'top',
			'class' => '',
			'callout' => 'no', //yes, no
			'theme' => 'grey', //yellow
			'action' => 'hover', //click
		), $atts );

		$callout = $atts['callout']=='yes';
        
        return $this->get( $atts['text'], $atts['valign'], $atts['halign'], $atts['trigger'], $atts['callout'], $atts['class'], $atts['theme'] );
    }
	
	
	/* =================================================================*/
	/* = DISPLAY TOOLTIP    
	/* =================================================================*/
    public static function display( $content, $valign, $halign, $trigger='hover', $callout=false, $class='', $theme='grey'  ) {
		echo self::get( $content, $valign, $halign, $trigger, $callout, $class, $theme );
    }
	


	/* =================================================================*/
	/* = RETURN TOOLTIP HTML    
	/* =================================================================*/
	public static function get( $content, $valign, $halign, $trigger, $callout, $class, $theme='grey'  ) {
		$uri = self::$PLUGIN_URI . 'assets/img/' . $theme . '/callout_'. $valign . '.png';
		
		$html ='<div class="tooltip-content ' . $valign . ' ' . $halign . ' ' . $class . ' ' . $trigger . ' ' . $theme . '">';
		$html.='<div class="wrap">';
		$html.= $content;
		$html.= $callout?'<img class="callout" data-no-lazy="1" src="' . $uri . '">':'';
		$html.='</div>';
		$html.='</div>';

		return $html;
	}

}



