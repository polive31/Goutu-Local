<?php
/*
Description: Common functions for batch manage posts
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if (!defined('ABSPATH'))
	die('-1');

class CBMP_Helpers
{

	/**
	 * Extract arguments passed via Ajax call and echo value
	 *
	 * @param  mixed $name
	 * @param  mixed $label
	 * @param  mixed $default
	 * @return void
	 */
	public static function get_ajax_arg($name, $label = '', $default='-1')
	{
		$value = '';
		if (isset($_POST['args'][$name])) {
			$value = $_POST['args'][$name];
			$label = (empty($label)) ? ucfirst($name) : $label;
		}
		else
			$value = $default;
		echo sprintf("CBMP_Helpers:: <b> %s </b> = %s", $label, $value);
		echo "<br>";

		return $value;
	}


	/**
	 * Transforms a list of consecutive values into $key=>$value pairs
	 *
	 * @param  mixed $string
	 * @param  mixed $delimiter
	 * @return void
	 */
	public static function extractKeyValuePairs($string, $delimiter = ' ')
	{
		$params = explode($delimiter, $string);
		$pairs = [];
		for ($i = 0; $i < count($params); $i++) {
			$pairs[$params[$i]] = $params[++$i];
		}

		return $pairs;
	}


	/**
	 * Gets the HTML of the formatted list of shortcode parameters
	 *
	 * @param  mixed $a
	 * @return void
	 */
	public static function show_params($a)
	{
		$html='';
		foreach ($a as $key => $value) {
			if (!empty($value)) {
				$html .= sprintf("<b> %s </b> = %s", ucfirst($key), $value);
				$html .= "<br>";
			}
		}
		return $html;
	}

	/**
	 * Transforms a list of consecutive values into $key=>$value pairs
	 *
	 * @param  mixed $a
	 * @param  mixed $script_name
	 * @return void
	 */
	public static function get_ajax_arg_array($a, $script_name)
	{
		$a = array_map('utf8_encode', $a);
		$ajson = json_encode($a);
		$nonce = wp_create_nonce($script_name . $a['cmd']);

		// Localize and enqueue the script with new data
		$jsargs = array(
			'nonce' => $nonce,
			'url' => admin_url('admin-ajax.php'),
			'data' => $ajson,
		);

		return $jsargs;
	}


	/**
	 * Nonce check on AJAX referred page
	 *
	 * @param  mixed $nonceurl
	 * @return void
	 */
	public static function is_secure($nonceurl)
	{
		$result = false;
		$nonce_check = check_ajax_referer($nonceurl, false, false);
		if ( $nonce_check && is_user_logged_in() && current_user_can('edit_others_posts') )
			$result = true;
		else
			echo 'Security check failed, script stopped';
		return $result;
	}

	/**
	 * Outputs submit button in relation with the cmd shortcode argument
	 *
	 * @param  mixed $script_id
	 * @param  mixed $script_name
	 * @param  mixed $cmd
	 * @return void
	 */
	public static function get_submit_button($script_id, $script_name, $cmd)
	{
		$form = '';
		ob_start();
		?>
			<div id="center">
				<input type="submit" id="button" data-name="<?= $script_name; ?>" data-instance="<?= $script_id; ?>" name="Submit_<?= $script_id; ?>" value="<?= $cmd; ?>">
			</div>
			<div id="resp<?= $script_name; ?><?= $script_id; ?>"></div>
			<br>
		<?php
		$form = ob_get_contents();
		ob_end_clean();

		return $form;
	}
}
