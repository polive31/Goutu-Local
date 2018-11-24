jQuery(document).ready(function() {

    // console.log("In lightbox.js script");

    var selector = 'a#lightbox';
    var instance = jQuery(selector);

    instance.imageLightbox({
        selector:       'id="imagelightbox"',   // string;
        // allowedTypes:   'png|jpg|jpeg|gif',     // string;
        allowedTypes:   'png|jpg|jpeg',     // string;
        animationSpeed: 250,                    // integer;
        preloadNext:    true,                   // bool;            silently preload the next image
        enableKeyboard: true,                   // bool;            enable keyboard shortcuts (arrows Left/Right and Esc)
        quitOnEnd:      false,                  // bool;            quit after viewing the last image
        quitOnImgClick: false,                  // bool;            quit when the viewed image is clicked
        quitOnDocClick: true,                   // bool;            quit when anything but the viewed image is clicked
        onStart:        function() { overlayOn(); closeButtonOn( instance ); arrowsOn( selector ); },
        onEnd:          function() { overlayOff(); captionOff(); closeButtonOff(); arrowsOff(); activityIndicatorOff(); },
        // onLoadStart:    function() { captionOff(); activityIndicatorOn(); },
        onLoadStart:    function() { activityIndicatorOn(); },
        // onLoadEnd:      function() { captionOn(); activityIndicatorOff(); jQuery( '.imagelightbox-arrow' ).css( 'display', 'block' ); }
        onLoadEnd:      function() { activityIndicatorOff(); jQuery( '.imagelightbox-arrow' ).css( 'display', 'block' ); }
    });

    // ACTIVITY INDICATOR

    var activityIndicatorOn = function() {
        jQuery( '<div id="imagelightbox-loading"><div></div></div>' ).appendTo( 'body' );
    };

    var activityIndicatorOff = function() {
        jQuery( '#imagelightbox-loading' ).remove();
    };

    // OVERLAY

    var overlayOn = function() {
        jQuery( '<div id="imagelightbox-overlay"></div>' ).appendTo( 'body' );
    };

    var overlayOff = function() {
        jQuery( '#imagelightbox-overlay' ).remove();
    };


    // CLOSE BUTTON

    var closeButtonOn = function( instance ) {
        jQuery( '<button type="button" id="imagelightbox-close" title="Close"></button>' ).appendTo( 'body' ).on( 'click touchend', function(){ jQuery( this ).remove(); instance.quitImageLightbox(); return false; });
    };

    var closeButtonOff = function() {
        jQuery( '#imagelightbox-close' ).remove();
    };


    // CAPTION

    var captionOn = function() {
        var description = jQuery( 'a[href="' + jQuery( '#imagelightbox' ).attr( 'src' ) + '"] img' ).attr( 'alt' );
        if( description.length > 0 )
            jQuery( '<div id="imagelightbox-caption">' + description + '</div>' ).appendTo( 'body' );
    };

    var captionOff = function() {
        jQuery( '#imagelightbox-caption' ).remove();
    };


    // ARROWS

    var arrowsOn = function( selector ) {
        console.log('In arrows On ');

        // var $scope = jQuery.uniqueSort( jQuery( selector ) );
        var $scope = jQuery( selector );

        console.log('Scope : ', $scope );
        console.log('Total nb objects in scope : ', $scope.length);

        if ($scope.length == 1) return;

        var $arrows = jQuery( '<button type="button" class="imagelightbox-arrow imagelightbox-arrow-left"></button><button type="button" class="imagelightbox-arrow imagelightbox-arrow-right"></button>' );

        $arrows.appendTo( 'body' );

        $arrows.on( 'click touchend', function( e ) {
            e.preventDefault();

            var $this   = jQuery( this );
                // $current = jQuery( selector + '[href="' + jQuery( '#imagelightbox' ).attr( 'src' ) + '"]' ),
            var index   = jQuery( '#imagelightbox' ).data('id');

            console.log('Image courante : ', jQuery( '#imagelightbox' ).attr( 'src' ));
            console.log('Index image courante : ', index);

            if( $this.hasClass( 'imagelightbox-arrow-left' ) ) {
                console.log( 'Click on left arrow');
                index = index - 1;
                if( !$scope.eq( index ).length )
                    index = jQuery( selector ).length; //Go back to the last image in the scope
            }
            else {
                console.log( 'Click on right arrow');
                index = index + 1;
                if( !$scope.eq( index ).length )
                    index = 0; //Go back to the first image in the scope
            }

            instance.switchImageLightbox( index );
            return false;
        });
    };

    var arrowsOff = function() {
        jQuery( '.imagelightbox-arrow' ).remove();
    };



});