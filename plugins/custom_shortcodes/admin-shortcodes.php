<?php
/*
Plugin Name: Admin Shortcodes
Plugin URI: http://goutu.org/
Description: Administrator shortcodes for Goutu
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

	
/* =================================================================*/
/* =               TEST SHORTCODE
/* =================================================================*/

add_shortcode('test-shortcode', 'test_shortcode');

function test_shortcode() {
	echo 'TEST SHORTCODE';
}
	
	
/* =================================================================*/
/* =               BATCH UPDATE USER RATINGS
/* =================================================================*/

add_shortcode('batch-update-meta', 'batch_update_meta');

/* Batch update user_ratings_ratings custom field */
function batch_update_meta($atts) {
	
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'key' => 'user_rating_stats',
		'array' => 'true',
		'value' => 'stars 0 votes 0 rating 0 half-star false',
		'erase' => 'false',
	), $atts );
	
	echo "BATCH UPDATE META SHORTCODE" . "\n";
	
	$key = $a['key'];
  $post_type = $a['post-type'];
	$array = $a['array'];
	$value = $a['value'];
	$erase = $a['erase'];
	$msg=($erase=='true')?'WARNING:Existing values will be erased !':'';
	ob_start();?>
	
	<div id = "center">
	<form action="." method="post">
	<p>Batch update meta in <?php echo $post_type;?></p>
	<p><?php echo ($erase=='true')?'WARNING:Existing values will be erased !':'';?></p>
	<input type="submit" name="Submit" value="Launch">
	</form>
	</div>
	
	<?php
	$form=ob_get_contents();
	ob_end_clean();
	
	echo $form;
	
	if (isset($_POST['Submit'])) {
			
			echo "BATCH UPDATE META SHORTCODE" . "\n";
			
			if ( $array == 'true' )
				$value = extractKeyValuePairs( $value );
			else
				$value = $a['value'];
			
			//PC:debug( array('$value after explode : '=>$value) );
				
			echo '<div class="clearfix">';
			echo "Batch Update Meta script started..." . "\n";

		  
		  $post_type_object = get_post_type_object($post_type);
		  $label = $post_type_object->label;
		  echo  "Updating all " . $label . "\n";
		  $posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

		  foreach ($posts as $post) {
		    $meta_value = get_post_meta($post->ID, $key, True);
		    if (empty($meta_value) || ($erase=='true') ){
		    //$meta_value2 = media_process($meta_value1, $post->ID); //Returns a string after it finishes process.
		    update_post_meta($post->ID, $key, $value);
		    echo $post->post_title." UPDATED \n"; //Prints updated after ran.
		    }
		  }
		  echo '/div';
	}
	
}

function confirmChoice() {
	echo __("Are you sure you want to do this?  Type 'yes' to continue: ");
	$handle = fopen ("php://stdin","r");
	$line = fgets($handle);
	if (trim($line) != __('yes')) {
	    echo "ABORTING!\n";
	    return false;
	}
	fclose($handle);
	echo "\n"; 
	echo __('Thank you, continuing...') . "\n";
	return true;
}

function extractKeyValuePairs($string, $delimiter = ' ') {
    $params = explode($delimiter, $string);

    $pairs = [];
    for ($i = 0; $i < count($params); $i++) {
        $pairs[$params[$i]] = $params[++$i];
    }

    return $pairs;
}

?>