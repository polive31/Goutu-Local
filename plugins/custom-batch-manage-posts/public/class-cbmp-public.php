<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



class CBMP_Public
{
	private static $PLUGIN_URI;
	private static $PLUGIN_PATH;

	public  function __construct() {
		self::$PLUGIN_URI = plugin_dir_url(dirname(__FILE__));
		self::$PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
	}

	public function init_scripts()
	{
		wp_register_script('ajax_call_batch_manage', self::$PLUGIN_URI . '/assets/ajax_call_on_button_press.js', array('jquery'), '1.0', true);
	}


}
