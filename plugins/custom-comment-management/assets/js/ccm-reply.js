jQuery(document).ready(function () {
    // console.log('In ccm-reply.js');

    const config={
        // Comment Form fields
        commentField: 'textarea',
        parentID: '#comment_parent',
        replyTitle: '#reply-title',
        newTitle: '#new-title',
        commentReply: '.comment-reply-link',
        cancelReply: '#cancel-comment-reply-link',
        anchorTag: 'wp-temp-form-div',
        // Replied Comment fields
        replyInfo: '.comment-reply a',
        replyContainer: '.comment-item'
    }

    // Initialize comment form objects
    var $commentForm = jQuery('.content #respond');
    var $form = {
        commentField: $commentForm.find(config.commentField),
        parentID: $commentForm.find(config.parentID),
        replyTitle: $commentForm.find(config.replyTitle),
        newTitle: $commentForm.find(config.newTitle),
        cancelReply: $commentForm.find(config.cancelReply),
    }

    jQuery(document).on('click', config.commentReply, function (e) {
        // e.preventDefault();
        // console.log('Click on comment reply button !!!');

        // Retrieve respond form data
        var $respondComment = jQuery(this).closest('li.comment');
        var $respond = {
                info: $respondComment.find(config.replyInfo),
                container: $respondComment.children(config.replyContainer),
            };
        var respondComment = {
                Id : $respond.info.data('commentid'),
                belowElement : $respond.info.data('belowelement'),
                author: $respondComment.data('author'),
            };

        //  Update comment form title
        var replyText = $form.replyTitle.data('text');

        // Update the reply text with the author name */
        replyText=replyText.replace('%s', respondComment.author)

        // Remove any text below the <h3> tag, without deleting the <small>...</small> markup containing the cancelbutton !
        // document.getElementById("reply-title").childNodes[0].nodeValue="";
        $form.replyTitle.contents().get(0).nodeValue=""

        //  Insert correct title text at the beginning of the <h3> tag
        $form.replyTitle.prepend(replyText);

        // Set proper visibility for each element
        $form.newTitle.hide();
        $form.replyTitle.show();
        $form.cancelReply.show();

        //  Update comment form submit button to indicate a parent
        $form.parentID.val(respondComment.Id);

        // If it doesn't exist already, create an anchor for the initial position of the form to be found easily later
        $commentForm.before('<div id="' + config.anchorTag + '"></div>');

        //  Move comment form under the replied comment
        $respond.container.append($commentForm);
    });

    jQuery(document).on('click', config.cancelReply, function (e) {
        // e.preventDefault();
        // console.log('Click on reply cancel button !!!');
        var $anchor = jQuery('#' + config.anchorTag);

        // Set proper visibility for each element
        $form.replyTitle.hide();
        $form.cancelReply.hide();
        $form.newTitle.show();

        //  Update comment form submit button to remove the parent
        $form.parentID.val(0);

        //  Empty the content of the form
        $form.commentField.val('');

        //  Move comment form under the replied comment
        $anchor.after($commentForm);

    });

});
