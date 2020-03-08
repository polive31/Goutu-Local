<?php
/*
Description: Administrator shortcodes for Goutu
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


class CBMP_Taxonomy_Meta {

	/* Batch update user_ratings_ratings custom field */
	public function batch_manage_taxonomy_meta_shortcode($atts)
	{
		$a = shortcode_atts(array(
			'tax' => 'ingredient',
			'key' => 'month',
			'limit' => '',// max number of terms to process
			'include' => '',// term ids to include
			'exclude' => '',// term ids to exclude
			'cmd' => 'migrate',
		), $atts);

		static $script_id; // allows several shortcodes on the same page
		++$script_id;

		$script_name = 'ManageTaxMeta';

		echo "<h3>BATCH MANAGE TAXONOMY META SHORTCODE#" . $script_id . "</h3>";

		$jsargs = CBMP_Helpers::create_ajax_arg_array($a, $script_name, $script_id);

		wp_enqueue_script('ajax_call_batch_manage');
		wp_localize_script('ajax_call_batch_manage', 'script' . $script_name . $script_id, $jsargs);

		echo CBMP_Helpers::batch_manage_form($script_id, $script_name, $a['cmd']);
	}


	public function ajax_batch_manage_tax_meta() {

		// Shortcode parameters display
		$tax = CBMP_Helpers::get_ajax_arg('tax');
		$key = CBMP_Helpers::get_ajax_arg('key');
		$limit = (int) CBMP_Helpers::get_ajax_arg('limit');
		$include = CBMP_Helpers::get_ajax_arg('include',__('Limit to terms'));
		$exclude = CBMP_Helpers::get_ajax_arg('exclude',__('Exclude terms'));
		$cmd = CBMP_Helpers::get_ajax_arg('cmd');

		if ( !(CBMP_Helpers::is_secure('ManageTaxMeta' . $cmd) ) ) exit;

		echo "<p>Batch Manage Taxonomy Meta script started...</p>";

		$args = array(
			// 'orderby'            => $orderby,
			// 'order'              => $ascdsc,
			// 'child_of'           => $childof,
			'exclude'            => $exclude,
			//'exclude_tree'       => '',
			'include'            => $include,
			// 'hierarchical'       => $hierarchical,
			'number'             => $limit,
			// 'depth'              => 0,
			//'current_category'   => 0,
			//'pad_counts'         => 0,
			'taxonomy'           => $tax,
			// 'walker'             => $walker,
			// 'meta_query' => array(
			// 	array(
			// 		'key'     => 'mykey',     // Adjust to your needs!
			// 		'value'   => 'myvalue',   // Adjust to your needs!
			// 		'compare' => '=',         // Default
			// 	)
		);
		$terms = get_terms( $args );

		foreach ($terms as $key=>$term) {
			echo sprintf('Check term %s <br>', $term->name);
			$id = $term->term_id;
			$option = get_option("taxonomy_$id");
			if ( isset($option['month']) && is_array( $option['month'] ) ) {
				echo sprintf('<span style="color:red">Legacy month found for term %s </span><br>', $term->name);
				echo '<pre>' . print_r( $option, true ) . '</pre>';
				echo 'Checking term meta for new month definition...<br>';

				$meta =get_term_meta( $id, 'month', true);
				if (is_array($meta)) {
					echo 'New month definition found, option will be deleted';
					echo '<pre>' . print_r( $meta, true ) . '</pre>';
				}
				else {
					echo 'No new month definition found, option will be migrated to new definition<br>';
					$months = $this->convert($option['month']);
					echo 'Converted option is : <br>';
					echo '<pre>' . print_r($months, true ) . '</pre>';
					update_term_meta( $id, 'month', $months);
				}
			}
		}


	}


	public function convert( $legacy_months ) {

		$new_months=array();

		for ($i=1; $i<=12; $i++) {
			if (isset($legacy_months[$i])) {
				$new_months[] = $i;
			}
		}

		return $new_months;

	}

}
