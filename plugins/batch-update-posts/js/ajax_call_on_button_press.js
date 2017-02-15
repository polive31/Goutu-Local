/* <![CDATA[ */

jQuery(document).ready(function(){
	
  jQuery('input[type="submit"]').click(function(){
  	var ScriptName = jQuery(this).data('name');
  	var ScriptInst = jQuery(this).data('instance');
  	
		console.log('Lancement du script = '  + 'script' + ScriptName + ScriptInst);
		
		var WPLocalizeVar = window['script' + ScriptName + ScriptInst];
		var ShortcodeArgs = WPLocalizeVar.data;
		var AjaxURL = WPLocalizeVar.url;
		
		console.log('Arguments du shortcode = '  + ShortcodeArgs);
		console.log('URL de la page = '  + AjaxURL);
		
		jQuery.ajax({
		    type    : 'POST',
		    url     : script.url,
		    data    : script.jsondata,
		    success : function(response) {
		        alert('Success !!!');
		        //alert(response);
		    }    
		});
  
  });
});

/* ]]> */	