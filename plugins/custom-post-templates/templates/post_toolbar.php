<!-- Function buttons  -->
<div class="post-top">
	
	<div class="toolbar-buttons">		

		<!-- Like Button -->
		<div class="toolbar-button tooltip tooltip-above tooltip-left" id="like">
		<?php
			$post_like = new Custom_Social_Like_Post( 'post' );
			$post_like->display('above','left');
		?>
		</div>		

		<!-- Post Print Button -->
		<div class="toolbar-button tooltip" id="print">
			<a class="post-print-button" href="javascript:window.print()" target="_blank">
			<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
			</a>
			<?php
			Tooltip::display( __('Print this post','foodiepro') , 'above', 'right');  
			?> 
		</div>	

		<!-- Post Share Button -->
		<div class="toolbar-button tooltip tooltip-above" id="share">
			<a class="post-share-button" id="post-share" cursor-style="pointer">
			<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
			</a> 
			<?php //echo Custom_WPURP_Templates::output_tooltip(__('Share this recipe','foodiepro'),'above');
				$share = do_shortcode('[mashshare]');
				Tooltip::display( $share, 'above', 'left', 'hidden large');  
			?>  
		</div>				
											
	</div>
	
</div>