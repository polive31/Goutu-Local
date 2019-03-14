jQuery(document).ready(function () {

    jQuery('.rating-form').on('submit', 'form', function (e) {
        console.log('On form submit');
        
        commentForm = jQuery(this).closest('form');
        console.log( 'Comment form is ', commentForm );
        
        commentText = commentForm.find('textarea#comment');
        console.log( 'Comment text is ', commentText );

        commentAuthor = commentForm.find('input#author');
        console.log('Author name is ', commentAuthor);

        commentEmail = commentForm.find('input#email');
        console.log('Email address is ', commentEmail.val());        
        
        if (!commentText.val().length) {
            alert( csr.emptyComment );
            e.preventDefault();
            e.stopPropagation();
        }
        else if (!commentAuthor.val().length) {
            alert(csr.emptyAuthor);
            e.preventDefault();
            e.stopPropagation();
        }        
        else if (!csrValidateEmail(commentEmail.val())) {
            alert( csr.invalidEmail );
            e.preventDefault();
            e.stopPropagation();
        }
        else {
            console.log('Submit the form');
        }  
    });


});

function csrValidateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,6})?$/;
    return emailReg.test($email);
}

function csrOnSubmit(token) {
    console.log('Click on comment submit !!!');
    form = jQuery('.rating-form form.comment-form');
    console.log('Form is : ', form);
    form.submit();
}
