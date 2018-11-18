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

});