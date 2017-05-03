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
		WPSSM_Debug::log('In load_admin_assets_cb');
		//WPSSM_Debug::log( plugins_url( '/css/wpssm_options_page.css', __FILE__ ) );
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
		WPSSM_Debug::log('In hydrate admin, $this->displayed_assets', $this->displayed_assets);								
	}
	
	public function filter_assets( $asset ) {
		$match=true;
		foreach ($this->filter_args as $field=>$value) {
			//WPSSM_Debug::log('In filter assets filter args loop', array($field=>$value));
			$match=($this->get_field_value($asset,$field)==$value)?$match:false;
		}
		return $match;
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



	public function output_section_cb( $section ) {
		//WPSSM_Debug::log('In section callback');
	}

	public function output_toggle_switch_recording_cb() {
		$this->output_toggle_switch( 'general_record', $this->opt_general_settings['record']);
	}	

	public function output_toggle_switch_optimize_cb() {
		$this->output_toggle_switch( 'general_optimize', $this->opt_general_settings['optimize']);
	}		
	
	public function output_toggle_switch_javasync_cb() {
		$this->output_toggle_switch( 'general_javasync', $this->opt_general_settings['javasync']);
	}	

	protected function output_toggle_switch( $input_name, $value ) {
		WPSSM_Debug::log( 'in output toggle switch for ' . $input_name , $value);
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
	
	public function output_async_scripts_list() {
		$this->	output_items_list('scripts', 'async' );
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
		//WPSSM_Debug::log( array( 'sorted list : '=>$list ));

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
		//WPSSM_Debug::log('Output items list', $type . ' : ' . $location);
		$assets = $this->displayed_assets[$type][$location]['assets'];
		//WPSSM_Debug::log( array('$this->displayed_assets' => $assets));
		$sorted_list = $this->get_sorted_list( $assets );
		//WPSSM_Debug::log( array('$sorted_list' => $sorted_list));
		
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
			//WPSSM_Debug::log(array('Asset in output_items_list : ' => $asset));
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
  					<option value="async" <?php echo ($location=='async')?'selected':'';?> >asynchronous</option>
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
	
	private function prepare_displayed_assets($type) {
		// Preparation of data to be displayed
   	//$types=array('scripts', 'styles');
    //$locations=array('header', 'footer', 'async', 'disabled');
		//WPSSM_Debug::log('In prepare_displayed_assets $this->displayed_assets before : ', $this->displayed_assets);
		$assets=$this->opt_enqueued_assets[$type];
		//if (! isset ( $this->opt_enqueued_assets[$type] ) ) continue;
		foreach ($this->config_settings_pages[$type]['sections'][0]['fields'] as $location=>$placeholder) {
			$this->filter_args = array( 'location' => $location );
			//WPSSM_Debug::log('Looping asset location : ', array($location => $assets));
			//$assets = $this->opt_enqueued_assets[$type];
			$filtered_assets = array_filter($assets, array($this, 'filter_assets') );	
			$this-> displayed_assets[$type][$location]['assets']=$filtered_assets;
			$this-> displayed_assets[$type][$location]['count']=count($filtered_assets);
			$this-> displayed_assets[$type][$location]['size']=array_sum( array_column( $filtered_assets, 'size'));
		}	
		//WPSSM_Debug::log('In WPSSM_Settings hydrate $this->displayed_assets: ', $this->displayed_assets);
	}
	
	private function output_user_notification( $asset ) {
		
		$size= $asset['size'];
		//WPSSM_Debug::log(array('size : '=>$size));
		$is_minified = $this->get_field_value( $asset, 'minify') == 'yes';
		//WPSSM_Debug::log(array('is_minified: '=>$is_minified));
		$in_footer = ( $this->get_field_value( $asset, 'location') == 'footer');
		
		$this->reset_user_notification();
		if (!$is_minified) {
			if ( $size > self::SIZE_LARGE ) {
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

		if ( ( $size > self::SIZE_LARGE ) && ( !$in_footer ) ) {
			$level = 'issue';
			$msg = __('Large files loaded in the header will slow down page display : make asynchronous, loading in footer or at least conditional enqueue recommended', 'jco');			
			$this->enqueue_user_notification( $msg, $level);
		}	
		
		if ( ( $size < self::SIZE_SMALL ) && (!isset( $asset['in_group']) ) ) {
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
		//WPSSM_Debug::log('In Field Value for ' . $field);
		//WPSSM_Debug::log(array('Asset : ' => $asset));
		if ( isset( $asset['mods'] ) && (isset( $asset['mods'][ $field ] ) ) ) {
			$value=$asset['mods'][ $field ];
			//WPSSM_Debug::log('Mod found !');
		}
		else {
			//WPSSM_Debug::log('Mod not found');
			$value=$asset[ $field ];
		}
		//WPSSM_Debug::log( array(' Field value of ' . $field . ' : ' => $value ));
		return $value;
	}






/* ENQUEUED SCRIPTS & STYLES MONITORING
-------------------------------------------------------*/

	public function auto_detect() {

		WPSSM_Debug::log('In auto detect !!!');

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
		WPSSM_Debug::log('In record header assets cb');
		$this->opt_enqueued_assets['pages'][get_permalink()] = array(get_permalink(), current_time( 'mysql' ));
		$this->record_enqueued_assets( false );
	}

	public function record_footer_assets_cb() {
		WPSSM_Debug::log('In record footer assets cb');
		$this->record_enqueued_assets( true );
		update_option( 'wpssm_enqueued_assets', $this->opt_enqueued_assets, true );
	}

	protected function record_enqueued_assets( $in_footer ) {
		WPSSM_Debug::log('In record enqueued assets');
		global $wp_scripts;
		global $wp_styles;

		/* Select data source depending whether in header or footer */
		if ($in_footer) {
			//WPSSM_Debug::log('FOOTER record');
			//WPSSM_Debug::log(array( '$header_scripts' => $this->header_scripts ));
			$scripts=array_diff( $wp_scripts->done, $this->header_scripts );
			$styles=array_diff( $wp_styles->done, $this->header_styles );
			//WPSSM_Debug::log(array('$source'=>$source));
		}
		else {
			$scripts=$wp_scripts->done;
			$styles=$wp_styles->done;
			$this->header_scripts = $scripts;
			$this->header_styles = $styles;
			//WPSSM_Debug::log('HEADER record');
			//WPSSM_Debug::log(array('$source'=>$source));
		}

	  //WPSSM_Debug::log(array('assets before update' => $this->opt_enqueued_assets));
				
		$assets = array(
			'scripts'=>array(
					'handles'=>$scripts,
					'registered'=> $wp_scripts->registered),
			'styles'=>array(
					'handles'=>$styles,
					'registered'=> $wp_styles->registered),
			);
				
		WPSSM_Debug::log( array( '$assets' => $assets ) );		
			
		foreach( $assets as $type=>$asset ) {
			WPSSM_Debug::log( $type . ' recording');		
					
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
					//WPSSM_Debug::log(array('dependencies loop : '=>$dep_handle));
					$this->opt_enqueued_assets[$type][$dep_handle]['dependents'][]=$handle;
				}
			}
		}
	  WPSSM_Debug::log(array('assets after update' => $this->opt_enqueued_assets));
	}
	
	protected function update_priority( $type, $handle ) {
		$asset = $this->opt_enqueued_assets[$type][$handle];
		$location = $this->get_field_value( $asset, 'location');
		
		if ( $location != 'disabled' ) {
			$minify = $this->get_field_value( $asset, 'minify');
			$size = $this->get_field_value( $asset, 'size');
			$score = ( $location == 'header' )?1000:0;
			//WPSSM_Debug::log(array('base after location'=>$score));
			$score += ( $size >= self::SIZE_LARGE )?500:0; 	
			$score += ( ($minify == 'no') && ( $size != 0 ))?200:0;
			//WPSSM_Debug::log(array('base after minify'=>$score));
			$score += ( $size <= self::SIZE_SMALL )?100:0; 	
			//WPSSM_Debug::log(array('base after size'=>$score));
			if ( $size >= self::SIZE_LARGE ) 
				$normalizer = self::SIZE_MAX;
			elseif ( $size <= self::SIZE_SMALL )
				$normalizer = self::SIZE_SMALL;
			else 
				$normalizer = self::SIZE_LARGE;
			//WPSSM_Debug::log(array('normalizer'=>$normalizer));
			$score += $size/$normalizer*100; 	
			//WPSSM_Debug::log(array('score'=>$score));
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

