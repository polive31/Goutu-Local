<?php 


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	


/* =================================================================*/
/* =                    INDEX LINKS GENERATION   
/* =================================================================*/

add_shortcode('index-link', 'add_index_link'); 

function add_index_link($atts) {
	 //Inside the function we extract parameter of our shortcode
	extract( shortcode_atts( array(
		'back' => 'false',
	), $atts ) );
	

	if ($back!='true'):
		
		$obj = get_queried_object();
		$tax_id = $obj -> taxonomy;
		$parent = $obj -> parent;
		$current = $obj -> term_id;

		switch ($tax_id) {
	    case 'course':
				$url = "/recettes/plats";
				//$msg = "De l'apéritif au dessert";
				$msg = __('Courses', 'foodiepro');
				break;
	    case 'season':
				$url = "/recettes/saisons";
				//$msg = "Cuisine de saisons";
				$msg = __('Seasons', 'foodiepro');
				break;
	    case 'occasion':
				$url = "/recettes/occasions";
				//$msg = "En toutes occasions";
				$msg = __('Occasions', 'foodiepro');
				break;
	    case 'diet':
				$url = "/recettes/regimes";
				//$msg = "Régimes et diététique";
				$msg = __('Diets', 'foodiepro');
				break;
	    case 'cuisine':
	    	$url="Parent" . $parent;
	    	if ($parent == 9996 || $current == 9996) {
	    		$url = "/recettes/regions";
					//$msg = "Cuisines de régions";
					$msg = __('France', 'foodiepro');}
	    	else {
	    		$url = "/recettes/monde";
					//$msg = "Cuisines du monde";
					$msg = __('World', 'foodiepro');}
	    	break;
	    case 'category':
				$url = "/blogs";
				$msg = __('All blogs', 'foodiepro');
				break;	
		}
		
	else:
			$url = 'javascript:history.back()';
			$msg = __('Previous page','foodiepro');
	endif;
	
	$output = '<ul class="menu"> <li> <a class="back-link" href="' . $url . '">' . $msg . '</a> </li> </menu>';
	return $output;
}
