//TODO Refactor this asap.
jQuery(document).ready(function() {

    /* 
     * Display taxonomy select fields
     */
    jQuery('.taxonomy-select-boxes').removeClass('nodisplay');
    jQuery('.taxonomy-select-spinner').addClass('nodisplay');

    /* 
     * Submit buttons
     */
    // jQuery('.formbutton').on('click', function() {
    //     console.log( 'CLICK');
    //     console.log( jQuery(this).html());
    //     jQuery(this).children('input').val(true);
    //     // jQuery('#new_recipe').submit();
    // });

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

    jQuery('.ingredient-group-delete').on('click', function(){
        jQuery(this).parents('tr').remove();

        calculateIngredientGroups();
    });

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

    /*
     * Recipe ingredients
     * */
    jQuery('#recipe-ingredients tbody').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.sort-handle',
        update: function() {
            addRecipeIngredientOnTab();
            calculateIngredientGroups();
            updateIngredientIndex();
        }
    });

    // Hide AutoSuggest box on TAB or click
    jQuery('#recipe-ingredients').on('keydown', function(e) {
        var keyCode = e.keyCode || e.which;

        if (keyCode == 9) {
            jQuery('ul.ac_results').hide();
        }
    });
    jQuery('#recipe-ingredients').on('click', function() {
        jQuery('ul.ac_results').hide();
    });

    jQuery('.ingredients-delete').on('click', function(){
        jQuery(this).parents('tr').remove();
        addRecipeIngredientOnTab();
        updateIngredientIndex();
    });

    jQuery('#ingredients-add').on('click', function(e){
        e.preventDefault();
        addRecipeIngredient();
    });

    function addRecipeIngredient()
    {
        var nbr_ingredients = jQuery('#recipe-ingredients tr.ingredient').length;
        var last_row = jQuery('#recipe-ingredients tr:last');
        var last_ingredient = jQuery('#recipe-ingredients tr.ingredient:last');

        last_ingredient.find('input').attr('placeholder','');
        var clone_ingredient = last_ingredient.clone(true);

        clone_ingredient
            .insertAfter(last_row)
            .find('input, select').val('')
            .attr('name', function(index, name) {
                return name.replace(/(\d+)/, nbr_ingredients);
            })
            .attr('id', function(index, id) {
                return id.replace(/(\d+)/, nbr_ingredients);
            })
            .parent().find('input.ingredients_name')
            .attr('onfocus', function(index, onfocus) {
                return onfocus.replace(/(\d+)/, nbr_ingredients);
            });

        clone_ingredient.find('span.ingredients-delete').show();

        addRecipeIngredientOnTab();

        jQuery('#recipe-ingredients tr:last .ingredients_amount').focus();
        calculateIngredientGroups();
    }

    addRecipeIngredientOnTab();
    function addRecipeIngredientOnTab()
    {
        jQuery('#recipe-ingredients .ingredients_notes')
            .unbind('keydown')
            .last()
            .bind('keydown', function(e) {
                var keyCode = e.keyCode || e.which;

                if (keyCode == 9) {
                    e.preventDefault();
                    addRecipeIngredient();
                }
            });
    }

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
                .parent().find('input.ingredients_name')
                .attr('onfocus', function(index, onfocus) {
                    return onfocus.replace(/(\d+)/, i);
                });
        });
    }

    /*
     * Recipe instructions
     * */
    jQuery('#recipe-instructions tbody').sortable({
        opacity: 0.6,
        revert: true,
        cursor: 'move',
        handle: '.sort-handle',
        update: function() {
            addRecipeInstructionOnTab();
            calculateInstructionGroups();
            updateInstructionIndex();
        }
    });

    jQuery('.instructions-delete').on('click', function(){
        jQuery(this).parents('tr').remove();
        addRecipeInstructionOnTab();
        updateInstructionIndex();
    });

    jQuery('#instructions-add').on('click', function(e){
        e.preventDefault();
        addRecipeInstruction();
    });

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
        addRecipeInstructionOnTab();

        jQuery('#recipe-instructions tr:last textarea').focus();
        calculateInstructionGroups();

    }

    addRecipeInstructionOnTab();
    function addRecipeInstructionOnTab()
    {
        jQuery('#recipe-instructions textarea')
            .unbind('keydown')
            .last()
            .bind('keydown', function(e) {
                var keyCode = e.keyCode || e.which;

                if (keyCode == 9 && e.shiftKey == false) {
                    var last_focused = jQuery('#recipe-instructions tr:last').find('textarea').is(':focus')

                    if(last_focused == true) {
                        e.preventDefault();
                        addRecipeInstruction();
                    }

                }
            });
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

    jQuery('.recipe_instructions_add_image').on('click', function(e) {

        e.preventDefault();

        var button = jQuery(this);

        image = button.siblings('.recipe_instructions_image');
        preview = button.siblings('.recipe_instructions_thumbnail');

        if(typeof wp.media == 'function') {
            var custom_uploader = wp.media({
                title: 'Insert Media',
                button: {
                    text: 'Add instruction image'
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

    jQuery('.recipe_instructions_remove_image').on('click', function(e) {
        e.preventDefault();

        var button = jQuery(this);

        button.siblings('.recipe_instructions_image').val('').trigger('change');
        button.siblings('.recipe_instructions_thumbnail').attr('src', wpurp_recipe_form.coreUrl + '/img/image_placeholder.png');
    });

    jQuery('.recipe_instructions_image').on('change', function() {
        var image = jQuery(this);
        if(image.val() == '') {
            image.siblings('.recipe_instructions_add_image').removeClass('wpurp-hide');
            image.siblings('.recipe_instructions_remove_image').addClass('wpurp-hide');
        } else {
            image.siblings('.recipe_instructions_remove_image').removeClass('wpurp-hide');
            image.siblings('.recipe_instructions_add_image').addClass('wpurp-hide');
        }
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


    // Select2
    jQuery('#wpurp_user_submission_form select[multiple]').select2wpurp({
        allowClear: true,
        width: 'off',
        dropdownAutoWidth: false
    });


});