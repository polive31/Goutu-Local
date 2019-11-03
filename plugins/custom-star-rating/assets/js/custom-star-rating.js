var currentInstance = 0;

jQuery(document).ready(function () {

    // jQuery(document).on('submit', '.comment-form', function (e) {

    jQuery(document).on('click', 'form.comment-form #submit', function (e) {
        e.preventDefault();
        // e.stopPropagation();
        console.log('Click on comment form submit !!!');

        commentForm = jQuery(this).closest('form.comment-form');
        console.log( 'Comment form is ', commentForm );

        commentText = commentForm.find('textarea');
        console.log( 'Comment text val is ', commentText.val() );

        commentAuthor = commentForm.find('input.author');
        console.log('Author name val is ', commentAuthor.val() );

        commentEmail = commentForm.find('input.email');
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
            currentInstance = jQuery(this).data("instance");
            console.log('Form instance is ', currentInstance);
            /* Check if recaptcha present */
            check = jQuery(this).prev();
            if ( check.attr('id')=='recaptcha') {
                console.log('Recaptcha found, executing callback');
                grecaptcha.execute();
            }
            else {
                console.log('No Recaptcha found, submitting form directly');
                commentForm.submit();
            }
        }
    });
});

function csrValidateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
    return emailReg.test($email);
}

// function csrOnSubmit(token) {
//     console.log('in csrOnSubmit, recapatcha successful !!!');
//     form = jQuery('form#foodiepro_comment' + currentInstance);
//     // formId = "foodiepro_comment" + currentInstance;
//     // console.log('Form ID is : ', formId);
//     // form = document.getElementById(formId);
//     console.log('Form is : ', form);
//     form.submit();
// }
