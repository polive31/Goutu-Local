
jQuery(document).ready(function(){
	
	
	jQuery(document).on('focusin', '.asset-setting', function(){
    console.log("Saving value " + jQuery(this).val());
    jQuery(this).data('val', jQuery(this).val());
	}).on('change','.asset-setting', function(){
    var prev = jQuery(this).data('val');
    var current = jQuery(this).val();
    console.log("Prev value " + prev);
    console.log("New value " + current);
    
    
    // Add modified class as soon as a given field is changed
		var table_cell=jQuery(this).closest('td') ;                            
		var table_cell_class=table_cell.attr("class"); 
		console.log('class : ', table_cell_class); 
		if ( table_cell_class != "modified") {
			//console.log('modified class not found'); 
			table_cell.addClass( "modified" );   
		} 	
    
    // Check script dependencies
		var thisClass = jQuery(this).attr("class");

		if ( (current=='footer') || (current=='header') || (current=='disabled') )	{
			var handle=jQuery(this).attr("id"); 	
			console.log("Location for script " + handle + " modified !");
  		var dependencies = jQuery(this).data("dependencies");                         
			console.log("Script has following dependencies : ", dependencies);
  		var dependents = jQuery(this).data("dependents");                         
			console.log("Script has following dependents : ", dependents);
			var depsList='';
		}

		if (current=='footer')	{
			console.log('Move to footer !');
			jQuery.each(dependents, function(key, value) {
    		console.log(key, value);
    		var depLocation = table_cell.closest('.enqueued-assets').find('.location select[id="' + value + '"]').val();
    		console.log(depLocation);
    		if (depLocation == 'header') {
    			depsList=depsList + '\u2022 ' + value + '\n';
    			};
			});
			console.log(depsList);
			if (depsList!='') {
				alert('It is not possible to move ' + handle + ' to the footer, since other header assets depend on it.\nThose assets need to be moved or disabled first :\n'+depsList);
				jQuery(this).val("header");
				//table_cell.removeClass( "modified" );
			}
		}
		if (current=='header')	{
			console.log('Move to header!');
			jQuery.each(dependencies, function(key, value) {
    		console.log(key, value);
    		var depLocation = table_cell.closest('.enqueued-assets').find('.location select[id="' + value + '"]').val();
    		console.log(depLocation);
    		if (depLocation == 'footer') {
    			depsList=depsList + '\u2022 ' + value + '\n';
    			};
			});
			console.log(depsList);
			if (depsList!='') {
				alert('It is not possible to move ' + handle + ' to the header, since it depends on other footer assets.\nThose assets need to be moved or disabled first :\n'+depsList);
				jQuery(this).val("footer");
				//table_cell.removeClass( "modified" );
			}
		}
		else if (current=='disabled') {
			console.log('Disable !');
			jQuery.each(dependents, function(key, value) {
    		console.log(key, value);
    		var depLocation = table_cell.closest('.enqueued-assets').find('.location select[id="' + value + '"]').val();
    		console.log(depLocation);
    		if (depLocation != 'disable') {
    			depsList=depsList + '\u2022 ' + value + '\n';
    			};
			});
			console.log(depsList);
			if (depsList!='') {
				alert('It is not possible to disable ' + handle + ', since other active assets depend on it.\nThose assets need to be disabled first :\n'+depsList);
				jQuery(this).val( prev );
				table_cell.removeClass( "modified" );
			}			
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