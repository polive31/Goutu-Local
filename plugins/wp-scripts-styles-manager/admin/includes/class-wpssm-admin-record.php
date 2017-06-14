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
 	private $Assets;
 	 	
  public function __construct( $args ) {
		//PHP_Debug::trace('*** In WPSSM_Admin_Record __construct ***' );		  	
  	$this->hydrate_args( $args );	
		$this->Assets = new WPSSM_Options_Assets( $args );
  }
  
/* RECORDING CALLBACKS 
----------------------------------------------------*/
	public function record_header_assets_cb() {
		//PHP_Debug::trace('In record header assets cb');
		$this->record( false );
	}

	public function record_footer_assets_cb() {
		//PHP_Debug::trace('In record footer assets cb');
		$this->record( true );
	}

/* RECORDING FUNCTIONS
-----------------------------------------------------------*/
 
	public function record( $in_footer ) {
		//PHP_Debug::trace('In record enqueued assets');
		global $wp_scripts;
		global $wp_styles;

		/* Select data source depending whether in header or footer */
		if ($in_footer) {
			//PHP_Debug::trace('FOOTER record');
			//PHP_Debug::trace(array( '$header_scripts' => $this->header_scripts ));
			$scripts=array_diff( $wp_scripts->done, $this->header_scripts );
			$styles=array_diff( $wp_styles->done, $this->header_styles );
			//PHP_Debug::trace(array('$source'=>$source));
		}
		else {
			$page_info = array(get_permalink(), current_time( 'mysql' ));
			$this->Assets->store_page(	$page_info, get_permalink() );
			$scripts=$wp_scripts->done;
			$styles=$wp_styles->done;
			$this->header_scripts = $scripts;
			$this->header_styles = $styles;
			//PHP_Debug::trace('HEADER record');
			//PHP_Debug::trace(array('$source'=>$source));
		}
	  //PHP_Debug::trace(array('assets before update' => $this->opt_enqueued_assets));
				
		$assets = array(
			'scripts'=>array(
					'handles'=>$scripts,
					'registered'=> $wp_scripts->registered),
			'styles'=>array(
					'handles'=>$styles,
					'registered'=> $wp_styles->registered),
		);
				
		//PHP_Debug::trace( array( '$assets' => $assets ) );		
			
		foreach( $assets as $type=>$asset ) {
			//PHP_Debug::trace( $type . ' recording');		
					
			foreach( $asset['handles'] as $index => $handle ) {
				$obj = $asset['registered'][$handle];
				$location=$in_footer?'footer':'header';
				$this->Assets->store( $type, $handle, $obj, $location );
			}
		}
	  //PHP_Debug::trace(array('assets after update' => $this->opt_enqueued_assets));
	  if ( $in_footer )	$this->Assets->update_opt();
	}

	
	
}