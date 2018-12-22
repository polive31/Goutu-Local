jQuery(document).ready(function(){
  // console.log('Favorite Recipe loaded !');	
  // jQuery(document).on('click', '.wpurp-recipe-favorite.logged-in', function(e) {
  // jQuery(document).on('click', '.wpurp-recipe-favorite.logged-in', function(e) {
  // // console.log('Click on favorite detected !');
  //     e.preventDefault();
  //     e.stopPropagation();
  // });
  

});
    
function addToFavoritesCancel(elemnt) {
  console.log('In add To Favorites Cancel !!!');

  var tooltip = jQuery(elemnt).parents('.tooltip-content'); 

  openCloseTooltip( tooltip );
}
    
function addToFavoritesUpdate(elemnt) {

  console.log('In addToFavorites function');
  var tooltipForm = jQuery(elemnt).parents('.tooltip-content');
  var tooltipTarget = jQuery(elemnt).parents('.tooltip-content').siblings('.tooltip-target');
  
  console.log('tooltipTarget : ', tooltipTarget);
  openCloseTooltip( tooltipForm );
  
  //console.log('Tooltip %0', tooltip);
  
  var listChoice = tooltipForm.find("input:radio[name ='favlist']:checked");
  
  console.log( 'chosen option : ' + listChoice.val());
  if ( listChoice.val()=='remove' ) {
    // Activate shopping list button
    tooltipTarget.removeClass('is-favorite');
  }
  else {
    tooltipTarget.addClass('is-favorite');
  }

  var data = {
    action: 'custom_favorite_recipe',
    security: custom_favorite_recipe.nonce,
    recipe_id: tooltipTarget.data('recipe-id'),
    choice: listChoice.val(),
  };

  console.log('data.action : '+data.action);
  console.log('data.security : ' + data.security);
  console.log('data.recipe_id : ' + data.recipe_id);
  console.log('ajax URL : ' + custom_favorite_recipe.ajaxurl);

  jQuery.post(
      custom_favorite_recipe.ajaxurl, 
      data, 
      function(response) {
        console.log('Add to Favorites AJAX call completed');
        console.log('Response : ' + response);
        }
  );

}  