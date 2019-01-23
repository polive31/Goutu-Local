jQuery(document).ready(function() {
	var $container = jQuery('#genesis-content');
	$container.imagesLoaded(function(){
	  $container.masonry({
			itemSelector : 'article.simple-grid',
			columnWidth: $container.width()/2
	  });
	});
});

