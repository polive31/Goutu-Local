<?php

// Block direct requests
if (!defined('ABSPATH'))
	die('-1');



// Creating the widget
class Taxonomy_Dropdown_Widget extends WP_Widget
{
	private static $TAX_LIST;
	private $dropdown_args=array();

	function __construct()
	{
		parent::__construct(
			// Base ID of your widget
			'taxonomy_dropdown_widget',
			// Widget name will appear in UI
			__('Taxonomy Dropdown widget', 'foodiepro'),
			// Widget description
			array('description' => __('Displays a dropdown list allowing to navigate between taxonomy terms', 'foodiepro'))
		);
		self::$TAX_LIST = array(
			'course' 	=> __('Filter by course', 'foodiepro'),
			'ingredient'=> __('Filter by ingredient', 'foodiepro'),
			'cuisine' 	=> __('Filter by country', 'foodiepro'),
			'diet' 		=> __('Filter by diet', 'foodiepro'),
			'season' 	=> __('Filter by season', 'foodiepro'),
			'occasion' 	=> __('Filter by occasion', 'foodiepro'),
			'category'	=> __('Filter by category', 'foodiepro'),
			'difficult'	=> __('Filter by level', 'foodiepro'),
		);

	}


	// Creating widget front-end
	public function widget($args, $instance)
	{
		static $dropdown_id=0;
		$dropdown_id++;

		// if (!is_archive() && !is_search()) return;
		if ( !is_archive() ) return;

		$this->set_dropdown_args($instance['tax-list']);

		$title = apply_filters('widget_title', $instance['title']);
		if ($title=='') {
			$title = $this->get_tax_name( $this->get_dropdown_arg('taxonomy') );
		}

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		// Widget title
		echo $args['before_title'] . $title . $args['after_title'];

		// Dropdown display
		echo $this->output_custom_dropdown('dropdown-select', $this->get_dropdown_arg('taxonomy') . $dropdown_id);

		// Output end
		echo $args['after_widget'];
	}

	public function output_custom_dropdown($class='', $id='dropdown0') {
		$html = $this->get_dropdown_html($class = '', $id);
		$html .= $this->get_dropdown_js($id);
		return $html;
	}

	/* =================================================================
	GETTERS
	====================================================================*/
	public function get_dropdown_html($class = '', $id = '0') {
		$html = '<label class="screen-reader-text" for="' . esc_attr($id) . '"> . $label . </label>';
		$html .= '<select lang="fr" name="course1" id="' . esc_attr($id) . '" class="' . esc_attr($class) . '">';
		$html .= '<option value="none">' . __('Choose...', 'foodiepro') . '</option>';
		$terms = get_terms( $this->get_dropdown_args() );

		foreach ($terms as $term) {
			if ( $this->get_queried_term_id()===$term->parent ) {
				$value = esc_url( add_query_arg($term->taxonomy, $term->slug, home_url()) );
			}
			else
				$value = esc_url(home_url(add_query_arg($term->taxonomy, $term->slug)));
			$html .= '<option class="level-0" value="' . $value . '" ' . selected($term->slug, get_query_var($term->taxonomy), false) . '>' . $term->name . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	public function get_queried_term_id() {
		$queried_term_slug = get_query_var($this->get_dropdown_arg('taxonomy'));
		if (!$queried_term_slug) return false;
		$queried_term= get_term_by('slug', $queried_term_slug, $this->get_dropdown_arg('taxonomy'));
		if (!isset($queried_term->term_id)) return false;
		$queried_term_id= $queried_term->term_id;
		return $queried_term_id;
	}

	public function get_dropdown_js($id) {
		ob_start();

		// $search_term = '?';
		// if (is_search()) {
		// 	$squery = get_search_query();
		// 	if (!empty($squery))
		// 		$search_term .= 's=' . $squery . '&';
		// }

		?>
		<script type='text/javascript'>
			/* <![CDATA[ */
			(function() {
				var dropdown_<?= $id;?> = document.getElementById( "<?= esc_js( $id );?>" );
				function on_<?= $id; ?>_Change() {
					var choice = dropdown_<?= $id;?>.options[ dropdown_<?= $id;?>.selectedIndex ].value;
					if ( choice !="none" ) {location.href = choice};
				}
				dropdown_<?= $id;?>.onchange = on_<?= $id;?>_Change;
			})();
			/* ]]> */
		</script>

		<?php
		$js = ob_get_contents();
		ob_end_clean();

		return $js;
	}

	public function get_tax_list()
	{
		return self::$TAX_LIST;
	}

	public function get_tax($id = '0')
	{
		$tax_slugs = array_keys(self::$TAX_LIST);
		return $tax_slugs[$id];
	}

	public function get_dropdown_arg($key) {
		if (isset($this->dropdown_args[$key])) {
			$value = $this->dropdown_args[$key];
		}
		else {
			$value=false;
		}
		return $value;
	}

	public function get_dropdown_args($key = false)
	{
		return $this->dropdown_args;
	}

	public function get_tax_name($tax_slug)
	{
		if (!isset(self::$TAX_LIST[$tax_slug])) return false;
		$tax_name = self::$TAX_LIST[$tax_slug];
		return $tax_name;
	}


	/* =================================================================
	HELPERS
	====================================================================*/
	public function is_parent_term($term_slug, $taxonomy)
	{
		$term = get_term_by('slug', $term_slug, $taxonomy);
		$has_children = !empty(get_term_children($term->term_id, $taxonomy));
		return $has_children;
	}

	public function is_region($obj)
	{
		if (!isset($obj->slug)) return;
		if ($obj->slug == 'france') return true;
		$parent = get_term_by('id', $obj->parent, 'cuisine');
		return ($parent->slug == 'france');
	}


	/* =================================================================
			SETTERS
	====================================================================*/
	public function set_dropdown_args($backend_tax)
	{
		if ($backend_tax == 'auto') {
			global $wp;
			$child_of = '';
			if (is_author() || is_post_type_archive('recipe')) {
				$tax = 'course';
			} elseif (is_tax()) {
				$qvars = array_keys($wp->query_vars);
				$tax = 'difficult';
				foreach ($this->get_tax_list() as $tax_slug => $name) {
					if ((!in_array($tax_slug, array('course', $tax))) && (in_array($tax_slug, $qvars))) {
						$tax = 'course';
						if ($tax_slug == 'cuisine') {
							$term_slug = get_query_var('cuisine');
							$term = get_term_by('slug', $term_slug, 'cuisine');
							$is_parent = !empty(get_term_children($term->term_id, 'cuisine'));
							$tax = $is_parent ? 'cuisine' : $tax;
							$child_of = $is_parent ? $term->term_id : '';
						}
					}
				}
			} elseif (is_tag()) {
				$tax = 'difficult';
			} elseif (is_search()) {
				return;
			} else {
				$tax = 'course';
			}
		}
		$tax = apply_filters('cnh_taxonomies_dropdown', $tax);
		$this->dropdown_args['taxonomy'] = $tax;
		$this->dropdown_args['hierarchical'] = ($tax == 'ingredient')?true:false;
		$this->dropdown_args['child_of'] = $child_of;
		$this->dropdown_args['exclude'] = array();
		$this->dropdown_args['childless'] = false;
		$this->dropdown_args['orderby'] = CNH_Assets::get_orderby($tax);
		$this->dropdown_args['role_not_in'] = array('administrator', 'pending');
		$this->dropdown_args['show'] = 'user_login';
		$this->dropdown_args['option_none_value'] = 'none';
	}


	/* =================================================================
			WIDGET BACKEND
	====================================================================*/
	public function form($instance)
	{
		if (isset($instance['title']))
			$title = $instance['title'];
		else
			$title = __('New title', 'foodiepro');

		if (isset($instance['tax-list']))
			$selected_tax = $instance['tax-list'];
		else
			$selected_tax = $this->get_tax();
		// Widget admin form
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label>
				<?php _e('Dropdown taxonomy', 'foodiepro'); ?>
			</label>
			<br>
			<select class="full-width" id="<?php echo $this->get_field_id('tax-list'); ?>" name="<?php echo $this->get_field_name('tax-list'); ?>">
				<option value="<?= 'auto' ?>" <?php selected($selected_tax, 'auto'); ?>><?= __('Automatic', 'foodiepro'); ?></option>
				<?php foreach ($this->get_tax_list() as $tax => $name) : ?>
					<option value="<?= $tax;?>" <?php selected($selected_tax, $tax); ?>><?= $name ?></option>
				<?php endforeach; ?>
			</select>

		</p>
<?php
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['tax-list'] = (!empty($new_instance['tax-list'])) ? strip_tags($new_instance['tax-list']) : $this->get_tax();
		return $instance;
	}

} // Class wpb_widget ends here

// Register and load the widget
add_action('widgets_init', 'cnh_load_taxonomy_dropdown_widget');
function cnh_load_taxonomy_dropdown_widget()
{
	register_widget('taxonomy_dropdown_widget');
}
