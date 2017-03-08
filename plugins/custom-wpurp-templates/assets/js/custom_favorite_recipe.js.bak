//jQuery(document).ready(function() {
//	console.log('Custom favorite recipe loaded');
//});
//

jQuery(document).ready(function(){
    jQuery(document).on('click', '.wpurp-recipe-favorite', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
        
				console.log('ADD TO FAVORITES CLICK !!!!');
				var buttonTitle = button.prop('title');
				var buttonTitleAlt = button.data('title-alt');
				button.prop('title', buttonTitleAlt);
				button.data('title-alt', buttonTitle);
				if(button.hasClass('is-favorite')) {
					// Activate shopping list button
					button.removeClass('is-favorite');
				}
				else {
					button.addClass('is-favorite');
				}
		        
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