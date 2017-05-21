<?php

class WPSSM_Admin_Post {
	
	use Utilities;

 	private $type;

 	/* Class arguments */
 	private $plugin_name;
 	private $sizes;
 	private $form_action;
 	private $nonce;
 	
 	/* Objects */
 	private $general;
 	private $assets;
 	private $mods;
 	 	
  public function __construct( $args ) {
 		WPSSM_Debug::log('*** In WPSSM_Admin_Post __construct ***' );		  	 	
  	$this->hydrate_args( $args );
  }
  
  public function init_post_cb() {
 		WPSSM_Debug::log('*** In WPSSM_Admin_Post init_post_cb ***' );		  	 	
  }  
  

/* FORM SUBMISSION
--------------------------------------------------------------*/
	public function update_settings_cb() {
		WPSSM_Debug::log('In update_settings_cb');
		// check user capabilities
    if (!current_user_can('manage_options')) return;
    
    if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->form_action ) )
        die( 'Invalid nonce.' . var_export( $_POST, true ) );
		//WPSSM_Debug::log('In update_settings_cb function');
		
		if ( ! isset ( $_POST['_wpssm_http_referer'] ) )
		    die( 'Missing valid referer' );
		else
			$url = $_POST['_wpssm_http_referer'];
		
		$type = $this->get_tab();
		$query_args=array();
		$query_args['tab']=$type;
		
		if ( isset ( $_POST[ 'wpssm_reset' ] ) ) {
		   	WPSSM_Debug::log( 'In Form submission : RESET' );
  			$this->assets 	= new WPSSM_Options_Assets;
  			$this->mods   	= new WPSSM_Options_Mods;
				WPSSM_Debug::log( 'assets before submission' , $this->assets->get_assets($type) );
				$this->assets->reset( $type );
				$this->mods->delete( $type );
				WPSSM_Debug::log( 'assets after submission', $this->assets->get_assets($type));
		    $query_args['msg']='reset';
		}
		elseif ( isset ( $_POST[ 'wpssm_delete' ] ) ) {
		   	WPSSM_Debug::log( 'In Form submission : DELETE' );
			  $this->general 	= new WPSSM_Options_General;
  			$this->assets 	= new WPSSM_Options_Assets;
  			$this->mods   	= new WPSSM_Options_Mods;
		    $this->general->delete();
		    $this->assets->delete();
		    $this->mods->delete();
		    $query_args['msg']='delete';
		}
		else {
				WPSSM_Debug::log( 'In Form submission : SAVE, tab ' . $type );
				if ( $type=='general' ) {
			  	$this->general 	= new WPSSM_Options_General;
					//WPSSM_Debug::log('general save self::$opt_general_settings' ,self::$opt_general_settings);
					$this->general->update_from_post();
					$this->general->update_opt();
				}
				else {
  				$this->assets 	= new WPSSM_Options_Assets;
  				$this->mods   	= new WPSSM_Options_Mods;
					$this->mods[$type]=array();	
					WPSSM_Debug::log( 'assets before submission',$this->assets->get() );
					WPSSM_Debug::log( 'mods before submission',$this->mods->get() );
					foreach ( $this->assets->get($type) as $handle=>$asset ) {
						//WPSSM_Debug::log( array('Looping : asset = ' => $asset ) );
						//WPSSM_Debug::log( array('Looping : handle = ' => $handle ) );
						$result=$this->assets->update_from_post($type, $handle, 'location');
						if ($result[0]) $this->mods->add( $type, $result[1], $handle);
						$result=$this->assets->update_from_post($type, $handle, 'minify');
						if ($result[0]) $this->mods->add( $type, 'minify', $handle);
						$this->assets->update_priority( $type, $handle ); 
					}
					WPSSM_Debug::log( 'assets after submission',$this->assets->get() );				
					WPSSM_Debug::log( 'mods after submission',$this->mods->get()) ;				
					$this->assets->update_opt();
					$this->mods->update_opt();
				}
		    $query_args['msg']='save';
		}

		WPSSM_Debug::log('http referer',$url);
		$url = add_query_arg( $query_args, $url) ;
		WPSSM_Debug::log('url for redirection',$url);
					 
		wp_safe_redirect( $url );
		exit;
	}
	




/* RECORDING CALLBACKS 
----------------------------------------------------*/
	public function record_header_assets_cb() {
		WPSSM_Debug::log('In record header assets cb');
		$this->assets->record( false );
	}

	public function record_footer_assets_cb() {
		WPSSM_Debug::log('In record footer assets cb');
		$this->assets->record( true );
	}
	
	
  

	
	
}