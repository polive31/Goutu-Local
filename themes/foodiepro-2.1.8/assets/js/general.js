/**
 * Dear General Scripts
 *
 * @copyright Copyright (c) 2016, Shay Bocks
 * @license   MIT
 */
(function( $, undefined ) {
	'use strict';

	var $document = $( document ),
		$navs     = $( 'nav' );

	/**
	 * Debounce a window resize event.
	 */
	function debouncedResize( c, t ) {
		onresize = function() {
			clearTimeout( t );
			t = setTimeout( c, 100 );
		};
		return c;
	}

	/**
	 * Check whether or not a given element is visible.
	 *
	 * @param  {object} $object a jQuery object to check
	 * @return {bool} true if the current element is hidden
	 */
	function isHidden( $object ) {
		var element = $object[0];
		return ( null === element.offsetParent );
	}

);
}( jQuery ) );
