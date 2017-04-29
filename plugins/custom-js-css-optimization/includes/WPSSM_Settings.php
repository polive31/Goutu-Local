<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Settings {

	protected static $SIZE_SMALL = 1000;
	protected static $SIZE_LARGE = 1000;
	protected static $SIZE_MAX = 200000;
	protected static $default_section = 'general_settings_section';
	
	protected $plugin_slug = 'wpssm';

	protected $config_settings_pages = array(
		'general' => array(
				'slug'=>'general_settings_page',
				'sections'=> array(
						array('slug'=>'general_settings_section', 'title'=>'General Settings Section'),
						array('slug'=>'general_info_section', 'title'=>'General Information'),
				)),
		'scripts' => array(
				'slug'=>'enqueued_scripts_page',
				'sections'=> array(
						array('slug'=>'enqueued_scripts_section', 'title'=>'Enqueued Scripts Section'),
				)),
		'styles' => array(
				'slug'=>'enqueued_styles_page',
				'sections'=> array(
						array('slug'=>'enqueued_styles_section', 'title'=>'Enqueued Styles Section'),
				)),
	);
	
	
	protected $form_action = 'wpssm_update_settings';
	protected $nonce = 'wp8756';
	protected $urls_to_request;
	protected $header_scripts;
	protected $header_styles;
	
	public $opt_general_settings = array('record'=>'off', 'optimize'=>'off');
	public $opt_enqueued_assets = array( 'pages'=>array(), 'scripts'=>array(), 'styles'=>array());

	protected $displayed_assets;
	protected $user_notification; 
	
	protected $filter_args = array( 'location' => 'header' );
	protected $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);

	public function __construct() {
		// Hydrate option class properties
		$this->hydrate();
			
		// Admin options page
		add_action( 'admin_menu', array($this, 'add_plugin_menu_option'));
		add_action( 'admin_init', array($this, 'init_settings_cb') );
		//add_action( 'admin_post_$this->action', array ( $this, 'update_settings_cb' ) );
		add_action( 'admin_post_' . $this->form_action, array ( $this, 'update_settings_cb' ) );

		// load assets for this page
    add_action( 'admin_enqueue_scripts', array($this,'load_admin_assets') );

		// configure AJAX actions
		//add_action( 'wp_ajax_my-action', array($this,'ajax_my_action_cb') );
		
		// manage frontend pages monitoring 
		//echo '<pre>In WPSSM Construct</pre>';
		if ( $this->opt_general_settings['record'] == 'on' ) {
			//echo '<pre>RECORD=ON</pre>';
			add_action( 'wp_head', array($this, 'record_header_assets_cb') );
			add_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets_cb') );
		}
		else {
			remove_action( 'wp_head', array($this, 'record_header_assets_cb') );
			remove_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets_cb') );
		}
		
	}

	public function load_admin_assets() {
		DBG::log('In load_admin_styles');
		DBG::log( plugins_url( '/css/wpssm_options_page.css', __FILE__ ) );

  	wp_enqueue_style( 'wpssm_admin_css', plugins_url( '../assets/css/wpssm_options_page.css', __FILE__ ) , false, '1.0.0' );
  	wp_enqueue_script( 'wpssm_admin_js', plugins_url( '../assets/js/wpssm_options_page.js', __FILE__ ) , false, '1.0.0' );
		wp_localize_script('wpssm_admin_js', 'WPLocalizeVar', array(
																					'url'=> admin_url( 'admin-ajax.php' ),
																					'nonce'=> wp_create_nonce( 'check-script-dependencies' ),
																					));
	}
	
	public function hydrate() {
		//pages to record
		$this->urls_to_request = array(
															home_url(),
															$this->get_permalink_by_slug('bredele'),
															$this->get_permalink_by_slug('les-myrtilles'),
														);
									
		// hydrate general settings property with options content
		$get_option = get_option( 'wpssm_general_settings' );
		if ($get_option!=false)
			$this->opt_general_settings=$get_option; 


		// hydrate enqueued assets property with options content
		$get_option = get_option( 'wpssm_enqueued_assets' );
		if ($get_option!=false)
			$this->opt_enqueued_assets = $get_option;
		
							
		// Preparation of data to be displayed
    $types=array('scripts', 'styles');
    $locations=array('header', 'footer', 'disabled');
		foreach ($types as $type) {
			if (! isset ( $this->opt_enqueued_assets[$type] ) ) continue;
			$assets = $this->opt_enqueued_assets[$type];
			foreach ($locations as $location) {
				$this-> filter_args = array( 'location' => $location );
				$filtered_assets = array_filter($assets, array($this, 'filter_assets') );	
				$this-> displayed_assets[$type][$location]['assets']=$filtered_assets;
				$this-> displayed_assets[$type][$location]['count']=count($filtered_assets);
				$this-> displayed_assets[$type][$location]['size']=array_sum( array_column( $filtered_assets, 'size'));
			}	
		}
		//DBG::log( array( '$this->displayed_assets: '=>$this->displayed_assets ));
	
	}
	
	public function filter_assets( $asset ) {
		return ( $this->get_field_value( $asset, 'location' ) == $this->filter_args['location'] );
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

	public function add_plugin_menu_option() {
		$opt_page_id = add_submenu_page(
      //'options-general.php',
      'tools.php',
      'WP Scripts & Styles Manager',
      'Scripts & Styles Manager',
      'manage_options',
      $this->plugin_slug,
      array($this, 'output_options_page')
	    );

		add_action( "load-$opt_page_id", array ( $this, 'load_option_page_cb' ) );
	}

	public function init_settings_cb() {
		
	    // register options
	    register_setting($this->config_settings_pages['general']['slug'], 'wpssm_record');
	    register_setting($this->config_settings_pages['general']['slug'], 'wpssm_optimize');
	    register_setting($this->config_settings_pages['general']['slug'], 'wpssm_enqueue_stats');
	    
	    register_setting($this->config_settings_pages['scripts']['slug'], 'wpssm_header_enqueued_scripts');
	    register_setting($this->config_settings_pages['scripts']['slug'], 'wpssm_footer_enqueued_scripts');
	    register_setting($this->config_settings_pages['scripts']['slug'], 'wpssm_disabled_scripts');
	    
	    register_setting($this->config_settings_pages['styles']['slug'], 'wpssm_header_enqueued_styles');
	    register_setting($this->config_settings_pages['styles']['slug'], 'wpssm_footer_enqueued_styles');
	    register_setting($this->config_settings_pages['styles']['slug'], 'wpssm_disabled_styles');

	    
	    // register all sections
	    foreach ($this->config_settings_pages as $page) {
	    	foreach ($page['sections'] as $section) {
					add_settings_section(
		        $section['slug'],
		        $section['title'],
		        array($this,'output_section_cb'),
		        $page['slug']
		    	);	   
	    	} 	
	    }

	    // register new fields in the general settings section
	    add_settings_field(
	        'wpssm_record',
	        'Record enqueued scripts & styles in frontend',
	        array($this,'output_recording_switch_cb'),
	        $this->config_settings_pages['general']['slug'],
	        $this->config_settings_pages['general']['sections'][0]['slug']
	    );

	    add_settings_field(
	        'wpssm_optimize',
	        'Optimize scripts & styles in frontend',
	        array($this,'output_optimize_switch_cb'),
	        $this->config_settings_pages['general']['slug'],
	        $this->config_settings_pages['general']['sections'][0]['slug']
	    );

	    // register new fields in the enqueued list section
	    add_settings_field(
	        'wpssm_recorded_pages',
	        'Recorded pages',
	        array($this,'output_pages_list'),
	        $this->config_settings_pages['general']['slug'],
	        $this->config_settings_pages['general']['sections'][1]['slug'],
					array( 
	        	'label_for' => 'jco-recorded-pages',
	        	'class' => 'foldable' )
	    );

	    // register new fields in the enqueued Scripts section;
	    $size = $this->displayed_assets['scripts']['header']['size'];
	    $count = $this->displayed_assets['scripts']['header']['count'];
	    add_settings_field(
	        'wpssm_header_enqueued_scripts',
	        'Enqueued Header Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_header_scripts_list'),
	        $this->config_settings_pages['scripts']['slug'],
	        $this->config_settings_pages['scripts']['sections'][0]['slug'],
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );
	    
			$size = $this->displayed_assets['scripts']['footer']['size'];
	    $count = $this->displayed_assets['scripts']['footer']['count'];
			add_settings_field(
	        'wpssm_footer_enqueued_scripts',
	        'Enqueued Footer Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_footer_scripts_list'),
	        $this->config_settings_pages['scripts']['slug'],
	        $this->config_settings_pages['scripts']['sections'][0]['slug'],
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );

	    $size = $this->displayed_assets['scripts']['disabled']['size'];
	    $count = $this->displayed_assets['scripts']['disabled']['count'];	    
			add_settings_field(
	        'wpssm_disabled_scripts',
	        'Disabled Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_disabled_scripts_list'),
	        $this->config_settings_pages['scripts']['slug'],
	        $this->config_settings_pages['scripts']['sections'][0]['slug'],
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );	    
	    
	    // register new fields in the enqueued Styles section
	    $size = $this->displayed_assets['styles']['header']['size'];
	    $count = $this->displayed_assets['styles']['header']['count'];
			add_settings_field(
	        'wpssm_header_enqueued_styles',
	        'Enqueued Header Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_header_styles_list'),
	        $this->config_settings_pages['styles']['slug'],
	        $this->config_settings_pages['styles']['sections'][0]['slug'],
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );
	    
	    $size = $this->displayed_assets['styles']['footer']['size'];
	    $count = $this->displayed_assets['styles']['footer']['count'];
			add_settings_field(
	        'wpssm_footer_enqueued_styles',
	        'Enqueued Footer Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_footer_styles_list'),
	        $this->config_settings_pages['styles']['slug'],
	        $this->config_settings_pages['styles']['sections'][0]['slug'],
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );	    

	    $size = $this->displayed_assets['styles']['disabled']['size'];
	    $count = $this->displayed_assets['styles']['disabled']['count'];	    
			add_settings_field(
	        'wpssm_disabled_styles',
	        'Disabled Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_disabled_styles_list'),
	        $this->config_settings_pages['styles']['slug'],
	        $this->config_settings_pages['styles']['sections'][0]['slug'],
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );	    
	}

	public function output_section_cb( $section ) {
		//DBG::log('In section callback');
	}

	public function output_recording_switch_cb() {
		$this->output_toggle_switch( 'general_record', $this->opt_general_settings['record']);
	}	

	public function output_optimize_switch_cb() {
		$this->output_toggle_switch( 'general_optimize', $this->opt_general_settings['optimize']);
	}		

	protected function output_toggle_switch( $input_name, $value ) {
		DBG::log( 'in output toggle switch for ' . $input_name , $value);
		$checked = ( $value == 'on')?'checked="checked"':'';
		?>
		<label class="switch">
  	<input type="checkbox" name="<?php echo $input_name;?>_checkbox" <?php echo $checked;?> value="on">
  	<div class="slider round"></div>
		</label>
		<?php
	}

	public function output_pages_list() {
		foreach ($this->opt_enqueued_assets['pages'] as $page) {
			echo '<p>' . $page[0] . ' on ' . $page[1] . '</p>';
		}
	}

	public function output_header_scripts_list() {
		$this->	output_items_list( 'scripts', 'header' );
	}
	
	public function output_footer_scripts_list() {
		$this->	output_items_list('scripts', 'footer' );
	}
	
	public function output_disabled_scripts_list() {
		$this->	output_items_list('scripts', 'disabled' );
	}
	
	public function output_header_styles_list() {
		$this->	output_items_list('styles', 'header' );
	}
	
	public function output_footer_styles_list() {
		$this->	output_items_list('styles', 'footer' );
	}
	
	public function output_disabled_styles_list() {
		$this->	output_items_list('styles', 'disabled' );
	}
	
		
	public function get_sorted_list( $assets ) {

		$sort_field = $this->sort_args['field'];
		$sort_order = $this->sort_args['order'];
		$sort_type = $this->sort_args['type'];

		$list = array_column($assets, $sort_field, 'handle' );		
		//DBG::log( array( 'sorted list : '=>$list ));

		if ( $sort_order == SORT_ASC)
			asort($list, $sort_type );
		else 
			arsort($list, $sort_type );
		
//		foreach ($sort_column as $key => $value) {
//			echo '<p>' . $key . ' : ' . $value . '<p>';
//		}

		return $list;
		
	}

	public function output_items_list( $type, $location ) {
		DBG::log('in output_items_list');
		DBG::log('this->enqueued_assets',$this->opt_enqueued_assets);
		$assets = $this->displayed_assets[$type][$location]['assets'];
		//DBG::log( array('$this->displayed_assets' => $assets));
		$sorted_list = $this->get_sorted_list( $assets );
		//DBG::log( array('$sorted_list' => $sorted_list));
		
		?>
		
    <table class="enqueued-assets">
    	<tr>
    		<th> handle </th>
    		<th> priority </th>
    		<!--<th> Dependencies </th>-->
    		<th> Dependents </th> 
    		<th> File size </th>
    		<th> Location </th>
    		<th> Minify </th>
    	</tr>
    	
    <?php
    foreach ($sorted_list as $handle => $priority ) {
    	$asset = $assets[$handle];
			//DBG::log(array('Asset in output_items_list : ' => $asset));
    	$filename = $asset['filename'];
    	$dependencies = $asset['dependencies'];
    	$dependents = $asset['dependents'];
    	$location = $this->get_field_value( $asset, 'location');
	    $minify = $this->get_field_value( $asset, 'minify');
	    $size = $this->get_field_value( $asset, 'size');
	    $asset_is_minified = ( $asset[ 'minify' ] == 'yes')?true:false; 
	    $already_minified_msg = __('This file is already minimized within its plugin', 'jco');
	    
    	?>
    	
    	<tr class="enqueued-asset <?php echo $type;?>" id="<?php echo $handle;?>">
	    	<td class="handle" title="<?php echo $filename;?>"><?php echo $handle;?><?php $this->output_user_notification( $asset );?></td>
	    	
	    	<td><?php echo $priority;?></td>
	    	
	    	<!-- <td class="dependencies"><?php foreach ($dependencies as $dep) {echo $dep . '<br>';}?></td> -->
	    	<td class="dependents"><?php foreach ($dependents as $dep) {echo $dep . '<br>';}?></td>
	    	
	    	<td class="size" title="<?php echo $filename;?>"><?php echo size_format( $size );?></td>
	    	
	    	<td class="location <?php echo $this->is_modified( $asset, 'location');?>">
	    		<select data-dependencies='<?php echo json_encode($dependencies);?>' data-dependents='<?php echo json_encode($dependents);?>' id="<?php echo $handle;?>" class="asset-setting location <?php echo $type;?>" name="<?php echo $this->get_field_name( $type, $handle, 'location');?>">
  					<option value="header" <?php echo ($location=='header')?'selected':'';?> >header</option>
  					<option value="footer" <?php echo ($location=='footer')?'selected':'';?> >footer</option>
  					<option value="disabled" <?php echo ($location=='disabled')?'selected':'';?>>disabled</option>
					</select>
				</td>
				
				<td class="minify <?php echo $this->is_modified( $asset, 'minify');?>">
	    		<select id="<?php echo $handle;?>" class="asset-setting minify <?php echo $type;?>" <?php echo ($asset_is_minified)?'disabled':'';?> <?php echo ($asset_is_minified)?'title="' . $already_minified_msg . '"' :'';?> name="<?php echo $this->get_field_name( $type, $handle, 'minify');?>">
  					<option value="no" <?php echo ($minify=='no')?'selected':'';?>  >no</option>
  					<option value="yes" <?php echo ($minify=='yes')?'selected':'';?> >yes</option>
					</select>
				</td>
    	
    	</tr>
    	<?php
    }?>
    </table>

		<?php
	}
	
	private function output_user_notification( $asset ) {
		
		$size= $asset['size'];
		//DBG::log(array('size : '=>$size));
		$is_minified = $this->get_field_value( $asset, 'minify') == 'yes';
		//DBG::log(array('is_minified: '=>$is_minified));
		$in_footer = ( $this->get_field_value( $asset, 'location') == 'footer');
		
		$this->reset_user_notification();
		if (!$is_minified) {
			if ( $size > self::$SIZE_LARGE ) {
				$level = 'issue';
				$msg = __('This file is large and not minified : minification highly recommended', 'jco');	
				$this->enqueue_user_notification( $msg, $level);
			}
			elseif ( $size != 0 ) {
				$level = 'warning';
				$msg = __('This file is not minified : minification recommended', 'jco');	
				$this->enqueue_user_notification( $msg, $level);
			}
		}

		if ( ( $size > self::$SIZE_LARGE ) && ( !$in_footer ) ) {
			$level = 'issue';
			$msg = __('Large files loaded in the header will slow down page display : moving to footer or at least conditional enqueue recommended', 'jco');			
			$this->enqueue_user_notification( $msg, $level);
		}	
		
		if ( ( $size < self::$SIZE_SMALL ) && (!isset( $asset['in_group']) ) ) {
			$level = 'warning';
			$msg = __('This file is small and requires a specific http request : it is recommended to inline it, or to group it with other files', 'jco');			
			$this->enqueue_user_notification( $msg, $level);
		}	
		
		echo $this->user_notification;
		
	}
	
	private function reset_user_notification() {
		$this->user_notification='';
	}
	
	private function enqueue_user_notification( $msg, $level) {
		if ($msg != '') {
			$this->user_notification .= '<i class="user-notification" id="' . $level . '" title="' . $msg . '"></i>';
		}		
	}
	
	protected function is_modified( $asset, $field ) {
		if ( isset( $asset['mods'][ $field ] ) ) {
			return 'modified';
		}
	}
	
	protected function get_field_name( $type, $handle, $field ) {
		return  $type . '_' . $handle . '_' . $field;
	}
	
	protected function get_field_value( $asset, $field ) {
		//DBG::log('In Field Value for ' . $field);
		//DBG::log(array('Asset : ' => $asset));
		if ( isset( $asset['mods'] ) && (isset( $asset['mods'][ $field ] ) ) ) {
			$value=$asset['mods'][ $field ];
			//DBG::log('Mod found !');
		}
		else {
			//DBG::log('Mod not found');
			$value=$asset[ $field ];
		}
		//DBG::log( array(' Field value of ' . $field . ' : ' => $value ));
		return $value;
	}

	public function output_options_page() {
	    // check user capabilities
	    if (!current_user_can('manage_options')) {
	        return;
	    }
			
			$referer = menu_page_url( $this->plugin_slug, FALSE  );
			$active_tab = isset( $_GET[ 'tab' ] ) ? esc_html($_GET[ 'tab' ]) : 'general';
			
			?>

	    <div class="wrap">
	        <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        
						<h2 class="nav-tab-wrapper">
						<a href="?page=<?php echo $this->plugin_slug;?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
						<a href="?page=<?php echo $this->plugin_slug;?>&tab=scripts" class="nav-tab <?php echo $active_tab == 'scripts' ? 'nav-tab-active' : ''; ?>">Scripts</a>
						<a href="?page=<?php echo $this->plugin_slug;?>&tab=styles" class="nav-tab <?php echo $active_tab == 'styles' ? 'nav-tab-active' : ''; ?>">Styles</a>
						</h2>
	        
		        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
	        		<?php	
							$this->output_form_buttons($referer, $active_tab);
							settings_fields($this->config_settings_pages[$active_tab]['slug']);
							do_settings_sections($this->config_settings_pages[$active_tab]['slug']);
							$this->output_form_buttons($referer, $active_tab);
	        		?>	
	        
		        </form>  
	    </div>
	    <?php
	}
	
	protected function output_form_buttons($referer, $active_tab) { 
		?>
		<!-- Output form buttons -->
	  <table class="button-table" col="2">
	    <tr>
				<input type="hidden" name="action" value="<?php echo $this->form_action; ?>">
				<?php wp_nonce_field( $this->form_action, $this->nonce, FALSE ); ?>
				<input type="hidden" name="_wpssm_http_referer" value="<?php echo $referer; ?>">
				<input type="hidden" name="_wpssm_active_tab" value="<?php echo $active_tab; ?>">

	    	<td><?php submit_button( 'Save ' . $active_tab . ' settings', 'primary', 'wpssm_save', true, array('tabindex'=>'1') );?> </td>
	    	<?php if ($active_tab != 'general') { ?>
	    	<td><?php submit_button( 'Reset ' . $active_tab . ' settings', 'secondary', 'wpssm_reset', true, array('tabindex'=>'2') );?> </td>
	    	<?php } ?>
	    	<td><?php submit_button( 'Delete everything', 'delete', 'wpssm_delete', true, array('tabindex'=>'3') );?> </td>
	  	</tr>
	  </table>
	<?php 
	}


/* FORM SUBMISSION
--------------------------------------------------------------*/

	public function update_settings_cb() {

		// check user capabilities
    if (!current_user_can('manage_options'))
        return;

    if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->form_action ) )
        die( 'Invalid nonce.' . var_export( $_POST, true ) );
		//DBG::log('In update_settings_cb function');
		
		if ( ! isset ( $_POST['_wpssm_http_referer'] ) )
		    die( 'Missing valid referer' );
		else
			$url = $_POST['_wpssm_http_referer'];
		
		$type = isset($_POST[ '_wpssm_active_tab' ])?$_POST[ '_wpssm_active_tab' ]:'general';
		$query_args=array();
		$query_args['tab']=$type;
		
		if ( isset ( $_POST[ 'wpssm_reset' ] ) ) {
		   	DBG::log( 'In Form submission : RESET' );
				DBG::log( 'assets before submission' , $this->opt_enqueued_assets );
				foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
					unset($this->opt_enqueued_assets[$type][$handle]['mods']); 
					$this->update_priority( $type, $handle ); 
				}
				DBG::log( 'assets after submission',$this->opt_enqueued_assets);
				update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets);
		    $query_args['msg']='reset';
		}
		elseif ( isset ( $_POST[ 'wpssm_delete' ] ) ) {
		   	DBG::log( 'In Form submission : DELETE' );
		    $this->opt_enqueued_assets = array();
		    $this->opt_general_settings = array();
		    update_option( 'wpssm_enqueued_assets', array() );
		    update_option( 'wpssm_general_settings', array() );
		    $query_args['msg']='delete';
		}
		else {
				DBG::log( 'In Form submission : SAVE, tab ' . $type );
				if ( $type=='general' ) {
					DBG::log('general save $this->opt_general_settings' ,$this->opt_general_settings);
					$settings=array('record','optimize');
					foreach ($settings as $setting) {
						$this->opt_general_settings[$setting]= isset($_POST[ 'general_' . $setting . '_checkbox' ])?$_POST[ 'general_' . $setting . '_checkbox' ]:'off';
					}			
					update_option( 'wpssm_general_settings', $this->opt_general_settings );
				}
				else {
					DBG::log( 'assets before submission',$this->opt_enqueued_assets );
					foreach ( $this->opt_enqueued_assets[$type] as $handle=>$asset ) {
						//DBG::log( array('Looping : asset = ' => $asset ) );
						//DBG::log( array('Looping : handle = ' => $handle ) );
						$this->update_field($type, $handle, 'location');
						$this->update_field($type, $handle, 'minify');
						$this->update_priority( $type, $handle ); 
					}
					DBG::log( 'assets after submission',$this->opt_enqueued_assets);				
					update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets);
				}
		    $query_args['msg']='save';
		}
		

		DBG::log('http referer',$url);
		$url = add_query_arg( $query_args, $url) ;
		DBG::log('url for redirection',$url);
					 
		wp_safe_redirect( $url );
		exit;
	}
	
	public function update_field( $type, $handle, $field ) {
		$input = $this->get_field_name($type, $handle, $field);
		if ( ( isset($_POST[ $input ] )) && ( $_POST[ $input ] != $this->opt_enqueued_assets[$type][$handle][$field]  ) ) {
			$this->opt_enqueued_assets[$type][$handle]['mods'][$field] = $_POST[ $input ];
			DBG::log( 'Asset field modified (mods) !' ,$this->opt_enqueued_assets[$type][$handle]);
			DBG::log( '$input', $input );
			DBG::log( 'POST content for this field',$_POST[ $input ] );
		}
		else {
			if ( isset( $this->opt_enqueued_assets[$type][$handle]['mods'][$field]) ) {
				unset($this->opt_enqueued_assets[$type][$handle]['mods'][$field]);
				DBG::log( 'Mod Field removed !' ,$this->opt_enqueued_assets[$type][$handle] );
			}
		}
	}

  public function load_option_page_cb() {
		//DBG::log('In load_option_page_cb function');
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


/* ENQUEUED SCRIPTS & STYLES MONITORING
-------------------------------------------------------*/

	public function auto_detect() {

		DBG::log('In auto detect !!!');

		foreach ($this->urls_to_request as $url) {
			$request = array(
				'url'  => $url,
				'args' => array(
					'timeout'   => 0.01,
					'blocking'  => false,
					'sslverify' => apply_filters('https_local_ssl_verify', true)
				)
			);

			wp_remote_get($request['url'], $request['args']);
		}
	}

	public function get_permalink_by_slug( $slug) {
    $permalink = null;
    $page = get_page_by_path( $slug );
    if( null != $page ) {
        $permalink = get_permalink( $page->ID );
    }
    return $permalink;
	}

	public function record_header_assets_cb() {
		DBG::log('In record header assets cb');
		$this->opt_enqueued_assets['pages'][get_permalink()] = array(get_permalink(), current_time( 'mysql' ));
		$this->record_enqueued_assets( false );
	}

	public function record_footer_assets_cb() {
		DBG::log('In record footer assets cb');
		$this->record_enqueued_assets( true );
		update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets, true );
	}

	protected function record_enqueued_assets( $in_footer ) {
		DBG::log('In record enqueued assets');
		global $wp_scripts;
		global $wp_styles;

		/* Select data source depending whether in header or footer */
		if ($in_footer) {
			//DBG::log('FOOTER record');
			//DBG::log(array( '$header_scripts' => $this->header_scripts ));
			$scripts=array_diff( $wp_scripts->done, $this->header_scripts );
			$styles=array_diff( $wp_styles->done, $this->header_styles );
			//DBG::log(array('$source'=>$source));
		}
		else {
			$scripts=$wp_scripts->done;
			$styles=$wp_styles->done;
			$this->header_scripts = $scripts;
			$this->header_styles = $styles;
			//DBG::log('HEADER record');
			//DBG::log(array('$source'=>$source));
		}

	  //DBG::log(array('assets before update' => $this->opt_enqueued_assets));
				
		$assets = array(
			'scripts'=>array(
					'handles'=>$scripts,
					'registered'=> $wp_scripts->registered),
			'styles'=>array(
					'handles'=>$styles,
					'registered'=> $wp_styles->registered),
			);
				
		DBG::log( array( '$assets' => $assets ) );		
			
		foreach( $assets as $type=>$asset ) {
			DBG::log( $type . ' recording');		
					
			foreach( $asset['handles'] as $index => $handle ) {
				$obj = $asset['registered'][$handle];
				$path = strtok($obj->src, '?'); // remove any query parameters
				
				if ( strpos( $path, 'wp-' ) != false) {
					$path = wp_make_link_relative( $path );
					$uri = $_SERVER['DOCUMENT_ROOT'] . $path;
					$size = filesize( $uri );
					$version = $obj->ver;
				}
				else {
					$path = $obj->src;
					$version = $obj->ver;
					$size = 0;
				}
				
				// Update current asset properties
				$this->opt_enqueued_assets[$type][$handle] = array(
					'handle' => $handle,
					'enqueue_index' => $index,
					'filename' => $path,
					'location' => $in_footer?'footer':'header',
					'dependencies' => $obj->deps,
					'dependents' => array(),
					'minify' => (strpos( $obj->src, '.min.' ) != false )?'yes':'no',
					'size' => $size,
					'version' => $version,
				);
				// Update current asset priority
				$priority = $this->update_priority( $type, $handle );
				// Update all dependancies assets properties
				foreach ($obj->deps as $dep_handle) {
					//DBG::log(array('dependencies loop : '=>$dep_handle));
					$this->opt_enqueued_assets[$type][$dep_handle]['dependents'][]=$handle;
				}
			}
		}
	  DBG::log(array('assets after update' => $this->opt_enqueued_assets));
	}
	
	protected function update_priority( $type, $handle ) {
		$asset = $this->opt_enqueued_assets[$type][$handle];
		$location = $this->get_field_value( $asset, 'location');
		
		if ( $location != 'disabled' ) {
			$minify = $this->get_field_value( $asset, 'minify');
			$size = $this->get_field_value( $asset, 'size');
			$score = ( $location == 'header' )?1000:0;
			//DBG::log(array('base after location'=>$score));
			$score += ( $size >= self::$SIZE_LARGE )?500:0; 	
			$score += ( ($minify == 'no') && ( $size != 0 ))?200:0;
			//DBG::log(array('base after minify'=>$score));
			$score += ( $size <= self::$SIZE_SMALL )?100:0; 	
			//DBG::log(array('base after size'=>$score));
			if ( $size >= self::$SIZE_LARGE ) 
				$normalizer = self::$SIZE_MAX;
			elseif ( $size <= self::$SIZE_SMALL )
				$normalizer = self::$SIZE_SMALL;
			else 
				$normalizer = self::$SIZE_LARGE;
			//DBG::log(array('normalizer'=>$normalizer));
			$score += $size/$normalizer*100; 	
			//DBG::log(array('score'=>$score));
		}
		else 
			$score = 0;

		$this->opt_enqueued_assets[$type][$handle]['priority'] = $score;
	}

	/* AJAX FUNCTIONS
	--------------------------------------------------------------*/
	public function ajax_check_dependencies_cb() {
		$nonce = $_POST['checkDepsNonce'];

		// check to see if the submitted nonce matches with the
		// generated nonce we created earlier
		if ( ! wp_verify_nonce( $nonce, 'check-script-dependencies' ) )
		die ( 'Invalid nonce.');

		// ignore the request if the current user doesn't have
		// sufficient permissions
		//if ( current_user_can( 'edit_posts' ) ) {
		// get the submitted parameters
		$scriptHandle = isset($_POST['checkDepsArgs']['handle'])?$_POST['checkDepsArgs']['handle']:'Error !';

		echo 'IN AJAX CHECK SCRIPT DEPENDENCIES';
		echo '<pre>' . $scriptHandle . '</pre>';
		die();
	}

	/* Extract arguments passed via Ajax call and echo value
	---------------------------------------------------------*/
	private function get_ajax_arg($name,$label='') {
		$value='';
		if ( isset($_POST['args'][$name]) ) {
			$value = esc_html($_POST['args'][$name]);
			$label = esc_html(( empty($label) )?ucfirst($name):$label);	
		}
		else $value='-1';
		echo sprintf("<b> %s </b> = %s",$label,$value);
		echo "<br>";
		return $value;
	}


}
