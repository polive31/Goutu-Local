<?php

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



// Creating the widget
class Taxonomy_Search_Widget extends WP_Widget {

	function __construct() {
	parent::__construct(
		// Base ID of your widget
		'taxonomy_search_widget',

		// Widget name will appear in UI
		__('Taxonomy Search widget', 'foodiepro'),

		// Widget description
		array( 'description' => __( 'Displays taxonomy terms corresponding to the current search terms', 'foodiepro' ), )
		);
	}


	// Creating widget front-end
	public function widget( $args, $instance ) {
		if ( !is_search() ) return;

		global $wp;

		echo $args['before_widget'];

		$tax = 'ingredient';

		$search_term = get_search_query();
		$search_term = $this->remove_accents($search_term);

		$query_args = array(
			'taxonomy'		=> $tax,
			'hide_empty'	=> false,
			// 'name' 			=> $search_term,
			// name__like too complex to handle, since it displays multiple children difficult to filter
			'name__like' 	=> $search_term,
		);
		$query_terms = get_terms( $query_args );

		$related_terms=array();
		foreach ($query_terms as $term) {
			$term_name=$this->remove_accents($term->name);
			if ( $this->foundWord($term_name, $search_term) ||  $this->foundWord($term_name, $search_term . 's')) {
				$related_terms[]=$term;
			}
		}

		if (!empty($related_terms)) {

			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $args['before_title'] . $title . $args['after_title'];

			// before and after widget arguments are defined by themes

			echo '<ul class="related-terms">';
			foreach ($related_terms as $term) {
				echo '<li>';
				$link =  get_term_link( $term, $tax );
				echo '<div class="entry-header-overlay">';
				$image = CNH_Assets::get_term_image($term, 'small-thumbnail', 'taxonomy-search-image', 'skip-lazy');
				$image_html = sprintf('<span class="archive-image">%s</span>', $image  );
				echo sprintf('<a href="%1$s" title="%2$s">%3$s</a>', $link, ucfirst($term->name), $image_html) ;
				echo "</div>";
				echo '<h2 class="entry-title">';
				echo sprintf('<a href="%1$s">%2$s</a>', $link, ucfirst($term->name)) ;
				echo '</h2>';
				echo '</li>';
			}
			echo '</ul>';

		}
		else
			echo '&nbsp;';

		// Output end
		echo $args['after_widget'];
	}
	public function remove_accents($word) {
		$from = "éêèëâôçû";
		$to  =  "eeeeaocu";
		$trans=array(
			"é"=>"e",
			"è"=>"e",
			"ê"=>"e",
			"â"=>"a",
			"à"=>"a",
			"ô"=>"o",
			"'"=>" ",
		);
		// $word = strtr($word, $from, $to));
		$word = strtr($word, $trans);
		return $word;
	}

	public function foundWord($haystack, $needle){
		$pattern = "/\b($needle)\b/i";
		return preg_match($pattern, $haystack);
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
add_action( 'widgets_init', 'cnh_load_taxonomy_search_widget' );
function cnh_load_taxonomy_search_widget() {
	register_widget( 'taxonomy_search_widget' );
}
