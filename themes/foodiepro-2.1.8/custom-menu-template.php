<?php

add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );

function wpurp_custom_menu_template( $form, $menu ) {
	return '';
}




//	ob_start();
//	?>
//	
//	<h3> TOTO C'EST ICI LE MENU !!!! </h3>
//
//	<?php
//  $output = ob_get_contents();
//  ob_end_clean();
//
//	//return $output;


    
?>