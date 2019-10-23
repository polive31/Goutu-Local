<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


// Creating the widget
class Recipe_Taxonomy_Accordion extends WP_Widget {

	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'Recipe_Taxonomy_Accordion',
			// Widget name will appear in UI
			__('Recipe Taxonomies Accordion widget', 'foodiepro'),
			// Widget description
			array( 'description' => __( 'Displays a accordion-style list allowing to navigate between chosen recipe taxonomy terms', 'foodiepro' ), )
		);
		//enqueue CSS and JS on frontend only if widget is active.
		add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
	}

	public function load_scripts() {
		if(is_active_widget(false, false, $this->id_base)) {
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-accordion');
		}
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		global $wp;

		$title = apply_filters( 'widget_title', $instance['title'] );

		$show_count = $instance['show_count']?'true':'false';
		$displayed_user = $instance['displayed_user'];

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		// Widget code starts

		$obj = get_queried_object();
		$tax_slug='';
		$tax_parent='';
		if (isset($obj->taxonomy)) {
			$tax_slug = $obj->taxonomy;
			// echo '<pre>' . 'taxonomy : ' . $tax_slug . '</pre>';
			$tax_parent = $obj->parent;
			// echo '<pre>' . 'parent : ' . $tax_parent . '</pre>';
		}
		$page_slug=get_post_meta( get_the_ID(), 'page_slug', true );

		if ($tax_slug=="course" || $page_slug=="course") $current_menu_item=0;
		elseif ($tax_slug=="season" || $page_slug=="season") $current_menu_item=1;
		elseif ($tax_slug=="occasion" || $page_slug=="occasion") $current_menu_item=2;
		elseif ( ($tax_slug=="cuisine" && $tax_parent!=9996) || ( $page_slug=="world" ) ) $current_menu_item=3;
		elseif ( ($tax_slug=="cuisine" && $tax_parent==9996) || ( $page_slug=="region" ) ) $current_menu_item=4;
		elseif ($tax_slug=="diet" || $page_slug=="diet") $current_menu_item=5;
		elseif ($tax_slug=="post_tag") $current_menu_item=6;
		else $current_menu_item=0;
		// echo '<pre>' . 'current tax : ' . $current_menu_item . '</pre>';


		// TODO COMPLETE THIS : LIMIT TO USER
		if ( $displayed_user && class_exists('Peepso') ) {
			$user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
			if ( empty($user_id) ) $displayed_user=false;
		}
		else
			$displayed_user=false;

		echo '<div id="accordion">';
		// echo do_shortcode('[ct-terms-menu page_slug="plats" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="course" title="' . __('Courses', 'foodiepro') . '" orderby="name" author="0" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu  page_slug="saisons" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="season" title="' . __('Seasons', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu  page_slug="occasions" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="occasion" title="' . __('Occasions', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu  page_slug="monde" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="cuisine" parent="0" drill="true" exclude="9996" title="' . __('World', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu  page_slug="regions" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="cuisine" parent="9996" title="' . __('France', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu  page_slug="regimes" page_title="' . __('Latest recipes', 'foodiepro') . '" tax="diet" title="' . __('Diets', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="course" title="' . __('Courses', 'foodiepro') . '" orderby="description" author="0" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="season" title="' . __('Seasons', 'foodiepro') . '" orderby="description" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="occasion" title="' . __('Occasions', 'foodiepro') . '" orderby="description" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="cuisine" parent="0" drill="true" exclude="9996" title="' . __('World', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="cuisine" parent="9996" title="' . __('France', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[ct-terms-menu tax="diet" title="' . __('Diets', 'foodiepro') . '" orderby="description" order="ASC" count="' . $show_count . '"]');
		echo do_shortcode('[tags-menu post_type="recipe" title="' . __('Ideas', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo '</div>';


		?>
		<script>
			jQuery(document).ready(function() {
				// console.log('Ca démarre !');
				jQuery( "#accordion" ).accordion({ collapsible: true, header: "h3", heightStyle: "content", active: <?php echo $current_menu_item ?> });
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
		if ( isset( $instance[ 'show_count' ] ) ) $show_count = $instance[ 'show_count' ];
			else $show_count = false;
		if ( isset( $instance[ 'displayed_user' ] ) ) $displayed_user = $instance[ 'displayed_user' ];
			else $displayed_user = false;

	// Widget admin form
	?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'foodiepro' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Show Recipe Count', 'foodiepro' ); ?></label>
		    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" <?php checked( $instance[ 'show_count' ], 'on' ); ?>  >
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'displayed_user' ); ?>"><?php _e( 'Limit Recipes to Current Displayed User', 'foodiepro' ); ?></label>
		    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'displayed_user' ); ?>" name="<?php echo $this->get_field_name( 'displayed_user' ); ?>" <?php checked( $instance[ 'displayed_user' ], 'on' ); ?>  >
		</p>
	<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['show_count'] = $new_instance['show_count'];
	$instance['displayed_user'] = $new_instance['displayed_user'];
	return $instance;
	}

} // Class wpb_widget ends here

// Register and load the widget
add_action( 'widgets_init', 'wpb_load_widget_accordion_taxonomy' );
function wpb_load_widget_accordion_taxonomy() {
	register_widget( 'Recipe_Taxonomy_Accordion' );
}
