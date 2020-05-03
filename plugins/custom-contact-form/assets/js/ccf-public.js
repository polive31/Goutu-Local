jQuery(document).ready(function() {
	console.log('%c In custom comment form public.js', "background:#8F3B59; color:white")

	jQuery('form#contactForm').submit(function() {
		jQuery('form#contactForm .error').remove();
		var hasError = false;
		jQuery('.requiredField').each(function() {
			if(jQuery.trim(jQuery(this).val()) == '') {
				var labelText = jQuery(this).prev('label').text();
				jQuery(this).parent().append('<span class="error">Merci de renseigner votre '+labelText+'.</span>');
				hasError = true;
			} else if(jQuery(this).hasClass('email')) {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?jQuery/;
				if(!emailReg.test(jQuery.trim(jQuery(this).val()))) {
					var labelText = jQuery(this).prev('label').text();
					jQuery(this).parent().append('<span class="error">Vous avez entré un '+labelText+' invalide.</span>');
					hasError = true;
				}
			}
		});
		if(!hasError) {
			jQuery('form#contactForm li.buttons button').fadeOut('normal', function() {
				jQuery(this).parent().append('<img src="/wp-content/themes/td-v3/images/template/loading.gif" alt="Loading&hellip;" height="31" width="31" />');
			});
			var formInput = jQuery(this).serialize();
			jQuery.post(jQuery(this).attr('action'),formInput, function(data){
				jQuery('form#contactForm').slideUp("fast", function() {
					jQuery(this).before('<p class="thanks"><strong>Merci!</strong> Your email was successfully sent. I check my email all the time, so I should be in touch soon.</p>');
				});
			});
		}
		return false;
	});
});
