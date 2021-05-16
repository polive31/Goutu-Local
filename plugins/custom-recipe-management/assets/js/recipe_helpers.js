var wpurp_adjustable_servings = {};


jQuery(document).ready(function() {

    /* Ingredient / Instructions menu bar
    ---------------------------------------------------------------- */
    jQuery(document).on('click', '.menu-bar .menu-tab', function (e) {
        jQuery(this).addClass('selected');
        var target=jQuery(this).data('target');
        var $container=jQuery('#'+target);
        $container.show();

        jQuery.each( jQuery(this).siblings(), function() {
            jQuery(this).removeClass('selected');
            var target=jQuery(this).data('target');
            jQuery('#'+target).hide();
        });

        jQuery('html, body').animate({
            scrollTop: ($container.offset().top - 100)
        }, 0);
    });

 /* Ingredients share button */
    if ( !navigator.share ) {
        console.log('Share API is NOT supported !');
        jQuery('#ingredient_share_button').hide();
    }

    jQuery(document).on('click', '#ingredient_share_button', function (e) {
        console.log('Click on cart button !');
        var ingredientsList = '';
        jQuery('.wpurp-recipe-ingredients li').each(function(index) {
            ingredientsList = ingredientsList + jQuery(this).text() + '\n';
        });
        var recipeTitle = jQuery(location).attr('href');

        console.log(ingredientsList);
        if ( navigator.share ) {
            console.log('Share API is supported !');
            navigator.share({
                title: recipeTitle,
                text: ingredientsList,
                url: jQuery(location).attr("href")
                }).then(() => {
                console.log('Sharing done');
                })
                .catch(err => {
                console.log(`Couldn't share because of`, err.message);
                });
        } else {
            console.log('Share API is NOT supported');
        }
    });



    /* Ingredient checkboxes
    ---------------------------------------------------------------- */
    jQuery(document).on('click', '.wpurp-recipe-ingredient  .ingredient-checkbox', function (e) {
        // console.log('Click on ingredient checkbox detected !');
        e.preventDefault();
        e.stopPropagation();
        jQuery(this).toggleClass('checked');
    });


    // Prevent context menu to appear on long touch
    jQuery('tooltip').oncontextmenu = function(event) {
        event.preventDefault();
        event.stopPropagation();
        return false;
    };


    // Custom increase & decrease quantity buttons
    jQuery(document).on("click", "#servings .qty", function() {
        var $button = jQuery(this);
        var $input= $button.parent().find("input");
        var oldValue = $input.val();
        if ($button.attr('id') == "inc") {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 1) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 1;
            }
        }
        $input.val(newVal);
        $input.trigger("change");
    });

    jQuery(document).on('keyup change', '.adjust-recipe-servings', function(e) {
        var servings_input = jQuery(this);

        console.log('In adjust recipe servings keyup change');
        var amounts = servings_input.parents('.ingredients-container').find('.recipe-ingredient-quantity');
        var servings_original = parseFloat(servings_input.data('original'));
        var servings_new = servings_input.val();

        var $printButton = jQuery('#recipe_print_button');
        var url = $printButton.attr('href');

        if(isNaN(servings_new) || servings_new <= 0){
            servings_new = 1;
        }

        /* Update print URL */
        url = url.replace(/(\d+)/, servings_new);
        $printButton.attr('href', url);

        wpurp_adjustable_servings.updateAmounts(amounts, servings_original, servings_new);
    });


    // jQuery(document).on('blur', '.adjust-recipe-servings', function(e) {
    //     var servings_input = jQuery(this);
    //     var servings_new = servings_input.val();

    //     if( isNaN(servings_new) || servings_new <= 0){
    //         servings_new = 1;
    //     }

    //     servings_input.parents('.wpurp-container').find('.adjust-recipe-servings').each(function() {
    //         jQuery(this).val(servings_new);
    //     });
    // });

});

/* HELPERS
----------------------------------------------------------------------*/
wpurp_adjustable_servings.parse_quantity = function(sQuantity) {
    // Use . for decimals
    sQuantity = sQuantity.replace(',', '.');

    // Replace fraction characters with equivalent
    var fractionsRegex = /(\u00BC|\u00BD|\u00BE|\u2150|\u2151|\u2152|\u2153|\u2154|\u2155|\u2156|\u2157|\u2158|\u2159|\u215A|\u215B|\u215C|\u215D|\u215E)/;
    var fractionsMap = {
        '\u00BC': ' 1/4', '\u00BD': ' 1/2', '\u00BE': ' 3/4', '\u2150': ' 1/7',
        '\u2151': ' 1/9', '\u2152': ' 1/10', '\u2153': ' 1/3', '\u2154': ' 2/3',
        '\u2155': ' 1/5', '\u2156': ' 2/5', '\u2157': ' 3/5', '\u2158': ' 4/5',
        '\u2159': ' 1/6', '\u215A': ' 5/6', '\u215B': ' 1/8', '\u215C': ' 3/8',
        '\u215D': ' 5/8', '\u215E': ' 7/8'
    };
    sQuantity = (sQuantity + '').replace(fractionsRegex, function(m, vf) {
        return fractionsMap[vf];
    });

    // Split by spaces
    sQuantity = sQuantity.trim();
    var parts = sQuantity.split(' ');
    var quantity = false;

    if(sQuantity !== '') {
        quantity = 0;

        // Loop over parts and add values
        for(var i = 0; i < parts.length; i++) {
            if(parts[i].trim() !== '') {
                var division_parts = parts[i].split('/', 2);
                var part_quantity = parseFloat(division_parts[0]);

                if(division_parts[1] !== undefined) {
                    var divisor = parseFloat(division_parts[1]);

                    if(divisor !== 0) {
                        part_quantity /= divisor;
                    }
                }

                quantity += part_quantity;
            }
        }
    }

    return quantity;
}

wpurp_adjustable_servings.updateAmounts = function(amounts, servings_original, servings_new)
{
    amounts.each(function() {

        var amount = parseFloat(jQuery(this).data('normalized'));
        if(servings_original == servings_new)
        {
            jQuery(this).text(jQuery(this).data('original'));
        }
        else
        {
            if (!isFinite(amount) || amount==0 ) {
                // This is the case when amount is a string, ex : "quelques"
                jQuery(this).addClass('recipe-ingredient-nan');
            }
            else {
                var fraction = jQuery(this).data('fraction');
                var new_amount = servings_new * amount/servings_original;
                var new_amount_text = wpurp_adjustable_servings.toFixed(new_amount, fraction);
                var $ingredientNameContainer = jQuery(this).parents('.wpurp-recipe-ingredient').find('.recipe-ingredient-name');
                var $ingredientNameRoot = jQuery(this).parents('.wpurp-recipe-ingredient').find('#ingredient_name_root');
                var $ingredientUnitContainer = jQuery(this).next();
                var unitSingular = $ingredientUnitContainer.data('original');
                var unitPlural = $ingredientUnitContainer.data('plural');

                jQuery(this).text(new_amount_text + ' ');

                // Change ingredient name to singular or plural if needed
                if (new_amount >= 2) {
                    if ( $ingredientNameContainer.data('plural') )
                        $ingredientNameRoot.html( $ingredientNameContainer.data('plural') );
                    if ( unitPlural.length!=0 )
                        $ingredientUnitContainer.text( unitPlural );
                    }
                else {
                    if ( $ingredientNameContainer.data('singular') && unitSingular.length == 0 )
                        $ingredientNameRoot.html( $ingredientNameContainer.data('singular') );
                    $ingredientUnitContainer.text( unitSingular );

                }


            }
        }
    });
}

wpurp_adjustable_servings.toFixed = function(amount, fraction)
{
    if(fraction) {
        var fractioned_amount = Fraction(amount.toString()).snap();
        if(fractioned_amount.denominator < 100) {
            return fractioned_amount;
        }
    }

    if(amount == '' || amount == 0) {
        return '';
    }
    // reformat to fixed
    var precision = parseInt(wpurp_servings.precision);
    var formatted = amount.toFixed(precision);

    // increase the precision if reformated to 0.00, failsafe for endless loop
    while(parseFloat(formatted) == 0) {
        precision++;
        formatted = amount.toFixed(precision);

        if(precision > 10) {
            return '';
        }
    }

    // ends with .00, remove
    if(precision > 0) {
        var zeroes = Array(precision+1).join('0');
        formatted = formatted.replace(new RegExp('\.' + zeroes + '$'),'');
    }

    // Change decimal character
    if(typeof wpurp_servings !== 'undefined') {
        formatted = formatted.replace('.', wpurp_servings.decimal_character);
    }

    return formatted;
}
