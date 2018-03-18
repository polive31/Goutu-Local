<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	


// Creating the widget 
class Taxonomy_Accordion_Widget extends WP_Widget {

function __construct() {
parent::__construct(
	// Base ID of your widget
	'taxonomy_accordion_widget', 

	// Widget name will appear in UI
	__('Taxonomy Accordion widget', 'foodiepro'), 

	// Widget description
	array( 'description' => __( 'Displays a accordion-style list allowing to navigate between chosen taxonomy terms', 'foodiepro' ), ) 
	);

	//enqueue CSS and JS on frontend only if widget is active.
	if(is_active_widget(false, false, $this->id_base)) {
		add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
	}
}

function load_scripts() {
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-accordion');
}

// Creating widget front-end
public function widget( $args, $instance ) {
	global $wp;
		
	$title = apply_filters( 'widget_title', $instance['title'] );
		
	// before and after widget arguments are defined by themes
	echo $args['before_widget'];
	
	// Widget code starts


	$obj = get_queried_object();
	if (isset($obj->taxonomy)) {
		$tax_slug = $obj->taxonomy;
		// echo '<pre>' . 'taxonomy : ' . $tax_slug . '</pre>';
		$tax_parent = $obj->parent;
		// echo '<pre>' . 'parent : ' . $tax_parent . '</pre>';
		if ($tax_slug=="course") $current_tax=0;
		elseif ($tax_slug=="season") $current_tax=1;
		elseif ($tax_slug=="occasion") $current_tax=2;
		elseif ($tax_slug=="cuisine") {
			if ($tax_parent!=9996) $current_tax=3;
			else $current_tax=4;
		}
		elseif ($tax_slug=="diet") $current_tax=5;
	}
	else $current_tax=0;
	// echo '<pre>' . 'current tax : ' . $current_tax . '</pre>';

	echo '<div id="accordion">';
	echo do_shortcode('[ct-terms-menu tax="course" title="De l\'Apéro au Dessert" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[ct-terms-menu tax="season" title="Cuisine de Saisons" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[ct-terms-menu tax="occasion" title="Pour toutes les occasions" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[ct-terms-menu tax="cuisine" exclude="9996" title="Cuisines du Monde" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[ct-terms-menu tax="cuisine" parent="9996" title="Cuisines de nos Régions" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[ct-terms-menu tax="diet" title="Régimes et Diététique" orderby="name" order="ASC" count="no" hide_empty="true"]');
	echo do_shortcode('[tags-menu title="Inspirations" orderby="name" order="ASC" count="true" hide_empty="true"]');	
	echo '</div>';


	?>
	<script>
		jQuery(document).ready(function() {
			// console.log('Ca démarre !');
			jQuery( "#accordion" ).accordion({ collapsible: true, header: "h3", heightStyle: "content", active: <?php echo $current_tax ?> });
		});
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
add_action( 'widgets_init', 'wpb_load_widget_accordion_taxonomy' );
function wpb_load_widget_accordion_taxonomy() {
	register_widget( 'taxonomy_accordion_widget' );
}



