jQuery(document).ready(function() {
		
	console.log('In social sharing buttons script');

	jQuery('.share-icons#whatsapp a').on('click', function(){

		var url='';
		console.log('Click on Whatsapp share detected !');

		if( /Android|webOS|iPhone|iPad|iPod|Opera Mini/i.test(navigator.userAgent) ) {
			console.log('On mobile device');
			url = jQuery(this).data('mobile');
		}
		else {
			console.log('Not on mobile device');
			url = jQuery(this).data('web');
		}

		window.open(url,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=250,width=600');

    });


});