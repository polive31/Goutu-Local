<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =                   Display Recipe Evaluation  
/* =================================================================*/

//function display_wpurp_recipe_rating($atts) {
//		$a = shortcode_atts( array(
//  	'half' => 'false', // support of half stars
//	), $atts );
//
//	if ( is_user_logged_in() ) {
//		
//			$stars = new WPURP_Template_Recipe_Stars();  
//			$html = $stars->output( $recipe );
//	
//	} /* End if loggued-in */
//
//	return $html;
//	
//} /* End funtion */
	
add_shortcode('wpurp-recipe-rating', 'display_wpurp_recipe_rating');
	

?>