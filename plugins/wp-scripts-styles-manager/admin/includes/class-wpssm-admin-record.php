<?php

class WPSSM_Admin_Record {
	/* Class triggerred independantly from WPSSM_Public even if it runs in frontend */

	use Utilities;

 	/* Class local attributes*/
 	private $type;
 	private $header_scripts;
 	private $header_styles;

 	/* Class parameters */
 	private $plugin_name;
 	
 	/* Objects */
 	private $assets;
 	 	
  public function __construct( $args ) {
		WPSSM_Debug::log('*** In WPSSM_Admin_Record __construct ***' );		  	
  	$this->hydrate_args( $args );	
		$this->assets = new WPSSM_Options_Assets( array(	'plugin_name' => $this->plugin_name ));
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
	
	
	

/* RECORDING FUNCTIONS
-----------------------------------------------------------*/
  

	public function record( $in_footer ) {
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
			$page_info = array(get_permalink(), current_time( 'mysql' ));
			$this->assets->store_page(	$page_info, get_permalink() );
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
				$location=$in_footer?'footer':'header';
				$this->assets->store( $type, $handle, $obj, $location );
			}
		}
	  WPSSM_Debug::log(array('assets after update' => $this->opt_enqueued_assets));
	  if ( $in_footer )	$this->assets->update_opt();
	}

	
	
}