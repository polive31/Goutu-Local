//jQuery(document).ready(function() {
//	console.log('Custom shopping list loaded');
//});


jQuery(document).ready(function() {

    jQuery(document).on('click', '.wpurp-recipe-add-to-shopping-list.logged-in', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var button = jQuery(this);
				var tooltip=button.parent().find('div.toggle');
				console.log('Tooltip',tooltip);

        if(!button.hasClass('in-shopping-list')) {
        //if(true) {
            // Placed here rather than ajax return such that the user sees the change immediately
            button.addClass('in-shopping-list');
						tooltip.toggle();
		            
		        // Prepare AJAX call                
            var recipeId = button.data('recipe-id');
						console.log('Recipe ID :'+recipeId);
						
            var recipe = button.parents('.wpurp-container');
            var servings = 0;
            // Check if there is a servings changer (both free and Premium)
            var servings_input = recipe.find('input.adjust-recipe-servings');
						console.log('Servings input : %O', servings_input);
            
            if(servings_input.length == 0) {
                servings_input = recipe.find('input.advanced-adjust-recipe-servings');
            }

            // Take servings from serving changer if available
            if(servings_input.length != 0) {
                servings = parseInt(servings_input.val());
            }

            var data = {
                action: 'add_to_shopping_list',
                security: wpurp_add_to_shopping_list.nonce,
                recipe_id: recipeId,
                servings_wanted: servings
            };

            jQuery.post(wpurp_add_to_shopping_list.ajaxurl, data, function(html) {
							console.log('Add to Shopping List AJAX call completed');
            });
            
        }    
    });
});
