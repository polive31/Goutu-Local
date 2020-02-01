var currentRowId;
var previousRowId;
var xhr_ingredient=[];

jQuery(document).ready(function() {

/* Ingredient Preview
---------------------------------------------------------------- */

    jQuery('#recipe-ingredients').on('focusin','tr.ingredient',function() {
        currentRowId = jQuery(this).attr('id');
        jQuery(this).removeClass('saved new');
        jQuery(this).addClass('edit');

        // console.log('in focusin event',currentRowId);
    });

    jQuery('#recipe-ingredients').on('blur','tr.ingredient',function() {
        currentRowId=-1; //allows checking whenever the focus went on a different; non-ingredient section
        previousRowId = jQuery(this).attr('id');
        var rowId;

        // console.log('in blur event', previousRowId);

        setTimeout(function() {
            console.log('after timeout variables are : ',previousRowId,currentRowId);
            // if (currentRowId==-1) {
            //     console.log('%c Click outside of the ingredients, global preview','background:red;color:black');
            //     jQuery('#recipe-ingredients').find('tr.edit').not('.new').each(function() {
            //         rowId = jQuery(this).attr('id');
            //         console.log('displayPreview for ingredient#' + rowId);
            //         if ( isValid(previousRowId) ) displayIngredientPreview(rowId);
            //     });
            // }
            if (previousRowId != currentRowId)  {
                if ( isValid(previousRowId) ) displayIngredientPreview(previousRowId);
            }
        }, 100);
    });

    jQuery(document).on('click', 'tr.saved', function(){
        console.log("%c Click on tr.ingredient.saved" + jQuery(this).attr('id'),'background:#D7EEC0;color:green');
        jQuery(this).focus();
    });

});

function isValid( id ) {
    var isValid=true;
    // var requiredFields = ['name'];
    var thisIngredient=getIngredientbyId(id);
    var inputField = thisIngredient.find('.name input');
    // console.log('In isValid, checking value for ' + inputField.attr('class') + ' ' + inputField.attr('id') , inputField.val());
    // if ( inputField.val().length == 0 ) isValid=false;
    if ( inputField.val() == null ) isValid=false;
    // thisIngredient.children('input').each(function() {
    //     var inputField = jQuery(this);
    //     console.log('In isValid, checking value for ' + inputField.attr('class'), inputField.val());
    //     if (inputField.val() == "") isValid = false;
    // });
    console.log('Is Valid = ' + isValid);
    return isValid;
}

function getIngredientbyId( id ) {
    return jQuery('#recipe-ingredients').find('#' + id);
}

function displayIngredientPreview( thisIngredientId ) {

        console.log("%c Display Ingredient Preview on " + thisIngredientId, 'background: #ccc; color: blue');

        var thisIngredient=getIngredientbyId(thisIngredientId);

        // console.log('XHR_Ingredient[thisIngredientId] : ', xhr_ingredient[thisIngredientId]);

        // Check whenever there is an ongoing ajax call on this ingredient
        try {
            xhr_ingredient[thisIngredientId].abort();
            console.log("%c Aborting previous ajax call for " + thisIngredientId, 'background: #ccc; color: blue');
        }
        catch(e){
            console.log("%c No previous ajax call for " + thisIngredientId, 'background: #ccc; color: blue');
        }

        console.log("%c Ajax call launched", 'background: #ccc; color: blue');
        console.log("Ajaxurl", ingredient_preview.ajaxurl );


        xhr_ingredient[thisIngredientId]=jQuery.ajax({
            url : ingredient_preview.ajaxurl,
            method : 'POST',
            data : {
                action : 'ingredient_preview',
                security : ingredient_preview.nonce,
                // ingredient_id : thisIngredientId,
                amount : thisIngredient.find('.ingredients_amount').val(),
                unit : thisIngredient.find('.ingredients_unit').val(),
                ingredient : thisIngredient.find('.ingredients_name').val(),
                notes : thisIngredient.find('.ingredients_notes').val(),
            },
            success : function( response ) {
                if( response.success ){
                    console.log( "Ajax ingredient preview success for " + thisIngredientId);
                    console.log( "Ingredient is " + thisIngredient);
                    // console.log( 'Response is : ' + response.data.msg );
                    var target = thisIngredient.find('td.ingredient-preview');
                    // console.log( 'Apply changes to : ', target );
                    jQuery('#recipe-ingredients').on('focusin','tr.ingredient', false);
                    jQuery('#recipe-ingredients').on('blur','tr.ingredient', false);
                    target.html( response.data.msg );
                    // console.log( 'Apply css changes to : ', thisIngredient );
                    thisIngredient.removeClass('edit new');
                    thisIngredient.addClass('saved');
                    // Last check in order to secure that there is no focus on this ingredient
                    // if (thisIngredient.children(':focus').length == 0) {
                // }
                // else {
                //     console.log('%c Current ingredient child has focus ! No change...','background:red;color:black');
                    jQuery('#recipe-ingredients').off('focusin','tr.ingredient', false);
                    jQuery('#recipe-ingredients').off('blur','tr.ingredient', false);
                }
                else {
                    console.log( 'Error on Ajax call processing : ' + response.data.msg );
                }
            },
            error : function( response ) {
                console.log( 'Ajax call failed : ' + response.msg );
            }
        });

}
