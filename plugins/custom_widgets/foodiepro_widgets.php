<?php
/*
Plugin Name: Foodiepro Custom Widgets
Plugin URI: http://goutu.org
Description: Provides additional widgets for FoodiePro theme  
Author: Pascal Olive 
Version: 1.0
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
add_action( 'widgets_init', function(){
     register_widget( 'Dropdown_Posts_Sort_Widget' );
});	

// Creating the widget 
class dropdown_posts_sort_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'dropdown_posts_sort_widget', 

// Widget name will appear in UI
__('Dropdown Posts Sort Widget', 'foodiepro'), /*$widget_ops ???*/

// Widget description
array( 'description' => __( 'Displays a dropdown list allowing to sort posts', 'foodiepro' ), ) 
);
}

// Creating widget front-end
public function widget( $args, $instance ) {
	global $wp;
	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	//if ( ! empty( $title ) )
	if ( is_tax() )
		echo $args['before_title'] . __('Sort recipes', 'foodiepro') . $args['after_title'];
	else 
		echo $args['before_title'] . __('Sort posts', 'foodiepro') . $args['after_title'];
	// Start of widget code
	
	$search_term = get_search_query();
	if ( !empty( $search_term ))
		$search_prefix = 's=' . $search_term . '&';

	echo '<div class="dropdown-select">';
	echo '<label class="screen-reader-text" for="sort_dropdown">' . $title . '</label>';
	echo '<select name="sort_dropdown" id="sort_dropdown" class="postform">';
	echo '<option value="none" class="separator">' . __('Select sort order...', 'foodiepro') . '</option>';
	echo '<option class="level-0" value="' . $search_prefix . 'orderby=title&order=ASC">'. __('Title : ascending', 'foodiepro') . '</option>';
	echo '<option class="level-0" value="' . $search_prefix . 'orderby=title&order=DESC">'. __('Title : descending', 'foodiepro') . '</option>';
	//echo '<option class="level-0" value="' . $search_prefix . 'orderby=author_name">'. __('Author', 'foodiepro') . '</option>';
	//echo '<option class="level-0 separator" value="author&order=ASC">'. __('Author : descending', 'foodiepro') . '</option>';
	echo '<option class="level-0" value="' . $search_prefix . 'orderby=date&order=DESC">'. __('Newest first', 'foodiepro') . '</option>';
	//echo '<option disabled>───────────</option>';
	//echo '<option class="level-0 last" value="meta_value_num&order=DESC&meta_key=recipe_user_ratings_rating">'. __('Rating', 'foodiepro') . '</option>';
	if ( is_category() )
		echo '<option class="level-0 last" value="' . $search_prefix . 'orderby=comment_count&order=DESC">'. __('Comment count', 'foodiepro') . '</option>';
	else
		echo '<option class="level-0 last" value="' . $search_prefix . 'orderby=rating">'. __('Rating', 'foodiepro') . '</option>';
	echo '</select>'; 
	echo '</div>';?>

	<script type="text/javascript">
		/* <![CDATA[ */
		(function() {
			var dropdown=document.getElementById("sort_dropdown");
			function onDropDownChange() {
				var choice = dropdown.options[dropdown.selectedIndex].value;
				if ( choice != "none" ) {
					/*location.href="<?php echo esc_url(add_query_arg(array('meta_key' => 'recipe_user_ratings_rating','orderby' => 'meta_value_num','order' => 'DESC'))); ?>";*/
					location.href="<?php echo esc_url( home_url(add_query_arg(array(),$wp->request)) ); ?>?"+choice;
					/*var query_url = "esc_url(add_query_arg(array(" + dropdown.options[dropdown.selectedIndex].value + ")));";
					location.href="<?php echo "+ query_url +"?>;";*/
				}
			}
			dropdown.onchange=onDropDownChange;
		})();
		/* ]]> */
	</script>
	
<?php
	// Output end
	echo $args['after_widget'];
}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'foodiepro' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'dropdown_posts_sort_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );



