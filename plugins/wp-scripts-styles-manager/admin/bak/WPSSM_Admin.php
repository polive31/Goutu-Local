<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Admin {

	const SIZE_SMALL = 1000;
	const SIZE_LARGE = 1000;
	const SIZE_MAX = 200000;

	protected $config_settings_pages; // Initialized in hydrate_settings
	
	protected $displayed_assets = array();
	
	protected $form_action = 'wpssm_update_settings';
	protected $nonce = 'wp8756';
//	protected $urls_to_request= array(
//																home_url(),
//																$this->get_permalink_by_slug('bredele'),
//																$this->get_permalink_by_slug('les-myrtilles'),
//															);
	protected $header_scripts;
	protected $header_styles;
	protected $active_tab;
	protected $user_notification; 
	
	public $opt_general_settings = array('record'=>'off', 'optimize'=>'off', 'javasync'=>'off', 'wpssm_version'=>self::WPSSM_VERSION);
	public $opt_enqueued_assets = array( 'pages'=>array(), 'scripts'=>array(), 'styles'=>array());
	public $opt_mods = array(
						'scripts'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'minify'=>array(),
									), 
						'styles'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'minify'=>array(),
									), 						
						);
	
	protected $filter_args = array( 'location' => 'header' );
	protected $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);

	public function __construct() {
		// Initialize attributes common to FrontEnd and Admin
		$this->hydrate_common();

		// Admin options page
		add_action( 'admin_menu', array($this, 'add_plugin_menu_option_cb') );
		add_action( 'admin_menu', array($this, 'admin_init_cb') );
		add_action( 'admin_post_' . $this->form_action, array ( $this, 'update_settings_cb' ) );
    add_action( 'admin_enqueue_scripts', array($this,'load_admin_assets_cb') );
		
		// manage frontend pages recording 
		if ( $this->opt_general_settings['record'] == 'on' ) {
			add_action( 'wp_head', array($this, 'record_header_assets_cb') );
			add_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets_cb') );
		}
		else {
			remove_action( 'wp_head', array($this, 'record_header_assets_cb') );
			remove_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets_cb') );
		}		
	}
	
	public function load_admin_assets_cb() {
		PHP_Debug::log('In load_admin_assets_cb');
		//PHP_Debug::log( plugins_url( '/css/wpssm_options_page.css', __FILE__ ) );
  	wp_enqueue_style( 'wpssm_admin_css', plugins_url( '../admin/css/wpssm_options_page.css', __FILE__ ) , false, self::WPSSM_VERSION );
  	wp_enqueue_script( 'wpssm_admin_js', plugins_url( '../admin/js/wpssm_options_page.js', __FILE__ ) , array('jquery'), self::WPSSM_VERSION );
	}
	

	public function hydrate_admin() {	
		// Initialize all attributes related to admin mode
		$this->config_settings_pages = array(
			'general' => array(
					'slug'=>'general_settings_page',
					'sections'=> array(
							array(
							'slug'=>'general_settings_section', 
							'title'=>'General Settings Section',
							'fields' => array(
										'record' => array(
													'slug' => 'wpssm_record',
													'title' => 'Record enqueued scripts & styles in frontend',
													'callback' => 'output_toggle_switch_recording_cb',
													),
										'optimize' => array(
													'slug' => 'wpssm_optimize',
													'title' => 'Optimize scripts & styles in frontend',
													'callback' => 'output_toggle_switch_optimize_cb',
													),	
										'javasync' => array(
													'slug' => 'wpssm_javasync',
													'title' => 'Allow improved asynchronous loading of scripts via javascript',
													'callback' => 'output_toggle_switch_javasync_cb',
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
													'callback' => 'output_pages_list',
													),	
										),
							),
					),
			),	
			'scripts' => array(
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
														'callback' => 'output_header_scripts_list',
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_scripts',
														'title' => 'Scripts loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => 'output_footer_scripts_list',
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_scripts',
														'title' => 'Scripts loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => 'output_async_scripts_list',
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_scripts',
														'title' => 'Disabed Scripts',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => 'output_disabled_scripts_list',
														),											
											)
								)
					),
			),
			'styles' => array(		
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
														'callback' => 'output_header_styles_list',
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_styles',
														'title' => 'Styles loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => 'output_footer_styles_list',
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_styles',
														'title' => 'Styles loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => 'output_async_styles_list',
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_styles',
														'title' => 'Disabled Styles',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-disabled-styles',
														'class' => 'foldable',
														'callback' => 'output_disabled_styles_list',
														),											
											),
								),
					),
			),
		);
		// Get active tab
		$this->active_tab = isset( $_GET[ 'tab' ] ) ? esc_html($_GET[ 'tab' ]) : 'general';
		// Prepare assets to disply
		if ($this->active_tab != 'general') $this->prepare_displayed_assets($this->active_tab);
		PHP_Debug::log('In hydrate admin, $this->displayed_assets', $this->displayed_assets);								
	}

	
	function add_plugin_menu_page() { 
    add_menu_page(
        'WP Scripts & Styles Manager', // The title to be displayed on the corresponding page for this menu
        'Scripts & Styles',                  // The text to be displayed for this actual menu item
        'administrator',            // Which type of users can see this menu
        'wpssm',                  // The unique ID - that is, the slug - for this menu item
        'wpssm_menu_page_display',// The name of the function to call when rendering the menu for this page
        ''
    );
	}






}

