<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class WPSSM_Admin {
	
	use Utilities;

 	/* Class arguments */
 	private $args;
 	private $plugin_name;
 	private $plugin_submenu;
 	private $plugin_version;
 	private $form_action;
 	private $nonce;
	private $record;
	private $optimize;
	private $javasync;
	private $sizes;
	
	/* Local class attributes */
	private $page; 	
		
	/* Objects */ 																									
	protected $Assets;														
	protected $Output;														

	public function __construct( $args ) {
		PHP_Debug::trace('*** In WPSSM_Admin __construct ***' );		
  	$this->args = $args;									
  	$this->hydrate_args( $args );		
	}														
														
	public function init_admin_cb() {
		PHP_Debug::trace( 'In WPSSM_Admin init_admin_cb()' );								
		if ( !is_admin() ) return;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options-assets.php' ;				
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-assets-display.php' ;		
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpssm-admin-output.php' ;	
	
		$this->Assets = 	new WPSSM_Assets_Display( $this->args );									
		$this->Output = 	new WPSSM_Admin_Output( $this->Assets, 	$this->args);

		// Initialize options settings 
		$this->page = $this->get_page_structure( $this->get_tab() );

		// Prepare assets to display
		PHP_Debug::trace('In WPSSM_Admin init_admin(), $this->get_tab()', $this->get_tab() );	
		$this->init_settings( $this->page );						
	}

	public function get_page_structure( $tab ) {
		if ( $tab == 'general' ) return array(
					'slug'=>'general_settings_page',
					'sections'=> array(
							array(
							'slug'=>'general_settings_section', 
							'title'=>'General Settings Section',
							'fields' => array(
										'record' => array(
													'slug' => 'wpssm_record',
													'title' => 'Record enqueued scripts & styles in frontend',
													'callback' => array($this->Output,'toggle_switch_recording_cb'),
													),
										'optimize' => array(
													'slug' => 'wpssm_optimize',
													'title' => 'Optimize scripts & styles in frontend',
													'callback' => array($this->Output,'toggle_switch_optimize_cb'),
													),	
										'javasync' => array(
													'slug' => 'wpssm_javasync',
													'title' => 'Allow improved asynchronous loading of scripts via javascript',
													'callback' => array($this->Output,'toggle_switch_javasync_cb'),
													),	
										),
							),
							array(
							'slug'=>'general_info_section', 
							'title'=>'General Information',
							'fields' => array(
										'pages' => array(
													'slug' => 'wpssm_recorded_pages',
													'title' => 'Recorded pages',
													'label_for' => 'wpssm-recorded-pages',
													'class' => 'foldable',
													'callback' => array($this->Output,'pages_list'),
													),	
										),
							),
					),
		);
									
		if ( $tab == 'scripts' ) return array(
					'slug'=>'enqueued_scripts_page',
					'sections'=> array(
								array(
								'slug'=>'enqueued_scripts_section', 
								'title'=>'Enqueued Scripts Section',
								'fields' => array(
											'header' => array(
														'slug' => 'wpssm_header_enqueued_scripts',
														'title' => 'Scripts loaded in Header',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->Output, 'header_items_list'),
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_scripts',
														'title' => 'Scripts loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->Output, 'footer_items_list'),
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_scripts',
														'title' => 'Scripts loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->Output, 'async_items_list'),
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_scripts',
														'title' => 'Disabed Scripts',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->Output, 'disabled_items_list'),
														),											
											)
								)
					),
		);
	
		if ( $tab == 'styles' ) return array(		
					'slug'=>'enqueued_styles_page',
					'sections'=> array(
								array(
								'slug'=>'enqueued_styles_section', 
								'title'=>'Enqueued Styles Section',
								'fields' => array(
											'header' => array(
														'slug' => 'wpssm_header_enqueued_styles',
														'title' => 'Styles loaded in Header',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->Output, 'header_items_list'),
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_styles',
														'title' => 'Styles loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->Output, 'footer_items_list'),
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_styles',
														'title' => 'Styles loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->Output, 'async_items_list'),
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_styles',
														'title' => 'Disabled Styles',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-disabled-styles',
														'class' => 'foldable',
														'callback' => array($this->Output, 'disabled_items_list'),
														),											
											),
								),
					),
		);				
	}



/* MENU OPTION
----------------------------------------------------------*/

	public function add_plugin_menu_option_cb() {
		PHP_Debug::trace('In add_plugin_menu_option_cb');								
		$page_id = add_submenu_page(
      $this->plugin_submenu,
      'WP Scripts & Styles Manager',
      'Scripts & Styles Manager',
      'manage_options',
      $this->plugin_name,
      array($this, 'output_options_page_cb' )
	    );
	  /* Add hook for admin notice display on page load */  
		add_action( "load-$page_id", 								array( $this, 'load_option_page_cb' ) );
		add_action( "admin_print_scripts-$page_id", array( $this, 'enqueue_admin_scripts_cb' ) );
	}
	
	public function load_option_page_cb() {
		//PHP_Debug::trace('In load_option_page_cb function');
		if (isset ( $_GET['msg'] ) )
			add_action( 'admin_notices', array ( $this->Output, 'admin_notice_cb' ) );
	}

	public function enqueue_admin_scripts_cb() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpssm-admin.css', array(), $this->plugin_version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpssm-admin.js', array( 'jquery' ), $this->plugin_version, false );
	}
		

/* SETTINGS INIT FOR OPTION PAGES
----------------------------------------------------------*/
	
	public function init_settings( $page ) {
			PHP_Debug::trace('In WPSSM_Admin init_settings');
			PHP_Debug::trace('=> $this->settings_pages_structure[$this->get_tab()]', $page);
	    // register all settings, sections, and fields
    	foreach ( $page['sections'] as $section ) {
    		PHP_Debug::trace('register loop - sections', $section );
				add_settings_section(
	        $section['slug'],
	        $section['title'],
	        array($this->Output,'section_headline'),
	        $page['slug']
	    	);	
    		foreach ($section['fields'] as $field => $settings) {
    			PHP_Debug::trace('register loop - fields', array($field => $settings));
    			register_setting($section['slug'], $settings['slug']);
    			if (isset($settings['stats'])) {
    				$count=$this->Assets->get_group_stat($field, 'count');
    				$size=$this->Assets->get_group_stat($field, 'size');
    				$stats=sprintf($settings['stats'],$count,size_format($size));
    			} else $stats='';
    			$label=(isset($settings['label_for']))?$settings['label_for']:'';
    			$class=(isset($settings['class']))?$settings['class']:'';
			    add_settings_field(
			        $settings['slug'],
			        $settings['title'] . ' ' . $stats,
			        $settings['callback'],
			        $page['slug'],
			        $section['slug'],
			        array( 
			        	'label_for' => $label,
			        	'class' => $class)
				  );	    			
		    }      
	    } 	
	}	
	
/* OPTIONS PAGE OUTPUT CALLBACKS
----------------------------------------------------------------*/
	public function output_options_page_cb() {
		$this->Output->options_page( $this->page['slug'] );
	}



}

