<!-- Function buttons  -->
<div class="post-top">
	
	<div class="toolbar-buttons">		

		<!-- Like Button -->
		<div class="toolbar-button tooltip tooltip-above tooltip-left" id="like">
		<?php
			$post_like = new Custom_Social_Like_Post( 'post' );
			$post_like->display();
		?>
		</div>		

		<!-- Post Print Button -->
		<div class="toolbar-button tooltip tooltip-above tooltip-right" id="print">
			<a class="post-print-button" href="javascript:window.print()" target="_blank">
			<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
			</a>
			<?php
			// echo do_shortcode('[tooltip text="' . __('Print this post','foodiepro') . '" pos="top"]');  
			Tooltip::display( __('Print this post','foodiepro') , 'top');  
			?> 
		</div>	

		<!-- Post Share Button -->
		<div class="toolbar-button tooltip tooltip-above" id="share">
			<a class="post-share-button" id="post-share" cursor-style="pointer">
			<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
			</a> 
			<?php //echo Custom_WPURP_Templates::output_tooltip(__('Share this recipe','foodiepro'),'top');
				$share = do_shortcode('[mashshare]');
				// echo do_shortcode('[tooltip text="' . $share . '" pos="top"]'); 
				Tooltip::display( $share, 'top');  
			?>  
		</div>				
											
	</div>
	
</div>