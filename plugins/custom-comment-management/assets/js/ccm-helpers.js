jQuery(document).ready(function () {

    console.log('%c In ccm-helpers.js', "background:#D75AA8; color:white");
    jQuery('.content .comment-form .comment-form-author').hide();
    jQuery('.content .comment-form .comment-form-email').hide();
    jQuery('.content .comment-form .recaptcha-container').hide();

    jQuery('.content').on('click', '.comment-form textarea', function(e) {
        console.log('%c Click on comment form textarea, unfolding', "background:#D75AA8; color:white");
        jQuery('.content .comment-form .comment-form-author').show();
        jQuery('.content .comment-form .comment-form-email').show();
        jQuery('.content .comment-form .recaptcha-container').show();
    });

    jQuery(document).on('click', '.comment-reply a', function(e) {
        console.log('%c Click on comment reply', "background:#D75AA8; color:white");
        var replyTo = jQuery(this).data('replyto');
        var $replyTitle = jQuery('#reply-title');
        var initialTitle = $replyTitle.contents().first().text();
        var $cancelLink = jQuery('#cancel-comment-reply-link');
        $replyTitle.contents().first().replaceWith(replyTo);
        $cancelLink.attr( 'data-title', initialTitle);
    });

    jQuery(document).on('click', '#cancel-comment-reply-link', function(e) {
        console.log('%c Click on reply cancel', "background:#D75AA8; color:white");
        var initialTitle = jQuery(this).data('title');
        var $replyTitle = jQuery('#reply-title');
        $replyTitle.contents().first().replaceWith(initialTitle);
    });


    jQuery(document).on('click', 'form.comment-form .submit, form.rating-form .submit', function (e) {
        e.preventDefault();
        // e.stopPropagation();
        console.log('%c Click on comment form submit', "background:#D75AA8; color:white");

        var $commentForm = jQuery(this).closest('form');
        var $commentText = $commentForm.find('textarea');
        var $commentAuthor = $commentForm.find('input.author');
        var $commentEmail = $commentForm.find('input.email');
        var $recaptcha = $commentForm.find('.recaptcha-container');
        var currentInstance = jQuery(this).data("instance");

        console.log( '%c * Comment form is '     , "background:#D75AA8; color:white" , $commentForm );
        console.log( '%c * Recaptcha is '        , "background:#D75AA8; color:white" , $recaptcha);
        console.log( '%c * Form instance is '    , "background:#D75AA8; color:white" , currentInstance);
        console.log( '%c * Comment text val is ' , "background:#D75AA8; color:white" , $commentText );
        console.log( '%c * Author name val is '  , "background:#D75AA8; color:white" , $commentAuthor );
        console.log( '%c * Email address val is ', "background:#D75AA8; color:white" , $commentEmail );

        if (!$commentText.val().length) {
            alert( csr.emptyComment );
            return;
        }
        else if ($commentAuthor.length && !$commentAuthor.val().length) {
            alert(csr.emptyAuthor);
            return;
        }
        else if ($commentEmail.length && (!$commentEmail.val().length || !csrValidateEmail( $commentEmail.val() )) ) {
            alert( csr.invalidEmail );
            return;
        }

        // Maybe captcha verification
        if ( $recaptcha.length ) {
            if ($recaptcha.hasClass('math') && !$commentForm.find('#captcha_math_answer').val().length ) {
                alert( csr.emptyRecaptcha );
                return;
            }
            else if ($recaptcha.hasClass('google')) {
                var instance = $recaptcha.data('instance');
                var response = grecaptcha.getResponse(instance);
                console.log('%c Google recaptcha found, value is ', "background:#D75AA8; color:white", response);
                if (response.length==0) {
                    alert(csr.emptyRecaptcha);
                    return;
                }
            }
        }

        console.log('%c All preconditions are met, submit the form', "background:#D75AA8; color:white");
        $commentForm.submit();


    });
});

function csrValidateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
    return emailReg.test($email);
}
