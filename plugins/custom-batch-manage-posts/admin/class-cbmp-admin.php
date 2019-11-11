<?php


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');



class CBMP_Admin
{

	public function add_cbmp_options()
	{
		add_options_page('Custom Batch Manage Posts', 'Custom Batch Manage Posts', 'manage_options', 'functions', array($this, 'cbmp_options_form'));
	}

	function cbmp_options_form()
	{
	?>
		<div class="wrap">
			<h2>Global Custom Options</h2>
			<form method="post" action="options.php">
				<?php wp_nonce_field('update-options') ?>
				<p><strong>Twitter ID:</strong><br />
					<input type="text" name="twitterid" size="45" value="<?php echo get_option('twitterid'); ?>" />
				</p>
				<p><input type="submit" name="Submit" value="Store Options" /></p>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="twitterid" />
			</form>
		</div>
	<?php
	}



}
