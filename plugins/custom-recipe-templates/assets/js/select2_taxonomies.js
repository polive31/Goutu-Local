jQuery(document).ready(function() {

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
    var readonlyBoxes = jQuery(".taxonomy-select-boxes select").not(".post_tag");

    readonlyBoxes.select2({
        width: 'off',
        dropdownAutoWidth: false,
        minimumResultsForSearch: -1,
        allowClear: false,
        templateSelection: formatItem,
        // closeOnSelect: false,
    }); 

    readonlyBoxes.next('.select2').addClass("readonly");

    jQuery(".taxonomy-select-boxes select.post_tag").select2({
        width: 'off',
        dropdownAutoWidth: false,
        // minimumInputLength: 3,
        allowClear: false,
        templateSelection: formatItem,        
    });

    function formatItem(data, container) {
        var elClass='';
        if (data.element.value == -1 || data.element.value == "") {
           elClass='class="option-none"';
        }
        var $state = jQuery('<span ' + elClass + '>' + data.text + '</span>');
        return $state;
    }

// Disable search option on some dropdowns
    jQuery('.taxonomy-select-boxes .select2.readonly').on('select2:opening select2:closing', function( event ) {
        var $searchfield = jQuery(this).parent().find('.select2-search__field');
        // $searchfield.prop('disabled', true);
    });
    jQuery(".select2.readonly .select2-search input").prop("readonly", true);
    // jQuery(".select2, .select2-multiple").not(".post_tag").on('select2:open', function (e) {
    //     jQuery('.select2-search input').not(".post_tag").prop('focus',false);
    // });

// Hide loading indicator once select2 is started 
    jQuery('.taxonomy-select-boxes').removeClass('nodisplay');
    jQuery('.taxonomy-select-spinner').addClass('nodisplay');

});