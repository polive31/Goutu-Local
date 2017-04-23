
jQuery(document).ready(function(){
	jQuery(document).on('change','.setting-input',function(){
		//alert("Script location modified");
		var select = jQuery(this);
		var table_cell=select.closest('td') ;                            
		//var table_row=select.closest('tr') ;                            
  	var table_cell_class=table_cell.attr("class"); 
  	//console.log('class : ', table_cell_class); 
  	if ( table_cell_class != "modified") {
  		//console.log('modified class not found'); 
  		table_cell.addClass( "modified" );   
		} 		
		
  	var assetHandle=select.attr("id"); 
		var thisClass = select.attr("class");
  	var locationChange = (thisClass.indexOf('location')>-1)  && (thisClass.indexOf('scripts')>-1) ; 
  	console.log('assetHandle => ', assetHandle);
  	console.log('locationChange => ', locationChange);
  	
		if (locationChange)	{

			jQuery.post(
			 AjaxUrl,
			 {
			 	_ajax_nonce : AjaxNonce,
			 	action : AjaxAction,
				args : AjaxArgs,
				dataType: "text",
			 },
			function( response ) {
				//console.log( 'Ajax call successfull');
				//console.log('div#resp' + ScriptName + ScriptInst);
				//console.log( response );
				jQuery('div#resp' + ScriptName + ScriptInst).html( response );
			});
					    
		}
               
	});
});


// Toggle section visibility
jQuery(document).ready(function(){
	jQuery( 'th[scope="row"]' ).click(function(e){
		e.preventDefault();
		e.stopPropagation();
		//alert("Script header clicked");
		var select = jQuery(this);
		//var section_content = select.siblings( "td" ).children( ".section-wrapper" );
		var section_content = select.siblings( "td" );
		//console.log( 'section_label', select );
		//console.log( 'section_content', section_content );
		if ( select.attr('class') != "arrow-up") {
			select.addClass( "arrow-up" );
		}
		else {
			select.removeClass( "arrow-up" );
		}
		section_content.slideToggle( 400, "swing" );
		
		
//  	var modified = table_row.find('input.modified');
//  	console.log('modified value before update : ', modified.val()); 
// 		modified.val('true');
//  	console.log('modified value after update : ', modified.val()); 
  	
  	//'name': $(this).children('input[name="paramName"]').val(),
    //'value': $(this).children('input[name="paramPrice"]').val()         
               
	});
});
	
	
/* Dependencies control when changing script location
---------------------------------------------------------*/	
jQuery(document).ready(function(){	
  jQuery('input[type="submit"]').click(function(){
	
  });
});