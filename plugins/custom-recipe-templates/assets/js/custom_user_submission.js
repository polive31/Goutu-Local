jQuery(document).ready(function() {


/* Autoselect "numeric" inputs on focux
---------------------------------------------------------------- */


jQuery('input.selectonfocus').focus(function() {
    console.log('select on focus !!!');
    jQuery(this).select();
});


/* Shortcode buttons
---------------------------------------------------------------- */

    jQuery('#insert-recipe-shortcode').on('click', function(){
        wpurp_add_to_editor('[recipe]');
    });

    jQuery('#insert-nutrition-shortcode').on('click', function(){
        wpurp_add_to_editor('[nutrition-label]');
    });

    var text_editor = jQuery('#recipe_description');
    function wpurp_add_to_editor(text) {
        if( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
            var current = text_editor.val();
            text_editor.val(current + text);
        } else {
            tinyMCE.execCommand('mceInsertContent', false, text);
        }
    }
  

    tinymce.init({
        selector: '#recipe_description, #recipe_notes',
        theme: 'modern',
        language: 'fr_FR',
        statusbar: false,
        menubar:false,
        toolbar: 'undo redo | styleselect | bold italic underline | link image | alignleft aligncenter alignright | bullist | searchreplace',
        plugins: 'autoresize link spellchecker searchreplace placeholder lists',
        autoresize_bottom_margin : 20,
        placeholder_attrs : {style: {
                position: 'absolute',
                top:'5px',
                left:0,
                color: '#888',
                'font-style': 'italic',
                padding: '1%',
                width:'98%',
                overflow: 'hidden',
                'white-space': 'pre-wrap'
            }
        }
    });

    // Inline editor for instructions, however cannot work at the moment 
    // since not <div> but <textarea>
    // tinymce.init({
    //     selector: 'textarea.recipe-instruction',
    //     theme: 'modern',
    //     language: 'fr_FR',
    //     inline: true
    // });    


 /* Ingredient and Instruction Submission (from WPURP)
---------------------------------------------------------------- */   

    /*
     * Do not allow removal of first ingredient/instruction
     */
    // jQuery('#recipe-ingredients tr.ingredient:first').find('span.ingredients-delete').hide();
    // jQuery('#recipe-instructions tr.instruction:first').find('span.instructions-delete').hide();

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

    jQuery('.ingredients-delete').on('click', function(){
        jQuery(this).parents('tr').remove();
        // addRecipeIngredientOnTab();
        updateIngredientIndex();
    });


    jQuery('#ingredients-add').on('click', function(e){
        e.preventDefault();
        addRecipeIngredient();
    });


    jQuery('#recipe-ingredients').on('keydown','.ingredients_notes, .ingredient-group-label',function(e) {
        console.log("%c Found keypress on " + jQuery(this).attr('class') + jQuery(this).attr('id'),"background:#CCC;color:blue");
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9 && e.shiftKey == false) {
            // var last_id = jQuery('#recipe-ingredients tr:last').attr('id');
            // console.log("Last ID = " + last_id);
            var currentRow = jQuery(this).closest('tr');
            console.log( 'currentRow = ' + currentRow.prop('tagName') + currentRow.attr('class'));
            // var current_id = current_ingredient.attr('id');

            // Prevents that the focus jumps over the next "wrapped" ingredient
            e.preventDefault();
            if ( isLastIngredient( currentRow ) ) {
                // console.log("Found keypress on tr::last .ingredient_notes !!!");
                addRecipeIngredient( currentRow );
            } 
            else {
                console.log('ingredient.next.focus');
                currentRow.next().focus();
            }
        }
    });

    jQuery(document).on('keyup', 'input', function(e) {
        // if(e.keyCode == 13 && e.target.type !== 'submit') {
        if( e.keyCode == 13 ) {
            e.preventDefault();
            var inputs = jQuery(e.target).parents("form").eq(0).find(":input:visible"),
            idx = inputs.index(e.target);
            if (idx == inputs.length - 1) {
                inputs[0].select()
            } else {
                inputs[idx + 1].focus();
                inputs[idx + 1].select();
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
            e.preventDefault()
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

    // Check if tab key is pressed on instruction step
    jQuery('#recipe-instructions .instruction:last-of-type .instruction-text').on('keydown',function(e) {
        // console.log("Found keypress on .ingredient_notes !!!");
        var keyCode = e.keyCode || e.which;
      
        if (keyCode == 9 && e.shiftKey == false) {
            e.preventDefault();
            addRecipeInstruction();
        }
    });


    // Recipe Featured Image 
    jQuery(".recipe-image-container").on("change", "input.recipe_thumbnail_image", function() { 
        PreviewImage('');
    });


    // Instruction Step Image 
    jQuery(".recipe-instructions-container").on("change", "input.recipe_instructions_image", function() { 
        var changedSelectId = jQuery(this).attr("id");
        var Id = changedSelectId.match(/\d+/);
        // console.log( "Changement sur l'input..." + Id );
        PreviewImage(Id);
    });


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
function isLastIngredient( thisRow ) {
    console.log( '%c In isLastIngredient','background:#CCC;color:red;padding:10px;border-radius:2px');
    console.log( 'item = ' + thisRow.attr('id'));
    var hasGroupAfter = thisRow.next( ".ingredient-group").length;
    console.log( 'next group = ' + thisRow.next( ".ingredient-group").prop('tagName'));
    console.log( 'has group after = ' + hasGroupAfter);
    console.log("#recipe-ingredients tr:last");
    var isLastIngredient = ( thisRow.attr('id') == jQuery("#recipe-ingredients tr:last").attr('id') );
    console.log( 'is last ingredient = ' + isLastIngredient);

    return (hasGroupAfter || isLastIngredient);
}


function PreviewImage(id) { 
    var fileInput = document.getElementById("recipe_thumbnail_input_" + id);

    // console.log('Max file size ' + custom_user_submissions.maxFileSize);
    // console.log('Authorized file types ' + custom_user_submissions.fileTypes);
    // console.log('Authorized file types ', custom_user_submissions.fileTypes);
    // console.log('File too big msg :  ' + custom_user_submissions.fileTooBig);
    // console.log('Wrong File Type msg :  ' + custom_user_submissions.wrongFileType);

    if (fileInput.files && fileInput.files[0]) {
        var extension = fileInput.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
        isSuccess = custom_user_submissions.fileTypes.indexOf(extension) > -1;  //is extension i
        if ( isSuccess ) {
            var imgSize = fileInput.files[0].size/1024;
            console.log('File size (kB) = ' + document.getElementById("recipe_thumbnail_input_" + id).files[0].size/1024);
            if (imgSize < custom_user_submissions.maxFileSize) {
                var oFReader = new FileReader();
                oFReader.readAsDataURL(fileInput.files[0]);
                oFReader.onload = function (oFREvent) {
                    document.getElementById("recipe_thumbnail_preview_" + id ).src = oFREvent.target.result;
                }    
            }
            else {
                jQuery(fileInput).val('');
                alert(custom_user_submissions.fileTooBig);   
            }
        }
        else {
            jQuery(fileInput).val('');
            alert(custom_user_submissions.wrongFileType);
        }
    }
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


function updateIngredientIndex()
{
    jQuery('#recipe-ingredients tr.ingredient').each(function(i) {
        jQuery(this)
            .find('input, textarea')
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

function addRecipeIngredient( currentIngredient )
{
    var nbr_ingredients = jQuery('#recipe-ingredients tr.ingredient').length;
    var last_row = jQuery('#recipe-ingredients tr:last');
    if ( currentIngredient ) 
        last_row = currentIngredient;

    // last_ingredient.find('input').attr('placeholder','');
    // var last_ingredient = jQuery('#recipe-ingredients tr.ingredient:last');
    
    // var clone_ingredient = last_ingredient.clone(true);
    var clone_ingredient = last_row.clone(true);

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