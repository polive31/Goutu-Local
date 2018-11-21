<?php
/*
YARPP Template: Goutu
Description: Customizable column count
Author: Pascal O.
*/ ?>

<?php
define ( 'COLUMNS', '5');
$grid = 'grid-' . COLUMNS . 'col';
$first = (COLUMNS & 1);

if (have_posts()):?>

<h2 class="widgettitle widget-title">
<?php
	if ( is_singular( 'recipe' ) ) 
		echo __('Related Recipes','foodiepro');
	else
		echo __('Related Posts','foodiepro');
?>
</h2>

<div class="rpwe-block related-posts <?php echo $grid; ?>">
	<ul class="rpwe-ul">
	<?php while (have_posts()) : the_post(); ?>
		<?php if (has_post_thumbnail()):?>
				<li class="rpwe-li rpwe-clearfix <?php echo ($first)?'rpwe-first':'';?>">
					<a class="rpwe-img" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( $first?'medium-thumbnail':'square-thumbnail', array( 'title'=> the_title_attribute('echo=0'), 'class' => 'rpwe-aligncenter rpwe-thumb', 'alt' => the_title_attribute('echo=0') ) ); ?>
					</a>
					<h3 class="rpwe-title">
						<a href="<?php the_permalink() ?>" rel="bookmark">
							<?php the_title_attribute(); ?>
						</a>	
					</h3>
					<?php $first=false;?>
				</li>
		<?php endif; ?>
	<?php endwhile; ?>
	</ul>
</div>

<?php else: 
//echo '<p>' . __('No related posts.', 'foodiepro') . '</p>';
endif; ?>
