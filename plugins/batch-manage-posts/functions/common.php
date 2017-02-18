<?php
/*
Description: Common functions for batch manage posts
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



/* Extract arguments passed via Ajax call and echo value
---------------------------------------------------------*/
function get_ajax_arg($name,$label='') {
	
	$value='';
	
	if ( isset($_POST['args'][$name]) ) {
		$value = $_POST['args'][$name];
		$label = ( empty($label) )?ucfirst($name):$label;	
	}
	else $value='-1';
	echo sprintf("<b> %s </b> = %s",$label,$value);
	echo "<br>";
	
	return $value;

}

/* Transforms a list of consecutive values into $key=>$value pairs
---------------------------------------------------------*/
function extractKeyValuePairs($string, $delimiter = ' ') {
    $params = explode($delimiter, $string);
    $pairs = [];
    for ($i = 0; $i < count($params); $i++) {
        $pairs[$params[$i]] = $params[++$i];
    }

    return $pairs;
}

/* Transforms a list of consecutive values into $key=>$value pairs
---------------------------------------------------------*/
function create_ajax_arg_array($a, $script_name, $script_id) {
	
	foreach ( $a as $key=>$value ) {
		if ( ! empty($value) ) {
			echo sprintf("<b> %s </b> = %s",ucfirst($key),$value);
			echo "<br>";
		}
	}
		
	$ajson = json_encode($a);
	$nonce = wp_create_nonce( $script_name . $a['cmd'] );
	//echo "Nonce = " . $nonce;
	//echo "<br>";
			
	// Localize and enqueue the script with new data
	$jsargs = array(
		'nonce' => $nonce,
		'url' => admin_url( 'admin-ajax.php' ),
		'data' => $ajson,
	);
	
	return $jsargs;

}

/* Security check on AJAX referred page
---------------------------------------------------------*/
function is_secure($nonceurl) {
	$result = true;
	$nonce_check = check_ajax_referer( $nonceurl, false, false );
	if ( ! ( $nonce_check && is_user_logged_in() && current_user_can('edit_others_posts') ) ) {
		echo 'Security check failed, script stopped';
		$result=false;
		exit;
	}
	return $result;
}

?>
