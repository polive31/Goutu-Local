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
		// echo do_shortcode('[ct-terms-menu tax="course" title="' . __('Courses', 'foodiepro') . '" orderby="description" author="0" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu tax="season" title="' . __('Seasons', 'foodiepro') . '" orderby="description" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu tax="occasion" title="' . __('Occasions', 'foodiepro') . '" orderby="description" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu tax="cuisine" parent="0" drill="true" exclude="9996" title="' . __('World', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		// echo do_shortcode('[ct-terms-menu tax="cuisine" parent="9996" title="' . __('France', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo $this->list_taxonomy_terms(array(
			'tax' => 'course',
			'count' => $show_count,
			'title' => __('Courses', 'foodiepro'),
		));

		echo $this->list_taxonomy_terms(array(
			'tax' => 'season',
			'count' => $show_count,
			'title' => __('Seasons', 'foodiepro'),
		));

		echo $this->list_taxonomy_terms(array(
			'tax' => 'occasion',
			'count' => $show_count,
			'title' => __('Occasions', 'foodiepro'),
		));

		echo $this->list_taxonomy_terms(array(
			'tax' => 'cuisine',
			'parent'	=> '0',
			'exclude'	=> '9996',
			'drill'		=> true,
			'count' => $show_count,
			'title' => __('World', 'foodiepro'),
		));

		echo $this->list_taxonomy_terms(array(
			'tax' => 'cuisine',
			'parent'	=> '9996',
			'count' => $show_count,
			'title' => __('France','foodiepro'),
		));

		echo $this->list_taxonomy_terms(array(
			'tax'=>'diet',
			'title' => __('Diets','foodiepro'),
			'count'=> $show_count,
		));
		// echo do_shortcode('[tags-menu post_type="recipe" title="' . __('Ideas', 'foodiepro') . '" orderby="name" order="ASC" count="' . $show_count . '"]');
		echo $this->list_tags(array(
			'post_type'	=> 'recipe',
			'title' 	=> __('Ideas', 'foodiepro'),
			'count' 	=> $show_count,
		));

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

	public function list_tags($atts)
	{
		$count = $atts['count'];
		$title = $atts['title'];
		$type = $atts['post_type'];
		$tags = get_tags(array(
			// New clause "tags_post_type" added to the WP_Query function
			// see term_clauses filter in functions.php
			'tags_post_type' => $type,
			'hide_empty' => true,
			'orderby' => 'name',
			'order'   => 'ASC'
		));

		$html = '<div class="tax-container">';
		$html .= '<h3>' . $title . '</h3>';
		$html .= '<div class="subnav" id="tags" style="display:none">';

		foreach ($tags as $tag) {
			$post_count = $count ? ' (' . $tag->count . ')' : '';
			$url = get_tag_link($tag->term_id);
			$url = add_query_arg('post_type', 'recipe', $url);
			$html .= '<li><a href="' . $url . '">' . $tag->name . $post_count . '</a></li>';
		}
		$html .= '</div></div>';
		return $html;
	}

	/* =================================================================*/
	/* = TAXONOMIES LIST SHORTCODE
	/* =================================================================*/

	public function list_taxonomy_terms($atts)
	{

		if (!empty($atts['tax']))
			$tax = $atts['tax'];
		else
			return;
		$drill = isset($atts['drill'])? $atts['drill']:false;
		$count = isset($atts['count'])? $atts['count']:false;
		$exclude = isset($atts['exclude'])? $atts['exclude']:'';
		$parent = isset($atts['parent'])? $atts['parent']:'';
		$page_order = isset($atts['page_order'])? $atts['page_order']:'last';

		if (empty($atts['title'])) {
			$tax_details = get_taxonomy($tax);
			$title = $tax_details->labels->name;
		} else
			$title = $atts['title'];

		$html = '<div class="tax-container">';
		$html .= '<h3>' . $title . '</h3>';
		$html .= '<div class="subnav" id="' . $tax . '" style="display:none">';

		$terms = get_categories(array(
			'taxonomy' => $tax,
			'exclude' => $exclude,
			'parent' => $parent,
			'author' => 0,
			'hide_empty' => true,
			'orderby' => CNH_Assets::get_orderby($tax),
			'order'   => 'ASC'
		));

		foreach ($terms as $term) {
			$post_count = 0;
			if ($count) {
				if ($drill) {
					$subterms = get_categories(array(
						'taxonomy' => $tax,
						'parent' => $term->term_id,
					));
					// echo '<pre>' . $term->name . '</pre>';
					foreach ($subterms as $subterm) {
						// echo '<pre>' . print_r($subterm) . '</pre>';
						$post_count += (int) $subterm->count;
					}
				}
				$post_count += (int) $term->count;
				$post_count = ' (' . $post_count . ')';
			}
			$html .= '<li><a href="' . get_term_link($term, $tax) . '">' . $term->name . $post_count . '</a></li>';
		}

		$html .= '</div></div>';

		return $html;
	}


	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
		else
			$title = __( 'New title', 'foodiepro' );
		if ( isset( $instance[ 'show_count' ] ) )
			$show_count = $instance[ 'show_count' ];
			else
			$show_count = false;
		if ( isset( $instance[ 'displayed_user' ] ) )
			$displayed_user = $instance[ 'displayed_user' ];
			else
			$displayed_user = false;

	// Widget admin form
	?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'foodiepro' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Show Recipe Count', 'foodiepro' ); ?></label>
		    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" <?php checked( $show_count, 'on' ); ?>  >
		</p>

		<p>
		    <label for="<?php echo $this->get_field_id( 'displayed_user' ); ?>"><?php _e( 'Limit Recipes to Current Displayed User', 'foodiepro' ); ?></label>
		    <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id( 'displayed_user' ); ?>" name="<?php echo $this->get_field_name( 'displayed_user' ); ?>" <?php checked( $displayed_user, 'on' ); ?>  >
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
add_action( 'widgets_init', 'cnh_load_taxonomy_accordion_widget' );
function cnh_load_taxonomy_accordion_widget() {
	register_widget( 'Recipe_Taxonomy_Accordion' );
}
