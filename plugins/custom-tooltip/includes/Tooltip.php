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

		add_action('genesis_after',array($this, 'add_overlay_markup') );

		// Shortcodes
		add_shortcode('tooltip', array($this,'output_tooltip_shortcode')); 

	}

	public function add_overlay_markup() {
		static $once=false;
		if ($once) return;
		$once=true;
		?>
		<!-- <div class="tooltip-overlay" style="display:none"></div>;  -->
		<div class="tooltip-overlay nodisplay"></div>; 
		<?php  
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
			'class' => 'grey', // color : yellow, shape : form, size : large
			'callout' => 'no', //yes, no
			'action' => 'hover', //click
			'title' => null, //
			'img' => null, // Image source
			// 'img' => null, // Image source
		), $atts );

		$callout = $atts['callout']=='yes';
        
        return $this->get( $atts['text'], $atts['valign'], $atts['halign'], $atts['trigger'], $atts['callout'], $atts['class'], $atts['title'], $atts['img'] );
    }
	
	/* =================================================================*/
	/* = RETURN TOOLTIP HTML    
	/* =================================================================*/
	public static function get( $content, $valign, $halign, $trigger, $callout, $class, $title=false, $img=false ) {
		$uri = self::$PLUGIN_URI . 'assets/img/' . $theme . '/callout_'. $valign . '.png';
		
		$html ='<div class="tooltip-content ' . $valign . ' ' . $halign . ' ' . $class . ' ' . $trigger . '">';
		$html.='<div class="wrap">';
		$html.=$img?'<div class="tooltip-img"><img src="' . $img . '"></div>':'';
		$html.=$title?'<h4 class="tooltip-title">' . $title . '</h4>':'';
		$html.= $content;
		$html.= $callout?'<img class="callout" data-no-lazy="1" src="' . $uri . '">':'';
		$html.='</div>';
		$html.='</div>';
		
		return $html;
	}
	
	/* =================================================================*/
	/* = DISPLAY TOOLTIP    
	/* =================================================================*/
	public static function display( $content, $valign, $halign, $trigger='hover', $callout=false, $class='', $title=false, $img=false  ) {
		echo self::get( $content, $valign, $halign, $trigger, $callout, $class, $title, $img );
	}
	
}



