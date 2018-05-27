jQuery(document).ready(function() {
	var $container = jQuery('#genesis-content');
	$container.imagesLoaded(function(){
	  $itemSelector = 'article.simple-grid';
	  $container.masonry({
	    itemSelector : $itemSelector,
	    isAnimated: false,
	  });
	});
});

