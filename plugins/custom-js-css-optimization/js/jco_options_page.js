
jQuery(document).ready(function(){
	jQuery(document).on('change','.in_footer_select',function(){
		//alert("Script location modified");
		var select = jQuery(this);
		var table_row=select.closest('tr') ;                            
  	//console.log('table row: ', table_row); 
  	table_row.addClass( "modified" );   
  	var modified = table_row.children('input[name*="_modified"]');
  	console.log('modified value: ', modified.val());  
  	
  	//'name': $(this).children('input[name="paramName"]').val(),
    //'value': $(this).children('input[name="paramPrice"]').val()         
               
	});
});