var currentInstance = 0;

jQuery(document).ready(function () {

    console.log('In ccm-validation.js');
    // jQuery(document).on('submit', '.comment-form', function (e) {

    jQuery(document).on('click', '.comment-reply a', function(e) {
        console.log( 'Click on comment reply !!!');
        var replyTo = jQuery(this).data('replyto');
        var $replyTitle = jQuery('#reply-title');
        var initialTitle = $replyTitle.contents().first().text();
        var $cancelLink = jQuery('#cancel-comment-reply-link');
        $replyTitle.contents().first().replaceWith(replyTo);
        $cancelLink.attr( 'data-title', initialTitle);
    });

    jQuery(document).on('click', '#cancel-comment-reply-link', function(e) {
        console.log( 'Click on reply cancel !!!');
        var initialTitle = jQuery(this).data('title');
        var $replyTitle = jQuery('#reply-title');
        $replyTitle.contents().first().replaceWith(initialTitle);
    });


    jQuery(document).on('click', 'form.comment-form .submit, form.rating-form .submit', function (e) {
        e.preventDefault();
        // e.stopPropagation();
        console.log('In ccm-validation.js, click on comment form submit');

        var commentForm = jQuery(this).closest('form');
        var commentText = commentForm.find('textarea');
        var commentAuthor = commentForm.find('input.author');
        var commentEmail = commentForm.find('input.email');
        var recaptcha = commentForm.find('.g-recaptcha');
        var currentInstance = jQuery(this).data("instance");

        console.log( 'Comment form is ', commentForm );
        console.log('Recaptcha is ', recaptcha);
        console.log('Form instance is ', currentInstance);
        console.log( 'Comment text val is ', commentText.val() );
        console.log('Author name val is ', commentAuthor.val() );
        console.log('Email address val is ', commentEmail.val() );

        if (!commentText.val().length) {
            alert( csr.emptyComment );
        }
        else if (commentAuthor.length && !commentAuthor.val().length) {
            alert(csr.emptyAuthor);
        }
        else if (commentEmail.length && (!commentEmail.val().length || !csrValidateEmail( commentEmail.val() )) ) {
            alert( csr.invalidEmail );
        }
        else {
            console.log('Submit the form');
            /* Check if recaptcha present */

            // if (recaptcha.length) {
            //     console.log('Recaptcha found, running challenge');
            //     grecaptcha.execute();
            // }
            // else {
            //     console.log('No Recaptcha found, submitting form directly');
                commentForm.submit();
            // }
        }
    });
});

function csrValidateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
    return emailReg.test($email);
}

function csrOnSubmit(token) {
    console.log('in csrOnSubmit, recaptcha successful !!!');
    var form = jQuery('form#foodiepro_comment' + currentInstance);
    // formId = "foodiepro_comment" + currentInstance;
    // console.log('Form ID is : ', formId);
    // form = document.getElementById(formId);
    console.log('Form is : ', form);
    form.submit();
}
