var currentInstance = 0;

jQuery(document).ready(function () {

    console.log('In ccm-validation.js');
    // jQuery(document).on('submit', '.comment-form', function (e) {

    jQuery(document).on('click', 'form.comment-form .submit, form.rating-form .submit', function (e) {
        e.preventDefault();
        // e.stopPropagation();
        console.log('In ccm-validation.js, click on comment form submit');

        commentForm = jQuery(this).closest('form');
        commentText = commentForm.find('textarea');
        commentAuthor = commentForm.find('input.author');
        commentEmail = commentForm.find('input.email');
        recaptcha = commentForm.find('.g-recaptcha');
        currentInstance = jQuery(this).data("instance");

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
    form = jQuery('form#foodiepro_comment' + currentInstance);
    // formId = "foodiepro_comment" + currentInstance;
    // console.log('Form ID is : ', formId);
    // form = document.getElementById(formId);
    console.log('Form is : ', form);
    form.submit();
}
