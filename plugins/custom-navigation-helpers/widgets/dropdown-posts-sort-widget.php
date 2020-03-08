<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



// Creating the widget
class Dropdown_Posts_Sort_Widget extends WP_Widget {

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

	/* Do not display in the case of a WPURP dropdown search widget result */
	$url = $_SERVER["REQUEST_URI"];

	$title = apply_filters( 'widget_title', $instance['title'] );
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	//if ( ! empty( $title ) )
	echo $args['before_title'] . __('Sort', 'foodiepro') . $args['after_title'];
	// if ( is_tax() )
	// 	echo $args['before_title'] . __('Sort recipes', 'foodiepro') . $args['after_title'];
	// elseif ( is_archive() )
	// 	echo $args['before_title'] . __('Sort posts', 'foodiepro') . $args['after_title'];
	// elseif ( is_search() )
	// 	echo $args['before_title'] . __('Sort results', 'foodiepro') . $args['after_title'];
	// Start of widget code
	// else {
	$search_term='?';
	if ( is_search() ) {
		$squery = get_search_query();
		if ( !empty($squery ) )
			$search_term .= 's=' . $squery . '&';
	}

	$orderby = get_query_var( 'orderby', false );
	$order = get_query_var( 'order', false );

	?>

	<label class="screen-reader-text" for="sort_dropdown"><?php echo $title;?></label>
	<!-- <div class="dropdown-select"> -->
	<select name="sort_dropdown" id="sort_dropdown" class="dropdown-select postform">
	<option value="none" class="separator"><?php echo __('Select sort order...', 'foodiepro');?></option>
	<?php
	if ( is_category() ) {?>
		<option class="level-0 last" <?= ($orderby=='comment_count'&&$order=='DESC')?'selected':''; ?> value="<?php echo $search_term;?>orderby=comment_count&order=DESC"><?php echo __('Comment count', 'foodiepro');?></option>
	<?php
	}
	else {//not a post so rating is applicable?>
		<option class="level-0 last" <?= ($orderby=='rating')?'selected':''; ?> value="<?php echo $search_term;?>orderby=rating"><?php echo __('Best rated', 'foodiepro');?></option>'
		<option class="level-0 last" <?= ($orderby=='like-count')?'selected':''; ?> value="<?php echo $search_term;?>orderby=like-count"><?php echo __('Most liked', 'foodiepro');?></option>'
	<?php
	}?>
	<option class="level-0" <?= ($orderby=='title'&&$order=='ASC')?'selected':''; ?> value="<?php echo $search_term;?>orderby=title&order=ASC"><?php echo __('Title : ascending', 'foodiepro');?></option>
	<option class="level-0" <?= ($orderby=='title'&&$order=='DESC')?'selected':''; ?> value="<?php echo $search_term;?>orderby=title&order=DESC"><?php echo __('Title : descending', 'foodiepro');?></option>
	<!-- <option class="level-0" value="' . $search_prefix . 'orderby=author_name">'. __('Author', 'foodiepro') . '</option> -->
	<!-- <option class="level-0 separator" value="author&order=ASC">'. __('Author : descending', 'foodiepro') . '</option> -->
	<option class="level-0" <?= ($orderby=='date'&&$order=='DESC')?'selected':''; ?> value="<?php echo $search_term;?>orderby=date&order=DESC"><?php echo __('Newest first', 'foodiepro');?></option>
	<!-- <option disabled>───────────</option>-->
	<!-- <option class="level-0 last" value="meta_value_num&order=DESC&meta_key=recipe_user_ratings_rating">'. __('Rating', 'foodiepro') . '</option>-->
	</select>
	<!-- </div> -->

	<script type="text/javascript">
		/* <![CDATA[ */
		(function() {
			var dropdown=document.getElementById("sort_dropdown");
			function onDropDownChange() {
				console.log( 'Change detected on post sort dropdown !');
				var choice = dropdown.options[dropdown.selectedIndex].value;
				if ( choice != "none" ) {
					console.log( 'Choice = ' + choice);
					location.href="<?= esc_url( home_url(add_query_arg(array(),$wp->request)) ); ?>"+choice;
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
	if ( isset( $instance[ 'title' ] ) )
		$title = $instance[ 'title' ];
	else
		$title = __( 'New title', 'foodiepro' );
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
add_action( 'widgets_init', 'cnh_load_dropdown_posts_sort_widget' );
function cnh_load_dropdown_posts_sort_widget() {
	register_widget( 'dropdown_posts_sort_widget' );
}
