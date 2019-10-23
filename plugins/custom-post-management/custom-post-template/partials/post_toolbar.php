<div class="share-buttons">
	<?= do_shortcode('[social-sharing-buttons target="post" class="small bubble"]'); ?>
</div>


<!-- Function buttons  -->
<div class="post-top">


	<div class="toolbar-buttons">

		<!-- Like Button -->
		<div class="toolbar-button tooltip-onhover alignleft" id="like">
		<?php
			$post_like = new Custom_Social_Like_Post( 'post' );
			$post_like->display('above','left');
		?>
		</div>

		<!-- Post Print Button -->
		<div class="toolbar-button tooltip-onhover alignright" id="print">
			<a class="post-print-button" href="javascript:window.print()" target="_blank">
			<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
			</a>
			<?php
			$args=array(
				'content' =>  __('Print this post','foodiepro'),
				'valign' => 'above',
				'halign' => 'right',
			);
			Tooltip::display($args);
			?>
		</div>

		<!-- Post Share Button -->
		<!-- <div class="toolbar-button tooltip-onhover alignright" id="share">
			<a class="post-share-button" id="post-share" cursor-style="pointer">
			<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
			</a>
		</div>				 -->

	</div>

</div>
