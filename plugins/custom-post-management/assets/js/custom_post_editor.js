jQuery(document).ready(function() {
    // console.log( 'In post editor.js');

    postId = jQuery('#new_post input[name="submit_post_id"]').val();
    // console.log( 'post id ', postId);
    // console.log('custom_post_submission_form ', custom_post_submission_form);

    // Editor setup
    tinymce.init({
        selector: '#post_content',
        theme: 'silver',
        language: 'fr_FR',
        //IMPORTANT DO NOT USE AUTORESIZE PLUGIN SINCE IT WILL MAKE THE PLACEHOLDER OVERFLOW ON MOBILE !!!
        plugins: 'autoresize link spellchecker searchreplace table lists image',
        statusbar: false,
        menubar: false,
        toolbar: 'undo redo | styleselect | bold italic underline | link image | table | alignleft aligncenter alignright | bullist | searchreplace',
        // toolbar: 'table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
        // table plugin params
        table_header_type: 'cells',
        // general params
        autoresize_bottom_margin: 20,
        min_height: 400,
        placeholder_attrs : {style: {
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
            formData.append('security', cpm_editor.nonce);
            // formData.append('postid', cpm_editor.postid);
            formData.append('postid', postId );
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            jQuery.ajax({
                url: cpm_editor.ajaxurl,
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
                    console.log('Ajax json is : ', json);
                    if (json.success) {
                        console.log('Image Upload Success !!! ');
                        console.log('Location is : ', json.data.location);
                        success(json.data.location);
                    }
                    else {
                        console.log('Image Upload Failed ', json.data.msg);
                        failure(json.data.msg)
                    }
                }
            });

        }

    });

});

/* Functions Library
----------------------------------------------------- */

function addButtonToEditor(text) {
    if (!tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
        var text_editor = jQuery('#post_content');
        var current = text_editor.val();
        text_editor.val(current + text);
    } else {
        tinyMCE.execCommand('mceInsertContent', false, text);
    }
};
