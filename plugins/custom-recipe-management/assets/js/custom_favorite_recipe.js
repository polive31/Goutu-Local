jQuery(document).ready(function(){
  console.log('%c In custom favorite recipe ', "background:#489; color:white");

  jQuery(document).on('click', '.toolbar-button#favorite', function (e) {
    console.log('%c Click on favorites button', "background:#489; color:white");
    if (typeof ga == 'function') {
      console.log('%c Google Analytics installed, trigger event', "background:#489; color:white");
      ga('send','event','join-us','click','recipe-favorite', 0);
    }
  });

  jQuery('#favorites_list_form').on('click', 'li', function (e) {
    console.log('%c Click in favorites list', "background:#489; color:white");
    e.preventDefault();
    e.stopPropagation();

    var choice = jQuery(this);
    console.log( 'Choice = ', choice.attr("class") );

    addToFavoritesUpdate( choice );
  });


});


function addToFavoritesUpdate( list_item ) {
  // console.log('In addToFavorites Update');
  var tooltipButton = jQuery(list_item).parents('.tooltip-content').siblings('.tooltip-onclick');
  var listChoice = list_item.attr("id");
  // console.log('Chosen option is ', listChoice );

  // closeTooltip( tooltipForm );
  Tooltip.closeAll();

  var data = {
    action: 'custom_favorite_recipe',
    security: custom_favorite_recipe.nonce,
    recipe_id: tooltipButton.data('recipe-id'),
    choice: listChoice,
  };

  // console.log('data.action : '+data.action);
  // console.log('data.security : ' + data.security);
  // console.log('data.recipe_id : ' + data.recipe_id);
  // console.log('ajax URL : ' + custom_favorite_recipe.ajaxurl);

  jQuery.post(
    custom_favorite_recipe.ajaxurl,
    data,
    function(response) {
      // console.log('Add to Favorites AJAX call completed');
      // console.log('Response list : ' + response.list);
      // console.log('Response tooltip : ' + response.tooltip);
      // console.log( 'Tooltip button', tooltipButton.html());

      // Update button id & tooltip text
      tooltipButton.attr( 'id', response.list );

      tooltipButton.siblings('.tooltip-content.hover').children('.wrap').html( response.tooltip );
      tooltip = new Tooltip( tooltipButton.siblings('.tooltip-content.hover') );

      // Update form
      list_item.addClass('isfav');
      list_item.siblings().removeClass('isfav');
      if ( listChoice=='remove' ) {
        list_item.addClass('nodisplay');
      }
      else {
        list_item.siblings('#remove').removeClass('nodisplay');
      }

    },
    'json',
  );

}
