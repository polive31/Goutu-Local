/* Prevent loosing data when closing the page
---------------------------------------------------------------- */
var formSubmitting = false;
jQuery(window).bind('beforeunload', function (e) {
    console.log('before unload listener setup');
    if (formSubmitting) {
        console.log( 'Detected form submission, so no warning');
        return undefined;
    }
    return true;
});

jQuery(document).ready(function() {

    jQuery("#new_post").submit(function () {
        console.log( 'Form submission, set formSubmitting to true');
        formSubmitting = true;
    });


    /* Autoselect "numeric" inputs on focux
    ---------------------------------------------------------------- */
    jQuery('input.selectonfocus').focus(function() {
        // console.log('select on focus !!!');
        jQuery(this).select();
    });


    /* Auto adjust textarea height
    ---------------------------------------------------------------- */
    /* On startup */
    jQuery(function () {
        jQuery('#recipe-instructions textarea').each(function () {
            jQuery(this).height(jQuery(this)[0].scrollHeight);
        });
    });

    /* On input/textarea change */
    jQuery('#recipe-instructions').on('input', 'textarea', function () {
        console.log('Auto adjust height on edit');
        jQuery(this).outerHeight(38).outerHeight(this.scrollHeight); // 38 or '1em' -min-height
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

    // jQuery('#wpurp-insert-recipe').on('click', function () {
    //     var shortcode = '[ultimate-recipe id=';

    //     shortcode += jQuery('#wpurp-recipe').find('option:selected').val();
    //     shortcode += ']';

    //     tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
    //     tinyMCE.activeEditor.windowManager.close();
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
        cursor: 'grabbing',
        handle: '.sort-handle',
        update: function() {
            // addRecipeIngredientOnTab();
            calculateIngredientGroups();
            updateIngredientIndex();
        }
    });

    jQuery('.ingredients-delete').on('click', function(){
        if (!confirm(custom_recipe_submission_form.deleteIngredient)) return;
        jQuery(this).parents('tr').remove();
        // addRecipeIngredientOnTab();
        updateIngredientIndex();
    });


    jQuery('#ingredients-add').on('click', function(e){
        e.preventDefault();
        addRecipeIngredient();
    });


    jQuery('#recipe-ingredients').on('keydown','.ingredients_notes',function(e) {
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
        // var last_id = jQuery('#recipe-ingredients tr:last').attr('id');
        var current_ingredient = jQuery(this).closest('tr.ingredient');
        var current_id = current_ingredient.attr('id');
        console.log ("Current ingredient : " + current_id);

        if (keyCode == 9 && e.shiftKey == true) {
            e.preventDefault()
            // console.log("Keypress shift !");
            var previous_ingredient = current_ingredient.prev();
            previous_ingredient.focus();
            // var prev_id = previous_ingredient.attr('id');
            // console.log ("Previous ingredient : " + prev_id);
        }
    });

    /*
     * Recipe instructions
     * */
    jQuery('#recipe-instructions tbody').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'grabbing',
        handle: '.sort-handle',
        update: function() {
            // addRecipeInstructionOnTab();
            calculateInstructionGroups();
            updateInstructionIndex();
        }
    });

    jQuery('.instructions-delete').on('click', function(){
        if (!confirm(custom_recipe_submission_form.deleteInstruction)) return;
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

    // Remove Image
    jQuery("#custom_post_submission_form").on("click", ".recipe_remove_image_button ", function () {

        console.log('Click on remove recipe image');
        if (!confirm(custom_recipe_submission_form.deleteImage)) return;
        var Id = jQuery(this).attr('id').match(/\d+/);
        Id = (Id !== null) ? Id[0] : '';
        thisPreview = jQuery('#recipe_thumbnail_preview_' + Id);
        thisInput = jQuery('#recipet_thumbnail_input_' + Id);
        postId = jQuery('#new_post input[name="post_id"]');
        console.log('thisPreview = ', thisPreview);
        console.log('thisInput = ', thisInput);
        console.log('thumbid = ', Id);

        var data = {
            action: 'crm_remove_recipe_image',
            security: custom_recipe_submission_form.nonce,
            postid: postId.val(),
            thumbid: Id
        };

        console.log('data : ', data);

        jQuery.post(
            custom_recipe_submission_form.ajaxurl,
            data,
            function (response) {
                console.log('Ajax call suceeded image removed !', response);
                thisPreview.removeAttr('src');
                thisPreview.parents('.thumbnail').addClass('nodisplay');
                thisInput.val('');
            }
        );

    });



    /* Image management
    ---------------------------------------------------*/

    // Recipe Featured Image
    jQuery("#custom_post_submission_form").on("change", "input.post_thumbnail_image", function () {
        PreviewImage('');
    });

    // Instruction Step Image
    jQuery("#custom_post_submission_form").on("change", "input.recipe_instructions_image", function() {
        var InstructionId = jQuery(this).attr("id");
        var Id = InstructionId.match(/\d+/);
        // console.log( "Changement sur l'input..." + Id );
        PreviewImage(Id);
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
    console.log('Entering PreviewImage');
    var fileInput = document.getElementById("recipe_thumbnail_input_" + id);

    // console.log('Max file size ' + custom_recipe_submission_form.maxFileSize);
    // console.log('Authorized file types ' + custom_recipe_submission_form.fileTypes);
    // console.log('Authorized file types ', custom_recipe_submission_form.fileTypes);
    // console.log('File too big msg :  ' + custom_recipe_submission_form.fileTooBig);
    // console.log('Wrong File Type msg :  ' + custom_recipe_submission_form.wrongFileType);

    if (fileInput.files && fileInput.files[0]) {
        var extension = fileInput.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
        isSuccess = custom_recipe_submission_form.fileTypes.indexOf(extension) > -1;  //is extension i
        if ( isSuccess ) {
            var imgSize = fileInput.files[0].size/1024;
            console.log('File size (kB) = ' + document.getElementById("recipe_thumbnail_input_" + id).files[0].size/1024);
            if (imgSize < custom_recipe_submission_form.maxFileSize) {
                var oFReader = new FileReader();
                oFReader.readAsDataURL(fileInput.files[0]);
                oFReader.onload = function (oFREvent) {
                    document.getElementById("recipe_thumbnail_preview_" + id ).src = oFREvent.target.result;
                    jQuery("#recipe_thumbnail_preview_" + id).parent(".thumbnail").removeClass('nodisplay');
                }
            }
            else {
                jQuery(fileInput).val('');
                alert(custom_recipe_submission_form.fileTooBig);
            }
        }
        else {
            jQuery(fileInput).val('');
            alert(custom_recipe_submission_form.wrongFileType);
        }
    }
};


function addRecipeIngredientGroup() {
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

function addRecipeIngredient( currentIngredient ) {

    console.log( 'In addrecipeIngredient' );

    var nbr_ingredients = jQuery('#recipe-ingredients tr.ingredient').length;
    var last_row = jQuery('#recipe-ingredients tr:last');
    var last_ingredient = jQuery('#recipe-ingredients tr.ingredient:last');

    console.log( 'last row : ', last_row);
    console.log( 'last ingredient : ', last_ingredient);

    if ( currentIngredient ) {
        last_row = currentIngredient;
        last_ingredient = currentIngredient;
    }

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
        .attr('name', function(index, name) {
            if (name!=null)
            return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function(index, id) {
            if (id!=null)
            return id.replace(/(\d+)/, nbr_instructions);
        });

    new_instruction
        .find('input')
        .val('')
        .attr('name', function(index, name) {
            if (name!=null)
            return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function(index, id) {
            if (id!=null)
            return id.replace(/(\d+)/, nbr_instructions);
        });

    new_instruction
        .find('textarea')
        .val('')
        .attr('name', function (index, name) {
            if (name != null)
                return name.replace(/(\d+)/, nbr_instructions);
        })
        .attr('id', function (index, id) {
            if (id != null)
                return id.replace(/(\d+)/, nbr_instructions);
        });

    new_instruction
        .find('.instruction-image > *')
        .attr('id', function (index, id) {
            if (id != null)
                return id.replace(/(\d+)/, nbr_instructions);
        });

    new_instruction
        .find('.instruction-image')
        .addClass('nodisplay')

    new_instruction.find('span.instructions-delete').show();

    jQuery('#recipe-instructions tr:last textarea').focus();
    calculateInstructionGroups();

}


function updateInstructionIndex()
{
    jQuery('#recipe-instructions tr.instruction').each(function(i) {
        jQuery(this)
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            });

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
            })
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            });

        jQuery(this)
            .find('.instruction_thumbnail')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, i);
            });

        jQuery(this)
            .find('.post_thumbnail')
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
            });

        jQuery(this)
            .find('.recipe_remove_image_button')
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, i);
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
