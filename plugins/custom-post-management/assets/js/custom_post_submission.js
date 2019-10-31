/* Prevent loosing data when closing the page
---------------------------------------------------------------- */
var formSubmitting = false;
// Add onclick="formSubmitting=true;" to any link on your page to bypass the alert message

jQuery(window).bind('beforeunload', function (e) {
    console.log('before unload listener setup');
    if (formSubmitting) {
        console.log('Detected form submission, so no warning');
        return undefined;
    }
    return true;
});


jQuery(document).ready(function() {
    // console.log( 'In post submission.js');

    jQuery("#new_post").submit(function () {
        console.log('Form submission, set formSubmitting to true');
        formSubmitting = true;
    });


    /* Shortcode buttons
    --------------------------------------------------------------- */
    postId = jQuery('#new_post input[name="post_id"]');

    tinymce.init({
        selector: '#post_content',
        theme: 'modern',
        language: 'fr_FR',
        statusbar: false,
        menubar:false,
        toolbar: 'undo redo | styleselect | bold italic underline | link image | alignleft aligncenter alignright | bullist | searchreplace',
        plugins: 'autoresize link spellchecker searchreplace placeholder lists image',
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
        },
        images_upload_handler: function (blobInfo, success, failure) {

            var formData = new FormData();
            formData.append('action', 'cpm_tinymce_upload_image');
            formData.append('security', custom_post_submission_form.nonce);
            // formData.append('postid', custom_post_submission_form.postid);
            formData.append('postid', postId.val() );
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            jQuery.ajax({
                url: custom_post_submission_form.ajaxurl,
                xhrFields: {
                    withCredentials: false
                },
                type: 'POST',
                data: formData,
                async: true,
                cache: false,
                processData: false,
                contentType: false,
                dataType: 'json',
                // enctype: 'multipart/form-data',
                success: function (json, textStatus, jqXHR) {
                    console.log('Image Upload Success !!! ');
                    console.log('Location is : ', json.location);
                    success(json.location);
                }
            });

        }

    });

    /* Image management
    ---------------------------------------------------*/

    // Post Featured Image
    jQuery("#custom_post_submission_form").on("change", "input.post_thumbnail_image", function() {
        console.log('Change on featured image input detected')
        PreviewImage('');
    });

    // Remove Image
    jQuery("#custom_post_submission_form").on("click", ".post_remove_image_button", function () {

        console.log('Click on remove image');
        if (!confirm(custom_post_submission_form.deleteImage)) return;
        var Id = jQuery(this).attr('id').match(/\d+/);
        Id = (Id !== null) ? Id : '';
        thisPreview = jQuery('#post_thumbnail_preview_' + Id);
        thisInput = jQuery('#post_thumbnail_input_' + Id);
        postId = jQuery('#new_post input[name="post_id"]');
        console.log('thisPreview = ', thisPreview);
        console.log('thisInput = ', thisInput);

        var data = {
            action: 'cpm_remove_featured_image',
            security: custom_post_submission_form.nonce,
            postid: postId.val(),
            thumbid: Id
        };

        jQuery.post(
            custom_post_submission_form.ajaxurl,
            data,
            function (response) {
                console.log('Ajax call suceeded image removed !', response);
                thisPreview.removeAttr('src');
                thisPreview.parents('.thumbnail').addClass('nodisplay');
                thisInput.val('');
            }
        );

    });

});


/* Functions Library
----------------------------------------------------- */

function PreviewImage(id) {
    console.log('Entering PreviewImage');
    var fileInput = document.getElementById("post_thumbnail_input_" + id);

    // console.log('File input search string : ', "post_thumbnail_input_" + id);
    // console.log('File input : ', fileInput);
    // console.log('Max file size ' + custom_post_submission_form.maxFileSize);
    // console.log('Authorized file types ' + custom_post_submission_form.fileTypes);
    // console.log('Authorized file types ', custom_post_submission_form.fileTypes);
    // console.log('File too big msg :  ' + custom_post_submission_form.fileTooBig);
    // console.log('Wrong File Type msg :  ' + custom_post_submission_form.wrongFileType);

    if (fileInput.files && fileInput.files[0]) {
        var extension = fileInput.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
        isSuccess = custom_post_submission_form.fileTypes.indexOf(extension) > -1;  //is extension i
        if ( isSuccess ) {
            var imgSize = fileInput.files[0].size/1024;
            console.log('File size (kB) = ' + fileInput.files[0].size/1024);
            if (imgSize < custom_post_submission_form.maxFileSize) {
                var oFReader = new FileReader();
                oFReader.readAsDataURL(fileInput.files[0]);
                oFReader.onload = function (oFREvent) {
                    document.getElementById("post_thumbnail_preview_" + id ).src = oFREvent.target.result;
                    jQuery("#post_thumbnail_preview_" + id).parent(".thumbnail").removeClass('nodisplay');
                    // jQuery("#recipe_instruction_" + id +" .instruction-image" ).removeClass('nodisplay');
                }
            }
            else {
                jQuery(fileInput).val('');
                alert(custom_post_submission_form.fileTooBig);
            }
        }
        else {
            jQuery(fileInput).val('');
            alert(custom_post_submission_form.wrongFileType);
        }
    }
};


function addButtonToEditor(text) {
    if (!tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
        var text_editor = jQuery('#post_content');
        var current = text_editor.val();
        text_editor.val(current + text);
    } else {
        tinyMCE.execCommand('mceInsertContent', false, text);
    }
};
