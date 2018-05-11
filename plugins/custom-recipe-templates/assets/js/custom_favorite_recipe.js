jQuery(document).ready(function(){
    console.log('Favorite Recipe loaded !');	
    jQuery(document).on('click', '.wpurp-recipe-favorite.logged-in', function(e) {
		console.log('Click on favorite detected !');
        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
				var tooltip=button.parent().find('div.toggle');
				
				if(button.hasClass('is-favorite')) {
					// Activate shopping list button
					button.removeClass('is-favorite');
				}
				else {
					button.addClass('is-favorite');
				}
				tooltip.toggle();
				//console.log('Tooltip %0', tooltip);
		        
				// Prep ajax call
        var recipeId = button.data('recipe-id');
				//console.log('Recipe ID :'+recipeId);

        var data = {
          action: 'favorite_recipe',
          security: wpurp_favorite_recipe.nonce,
          recipe_id: recipeId
        };

        jQuery.post(wpurp_favorite_recipe.ajaxurl, data, function(html) {
			console.log('Add to Favorites AJAX call completed');

        });
    });
});