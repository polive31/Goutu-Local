/* Prevent loosing data when closing the page
---------------------------------------------------------------- */
var formSubmitting = false;
// Add onclick="formSubmitting=true;" to any link on your page to bypass the alert message


var xhr_remove_image = null;
var xhr_upload_image = null;

jQuery(window).bind('beforeunload', function (e) {
    console.log('Detected that user is leaving page...');
    if (formSubmitting) {
        console.log('... this is a form submission, so no warning');
        return undefined;
    }
    return true;
});


jQuery(document).ready(function() {
    console.log('%c In post submission script', 'background: #7ff; color: blue');

    jQuery("#new_post").submit(function () {
        console.log('Form submission, set formSubmitting to true');
        formSubmitting = true;
    });

    var postId = jQuery('#new_post input[name="submit_post_id"]').val();
    var postType = jQuery('#new_post input[name="submit_post_type"]').val();
    console.table([{ 'post id': postId, 'postType': postType }]);
    // console.log( 'custom_post_submission_form ', cpm_submission);


    /* Image management
    ---------------------------------------------------*/
    /* Add image to the post, it can be the main featured image
       or another one, as long as two rules are respected :
       1) File input button must have the "post_image_thumbnail" class set
       2) File input button must have an id of the form "post_thumbnail_input_xxx" */
    jQuery("#custom_post_submission_form").on("change", "input.post_image_thumbnail", function() {
        var imageId = jQuery(this).attr("id");
        var Id = imageId.match(/\d+/);
        Id = (Id!=null)?Id:'featured';
        console.log("%c Click on add image " + Id, 'background: #7ff; color: blue' );
        SaveThumbnail(Id, postId, postType);
    });


    // Remove Image
    jQuery("#custom_post_submission_form").on("click", ".post_remove_image_button", function () {
        if (!confirm(cpm_submission.deleteImage)) return;
        var Id = jQuery(this).attr('id').match(/\d+/);
        Id = (Id !== null) ? Id[0] : 'featured';
        console.log("%c Click on remove image " + Id, 'background: #7ff; color: blue');
        RemoveThumbnail(Id, postId, postType);
    });

});


/* Functions Library
----------------------------------------------------- */

function SaveThumbnail(id, postId, postType) {
    console.log('%c Entering SaveThumbnail', 'background: #7ff; color: blue');
    var fileInput = document.getElementById("post_thumbnail_input_" + id);

    if (fileInput.files && fileInput.files[0]) {
        var extension = fileInput.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
        isSuccess = cpm_submission.fileTypes.indexOf(extension) > -1;  //is extension i
        if ( isSuccess ) {
            var imgSize = fileInput.files[0].size/1024;
            // console.log('File size (kB) = ' + fileInput.files[0].size/1024);
            if (imgSize < cpm_submission.maxFileSize) {
                UploadAndAttachImage(id, postId, postType);
            }
            else {
                jQuery(fileInput).val('');
                alert(cpm_submission.fileTooBig);
            }
        }
        else {
            jQuery(fileInput).val('');
            alert(cpm_submission.wrongFileType);
        }
    }
}


function RemoveThumbnail(id, postId, postType) {
    var $Preview = jQuery('#post_thumbnail_preview_' + id);
    var $InputButton = jQuery('#post_thumbnail_input_' + id);
    if (id=='featured')
        var $imageInput = jQuery('#' + postType + '_image_attachment');
    else
        var $imageInput = jQuery('#' + postType + '_image_attachment_' + id);

    var data = {
        action: 'cpm_remove_' + postType + '_image',
        security: cpm_submission.nonce,
        postId: postId,
        thumbId: id,
        attachId: $imageInput.val(),
    };

    // try {
    //     xhr_remove_image.abort();
    //     console.log("%c Ajax 'remove image' call already ongoing, aborting.", 'background: #7ff; color: blue');
    // }
    // catch (e) {
    //     console.log("%c No previous 'remove image' ajax call, ...", 'background: #7ff; color: blue');
    // }
    xhr_remove_image = jQuery.post(
        cpm_submission.ajaxurl,
        data,
        function (response) {
            if (response.success) {
                console.log('%c Ajax call suceeded image removed ! Response is ', 'background: #7ff; color: blue' , response.data);
                $Preview.removeAttr('src');
                $Preview.parents('.thumbnail').addClass('nopic');
                $InputButton.val('');
                $imageInput.val('');
            }
            else {
                console.log('Ajax call failed', response.data.msg);
            }
        }
        );
    }


    function UploadAndAttachImage(id, postId, postType) {

        var $Preview = jQuery('#post_thumbnail_preview_' + id);
        if (id=='featured')
            var $imageInput = jQuery('#' + postType + '_image_attachment');
        else
            var $imageInput = jQuery('#' + postType + '_image_attachment_' + id);

        var $previewContainer = $Preview.parent(".thumbnail");
        var postTitle = jQuery('#post_title').val();

        $previewContainer.addClass('loading');

        var fileInput = jQuery('#post_thumbnail_input_' + id).prop('files')[0];
        var form_data = new FormData();

        form_data.append('action', 'cpm_upload_' + postType + '_image');
        form_data.append('security', cpm_submission.nonce);
        form_data.append('postId', postId);
        form_data.append('thumbId', id);
        form_data.append('imageAlt', postTitle);
        form_data.append('file', fileInput);

        // try {
        //     xhr_upload_image.abort();
        //     console.log("%c Ajax 'upload image' call already ongoing, aborting.", 'background: #7ff; color: blue');
        // }
        // catch (e) {
        //     console.log("%c No previous 'upload image' ajax call, ...", 'background: #7ff; color: blue');
        // }

        xhr_upload_image = jQuery.ajax({
        url: cpm_submission.ajaxurl,
        type: 'post',
        contentType: false,
        processData: false,
        data: form_data,
        success: function (response) {
            if (response.success) {
                console.log('%c Upload succeeded, src = ' + response.data.src, 'background: #7ff; color: blue' );
                $previewContainer.removeClass('loading');
                $Preview.attr('src', response.data.src);
                $previewContainer.removeClass('nopic');
                $imageInput.val(response.data.attachId);
            }
            else {
                console.log('%c Ajax call FAILED. Response is ', 'background: #7ff; color: blue', response);
            }
        },
    });

}
