jQuery(function(){

var appendthis =  ("<div class='modal-overlay js-modal-close'></div>");

  jQuery('a[data-modal-id]').click(function(e) {
    e.preventDefault();
    jQuery("body").append(appendthis);
    jQuery(".modal-overlay").fadeTo(500, 0.7);
    //jQuery(".js-modalbox").fadeIn(500);
    var modalBox = jQuery(this).attr('data-modal-id');
    jQuery('#'+modalBox).fadeIn(jQuery(this).data());
  });  
  
  
jQuery(".js-modal-close, .modal-overlay").click(function() {
  jQuery(".modal-box, .modal-overlay").fadeOut(500, function() {
    jQuery(".modal-overlay").remove();
  });
});
 
jQuery(window).resize(function() {
  jQuery(".modal-box").css({
    top: (jQuery(window).height() - jQuery(".modal-box").outerHeight()) / 2,
    left: (jQuery(window).width() - jQuery(".modal-box").outerWidth()) / 2
  });
});
 
jQuery(window).resize();
 
});