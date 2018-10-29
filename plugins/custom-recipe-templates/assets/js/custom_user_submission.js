var currentIngredientId=-1;

jQuery(document).ready(function() {

/* Ingredient Suggestions 
---------------------------------------------------------------- */
    jQuery(document).on('input', '.ingredients_name', function(){
        autoSuggestIngredient( jQuery(this) );
    });

    jQuery('input.ingredients_name').focusout(function(){
        // console.log('In ingredient focusout');
        spinnerHTML = jQuery(this).closest("td").next().children('.ajax-indicator');
        // console.log(spinnerHTML);
        spinnerHTML.css('visibility','hidden');
        try { xhr.abort(); } catch(e){}
    });


/* Ingredient Unit Suggestions 
---------------------------------------------------------------- */
    jQuery(document).on('input', '.ingredients_unit', function(){
        autoSuggestUnit( jQuery(this) );
    });


/* Ingredient Preview 
---------------------------------------------------------------- */
jQuery(document).on('focusin', 'tr.ingredient', function(){
    console.log("Focus in on tr.ingredient");
    toggleIngredientPreview(this);
});

jQuery(document).on('click', 'tr.ingredient.saved', function(){
    console.log("Click on tr.ingredient.saved");
    jQuery(this).focus();
});



/* Taxonomy selection fields 
---------------------------------------------------------------- */
    // Activate select2WPURP
    // jQuery('#wpurp_user_submission_form select[multiple]').select2wpurp({
    //     allowClear: true,
    //     width: 'off',
    //     dropdownAutoWidth: false
    // });

    // Activate select2
    // jQuery("select[multiple]").select2({
    jQuery("#wpurp_user_submission_form select").select2({
        width: 'off',
        dropdownAutoWidth: false,
        minimumResultsForSearch: -1,
        allowClear: false,
        templateSelection: formatItem,
        // closeOnSelect: false,
    });  

    function formatItem(data, container) {
        var elClass='';
        if (data.element.value == -1 || data.element.value == "") {
           elClass='class="option-none"';
        }
        var $state = jQuery('<span ' + elClass + '>' + data.text + '</span>');
        return $state;
    }

    jQuery(".select2-search input").prop("readonly", true);
    jQuery(".select2, .select2-multiple").on('select2:open', function (e) {
        jQuery('.select2-search input').prop('focus',false);
    });

    jQuery('.taxonomy-select-boxes').removeClass('nodisplay');
    jQuery('.taxonomy-select-spinner').addClass('nodisplay');

     /*
     * Add shortcode buttons
     */
    jQuery('#insert-recipe-shortcode').on('click', function(){
        wpurp_add_to_editor('[recipe]');
    });

    jQuery('#insert-nutrition-shortcode').on('click', function(){
        wpurp_add_to_editor('[nutrition-label]');
    });

    var text_editor = jQuery('textarea#content');
    function wpurp_add_to_editor(text) {
        if( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
            var current = text_editor.val();
            text_editor.val(current + text);
        } else {
            tinyMCE.execCommand('mceInsertContent', false, text);
        }
    }

    /*
     * Do not allow removal of first ingredient/instruction
     */
    jQuery('#recipe-ingredients tr.ingredient:first').find('span.ingredients-delete').hide();
    jQuery('#recipe-instructions tr.instruction:first').find('span.instructions-delete').hide();

    /*
     * Ingredient Groups
     * */

    calculateIngredientGroups();

    jQuery('#ingredients-add-group').on('click', function(e){
        e.preventDefault();
        addRecipeIngredientGroup();
    });

    var calculateIngredientGroupsTimer;
    jQuery('.ingredient-group-label').on('input', function() {
        window.clearTimeout(calculateIngredientGroupsTimer);
        calculateIngredientGroupsTimer = window.setTimeout(function() {
            calculateIngredientGroups();
        }, 500);
    });



    jQuery('.ingredient-group-delete').on('click', function(){
        jQuery(this).parents('tr').remove();

        calculateIngredientGroups();
    });



    /*
     * Instruction Groups
     * */
    calculateInstructionGroups();

    jQuery('#instructions-add-group').on('click', function(e){
        e.preventDefault();
        addRecipeInstructionGroup();
    });

    var calculateInstructionGroupsTimer;
    jQuery('.instruction-group-label').on('input', function() {
        window.clearTimeout(calculateInstructionGroupsTimer);
        calculateInstructionGroupsTimer = window.setTimeout(function() {
            calculateInstructionGroups();
        }, 500);
    });



    /*
     * Recipe ingredients
     * */
    jQuery('#recipe-ingredients tbody').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.sort-handle',
        update: function() {
            // addRecipeIngredientOnTab();
            calculateIngredientGroups();
            updateIngredientIndex();
        }
    });


    // // Hide AutoSuggest box on TAB or click
    // jQuery('#recipe-ingredients').on('keydown', function(e) {
    //     var keyCode = e.keyCode || e.which;

    //     if (keyCode == 9) {
    //         jQuery('ul.ac_results').hide();
    //     }
    // });
    // jQuery('#recipe-ingredients').on('click', function() {
    //     jQuery('ul.ac_results').hide();
    // });

    jQuery('.ingredients-delete').on('click', function(){
        jQuery(this).parents('tr').remove();
        // addRecipeIngredientOnTab();
        updateIngredientIndex();
    });


    jQuery('#ingredients-add').on('click', function(e){
        e.preventDefault();
        addRecipeIngredient();
    });

    jQuery('#recipe-ingredients .ingredients_notes').on('keydown',function(e) {
        // console.log("Found keypress on .ingredient_notes !!!");
        var keyCode = e.keyCode || e.which;
        var last_id = jQuery('#recipe-ingredients tr:last').attr('id');
        // console.log("Last ID = " + last_id);
        var current_ingredient = jQuery(this).closest('tr.ingredient');
        var current_id = current_ingredient.attr('id');
        
        if (keyCode == 9 && e.shiftKey == false) {
            e.preventDefault();
            if (current_id == last_id ) {
                // console.log("Found keypress on tr::last .ingredient_notes !!!");
                addRecipeIngredient();
            } 
            else {
                current_ingredient.next().focus();
            }
        }
    });

    jQuery('#recipe-ingredients .ingredients_amount').on('keydown',function(e) {
        // console.log("Keypress detected on ingredients amount !");
        
        var keyCode = e.keyCode || e.which;
        var last_id = jQuery('#recipe-ingredients tr:last').attr('id');
        var current_ingredient = jQuery(this).closest('tr.ingredient');
        var current_id = current_ingredient.attr('id');
        console.log ("Current ingredient : " + current_id);
        
        if (keyCode == 9 && e.shiftKey == true) {
            event.preventDefault()
            // console.log("Keypress shift !");
            var previous_ingredient = current_ingredient.prev();
            previous_ingredient.focus();
            var prev_id = previous_ingredient.attr('id');
            // console.log ("Previous ingredient : " + prev_id);
        }
    });

    /*
     * Recipe instructions
     * */
    jQuery('#recipe-instructions tbody').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.sort-handle',
        update: function() {
            // addRecipeInstructionOnTab();
            calculateInstructionGroups();
            updateInstructionIndex();
        }
    });

    jQuery('.instructions-delete').on('click', function(){
        jQuery(this).parents('tr').remove();
        // addRecipeInstructionOnTab();
        updateInstructionIndex();
    });

    jQuery('#instructions-add').on('click', function(e){
        e.preventDefault();
        addRecipeInstruction();
    });



    // addRecipeInstructionOnTab();


    // TODO To user submission js
    jQuery('.recipe_thumbnail_add_image').on('click', function(e) {

        e.preventDefault();

        var button = jQuery(this);

        image = button.siblings('.recipe_thumbnail_image');
        preview = button.siblings('.recipe_thumbnail');

        if(typeof wp.media == 'function') {
            var custom_uploader = wp.media({
                title: 'Insert Media',
                button: {
                    text: 'Add featured image'
                },
                multiple: false
            })
                .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    jQuery(preview).attr('src', attachment.url);
                    jQuery(image).val(attachment.id).trigger('change');
                })
                .open();
        } else { //fallback
            post_id = button.attr('rel');

            tb_show(button.attr('value'), 'wp-admin/media-upload.php?post_id='+post_id+'&type=image&TB_iframe=1');

            window.send_to_editor = function(html) {
                img = jQuery('img', html);
                imgurl = img.attr('src');
                classes = img.attr('class');
                id = classes.replace(/(.*?)wp-image-/, '');
                image.val(id).trigger('change');
                preview.attr('src', imgurl);
                tb_remove();
            }
        }

    });

    jQuery('.recipe_thumbnail_remove_image').on('click', function(e) {
        e.preventDefault();

        var button = jQuery(this);

        button.siblings('.recipe_thumbnail_image').val('').trigger('change');
        button.siblings('.recipe_thumbnail').attr('src', wpurp_recipe_form.coreUrl + '/img/image_placeholder.png');
    });

    jQuery('.recipe_thumbnail_image').on('change', function() {
        var image = jQuery(this);
        if(image.val() == '') {
            image.siblings('.recipe_thumbnail_add_image').removeClass('wpurp-hide');
            image.siblings('.recipe_thumbnail_remove_image').addClass('wpurp-hide');
        } else {
            image.siblings('.recipe_thumbnail_remove_image').removeClass('wpurp-hide');
            image.siblings('.recipe_thumbnail_add_image').addClass('wpurp-hide');
        }
    });

    // Instruction Step Image 

    jQuery(".recipe-instructions-container").on("change", "input.recipe_instructions_image", function() { 
        var changedSelectId = jQuery(this).attr("id");
        var Id = changedSelectId.match(/\d+/);
        // console.log( "Changement sur l'input..." + Id );
        PreviewImage(Id);
    });

    // jQuery('.recipe_instructions_add_image').on('click', function(e) {

    //     e.preventDefault();

    //     var button = jQuery(this);

    //     image = button.siblings('.recipe_instructions_image');
    //     preview = button.siblings('.recipe_instructions_thumbnail');

    //     if(typeof wp.media == 'function') {
    //         var custom_uploader = wp.media({
    //             title: 'Insert Media',
    //             button: {
    //                 text: 'Add instruction image'
    //             },
    //             multiple: false
    //         })
    //             .on('select', function() {
    //                 var attachment = custom_uploader.state().get('selection').first().toJSON();
    //                 jQuery(preview).attr('src', attachment.url);
    //                 jQuery(image).val(attachment.id).trigger('change');
    //             })
    //             .open();
    //     } else { //fallback
    //         post_id = button.attr('rel');

    //         tb_show(button.attr('value'), 'wp-admin/media-upload.php?post_id='+post_id+'&type=image&TB_iframe=1');

    //         window.send_to_editor = function(html) {
    //             img = jQuery('img', html);
    //             imgurl = img.attr('src');
    //             classes = img.attr('class');
    //             id = classes.replace(/(.*?)wp-image-/, '');
    //             image.val(id).trigger('change');
    //             preview.attr('src', imgurl);
    //             tb_remove();
    //         }
    //     }

    // });

    // jQuery('.recipe_instructions_remove_image').on('click', function(e) {
    //     e.preventDefault();

    //     var button = jQuery(this);

    //     button.siblings('.recipe_instructions_image').val('').trigger('change');
    //     button.siblings('.recipe_instructions_thumbnail').attr('src', wpurp_recipe_form.coreUrl + '/img/image_placeholder.png');
    // });

    // jQuery('.recipe_instructions_image').on('change', function() {
    //     var image = jQuery(this);
    //     if(image.val() == '') {
    //         image.siblings('.recipe_instructions_add_image').removeClass('wpurp-hide');
    //         image.siblings('.recipe_instructions_remove_image').addClass('wpurp-hide');
    //     } else {
    //         image.siblings('.recipe_instructions_remove_image').removeClass('wpurp-hide');
    //         image.siblings('.recipe_instructions_add_image').addClass('wpurp-hide');
    //     }
    // });

    jQuery('#wpurp-insert-recipe').on('click', function() {
        var shortcode = '[ultimate-recipe id=';

        shortcode += jQuery('#wpurp-recipe').find('option:selected').val();
        shortcode += ']';

        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        tinyMCE.activeEditor.windowManager.close();
    });

    jQuery('.wpurp-file-remove').on('click', function(e) {
        e.preventDefault();

        var button = jQuery(this);

        preview = button.siblings('img');
        fieldname = preview.attr('class');

        button.siblings('.' + fieldname + '_image').val('');
        button.siblings('.' + fieldname).attr('src', '');

        button.siblings('.wpurp-file-upload').removeClass('wpurp-hide');
        button.addClass('wpurp-hide');
    });

});


/* Functions Library
----------------------------------------------------- */


function toggleIngredientPreview(thisIngredient) {

        lastIngredientId = currentIngredientId;

        // thisIngredient = jQuery(thisInput).closest('tr.ingredient');
        currentIngredientId = jQuery(thisIngredient).attr('id').match(/\d+/);
        currentIngredientId = currentIngredientId[0];
        jQuery(thisIngredient).removeClass('saved new');
        jQuery(thisIngredient).addClass('edit');

        console.log('Current ingredient : ' + currentIngredientId );
        console.log('Last ingredient : ' + lastIngredientId );
        if (lastIngredientId == currentIngredientId || lastIngredientId==-1) {
            console.log( "No row change");
            return;
        }

        lastIngredient = jQuery('#recipe-ingredients').find('tr#ingredient_' + lastIngredientId);
        console.log("Toggle Ingredient Preview : Ajax call launched !", 'background: #ccc; color: blue');
        // console.log('Ingredients amount : ' + jQuery('#recipe-ingredients').find('#ingredients_amount_' + lastIngredientId).val() );
        // console.log('Target : ' + 'tr#ingredient_' + lastIngredientId + ' td.ingredient-preview')

        try { xhr_ingredient.abort(); } catch(e){}
        
        xhr_ingredient=jQuery.ajax({
            url : custom_user_submissions.ajaxurl,
            method : 'POST',
            data : {
                action : 'ingredient_preview',
                security : custom_user_submissions.nonce,
                target_ingredient_id : lastIngredientId,
                amount : lastIngredient.find('.ingredients_amount').val(),
                unit : lastIngredient.find('.ingredients_unit').val(),
                ingredient : lastIngredient.find('.ingredients_name').val(),
                notes : lastIngredient.find('.ingredients_notes').val(),
            }, 
            success : function( response ) {
                if( response.success ){
                    // console.log( "Ajax ingredient preview Success !!!!");
                    // console.log( 'Response is : ' + response.data.msg );
                    var target = jQuery('#recipe-ingredients').find('tr#ingredient_' + lastIngredientId + ' td.ingredient-preview');
                    target.html( response.data.msg );
                    lastIngredient.removeClass('edit new');
                    lastIngredient.addClass('saved');
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


function autoSuggestIngredient( thisInput ) {
    // console.log('In autoSuggestIngredient');
    term=thisInput.val();
    id=thisInput.attr('id');
    tax = 'ingredient';
    // console.log(term);
    // console.log(id);

    spinnerHTML = thisInput.closest("td").next().children('.ajax-indicator');
    spinnerHTML.css('visibility','hidden');
    // console.log(spinnerHTML);
    
    // jQuery('#' + id).autoComplete({
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
        }
    });
};

function autoSuggestUnit( thisInput ) {
    console.log('In autoSuggestUnit');
    // console.log( thisInput );
    term=thisInput.val();
    // console.log( term );
    jQuery( thisInput ).autoComplete({ 
        minChars: 1,
        source: function(term, suggest){
            term = term.toLowerCase();
            var choices = thisInput.parents('.recipe-ingredients-container').data('units');
            // console.log(choices);
            var matches = [];
            for (i=0; i<choices.length; i++)
                if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
            suggest(matches);
        }    
    })
}

function PreviewImage(id) {
    // console.log( "In Preview Image");
    var oFReader = new FileReader();
    oFReader.readAsDataURL(document.getElementById("recipe_thumbnail_input_" + id).files[0]);
    oFReader.onload = function (oFREvent) {
        document.getElementById("instruction_thumbnail_preview_" + id ).src = oFREvent.target.result;
    };
};


function addRecipeIngredientGroup()
{
    var last_group = jQuery('#recipe-ingredients tr.ingredient-group-stub')
    var last_row = jQuery('#recipe-ingredients tr:last')
    var clone_group = last_group.clone(true);

    clone_group
        .insertAfter(last_row)
        .removeClass('ingredient-group-stub')
        .addClass('ingredient-group');

    jQuery('.ingredient-groups-disabled').hide();
    jQuery('.ingredient-groups-enabled').show();

    calculateIngredientGroups();
}

function calculateIngredientGroups()
{
    if(jQuery('.ingredient-group').length == 1) {
        jQuery('#recipe-ingredients .ingredient .ingredients_group').val('');

        jQuery('.ingredient-groups-disabled').show();
        jQuery('.ingredient-groups-enabled').hide();
    } else {
        jQuery('#recipe-ingredients tr.ingredient').each(function(i, row){
            var group = jQuery(row).prevAll('.ingredient-group:first').find('.ingredient-group-label').val();

            if(group === undefined) {
                group = jQuery('.ingredient-group-first').find('.ingredient-group-label').val();
            }

            jQuery(row).find('.ingredients_group').val(group);
        });

        jQuery('.ingredient-groups-disabled').hide();
        jQuery('.ingredient-groups-enabled').show();
    }
}


function addRecipeInstructionGroup()
{
    var last_group = jQuery('#recipe-instructions tr.instruction-group-stub')
    var last_row = jQuery('#recipe-instructions tr:last')
    var clone_group = last_group.clone(true);

    clone_group
        .insertAfter(last_row)
        .removeClass('instruction-group-stub')
        .addClass('instruction-group');

    jQuery('.instruction-groups-disabled').hide();
    jQuery('.instruction-groups-enabled').show();

    calculateInstructionGroups();
}

jQuery('.instruction-group-delete').on('click', function(){
    jQuery(this).parents('tr').remove();

    calculateInstructionGroups();
});

function calculateInstructionGroups()
{
    if(jQuery('.instruction-group').length == 1) {
        jQuery('#recipe-instructions .instruction .instructions_group').val('');

        jQuery('.instruction-groups-disabled').show();
        jQuery('.instruction-groups-enabled').hide();
    } else {
        jQuery('#recipe-instructions tr.instruction').each(function(i, row){
            var group = jQuery(row).prevAll('.instruction-group:first').find('.instruction-group-label').val();

            if(group === undefined) {
                group = jQuery('.instruction-group-first').find('.instruction-group-label').val();
            }

            jQuery(row).find('.instructions_group').val(group);
        });

        jQuery('.instruction-groups-disabled').hide();
        jQuery('.instruction-groups-enabled').show();
    }
}

// function addRecipeIngredientOnTab()
// {    
//     // jQuery('#recipe-ingredients .ingredients_notes')
//     jQuery('#recipe-ingredients tr:last .ingredients_notes')
//         .unbind('keydown')
//         .last()
//         .bind('keydown', function(e) {
//             var keyCode = e.keyCode || e.which;

//             if (keyCode == 9 && e.shiftKey == false) {
//                 e.preventDefault();
//                 addRecipeIngredient();
//             }
//         });
// }

function updateIngredientIndex()
{
    jQuery('#recipe-ingredients tr.ingredient').each(function(i) {
        jQuery(this)
            .find('input')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, i);
            })
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            })
            // .parent().find('input.ingredients_name')
            // .attr('onfocus', function(index, onfocus) {
            //     return onfocus.replace(/(\d+)/, i);
            // });
    });
}

function addRecipeIngredient()
{
    var nbr_ingredients = jQuery('#recipe-ingredients tr.ingredient').length;
    var last_row = jQuery('#recipe-ingredients tr:last');
    var last_ingredient = jQuery('#recipe-ingredients tr.ingredient:last');

    // last_ingredient.find('input').attr('placeholder','');
    var clone_ingredient = last_ingredient.clone(true);

    // console.log("In add recipe ingredient");
    // console.log("Content of preview in new row : " + clone_ingredient.find('td.ingredient-preview').html());
    clone_ingredient.find('td.ingredient-preview').html('');

    clone_ingredient
        .insertAfter(last_row)
        .find('input, select, textarea').val('')
        .attr('name', function(index, name) {
            return name.replace(/(\d+)/, nbr_ingredients);
        })
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_ingredients);
        })     
        .closest('tr.ingredient')
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_ingredients);
        });

    clone_ingredient.find('span.ingredients-delete').show();

    // addRecipeIngredientOnTab();

    jQuery('#recipe-ingredients tr:last .ingredients_amount').focus();
    calculateIngredientGroups();
}  

function addRecipeInstruction()
{
    var nbr_instructions = jQuery('#recipe-instructions tr.instruction').length;
    var new_instruction = jQuery('#recipe-instructions tr.instruction:last').clone(true);

    new_instruction
        .insertAfter('#recipe-instructions tr:last')
        .find('textarea').val('')
        .attr('name', function(index, name) {
            return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_instructions);
        });

    new_instruction
        .find('.recipe_instructions_remove_image').addClass('wpurp-hide')

    new_instruction
        .find('.recipe_instructions_add_image').removeClass('wpurp-hide')

    new_instruction
        .find('.recipe_instructions_thumbnail').val('')

        // Thumbnail file selector input
    new_instruction
        .find('.recipe_instructions_image')
        .attr('name', function(index, name) {
            return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_instructions);
        }) 
        .val(null);

        // Thumbnail content
    new_instruction
        .find('.recipe_instructions_thumbnail')
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_instructions);
        })
        .attr('src', custom_user_submissions.placeholder );                 

        // instructions group
    new_instruction
        .find('.instructions_group')
        .attr('name', function(index, name) {
            return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function(index, id) {
            return id.replace(/(\d+)/, nbr_instructions);
        });


    new_instruction.find('span.instructions-delete').show();
    // addRecipeInstructionOnTab();

    jQuery('#recipe-instructions tr:last textarea').focus();
    calculateInstructionGroups();

}  

// function addRecipeInstructionOnTab()
// {
//     jQuery('#recipe-instructions textarea')
//         .unbind('keydown')
//         .last()
//         .bind('keydown', function(e) {
//             var keyCode = e.keyCode || e.which;

//             if (keyCode == 9 && e.shiftKey == false) {
//                 var last_focused = jQuery('#recipe-instructions tr:last').find('textarea').is(':focus')

//                 if(last_focused == true) {
//                     e.preventDefault();
//                     addRecipeInstruction();
//                 }

//             }
//         });
// }

function updateInstructionIndex()
{
    jQuery('#recipe-instructions tr.instruction').each(function(i) {
        jQuery(this)
            .find('textarea')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, i);
            })
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            });

        jQuery(this)
            .find('.recipe_instructions_image')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, i);
            });

        jQuery(this)
            .find('.instructions_group')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, i);
            })
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            });
    });
}