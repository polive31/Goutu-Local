<?php
/*
Description: Form template for Batch Update shortcode
Author: Pascal Olive
Author URI: http://goutu.org
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


function batch_manage_form($script_id, $script_name, $cmd) {
	
	$form='';
	
	$style='';
	if ($cmd=='delete') $style='background-color:red';
	if ($cmd=='update') $style='background-color:brown';
	
	ob_start();?>
	
	<div id = "center">
	<input style="<?php echo $style;?>" type="submit" id="button" data-name="<?php echo $script_name;?>" data-instance="<?php echo $script_id;?>" name="Submit_<?php echo $script_id;?>" value="<?php echo $cmd;?>">
	</div>
	<div id="resp<?php echo $script_name;?><?php echo $script_id;?>"></div>
	<br>

	<?php
	$form=ob_get_contents();
	ob_end_clean();

	return $form;	

}

