jQuery(document).ready(function () {
    console.log('In CCF ADMIN !');

    jQuery('#ccf_send_mail_submit').on('click', function (e) {
        console.log('Clicked on send mail button !');

        e.preventDefault();

        // console.log('Button is ' + jQuery(this).html());
        $thisForm = jQuery(this).closest('.ccf-send-mail-ajax-form');
        $thisForm.css("opacity", 0.5);
        $thisForm.css("pointer-events", "none");
        // console.log( "Form is ", $thisForm.html());

        userId = $thisForm.find("select[name='userid']").val();
        console.log('User ID is ' + userId);
        headline = $thisForm.find("input[name='headline']").val();
        console.log('Headline is ' + headline);

        if ((userId==null) || userId.length == 0) {
            alert('Please choose a user');
            return;
        }

        if (headline.length == 0) {
            alert('Mail subject cannot be empty');
            return;
        }

        var data = {
            action: 'send_contact_as_mail',
            security: ccf_admin.nonce,
            post_id: ccf_admin.postid,
            user_id: userId,
            subject: headline,
        };

        jQuery.ajax({
            url: ccf_admin.ajaxurl,
            type: 'post',
            data: data,
            success: function (response) {
                console.log('Send mail ajax call successful');
                $thisForm.css("opacity", "initial");
                $thisForm.html("Mail sent successfully !");
                // button.find(".button-caption").html(response);
            },
            error: function (request, error) {
                console.log('Failure');
            }
        });

    });

});
