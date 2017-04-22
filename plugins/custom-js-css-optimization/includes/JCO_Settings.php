<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class JCO_Settings {

	protected static $SIZE_SMALL = 1000;
	protected static $SIZE_LARGE = 1000;
	protected static $SIZE_MAX = 200000;
	protected static $default_section = 'general_settings_section';
	protected $menu_slug = 'js_css_optimization';
	protected $form_action = 'jco_update_settings';
	protected $nonce = 'wp8756';
	protected $urls_to_request;
	protected $header_scripts;
	protected $header_styles;
	protected $enqueued_assets;
	protected $displayed_assets;
	protected $user_notification; 
	protected $filter_args = array( 'location' => 'header' );
	protected $sort_args = array( 
														'field' => 'priority', 
														'order' => SORT_DESC, 
														'type' => SORT_NUMERIC);
//	$enqueued_assets format : 
//	array(
//			'pages' => array(
//				'slug1',
//				'slug2',
//				...
//			)
//		'scripts' => array(
//			'handle' => 'example',
//		  'enqueue_index' => 0, 1, 2...
//			'filename' => 'wp_content/plugins/example/example.js',
//			'location' => 'footer', 'header', 'disabled'
//			'size' => 
//			'dependencies' => array(
//					'handle1',
//					'handle2',
//					...
//			),
//			'mods'  => array(
//				'minify' => 'yes', 'no'
//				'location' => 'footer', 'header', 'disabled'
//				'group' => array( 
//					'name' => 'group1',
//					'index' => '0',
//				),
//			),
//		),
//		'styles' => array(
//			...
//		)
//	);

	public function __construct() {

		// Admin options page
		add_action( 'admin_menu', array($this, 'add_js_css_menu_option'));
		add_action( 'admin_init', array($this, 'jco_settings_init') );
		//add_action( 'admin_post_$this->action', array ( $this, 'update_settings_cb' ) );
		add_action( 'admin_post_' . $this->form_action, array ( $this, 'update_settings_cb' ) );

		// load assets for this page
    add_action( 'admin_enqueue_scripts', array($this,'load_admin_assets') );


		if ( get_option( 'jco_enqueue_recording' ) == 'on' ) {
			add_action( 'wp_head', array($this, 'record_header_assets') );
			add_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets') );
		}
		else {
			remove_action( 'wp_head', array($this, 'record_header_assets') );
			remove_action( 'wp_print_footer_scripts', array($this, 'record_footer_assets') );
		}

		$this->hydrate();

	}

	public function load_admin_assets() {
		PC::debug('In load_admin_styles');
		PC::debug( plugins_url( '/css/jco_options_page.css', __FILE__ ) );

  	wp_enqueue_style( 'jco_admin_css', plugins_url( '../assets/css/jco_options_page.css', __FILE__ ) , false, '1.0.0' );
  	//wp_enqueue_style( 'jco_admin_fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0.0' );
  	//wp_enqueue_style( 'jco_admin_fa', plugins_url( '../assets/fonts/font-awesome/css/font-awesome.min.css', __FILE__ ), array(), '4.7.0' );
  	//wp_enqueue_script( 'jco_admin_fa', 'https://use.fontawesome.com/96ebedc785.js', false, '1.0.0' );
  	wp_enqueue_script( 'jco_admin_js', plugins_url( '../assets/js/jco_options_page.js', __FILE__ ) , false, '1.0.0' );
	}
	
	
	public function hydrate() {
		
		//pages to record
		$this->urls_to_request = array(
															home_url(),
															$this->get_permalink_by_slug('bredele'),
															$this->get_permalink_by_slug('les-myrtilles'),
														);
									
		// hydrate properties with options content
		$this->enqueued_assets = get_option( 'jco_enqueued_assets' );
		if (!isset($this->enqueued_assets['pages'])) $this->enqueued_assets['pages']=array();
		if (!isset($this->enqueued_assets['scripts'])) $this->enqueued_assets['scripts']=array();
		if (!isset($this->enqueued_assets['styles'])) $this->enqueued_assets['styles']=array();
		
		// Preparation of data to be displayed
    $types=array('scripts', 'styles');
    $locations=array('header', 'footer', 'disabled');
		foreach ($types as $type) {
			if (! isset ( $this->enqueued_assets[$type] ) ) continue;
			$assets = $this->enqueued_assets[$type];
			foreach ($locations as $location) {
				$this-> filter_args = array( 'location' => $location );
				$filtered_assets = array_filter($assets, array($this, 'filter_assets') );	
				$this-> displayed_assets[$type][$location]['assets']=$filtered_assets;
				$this-> displayed_assets[$type][$location]['count']=count($filtered_assets);
				$this-> displayed_assets[$type][$location]['size']=array_sum( array_column( $filtered_assets, 'size'));
			}	
		}
		//PC::debug( array( '$this->displayed_assets: '=>$this->displayed_assets ));
	
	}
	
	public function filter_assets( $asset ) {
		return ( $this->get_field_value( $asset, 'location' ) == $this->filter_args['location'] );
	}

	public function add_js_css_menu_option() {
		$option_page_id = add_submenu_page(
      'tools.php',
      'JS & CSS Optimization',
      'JS & CSS Optimization',
      'manage_options',
      $this->menu_slug,
      array($this, 'output_options_page')
	    );

		add_action( "load-$option_page_id", array ( $this, 'load_option_page_cb' ) );
	}


	public function jco_settings_init() {
	    // register options
	    register_setting('enqueued_list_options', 'jco_enqueued_assets');
	    register_setting('enqueued_list_options', 'jco_enqueue_recording');
	    register_setting('enqueued_list_options', 'jco_enqueue_stats');

	    // register "general settings" section
	    add_settings_section(
	        'general_settings_section',
	        'General Settings Section',
	        array($this,'output_section_cb'),
	        'general_settings_section'
	    );

	    // register "enqueued scripts" section
	    add_settings_section(
	        'enqueued_scripts_section',
	        'Enqueued Scripts Section',
	        array($this,'output_section_cb'),
	        'enqueued_scripts_section'
	    );
	    
	    // register "enqueued styles" section
	    add_settings_section(
	        'enqueued_styles_section',
	        'Enqueued Styles Section',
	        array($this,'output_section_cb'),
	        'enqueued_styles_section'
	    );

	    // register new fields in the general settings section
	    add_settings_field(
	        'jco_enqueue_recording',
	        'Activate enqueued scripts & styles recording',
	        array($this,'jco_recording_output'),
	        'general_settings_section',
	        'general_settings_section'
	    );

	    // register new fields in the enqueued list section
	    add_settings_field(
	        'jco_recorded_pages',
	        'Pages recorded',
	        array($this,'output_pages_list'),
	        'general_settings_section',
	        'general_settings_section',
					array( 
	        	'label_for' => 'jco-recorded-pages',
	        	'class' => 'foldable' )
	    );
	    

	    // register new fields in the enqueued Scripts section;
	    $size = $this->displayed_assets['scripts']['header']['size'];
	    $count = $this->displayed_assets['scripts']['header']['count'];
	    add_settings_field(
	        'jco_header_enqueued_scripts',
	        'Enqueued Header Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_header_scripts_list'),
	        'enqueued_scripts_section',
	        'enqueued_scripts_section',
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );
	    
			$size = $this->displayed_assets['scripts']['footer']['size'];
	    $count = $this->displayed_assets['scripts']['footer']['count'];
			add_settings_field(
	        'jco_footer_enqueued_scripts',
	        'Enqueued Footer Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_footer_scripts_list'),
	        'enqueued_scripts_section',
	        'enqueued_scripts_section',
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );

	    $size = $this->displayed_assets['scripts']['disabled']['size'];
	    $count = $this->displayed_assets['scripts']['disabled']['count'];	    
			add_settings_field(
	        'jco_disabled_scripts',
	        'Disabled Scripts (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_disabled_scripts_list'),
	        'enqueued_scripts_section',
	        'enqueued_scripts_section',
	        array( 
	        	'label_for' => 'jco-enqueued-scripts',
	        	'class' => 'foldable' )
	    );	    
	    
	    // register new fields in the enqueued Styles section
	    $size = $this->displayed_assets['styles']['header']['size'];
	    $count = $this->displayed_assets['styles']['header']['count'];
			add_settings_field(
	        'jco_header_enqueued_styles',
	        'Enqueued Header Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_header_styles_list'),
	        'enqueued_styles_section',
	        'enqueued_styles_section',
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );
	    
	    $size = $this->displayed_assets['styles']['footer']['size'];
	    $count = $this->displayed_assets['styles']['footer']['count'];
			add_settings_field(
	        'jco_footer_enqueued_styles',
	        'Enqueued Footer Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_footer_styles_list'),
	        'enqueued_styles_section',
	        'enqueued_styles_section',
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );	    

	    $size = $this->displayed_assets['styles']['disabled']['size'];
	    $count = $this->displayed_assets['styles']['disabled']['count'];	    
			add_settings_field(
	        'jco_disabled_styles',
	        'Disabled Styles (' . $count . ' files, total size ' . size_format($size) . ')',
	        array($this,'output_disabled_styles_list'),
	        'enqueued_styles_section',
	        'enqueued_styles_section',
	        array(
	        	'label_for' => 'jco-enqueued-styles',
	        	'class' => 'foldable' )
	    );	    
	}

	public function output_section_cb( $section ) {
		//PC::debug('In section callback');

	}

	public function jco_recording_output() {
		PC::debug( array('jco_enqueue_recording'=>get_option( 'jco_enqueue_recording' )) );
		$record = ( get_option( 'jco_enqueue_recording' ) == 'on')?true:false;
		$checked = $record?'checked="checked"':'';
		?>
		<label class="switch">
  	<input type="checkbox" name="jco_recording_checkbox" <?php echo $checked;?> value="on">
  	<div class="slider round"></div>
		</label>
		<?php
	}

	public function output_pages_list() {
		foreach ($this->enqueued_assets['pages'] as $slug) {
			echo '<p>' . $slug . '</p>';
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
		//PC::debug( array( 'sorted list : '=>$list ));

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
		
		$assets = $this->displayed_assets[$type][$location]['assets'];
		PC::debug( array('$this->displayed_assets' => $assets));
		$sorted_list = $this->get_sorted_list( $assets );
		PC::debug( array('$sorted_list' => $sorted_list));
		
		?>
		
    <table>
    	<tr>
    		<th> handle </th>
    		<th> priority </th>
    		<th> Dependencies </th>
    		<th> File size </th>
    		<th> Location </th>
    		<th> Minify </th>
    	</tr>
    	
    <?php
    foreach ($sorted_list as $handle => $priority ) {
    	$asset = $assets[$handle];
			//PC::debug(array('Asset in output_items_list : ' => $asset));
    	$filename = $asset['filename'];
    	$deps = $asset['dependencies'];
    	$location = $this->get_field_value( $asset, 'location');
	    $minify = $this->get_field_value( $asset, 'minify');
	    $size = $this->get_field_value( $asset, 'size');
	    $asset_is_minified = ( $asset[ 'minify' ] == 'yes')?true:false; 
	    $already_minified_msg = __('This file is already minimized within its plugin', 'jco');
	    
    	?>
    	
    	<tr class="enqueued-asset <?php echo $type;?>" id="<?php echo $handle;?>">
	    	<td title="<?php echo $filename;?>"><?php echo $handle;?><?php $this->output_user_notification( $asset );?></td>
	    	<td><?php echo $priority;?></td>
	    	<td><?php foreach ($deps as $dep) {echo $dep . '<br>';}?></td>
	    
	    	<td title="<?php echo $filename;?>"><?php echo size_format( $size );?></td>
	    	
	    	<td class="<?php echo $this->get_field_class( $asset, 'location');?>">
	    		<select class="setting-input location" name="<?php echo $this->get_field_name( $type, $handle, 'location');?>">
  					<option value="header" <?php echo ($location=='header')?'selected':'';?> >header</option>
  					<option value="footer" <?php echo ($location=='footer')?'selected':'';?> >footer</option>
  					<option value="disabled" <?php echo ($location=='disabled')?'selected':'';?>>disabled</option>
					</select>
				</td>
				
				<td class="<?php echo $this->get_field_class( $asset, 'minify');?>">
	    		<select class="setting-input minify" <?php echo ($asset_is_minified)?'disabled':'';?> <?php echo ($asset_is_minified)?'title="' . $already_minified_msg . '"' :'';?> name="<?php echo $this->get_field_name( $type, $handle, 'minify');?>">
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
		//PC::debug(array('size : '=>$size));
		$is_minified = $this->get_field_value( $asset, 'minify') == 'yes';
		//PC::debug(array('is_minified: '=>$is_minified));
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
	
	protected function get_field_class( $asset, $field ) {
		$class = '';
		if ( isset( $asset['mods'][ $field ] ) ) {
			$class='modified';
		}
		return $class;
	}
	
	protected function get_field_name( $type, $handle, $field ) {
		return  $type . '_' . $handle . '_' . $field;
	}
	
	protected function get_field_value( $asset, $field ) {
		//PC::debug('In Field Value for ' . $field);
		//PC::debug(array('Asset : ' => $asset));
		if ( isset( $asset['mods'] ) && (isset( $asset['mods'][ $field ] ) ) ) {
			$value=$asset['mods'][ $field ];
			//PC::debug('Mod found !');
		}
		else {
			//PC::debug('Mod not found');
			$value=$asset[ $field ];
		}
		//PC::debug( array(' Field value of ' . $field . ' : ' => $value ));
		return $value;
	}

	public function output_options_page() {
	    // check user capabilities
	    if (!current_user_can('manage_options')) {
	        return;
	    }
		
			$redirect = menu_page_url( $this->menu_slug, FALSE );
			?>

	    <div class="wrap">
	        <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        
						<h2 class="nav-tab-wrapper">
						<a href="#" class="nav-tab">General Settings</a>
						<a href="#" class="nav-tab">Scripts</a>
						<a href="#" class="nav-tab">Styles</a>
						</h2>
	        
		        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
		        		<?php

	        			
	        			?><div class="tabs"><?php
		            settings_fields('general_settings_section');
		            do_settings_sections('general_settings_section');
		            
		            settings_fields('enqueued_scripts_section');
		            do_settings_sections('enqueued_scripts_section');
		            
		            settings_fields('enqueued_styles_section');
		            do_settings_sections('enqueued_styles_section');
	        			?></div><?php

		            ?>
		            <table class="button-table" col="2">
		            <tr>
									<input type="hidden" name="action" value="<?php echo $this->form_action; ?>">
									<?php wp_nonce_field( $this->form_action, $this->nonce, FALSE ); ?>
									<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">

		            	<td><?php submit_button( 'Save Settings', 'primary', 'jco_save', true, array('tabindex'=>'1') );?> </td>
		            	<td><?php submit_button( 'Reset settings', 'secondary', 'jco_reset', true, array('tabindex'=>'2') );?> </td>
		            	<td><?php submit_button( 'Delete everything', 'delete', 'jco_delete', true, array('tabindex'=>'3') );?> </td>
		          	</tr>
		        </form>
	    </div>
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
		//PC::debug('In update_settings_cb function');

		if ( isset ( $_POST[ 'jco_reset' ] ) ) {
		   	PC::debug( 'In Form submission : RESET' );
				PC::debug( array('assets before submission'=> $this->enqueued_assets) );
				foreach ( $this->enqueued_assets as $type=>$assets ) {
					if ( ( $type != 'scripts' ) && ($type != 'styles') ) continue;
					foreach ( $assets as $handle=>$asset ) {
						unset($this->enqueued_assets[$type][$handle]['mods']); 
						$this->update_priority( $type, $handle ); 
					}
				}
				PC::debug( array('assets after submission'=> $this->enqueued_assets) );
				update_option( 'jco_enqueued_assets', $this->enqueued_assets);
		    $msg = 'reset';
		}
		elseif ( isset ( $_POST[ 'jco_delete' ] ) ) {
		   	PC::debug( 'In Form submission : DELETE' );
		    update_option( 'jco_enqueued_assets', array() );
		    $this->enqueued_assets = array();
		    $msg = 'delete';
		}
		else {
				PC::debug( 'In Form submission : SAVE' );
				$recording = isset($_POST[ 'jco_recording_checkbox' ])?$_POST[ 'jco_recording_checkbox' ]:'off';
				update_option( 'jco_enqueue_recording', $recording);
				
				PC::debug( array('assets before submission'=> $this->enqueued_assets) );
				foreach ( $this->enqueued_assets as $type=>$assets ) {
					if ( ( $type != 'scripts' ) && ($type != 'styles') ) continue;
					foreach ( $assets as $handle=>$asset ) {
						PC::debug( array('Looping : type = ' => $type ) );
						PC::debug( array('Looping : asset = ' => $asset ) );
						PC::debug( array('Looping : handle = ' => $handle ) );
						$this->update_field($type, $handle, 'location');
						$this->update_field($type, $handle, 'minify');
						$this->update_priority( $type, $handle ); 
					}
				}
				PC::debug( array('assets after submission'=> $this->enqueued_assets) );
				update_option( 'jco_enqueued_assets', $this->enqueued_assets);
		    $msg = 'save';
		}

		$url = add_query_arg( 'msg', $msg, urldecode( $_POST['_wp_http_referer'] ) );
		if ( ! isset ( $_POST['_wp_http_referer'] ) )
		    die( 'Missing target.' );

		wp_safe_redirect( $url );
		exit;
	}
	
	public function update_field( $type, $handle, $field ) {
		$input = $this->get_field_name($type, $handle, $field);
		if ( $_POST[ $input ] != $this->enqueued_assets[$type][$handle][$field] ) {
			$this->enqueued_assets[$type][$handle]['mods'][$field] = $_POST[ $input ];
			PC::debug( array('Asset field modified (mods) !' => $this->enqueued_assets[$type][$handle]) );
			PC::debug( array('$input' => $input ) );
			PC::debug( array('POST content for this field' => $_POST[ $input ] ) );
		}
		else {
			if ( isset( $this->enqueued_assets[$type][$handle]['mods'][$field]) ) {
				unset($this->enqueued_assets[$type][$handle]['mods'][$field]);
				PC::debug( array('Mod Field removed !' => $this->enqueued_assets[$type][$handle] ) );
			}
		}
	}

  public function load_option_page_cb() {
		//PC::debug('In load_option_page_cb function');
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

		PC::debug('In auto detect !!!');

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

	public function record_header_assets() {
		//PC::debug('In save enqueued scripts !!!');
		if (!in_array(get_permalink(), $this->enqueued_assets['pages']) ) {
			$this->enqueued_assets['pages'][] = get_permalink();
		}
		$this->record_enqueued_assets( false );
	}

	public function record_footer_assets() {
		$this->record_enqueued_assets( true );
		update_option( 'jco_enqueued_assets', $this->enqueued_assets, true );
	}

	public function record_enqueued_assets( $in_footer ) {
		PC::debug('In record enqueued assets !!!');
		global $wp_scripts;
		global $wp_styles;

		/* Select data source depending whether in header or footer */
		if ($in_footer) {
			//PC::debug('FOOTER record');
			//PC::debug(array( '$header_scripts' => $this->header_scripts ));
			$scripts=array_diff( $wp_scripts->done, $this->header_scripts );
			$styles=array_diff( $wp_styles->done, $this->header_styles );
			//PC::debug(array('$source'=>$source));
		}
		else {
			$scripts=$wp_scripts->done;
			$styles=$wp_styles->done;
			$this->header_scripts = $scripts;
			$this->header_styles = $styles;
			//PC::debug('HEADER record');
			//PC::debug(array('$source'=>$source));
		}

	  PC::debug(array('assets before update' => $this->enqueued_assets));
				
		$assets = array(
			'scripts'=>array(
					'handles'=>$scripts,
					'registered'=> $wp_scripts->registered),
			'styles'=>array(
					'handles'=>$styles,
					'registered'=> $wp_styles->registered),
			);
				
		PC::debug( array( '$assets' => $assets ) );		
			
		foreach( $assets as $type=>$asset ) {
			PC::debug( $type . ' recording');		
					
			foreach( $asset['handles'] as $index => $handle ) {
				$obj = $asset['registered'][$handle];
				PC::debug(array('handle' => $handle));
				PC::debug( array('$obj'=>$obj) );
				
				$path = strtok($obj->src, '?'); // remove any query parameters
				PC::debug( array('$path'=>$path) );
				
				if ( strpos( $path, 'wp-' ) != false) {
					$path = wp_make_link_relative( $path );
					PC::debug( array('$path after relative'=>$path) );
					$uri = $_SERVER['DOCUMENT_ROOT'] . $path;
					PC::debug( array('$uri'=>$uri) );
					$size = filesize( $uri );
					PC::debug( array('$size'=>$size) );
					$version = $obj->ver;
				}
				else {
					$path = $obj->src;
					$version = $obj->ver;
					$size = 0;
				}
				
				$this->enqueued_assets[$type][$handle] = array(
					'handle' => $handle,
					'enqueue_index' => $index,
					'filename' => $path,
					'location' => $in_footer?'footer':'header',
					'dependencies' => $obj->deps,
					'minify' => (strpos( $obj->src, '.min.' ) != false )?'yes':'no',
					'size' => $size,
					'version' => $version,
				);
				$priority = $this->update_priority( $type, $handle );
				
				PC::debug( array('$enqueued asset after assignment'=>$this->enqueued_assets[$type]) );
			
			}
		}
	  PC::debug(array('assets after update' => $this->enqueued_assets));
	}
	
	
	private function update_priority( $type, $handle ) {
		
		$asset = $this->enqueued_assets[$type][$handle];
		
		$location = $this->get_field_value( $asset, 'location');
		
		if ( $location != 'disabled' ) {
			$minify = $this->get_field_value( $asset, 'minify');
			$size = $this->get_field_value( $asset, 'size');
			
			$score = ( $location == 'header' )?1000:0;
			//PC::debug(array('base after location'=>$score));
			
			$score += ( $size >= self::$SIZE_LARGE )?500:0; 	
			
			$score += ( ($minify == 'no') && ( $size != 0 ))?200:0;
			//PC::debug(array('base after minify'=>$score));
			
			$score += ( $size <= self::$SIZE_SMALL )?100:0; 	
			//PC::debug(array('base after size'=>$score));

			if ( $size >= self::$SIZE_LARGE ) 
				$normalizer = self::$SIZE_MAX;
			elseif ( $size <= self::$SIZE_SMALL )
				$normalizer = self::$SIZE_SMALL;
			else 
				$normalizer = self::$SIZE_LARGE;
			//PC::debug(array('normalizer'=>$normalizer));

			$score += $size/$normalizer*100; 	
			//PC::debug(array('score'=>$score));
		}
		else 
			$score = 0;

		$this->enqueued_assets[$type][$handle]['priority'] = $score;
	}



}

