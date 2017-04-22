/* <![CDATA[ */

jQuery(document).ready(function(){
	
	//alert('JSCRIPT lancé !');
	
  jQuery('input[type="submit"]').click(function(){
  	var ScriptName = jQuery(this).data('name');
  	var ScriptInst = jQuery(this).data('instance');
  	
		
  	// Récupération des données de WPLocalize propres au shortcode sélectionné
		var WPLocalizeVar = window['script' + ScriptName + ScriptInst];
		var ShortcodeArgs = jQuery.parseJSON( WPLocalizeVar.data );
		var AjaxURL = WPLocalizeVar.url;
		var AjaxNonce = WPLocalizeVar.nonce;
		
		console.log('Lancement du script = '  + 'script' + ScriptName + ScriptInst);
		console.log('Arguments du script JS = %0', WPLocalizeVar);
		console.log('Arguments du shortcode = %0', ShortcodeArgs);
		console.log('URL de la page = ' + AjaxURL);
		//console.log('Nonce = '  + AjaxNonce);
		
		if (ShortcodeArgs['cmd'] == 'delete') {
			deletionOK = confirm('This action will delete posts content !');
			if ( ! deletionOK ) return;
		}
		
		jQuery('div#resp' + ScriptName + ScriptInst).html( 'Ajax call launched...' );

		jQuery.post(
		 AjaxURL,
		 {
		 	_ajax_nonce : AjaxNonce,
		 	action : ScriptName,
			args : ShortcodeArgs,
			dataType: "text",
		 },
		function( response ) {
			//console.log( 'Ajax call successfull');
			//console.log('div#resp' + ScriptName + ScriptInst);
			//console.log( response );
			jQuery('div#resp' + ScriptName + ScriptInst).html( response );
		});
		
  });
});

/* ]]> */	