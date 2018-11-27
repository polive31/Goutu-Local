jQuery(document).ready(function() {
		
	console.log('In social sharing buttons script');

	jQuery('.share-icons#whatsapp a').on('click', function(){

		var body=encodeURIComponent( jQuery(this).data('body') );
		var post_url=encodeURIComponent( jQuery(this).data('url') );

		console.log('Click on Whatsapp share detected !');

		if( /Android|webOS|iPhone|iPad|iPod|Opera Mini/i.test(navigator.userAgent) ) {
			console.log('On mobile device');
			url = 'whatsapp://send" data-text="' + body + '" data-href="' + post_url + '"';
		}
		else {
			console.log('Not on mobile device');
			url = 'https://api.whatsapp.com/send?phone=&text=' + body;
			console.log('URL : ', url);
		}

		window.open(url,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=250,width=600');

    });


});