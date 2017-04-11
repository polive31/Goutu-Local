<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class JS_CSS_Optimize {
	
	public function __construct() {
		// Admin options page
		add_action( 'admin_menu', array($this, 'add_js_css_menu_option'));
		add_action( 'admin_init', array($this, 'jco_settings_init') );
		add_action( 'admin_post_$this->action', array ( $this, 'admin_page_actions' ) );
		
		//add_action( 'wp_enqueue_scripts', array($this, 'test_object_visibility'), 100 );

		// Scripts & Styles enqueing monitor
		add_action( 'wp_head', array($this, 'save_header_scripts') );
		add_action( 'wp_footer', array($this, 'save_footer_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'conditionally_deregister_scripts'), PHP_INT_MAX );
	}

	public function add_js_css_menu_option() {
		$hook_suffix = add_submenu_page(
      'tools.php',
      'JS & CSS Optimization',
      'JS & CSS Optimization',
      'manage_options',
      'js_css_optimization',
      array($this, 'output_options_page')
	    );
	    
		add_action( "load-$hook_suffix", array ( $this, 'parse_url' ) );
	}
	
	public function parse_url() {
		PC::debug('In Parse URL function');
		add_action( 'admin_notices', array ( $this, 'render_msg' ) );
	}
	
	public function render_msg() {
		echo 'RENDER MSG SUCCESSFUL !';
    echo '<p>' . esc_attr( $_GET['msg'] ) . '</p>';
	}
	
	public function jco_settings_init() {
	    // register a new setting for "reading" page
	    register_setting('enqueued_list_options', 'jco_enqueued_scripts');
	    register_setting('enqueued_list_options', 'jco_enqueued_styles');
	 
	    // register a new section in the "reading" page
	    add_settings_section(
	        'enqueued_list_section',
	        'Enqueued Scripts & Styles Section',
	        array($this,'jco_enqueued_list_section_cb'),
	        'js_css_optimization'
	    );
	 
	    // register a new field in the "wporg_settings_section" section, inside the "reading" page
	    add_settings_field(
	        'jco_enqueued_scripts',
	        'Enqueued Scripts',
	        array($this,'jco_scripts_output'),
	        'js_css_optimization',
	        'enqueued_list_section'
	    );
	    
			add_settings_field(
	        'styles',
	        'Enqueued Styles',
	        array($this,'jco_styles_output'),
	        'js_css_optimization',
	        'enqueued_list_section'
	    );
	}

	public function jco_enqueued_list_section_cb( $section ) {
		PC::debug('In section callback');
	  ?>
		<h1><?= esc_html($section['title']); ?></h1>
		<?php
	}
	
	public function jco_scripts_output() {
		$this->	output_items_list('jco_enqueued_scripts');
	}
	
	public function jco_styles_output() {
		$this->	output_items_list('jco_enqueued_styles');
	}
	
	public function output_items_list( $option) {
	  // get the value of the setting we've registered with register_setting()
    $setting = get_option( $option );
   
    
    // output the field
    if (! isset ($setting) ) return;?>
    <table col="3">
    	<tr>
    		<th> Handler </th>
    		<th> Dependencies </th>
    		<th> File size </th>
    		<th> Location </th>
    	</tr>
    <?php	
    foreach ($setting as $handle => $script ) {	
    	$filename = $script['filename'];
    	$deps = $script['deps'];
	    $path = parse_url($filename, PHP_URL_PATH);
			//To get the dir, use: dirname($path)
			$path = $_SERVER['DOCUMENT_ROOT'] . $path;
	    $size = size_format( filesize($path) );
	    $location = $script['in_footer']?'footer':'header';  	
    	?>
    	<tr>
	    	<td title="<?php echo $filename;?>"><?php echo $handle;?></td>
	    	<td><?php echo $deps;?></td>
	    	<td title="<?php echo $path;?>"><?php echo $size;?></td>
	    	<td><?php echo $location;?></td>
    	</tr>
    	<?php
    }?>
    </table>
		<?php
	}

	public function output_options_page() {
	    // check user capabilities
	    if (!current_user_can('manage_options')) {
	        return;
	    }
	    ?>
	    <div class="wrap">
	        <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        <div class="body">
	        </div>
	        <form action="options.php" method="post">
	        		<?php 
	            // output security fields for the registered setting "wporg_options"
	            settings_fields('options');
	            // output setting sections and their fields
	            // (sections are registered for "wporg", each field is registered to a specific section)
	            do_settings_sections('js_css_optimization');
	            
	            // output save settings button
	            //submit_button( $text, $type, $name, $wrap, $other_attributes )
	            ?>
	            <table class="button-table" col="2">
	            <tr>
	            	<td><?php submit_button( 'Save Settings', 'primary', 'jco_save', true, array('tabindex'=>'1') );?> </td>
	            	<td><?php submit_button('Reset enqueue list', 'delete', 'jco_reset' );?> </td>
	          	</tr>
	        </form>
	    </div>
	    <?php
	}

	
//	public function test_object_visibility() {
//	 	if ( is_page('accueil') ) {
//	  	self::$test='Gone through accueil';
//	 	}
//	}
//	
//	public function show_test_obj() {
//		PC::debug(array('Test object' => self::$test));
//	}
//		
		
	public function save_header_scripts() {
		$this->save_enqueued_scripts( false );
	}
	
	public function save_footer_scripts() {
		$this->save_enqueued_scripts( true );
	}
	
	public function save_enqueued_scripts( $location ) {
		
		if (is_page('options_delete') ) {
			delete_option();
			echo '<h3> Scripts buffer emptied ! </h3>';
			return;
		}
		
	  //PC::debug( 'In save enqueued scripts' );	
	  $scripts = get_option('jco_enqueued_scripts');
	  //PC::debug(array('scripts before update' => $scripts));
		
		global $wp_scripts;
		//PC::debug( array('WP SCRIPTS'=>$wp_scripts) );
		foreach( $wp_scripts->queue as $handle ) {
	     $obj = $wp_scripts->registered [$handle];
	  	 //PC::debug(array('handle' => $handle));
			 //PC::debug( array('$obj'=>$obj) );
	     $scripts[$handle]=array(
	     	'filename' => $obj->src,
	     	'in_footer' => $in_footer,
	     	'deps' => $obj->deps,
	     );
		}
		
		update_option( 'jco_enqueued_scripts', $scripts, true );
	  //PC::debug(array('scripts after update' => $scripts));
	  
	  
	}
	
	
	public function admin_page_actions()
    {
        if ( ! wp_verify_nonce( $_POST[ $this->option_name . '_nonce' ], $this->action ) )
            die( 'Invalid nonce.' . var_export( $_POST, true ) );

        if ( isset ( $_POST[ 'jco_reset' ] ) )
        {
            update_option( $this->option_name, $_POST[ $this->option_name ] );
            $msg = 'updated';
        }
        else
        {
            delete_option( $this->option_name );
            $msg = 'deleted';
        }

        if ( ! isset ( $_POST['_wp_http_referer'] ) )
            die( 'Missing target.' );

        $url = add_query_arg( 'msg', $msg, urldecode( $_POST['_wp_http_referer'] ) );

        wp_safe_redirect( $url );
        exit;
    }


	public function conditionally_deregister_scripts() {
		
		if ( !is_front_page() ) {
			wp_dequeue_script( 'easingslider' );
		}
		
		if ( !is_single() ) {
			//PC::debug(array('Not in POST OR RECIPE'));
			wp_dequeue_script( 'galleria' );
			wp_dequeue_script( 'galleria-fs' );
			wp_dequeue_script( 'galleria-fs-theme' );
		}
		
		wp_dequeue_script( 'cnss_js' );
		//wp_enqueue_script( 'cnss_js', PLUGINS_URL . '/easy-social-icons/js/cnss.js' , true );


		//wp_dequeue_script( 'jquery-ui-sortable' );
		//wp_dequeue_script( 'bp-confirm' );
		wp_deregister_script( 'bp-legacy-js' );
		wp_register_script( 'bp-legacy-js', 
			PLUGINS_URL . '/buddypress/bp-templates/bp-legacy/js/buddypress.min.js',
			array(),
			false,
			true );
		wp_enqueue_script( 'bp-legacy-js' );
	}


}

