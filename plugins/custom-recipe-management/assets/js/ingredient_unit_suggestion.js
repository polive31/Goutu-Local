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

// function isPlural( amount, unit ) {
//     var plural = (amount > 1) || (unit != '') || (amount=='' && unit=='');
//     return plural;
// }

function autoSuggestIngredient( thisInput ) {
    // console.log('In autoSuggestIngredient');
    term=thisInput.val();
    id=thisInput.attr('id');
    tax = 'ingredient';
    // console.log(term);

    spinnerHTML = thisInput.closest("td").next().children('#ajax-indicator');
    console.log('spinnerHTML content', spinnerHTML.html() );

    spinnerHTML.css('visibility','hidden');

    jQuery( thisInput ).autoComplete({
        minChars: 3,
        delay : 200,
        source: function(term, response) {
            spinnerHTML.css('visibility','visible');
            try { xhr.abort(); } catch(e){}
            xhr = jQuery.ajax({
                dataType: 'json',
                url: '/wp-admin/admin-ajax.php',
                data: 'action=get_tax_terms&tax='+tax+'&keys='+term,
                success:function(data) {
                    response(data);
                    spinnerHTML.css('visibility','hidden');
                },
                // error:function() {
                //     spinnerHTML.css('visibility','hidden');
                // },
                // complete: function() {
                // }
            });
        },
        // renderItem: function(item, search) {
        //     search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        //     var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
        //     return '<div class="autocomplete-suggestion" data-val="' + item + '">'+ item.replace(re, "<b>$1</b>") + '</div>';
        // }
    });
};


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
