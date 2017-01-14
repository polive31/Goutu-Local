<?php
/*
YARPP Template: Thumbnails
Description: Requires a theme which supports post thumbnails
Author: mitcho (Michael Yoshitaka Erlewine)
*/ ?>

<?php
define ( 'COLUMNS', '4');

if ( COLUMNS=='1' )
	$grid = 'grid-1col';
elseif ( COLUMNS=='2' )
	$grid = 'grid-2col';
elseif ( COLUMNS=='3' )
	$grid = 'grid-3col';
elseif ( COLUMNS=='4' )
	$grid = 'grid-4col';
elseif ( COLUMNS=='5' )
	$grid = 'grid-5col';
elseif ( COLUMNS=='6' )
	$grid = 'grid-6col';?>

<?php if (have_posts()):?>

<h3>
<?php
	if ( is_singular( 'recipe' ) ) 
		echo __('Related Recipes','foodiepro');
	else
		echo __('Related Posts','foodiepro');
?>
</h3>

<div class="rpwe-block <?php echo $grid; ?>">
	<ul class="rpwe-ul">
	<?php while (have_posts()) : the_post(); ?>
		<?php if (has_post_thumbnail()):?>
				<li class="rpwe-li rpwe-clearfix">
					<a class="rpwe-img" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'rpwe-aligncenter rpwe-thumb' ) ); ?>
					</a>
					<h3 class="rpwe-title">
						<a href="<?php the_permalink() ?>" rel="bookmark">
							<?php the_title_attribute(); ?>
						</a>	
					</h3>
				</li>
		<?php endif; ?>
	<?php endwhile; ?>
	</ul>
</div>

<?php else: 
//echo '<p>' . __('No related posts.', 'foodiepro') . '</p>';
endif; ?>
