/* =================================================================*/
/* =         REMOVE CUSTOMIZER                                     =*/
/* =================================================================*/

// add_action( 'customize_register', 'prefix_remove_css_section', 15 );
/**
 * Remove the additional CSS section, introduced in 4.7, from the Customizer.
 * @param $wp_customize WP_Customize_Manager
 */
// function prefix_remove_css_section( $wp_customize ) {
// 	$wp_customize->remove_section( 'custom_css' );
// }

// add_action( 'init', 'public_customizer_remove', 10 ); // was priority 5
// function public_customizer_remove() {
// 	add_filter( 'map_meta_cap', 'filter_to_remove_customize_capability', 10, 4 );
// }
// function filter_to_remove_customize_capability( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
// 	if ($cap == 'customize') {
// 		return array('nope'); 
// 	}
// 	return $caps;
// }
// add_action( 'admin_init', 'admin_customizer_remove', 10 ); // was priority 5
// function admin_customizer_remove() {
// 	// Drop some customizer actions
// 	remove_action( 'plugins_loaded', '_wp_customize_include', 10);
// 	remove_action( 'admin_enqueue_scripts', '_wp_customize_loader_settings', 11);

// 	// Manually overrid Customizer behaviors
// 	add_action( 'load-customize.php', 'override_load_customizer_action' );
// }
// function override_load_customizer_action() {
// 	// If accessed directly
// 	wp_die( __( 'The Customizer is currently disabled.', 'wp-crap' ) );
// }
