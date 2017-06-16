(function( $ ) {

	$(document).ready(function() {
			
		var bb_search = $('.sqpn-bb-search-page'),
			wp_search = $('.sqpn-wp-search-page'),
			bb_pages_wrap  = $('#wp-admin-bar-sqpn_bb_pages'),
			wp_pages_wrap  = $('#wp-admin-bar-sqpn_wp_pages');

		bb_pages_wrap.mouseover(function(){
			bb_search.focus();
		});
		
		wp_pages_wrap.mouseover(function(){
			wp_search.focus();
		});
		

		/**
		 * Quick Search
		 */
		bb_search.keyup(function(){
			
			var rex = new RegExp( $(this).val(), 'i');
	        bb_pages_wrap.find('.sqpn-bb-pages-sub-menu').hide();
	        bb_pages_wrap.find('.sqpn-bb-pages-sub-menu > .ab-item').filter(function () {
	        	var data_type = $(this).text();
	        	return rex.test( data_type );
            }).parent('.sqpn-bb-pages-sub-menu').show();

            if( $.trim( $(this).val() ) == '' ) {
				bb_pages_wrap.find('.sqpn-bb-pages-sub-menu').show();           	
            }
	    });

	    wp_search.keyup(function(){
			
			var rex = new RegExp( $(this).val(), 'i');
	        wp_pages_wrap.find('.sqpn-wp-pages-sub-menu').hide();
	        wp_pages_wrap.find('.sqpn-wp-pages-sub-menu > .ab-item').filter(function () {
	        	var data_type = $(this).text();
	        	return rex.test( data_type );
            }).parent('.sqpn-wp-pages-sub-menu').show();

            if( $.trim( $(this).val() ) == '' ) {
				wp_pages_wrap.find('.sqpn-wp-pages-sub-menu').show();           	
            }
	    });
	});

})( jQuery );
