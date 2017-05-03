<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Admin extends WPSSM {

	const SIZE_SMALL = 1000;
	const SIZE_LARGE = 1000;
	const SIZE_MAX = 200000;

	protected $config_settings_pages; // Initialized in hydrate_settings
	
	protected $displayed_assets = array();
	
	protected $form_action = 'wpssm_update_settings';
	protected $nonce = 'wp8756';

	protected $header_scripts;
	protected $header_styles;
	protected $active_tab;
	
	protected $filter_args = array( 'location' => 'header' );
	protected $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);


	public function __construct( 	$plugin_name, 
																$version, 
																$submenu,
																$opt_general_settings,
																$opt_enqueued_assets,
																$opt_mods							) {
																	
		$this->plugin_name = 					$plugin_name;
		$this->version = 							$version;
		$this->submenu = 							$submenu;
		$this->opt_general_settings = $opt_general_settings;
		$this->opt_enqueued_assets = 	$opt_enqueued_assets;
		$this->opt_mods = 						$opt_mods;
		
	}														
														
	public function enqueue_styles() {
		WPSSM_Debug::log('In WPSSM_Admin enqueue styles');
		WPSSM_Debug::log('In WPSSM_Admin enqueue styles : $this->opt_general_settings ', $this->opt_general_settings );
		WPSSM_Debug::log('In WPSSM_Admin enqueue styles : $this->opt_enqueued_assets', $this->opt_enqueued_assets );
		WPSSM_Debug::log('In WPSSM_Admin enqueue styles : $this->opt_mods', $this->opt_mods);
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpssm-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		WPSSM_Debug::log('In WPSSM_Admin enqueue scripts');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpssm-admin.js', array( 'jquery' ), $this->version, false );
	}
	
	
	public function hydrate() {	
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
		WPSSM_Debug::log('In hydrate admin, $this->displayed_assets', $this->displayed_assets);								
	}


	public function add_plugin_menu_option_cb() {
		$opt_page_id = add_submenu_page(
      $this->submenu,
      'WP Scripts & Styles Manager',
      'Scripts & Styles Manager',
      'manage_options',
      $this->plugin_name,
      array($this, 'output_options_page')
	    );
		add_action( "load-$opt_page_id", array ( $this, 'load_option_page_cb' ) );
	}
	

	public function admin_init_cb() {
			// Hydrate option class properties
			$this->hydrate();
			WPSSM_Debug::log('In admin init : $this->config_settings_pages', $this->config_settings_pages);
			
			$page = $this->config_settings_pages[$this->active_tab];
			WPSSM_Debug::log('In admin init : $this->config_settings_pages[$this->active_tab]', $page);
	    // register all settings, sections, and fields
    	foreach ( $page['sections'] as $section ) {
    		//WPSSM_Debug::log('register loop - sections', $section );
				add_settings_section(
	        $section['slug'],
	        $section['title'],
	        array($this,'output_section_cb'),
	        $page['slug']
	    	);	
    		foreach ($section['fields'] as $handler => $field) {
    			//WPSSM_Debug::log('register loop - fields', array($handler => $field));
    			register_setting($section['slug'], $field['slug']);
    			if (isset($field['stats'])) {
    				$count=$this->displayed_assets[$this->active_tab][$handler]['count'];
    				$size=$this->displayed_assets[$this->active_tab][$handler]['size'];
    				$stats=sprintf($field['stats'],$count,size_format($size));
    			} else $stats='';
    			$info=(isset($field['stats']))?sprintf($field['stats'],$count,size_format($size)):'';
    			$label=(isset($field['label_for']))?$field['label_for']:'';
    			$class=(isset($field['class']))?$field['class']:'';
			    add_settings_field(
			        $field['slug'],
			        $field['title'] . ' ' . $stats,
			        array($this, $field['callback']),
			        $page['slug'],
			        $section['slug'],
			        array( 
			        	'label_for' => $label,
			        	'class' => $class)
				  );	    			
		    }      
	    } 	
	}	
	

/* OPTIONS PAGE OUTPUT
--------------------------------------------------------------*/	
	public function output_options_page() {
	    // check user capabilities
	    if (!current_user_can('manage_options')) return;			
			$referer = menu_page_url( $this->plugin_name, FALSE  );
			?>

	    <div class="wrap">
	        <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        
						<h2 class="nav-tab-wrapper">
						<a href="?page=<?php echo $this->plugin_name;?>&tab=general" class="nav-tab <?php echo $this->active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
						<a href="?page=<?php echo $this->plugin_name;?>&tab=scripts" class="nav-tab <?php echo $this->active_tab == 'scripts' ? 'nav-tab-active' : ''; ?>">Scripts</a>
						<a href="?page=<?php echo $this->plugin_name;?>&tab=styles" class="nav-tab <?php echo $this->active_tab == 'styles' ? 'nav-tab-active' : ''; ?>">Styles</a>
						</h2>
	        
		        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
	        		<?php	
							$this->output_form_buttons($referer);
							settings_fields($this->config_settings_pages[$this->active_tab]['slug']);
							do_settings_sections($this->config_settings_pages[$this->active_tab]['slug']);
							$this->output_form_buttons($referer);
	        		?>	
	        
		        </form>  
	    </div>
	    <?php
	}
	
	protected function output_form_buttons($referer) { 
		?>
		<!-- Output form buttons -->
	  <table class="button-table" col="2">
	    <tr>
				<input type="hidden" name="action" value="<?php echo $this->form_action; ?>">
				<?php wp_nonce_field( $this->form_action, $this->nonce, FALSE ); ?>
				<input type="hidden" name="_wpssm_http_referer" value="<?php echo $referer; ?>">
				<input type="hidden" name="_wpssm_active_tab" value="<?php echo $this->active_tab; ?>">

	    	<td><?php submit_button( 'Save ' . $this->active_tab . ' settings', 'primary', 'wpssm_save', true, array('tabindex'=>'1') );?> </td>
	    	<?php if ($this->active_tab != 'general') { ?>
	    	<td><?php submit_button( 'Reset ' . $this->active_tab . ' settings', 'secondary', 'wpssm_reset', true, array('tabindex'=>'2') );?> </td>
	    	<?php } ?>
	    	<td><?php submit_button( 'Delete everything', 'delete', 'wpssm_delete', true, array('tabindex'=>'3') );?> </td>
	  	</tr>
	  </table>
	<?php 
	}
	


/* FORM SUBMISSION
--------------------------------------------------------------*/

	public function update_settings_cb() {
		
		WPSSM_Debug::log('IN update_settings_cb !!!!!');

		// check user capabilities
    if (!current_user_can('manage_options'))
        return;

    if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->form_action ) )
        die( 'Invalid nonce.' . var_export( $_POST, true ) );
		//WPSSM_Debug::log('In update_settings_cb function');
		
		if ( ! isset ( $_POST['_wpssm_http_referer'] ) )
		    die( 'Missing valid referer' );
		else
			$url = $_POST['_wpssm_http_referer'];
		
		$type = isset($_POST[ '_wpssm_active_tab' ])?$_POST[ '_wpssm_active_tab' ]:'general';
		$query_args=array();
		$query_args['tab']=$type;
		
		if ( isset ( $_POST[ 'wpssm_reset' ] ) ) {
		   	WPSSM_Debug::log( 'In Form submission : RESET' );
				WPSSM_Debug::log( 'assets before submission' , $this->opt_enqueued_assets );
				foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
					unset($this->opt_enqueued_assets[$type][$handle]['mods']); 
					$this->update_priority( $type, $handle ); 
				}
				WPSSM_Debug::log( 'assets after submission',$this->opt_enqueued_assets);
				update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets);
		    $query_args['msg']='reset';
		}
		elseif ( isset ( $_POST[ 'wpssm_delete' ] ) ) {
		   	WPSSM_Debug::log( 'In Form submission : DELETE' );
		    $this->opt_enqueued_assets = array();
		    $this->opt_general_settings = array();
		    update_option( 'wpssm_enqueued_assets', array() );
		    update_option( 'wpssm_general_settings', array() );
		    $query_args['msg']='delete';
		}
		else {
				WPSSM_Debug::log( 'In Form submission : SAVE, tab ' . $type );
				if ( $type=='general' ) {
					//WPSSM_Debug::log('general save $this->opt_general_settings' ,$this->opt_general_settings);
					$settings=array('record','optimize');
					foreach ($settings as $setting) {
						$this->opt_general_settings[$setting]= isset($_POST[ 'general_' . $setting . '_checkbox' ])?$_POST[ 'general_' . $setting . '_checkbox' ]:'off';
					}			
					update_option( 'wpssm_general_settings', $this->opt_general_settings );
				}
				else {
					$this->mods[$type]=array();	
					WPSSM_Debug::log( 'assets before submission',$this->opt_enqueued_assets );
					foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
						//WPSSM_Debug::log( array('Looping : asset = ' => $asset ) );
						//WPSSM_Debug::log( array('Looping : handle = ' => $handle ) );
						$result=$this->update_mod($type, $handle, 'location');
						if ($result[0]) $this->mods[$type][$result[1]][]=$handle;
						$result=$this->update_mod($type, $handle, 'minify');
						if ($result[0]) $this->mods[$type]['minify'][]=$handle;
						$this->update_priority( $type, $handle ); 
					}
					WPSSM_Debug::log( 'opt_enqueued_assets after submission',$this->opt_enqueued_assets);				
					WPSSM_Debug::log( '$this->mods after submission',$this->mods);				
					update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets);
					update_option( 'wpssm_mods', $this->mods);
				}
		    $query_args['msg']='save';
		}

		WPSSM_Debug::log('http referer',$url);
		$url = add_query_arg( $query_args, $url) ;
		WPSSM_Debug::log('url for redirection',$url);
					 
		wp_safe_redirect( $url );
		exit;
	}
	
	public function update_mod( $type, $handle, $field ) {
		$is_mod=false;
		$val='';
		$input = $this->get_field_name($type, $handle, $field);
		if ( ( isset($_POST[ $input ] )) && ( $_POST[ $input ] != $this->opt_enqueued_assets[$type][$handle][$field]  ) ) {
			WPSSM_Debug::log( 'Asset field modified (mods) !' ,$this->opt_enqueued_assets[$type][$handle]);
			//WPSSM_Debug::log( 'input name', $input );
			//WPSSM_Debug::log( 'POST content for this field',$_POST[ $input ] );
			$val = esc_html($_POST[ $input ]);
			$this->opt_enqueued_assets[$type][$handle]['mods'][$field] = $val;
			$is_mod=true;
		}
		elseif ( isset( $this->opt_enqueued_assets[$type][$handle]['mods'][$field]) ) {
			unset($this->opt_enqueued_assets[$type][$handle]['mods'][$field]);
			WPSSM_Debug::log( 'Mod Field removed !' ,$this->opt_enqueued_assets[$type][$handle] );
		}
		return array($is_mod, $val);
	}

  public function load_option_page_cb() {
		//WPSSM_Debug::log('In load_option_page_cb function');
		if (isset ( $_GET['msg'] ) )
			add_action( 'admin_notices', array ( $this, 'render_msg' ) );
	}

	public function render_msg() {
		?>
		<div class="notice notice-success is-dismissible">
        <p><?php echo 'JCO settings update completed : ' . esc_attr( $_GET['msg'] ) ?></p>
    </div>
		<?php
	}












}
