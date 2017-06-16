<?php

class WPSSM_Admin_Post {
	
	use Utilities;

 	/* Class local attributes */
 	private $type;
 	private $args;

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
 		//PHP_Debug::trace('*** In WPSSM_Admin_Post __construct ***' );		  	 	
  	$this->hydrate_args( $args );
  	$this->args = $args;
 		$this->type = $this->get_tab();
 		//PHP_Debug::trace('In WPSSM_Admin_Post __construct $this->type = ' . $this->type );		  	 	
  }
  
  
 /* Gets the active tab (not possible when submitting the post) */
 public function init_post_cb() {
 		$this->type = $this->get_tab();
 		//PHP_Debug::trace('In WPSSM_Admin_Post init_post_cb(), $this->type = ' . $this->type );		  	 	
 }
  

/* FORM SUBMISSION
--------------------------------------------------------------*/
	public function update_settings_cb() {
		//PHP_Debug::trace('In update_settings_cb, type = ' . $this->type );
		// check user capabilities
    if (!current_user_can('manage_options')) return;
    
    if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->form_action ) )
        die( 'Invalid nonce : ' . var_export( $_POST[ $this->nonce ], true ) . ' action : ' . $this->form_action);
		//PHP_Debug::trace('Security checks passed');
		
		if ( ! isset ( $_POST['_wpssm_http_referer'] ) )
		    die( 'Missing valid referer' );
		else
			$url = $_POST['_wpssm_http_referer'];
		
		$query_args=array();
		$query_args['tab']=$this->type;
		
		if ( isset ( $_POST[ 'wpssm_reset' ] ) ) {
		   	//PHP_Debug::trace( 'In Form submission : RESET' );
  			$this->Assets 	= new WPSSM_Options_Assets( $this->args );
  			$this->Mods   	= new WPSSM_Options_Mods( $this->args );
				//PHP_Debug::trace( 'assets before submission' , $this->Assets->get_assets($this->type) );
				$this->Assets->unset_mod( $this->type );
				$this->Mods->reset( $this->type );
				//PHP_Debug::trace( 'assets after submission', $this->Assets->get_assets($this->type));
		    $query_args['msg']='reset';
		}
		elseif ( isset ( $_POST[ 'wpssm_delete' ] ) ) {
		   	//PHP_Debug::trace( 'In Form submission : DELETE' );
			  $this->General 	= new WPSSM_Options_General( $this->args );
  			$this->Assets 	= new WPSSM_Options_Assets( $this->args );
  			$this->Mods   	= new WPSSM_Options_Mods( $this->args );
		    $this->General->delete_opt();
		    $this->Assets->delete_opt();
		    $this->Mods->delete_opt();
		    $query_args['msg']='delete';
		}
		elseif ( isset ( $_POST[ 'wpssm_save' ] ) ) {
				//PHP_Debug::trace( 'In Form submission : SAVE, tab ' . $this->type );
				if ( $this->type=='general' ) {
			  	$this->General 	= new WPSSM_Options_General( $this->args );
					//PHP_Debug::trace('In WPSSM_Post general save' );
					$this->General->update_from_post();
					$this->General->update_opt();
				}
				else {
  				$this->Assets 	= new WPSSM_Options_Assets( $this->args );
  				$this->Mods   	= new WPSSM_Options_Mods( $this->args );
					$this->Mods->reset($this->type);	
					//PHP_Debug::trace( 'assets before submission',$this->Assets->get() );
					//PHP_Debug::trace( 'mods before submission',$this->Mods->get() );
					foreach ( $this->Assets->get($this->type) as $handle=>$asset ) {
						//PHP_Debug::trace( array('Looping : asset = ' => $asset ) );
						//PHP_Debug::trace( array('Looping : handle = ' => $handle ) );
						$result=$this->Assets->update_from_post($this->type, $handle, 'location');
						if ($result['modified']) $this->Mods->add( $handle, $this->type, $result['value'] );
						$result=$this->Assets->update_from_post($this->type, $handle, 'minify');
						if ($result['modified']) $this->Mods->add( $handle, $this->type, 'minify' );
						$this->Assets->update_priority( $this->type, $handle ); 
					}
					//PHP_Debug::trace( 'assets after submission',$this->Assets->get() );				
					//PHP_Debug::trace( 'mods after submission',$this->Mods->get()) ;				
					$this->Assets->update_opt();
					$this->Mods->update_opt();
				}
		    //$query_args['msg']='save';
		    $query_args['msg']='save-' . $this->type;
		}

		//PHP_Debug::trace('http referer',$url);
		$url = add_query_arg( $query_args, $url) ;
		//PHP_Debug::trace('url for redirection',$url);
					 
		wp_safe_redirect( $url );
		exit;
	}

	
	
}