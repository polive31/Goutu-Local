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
			'callout' => false, // color theme
			'action' => 'hover', //click
			'title' => null, //
			'img' => null, // Image source
			// 'img' => null, // Image source
		), $atts );
        
        return self::get( $atts );
    }
	
	/* =================================================================*/
	/* = RETURN TOOLTIP HTML    
	/* 	$args=array(
		'content' 	=> tooltip content
		'valign' 	=> 'above', 'below'
		'halign'	=> 'left', 'right'
		'trigger'	=> 'click', 'hover'
		'callout'	=> false, 'yellow', ... any other valid color theme
		'class'		=> 'class1 class2 ...'
		'title'		=> tooltip title
		'img'		=> tooltip image
		) 
	*/
	/* =================================================================*/
	public static function get( $args ) {
		extract($args);

		// Default values
		$trigger = isset($trigger)?$trigger:'hover';
		$title = isset($title)?$title:'';
		$img = 	isset($img)?$img:'';
		$class = isset($class)?$class:'';
		$callout = isset($callout)?$callout:false;
		
		$html ='<div class="tooltip-content ' . $valign . ' ' . $halign . ' ' . $class . ' ' . $trigger . '">';
		$html.='<div class="wrap">';
		$html.=$img?'<div class="tooltip-img"><img src="' . $img . '"></div>':'';
		$html.=$title?'<h4 class="tooltip-title">' . $title . '</h4>':'';
		$html.= $content;
		if ($callout) {
			$callout_uri = self::$PLUGIN_URI . 'assets/img/' . $callout . '/callout_'. $valign . '.png';
			$html.= '<img class="tooltip-callout" data-no-lazy="1" src="' . $callout_uri . '">';
		}
		$html.='</div>';
		$html.='</div>';
		
		return $html;
	}
	
	/* =================================================================*/
	/* = DISPLAY TOOLTIP    
	/* =================================================================*/
	public static function display( $args) {
		echo self::get( $args );
	}
	
}



