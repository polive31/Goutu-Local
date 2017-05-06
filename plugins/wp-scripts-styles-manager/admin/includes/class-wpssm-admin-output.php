<?php

class WPSSM_Admin_Output {
 	
 	private $size_small;
 	private $size_large;
 	private $size_max;
 	private $assets;
 	private $user_notification;
 	
  public function __construct( $assets, $sizes ) {
  	$this->assets = $assets;
		WPSSM_Debug::log('In WPSSM_Output __construct(), $this->assets ', $this->assets);
  	$this->size_small=$sizes['small'];
  	$this->size_large=$sizes['large'];
  	$this->size_max=$sizes['max'];
  }


/* GENERAL SETTINGS PAGE
--------------------------------------------------------------------*/
	public function pages_list() {
		WPSSM_Debug::log('In WPSSM_Output pages_list(), $this->assets ', $this->assets);
		foreach ($this->assets as $page) {
			echo '<p>' . $page[0] . ' on ' . $page[1] . '</p>';
		}
	}
	

	public function toggle_switch( $input_name, $value ) {
		WPSSM_Debug::log( 'in output toggle switch for ' . $input_name , $value);
		$checked = ( $value == 'on')?'checked="checked"':'';
		?>
		<label class="switch">
  	<input type="checkbox" name="<?php echo $input_name;?>_checkbox" <?php echo $checked;?> value="on">
  	<div class="slider round"></div>
		</label>
		<?php
	}
	

/* SCRIPTS AND STYLES PAGES
--------------------------------------------------------------------*/  
	public function items_list( $sorted_list, $type, $location ) {
		?><table class="enqueued-assets"><?php
		$this->_item_headline();
    foreach ($sorted_list as $handle => $priority ) {
			WPSSM_Debug::log('Asset in WPSSM_Output->items_list() : ', $this->assets[$handle]);			
			$this->_item_content( $this->assets[$handle], $type, $location, $handle );  
    }
    ?></table><?php
	}
	

	private function _item_headline() {
		?>
    	<tr>
    		<th> handle </th>
    		<th> priority </th>
    		<!--<th> Dependencies </th>-->
    		<th> Dependents </th> 
    		<th> File size </th>
    		<th> Location </th>
    		<th> Minify </th>
    	</tr>	
		<?php
	}

	private function _item_content( $asset, $type, $location, $handle ) {
		
    	$filename = $asset['filename'];
    	$dependencies = $asset['dependencies'];
    	$dependents = $asset['dependents'];
    	$priority = $asset['priority'];
    	//$location = $this->get_field_value( $asset, 'location');
	    $minify = $this->assets->get_field_value( $asset, 'minify');
	    $size = $this->assets->get_field_value( $asset, 'size');
	    $name = $this->assets->get_field_name();
	    	
	    $asset_is_minified = ( $asset[ 'minify' ] == 'yes')?true:false; 
	    $already_minified_msg = __('This file is already minimized within its plugin', 'jco');
	    
	    
		?>
		   	<tr class="enqueued-asset <?php echo $type;?>" id="<?php echo $handle;?>">
	    	<td class="handle" title="<?php echo $filename;?>"><?php echo $handle;?><?php $this->output_user_notification( $asset );?></td>
	    	
	    	<td><?php echo $priority;?></td>
	    	
	    	<!-- <td class="dependencies"><?php foreach ($dependencies as $dep) {echo $dep . '<br>';}?></td> -->
	    	<td class="dependents"><?php foreach ($dependents as $dep) {echo $dep . '<br>';}?></td>
	    	
	    	<td class="size" title="<?php echo $filename;?>"><?php echo size_format( $size );?></td>
	    	
	    	<td class="location <?php echo $this->_is_modified( $asset, 'location');?>">
	    		<select data-dependencies='<?php echo json_encode($dependencies);?>' data-dependents='<?php echo json_encode($dependents);?>' id="<?php echo $handle;?>" class="asset-setting location <?php echo $type;?>" name="<?php echo $this->get_field_name( $type, $handle, 'location');?>">
  					<option value="header" <?php echo ($location=='header')?'selected':'';?> >header</option>
  					<option value="footer" <?php echo ($location=='footer')?'selected':'';?> >footer</option>
  					<option value="async" <?php echo ($location=='async')?'selected':'';?> >asynchronous</option>
  					<option value="disabled" <?php echo ($location=='disabled')?'selected':'';?>>disabled</option>
					</select>
				</td>
				
				<td class="minify <?php echo $this->_is_modified( $asset, 'minify');?>">
	    		<select id="<?php echo $handle;?>" class="asset-setting minify <?php echo $type;?>" <?php echo ($asset_is_minified)?'disabled':'';?> <?php echo ($asset_is_minified)?'title="' . $already_minified_msg . '"' :'';?> name="<?php echo $this->get_field_name( $type, $handle, 'minify');?>">
  					<option value="no" <?php echo ($minify=='no')?'selected':'';?>  >no</option>
  					<option value="yes" <?php echo ($minify=='yes')?'selected':'';?> >yes</option>
					</select>
				</td>
    	
    	</tr>
		<?php
	}


	private function _is_modified( $asset, $field ) {
		if ( isset( $asset['mods'][ $field ] ) ) {
			return 'modified';
		}
	}


/* USER NOTIFICATIONS
--------------------------------------------------------------*/		
	
	
	private function output_user_notification( $asset ) {
		
		$size= $asset['size'];
		//WPSSM_Debug::log(array('size : '=>$size));
		$is_minified = $this->get_field_value( $asset, 'minify') == 'yes';
		//WPSSM_Debug::log(array('is_minified: '=>$is_minified));
		$in_footer = ( $this->get_field_value( $asset, 'location') == 'footer');
		
		$this->reset_user_notification();
		if (!$is_minified) {
			if ( $size > $this->size_large ) {
				$level = 'issue';
				$msg = __('This file is large and not minified : minification highly recommended', 'jco');	
				$this->enqueue_user_notification( $msg, $level);
			}
			elseif ( $size != 0 ) {
				$level = 'warning';
				$msg = __('This file is not minified : minification recommended', 'jco');	
				$this->enqueue_user_notification( $msg, $level);
			}
		}

		if ( ( $size > $this->size_large ) && ( !$in_footer ) ) {
			$level = 'issue';
			$msg = __('Large files loaded in the header will slow down page display : make asynchronous, loading in footer or at least conditional enqueue recommended', 'jco');			
			$this->enqueue_user_notification( $msg, $level);
		}	
		
		if ( ( $size < $this->size_small ) && (!isset( $asset['in_group']) ) ) {
			$level = 'warning';
			$msg = __('This file is small and requires a specific http request : it is recommended to inline it, or to group it with other files', 'jco');			
			$this->enqueue_user_notification( $msg, $level);
		}	
		echo $this->user_notification;
		
	}


	private function reset_user_notification() {
		$this->user_notification='';
	}
	
	private function enqueue_user_notification( $msg, $level) {
		if ($msg != '') {
			$this->user_notification .= '<i class="user-notification" id="' . $level . '" title="' . $msg . '"></i>';
		}		
	}
	
	
}