<?php
/*
Name: Admin class 
Description: Backend management for custom rating plugin
Author: Pascal Olive
Version: 0.1
*/


class Custom_Star_Ratings_Admin {
	
  private $_var;

  const FORCE_PETITE = 20;
  const FORCE_MOYENNE = 50;
  const FORCE_GRANDE = 80;

  public function __construct() {
		add_action('admin_menu', array($this,'user_ratings_setup_menu') );
  }

	public function user_ratings_setup_menu() {
		add_submenu_page( 'options-general.php', 'Star Ratings Admin Page', 'Star Ratings', 'manage_options', 'custom-ratings-admin', array($this,'output_admin_page') );
	}

	public function output_admin_page() {
  	//echo __FILE__;
		echo '<h1>Hello World!</h1>';
	}
	  

}

?>

