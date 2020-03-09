<?php

// Block direct requests
if (!defined('ABSPATH'))
	die('-1');



// Creating the widget
class Taxonomy_Navigation_Widget extends WP_Widget
{
	private static $TAX_LIST;

	function __construct()
	{
		parent::__construct(
			// Base ID of your widget
			'taxonomy_navigation_widget',
			// Widget name will appear in UI
			__('Taxonomy Navigation widget', 'foodiepro'),
			// Widget description
			array('description' => __('Displays a dropdown list allowing to navigate between taxonomy terms', 'foodiepro'))
		);
		self::$TAX_LIST = array(
			'course' 	=> __('Course', 'foodiepro'),
			'cuisine' 	=> __('Origin', 'foodiepro'),
			// 'country' 	=> __('Country', 'foodiepro'),
			// 'region' 	=> __('Region', 'foodiepro'),
			'diet' 		=> __('Diet', 'foodiepro'),
			'season' 	=> __('Season', 'foodiepro'),
			'occasion' 	=> __('Occasion', 'foodiepro'),
			'category'	=> __('Categorie', 'foodiepro'),
		);
	}


	// Creating widget front-end
	public function widget($args, $instance)
	{
		if (!is_archive() && !is_search()) return;

		global $wp;

		$title = apply_filters('widget_title', $instance['title']);
		$selected_tax = apply_filters('cnh_taxonomies_dropdown', $instance['tax-list']);
		if ($title=='') {
			$tax_name = $this->get_tax_name($selected_tax);
			// $title=$tax_name;
			$title=__('Filter by ','foodiepro') . $tax_name;
		}

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		/* Do not display in the case of a search query or WPURP dropdown search widget result */
		$WPURP_search = strpos($_SERVER["REQUEST_URI"], 'wpurp-search');
		if ($WPURP_search) {
			echo '&nbsp;';
			return;
		}

		// Widget title
		echo $args['before_title'] . $title . $args['after_title'];

		// Dropdown display
		echo do_shortcode('[ct-terms taxonomy="' . $selected_tax . '" dropdown="true"]');

		// Output end
		echo $args['after_widget'];
	}

	// Widget Backend
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

	public function get_tax_list()
	{
		return self::$TAX_LIST;
	}

	public function get_tax($id = '0')
	{
		$tax_slugs = array_keys(self::$TAX_LIST);
		return $tax_slugs[$id];
	}

	public function get_tax_name($tax_slug)
	{
		$tax_name = self::$TAX_LIST[$tax_slug];
		return $tax_name;
	}

	public function is_region($obj)
	{
		if (!isset($obj->slug)) return;
		if ($obj->slug == 'france') return true;
		$parent = get_term_by('id', $obj->parent, 'cuisine');
		return ($parent->slug == 'france');
	}
} // Class wpb_widget ends here

// Register and load the widget
add_action('widgets_init', 'cnh_load_taxonomy_dropdown_widget');
function cnh_load_taxonomy_dropdown_widget()
{
	register_widget('taxonomy_navigation_widget');
}
