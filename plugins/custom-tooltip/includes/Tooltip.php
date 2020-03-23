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
		add_action( 'wp_enqueue_scripts', 	array($this, 'register_tooltip_assets') );
		// Overlay support
		add_action( 'genesis_after',		array($this, 'add_overlay_markup') );
		// Shortcodes
		add_shortcode( 'tooltip', 			array($this, 'output_tooltip_shortcode') );
	}

	public function add_overlay_markup() {
		static $once=false;
		if ($once) return;
		$once=true;
		?>
		<!-- Overlay markup for Tooltip plugin  -->
		<div class="tooltip-overlay nodisplay"></div>
		<?php
	}

	// public function enqueue_easing_script() {
    //     if (! is_single() ) return;
	// 	wp_enqueue_script( 'jquery-easing', self::$PLUGIN_URI . '/vendor/easing/jQuery_Easing.min.js', array( 'jquery' ), CHILD_THEME_VERSION, true);
	// }

	public function register_tooltip_assets() {
		custom_register_style( 'tooltip', '/assets/css/tooltip.css', self::$PLUGIN_URI, self::$PLUGIN_PATH, array(), CHILD_THEME_VERSION );
		custom_register_script( 'tooltip', '/assets/js/tooltip.js', self::$PLUGIN_URI, self::$PLUGIN_PATH, array(), CHILD_THEME_VERSION, true );
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

        return self::getContent( $atts );
    }

	/* =================================================================*/
	/* = GET TOOLTIP CONTENT
	/* 	$args=array(
		'content' 	=> tooltip content
		'valign' 	=> 'above', 'below'
		'halign'	=> 'left', 'right'
		'action'	=> 'click', 'hover'
		'callout'	=> false, 'yellow', ... any other valid color theme
		'id'		=> used to trigger the content visibility in case the content is not a sibling of the trigger
		'class'		=> 'class1 class2 ...'
		'title'		=> tooltip title
		'img'		=> tooltip image
		)

		The tooltip trigger is build using the following HTML markup  :
		* add "tooltip-onclick" or "tooltip-onhover" wherever you want to place the trigger
		* IMPORTANT: for hover tooltips, the tooltip content must be placed immediately below the container with tooltip-onhover class
		* For click tooltips it is assumed by default, that the trigger and the content are siblings from each other
		* If you want to separate the content from the trigger, you can use the "id" parameter to the content, and in the trigger you will use data-tooltip-id to indicate this id

	*/
	/* =================================================================*/
	public static function getContent( $args ) {

		// Default values
		$action = 'hover';
		$title = '';
		$valign = 'top';
		$halign = 'center';
		$content = 	'';
		$img = 	'';
		$class = '';
		$id = '';
		$callout = false;
		// Put args into variables
		extract($args);

		wp_enqueue_script('tooltip');
		wp_enqueue_style('tooltip');

		$display = ($action=='hover')?'':'display:none';
		$html ='<div class="tooltip-content ' . $valign . ' ' . $halign . ' ' . $class . ' ' . $action . '" id="' . $id . '" style="' . $display . '">';
		$html.='<div class="wrap">';
		$html.=$img? '<div class="tooltip-img"><img data-skip-lazy src="' . $img . '"></div>':'';
		$html.=$title?'<h4 class="tooltip-title">' . $title . '</h4>':'';
		$html.=$content;
		if ($callout) {
			$callout_uri = self::$PLUGIN_URI . 'assets/img/' . $callout . '/callout_'. $valign . '.png';
			$html.= '<img class="tooltip-callout" data-skip-lazy src="' . $callout_uri . '">';
		}
		$html.='</div>';
		$html.='</div>';

		return $html;
	}


	/* 	=================================================================
	   		Displays Tooltip Content Function
		=================================================================*/
	public static function display( $args) {
		echo self::getContent( $args );
	}

}
