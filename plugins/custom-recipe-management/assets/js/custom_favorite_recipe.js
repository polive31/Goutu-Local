jQuery(document).ready(function(){

  jQuery('#favorites_list_form').on('click', 'li', function (e) {
    console.log('Click in favorites list');
    e.preventDefault();
    e.stopPropagation();
    // var choice = jQuery(e.target).closest('.fav-item');
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
