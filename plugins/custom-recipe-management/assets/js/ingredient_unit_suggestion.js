var xhrSuggestion = null;
jQuery(document).ready(function() {

/* Ingredient Suggestions
---------------------------------------------------------------- */
    jQuery(document).on('input', '.ingredients_name', function(){
        autoSuggestIngredient( jQuery(this) );
    });

    jQuery('input.ingredients_name').focusout(function(){
        // console.log('In ingredient focusout');
        spinnerHTML = jQuery(this).closest("td").next().children('#ajax-indicator');
        // console.log(spinnerHTML);
        spinnerHTML.css('visibility','hidden');
        try { xhr.abort(); } catch(e){}
    });


/* Ingredient Unit Suggestions
---------------------------------------------------------------- */
    jQuery(document).on('input', '.ingredients_unit', function(){
        autoSuggestUnit( jQuery(this) );
    });

});

function autoSuggestIngredient( thisInput ) {
    console.log('%c In autoSuggestIngredient', 'background:#D7EEC0;color:green');
    console.log('%c ingredient_autocomplete.ajaxurl = ' + ingredient_autocomplete.ajaxurl, 'background:#D7EEC0;color:green' );
    term=thisInput.val();
    id=thisInput.attr('id');
    tax = 'ingredient';

    spinnerHTML = thisInput.closest("td").next().children('#ajax-indicator');
    console.log('spinnerHTML content', spinnerHTML.html() );

    spinnerHTML.css('visibility','hidden');

    jQuery( thisInput ).autoComplete({
        minChars: 3,
        delay : 200,
        menuClass : 'ingredient',
        source: function(term, response) {
            spinnerHTML.css('visibility','visible');
            try { xhr.abort(); } catch (e) { }
            xhr = jQuery.get(
                ingredient_autocomplete.ajaxurl,
                'action=get_tax_terms&tax=' + tax + '&keys=' + term,
                function (data) {
                    response(data);
                    spinnerHTML.css('visibility', 'hidden');
                },
                'json',
            );
        }
    });
}


function autoSuggestUnit( thisInput ) {
    console.log('In autoSuggestUnit');
    // console.log( thisInput );
    term=thisInput.val();
    qty=thisInput.parents('tr.ingredient').find('td.qty input').val();
    console.log('Qty = ' + qty);
    // console.log( term );
    jQuery( thisInput ).autoComplete({
        minChars: 1,
        source: function(term, suggest){
            term = term.toLowerCase();
            if (qty > 1) {
                var choices = thisInput.parents('.recipe-ingredients-container').data('units-plural');
            }
            else {
                var choices = thisInput.parents('.recipe-ingredients-container').data('units');
            }
            // console.log(choices);
            var matches = [];
            for (i=0; i<choices.length; i++)
                if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
            suggest(matches);
        }
    })
}
