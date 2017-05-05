<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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



