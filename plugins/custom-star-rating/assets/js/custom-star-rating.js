jQuery(document).ready(function () {

    jQuery('.rating-form').on('submit', 'form', function (e) {
        console.log('On form submit');
        
        commentForm = jQuery(this).closest('form');
        console.log( 'Comment form is ', commentForm );
        
        commentText = commentForm.find('textarea#comment');
        console.log( 'Comment text is ', commentText );
        
        if (!commentText.val().length) {
            alert( csr.emptyComment );
            e.preventDefault();
            e.stopPropagation();
        }
        else {
            console.log('Submit the form');
        }  
    });


});
