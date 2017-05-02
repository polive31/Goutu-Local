jQuery(document).ready(function(){


	jQuery(document).on('focusin', '.asset-setting', function(){
    console.log("Saving value " + jQuery(this).val());
    jQuery(this).data('val', jQuery(this).val());
	}).on('change','.asset-setting', function(){
    
    var prev = jQuery(this).data('val');
    var current = jQuery(this).val();
    //console.log("Prev value " + prev);
    //console.log("New value " + current);
    
    
    // Add modified class as soon as a given field is changed
		var tableCell=jQuery(this).closest('td') ;                            
		var tableCell_class=tableCell.attr("class"); 
		console.log('modified class : ', tableCell_class); 
		tableCell.addClass( "modified" );   
    

    // Check script dependencies
		if ( tableCell_class.indexOf("location") != -1 )	{
			//console.log('Location modified');
			var handle=jQuery(this).attr("id"); 	
			var dependents;
			var dependencies;
			var alertMsg;
			//console.log("Location for script " + handle + " modified !");
			//console.log("Script has following dependencies : ", dependencies);
			//console.log("Script has following dependents : ", dependents);

			if (current=='footer')	{  
				console.log('current == footer');                     
				alertMsg = 'It is not possible to move ' + handle + ' to the footer, since other header assets depend on it.\nThose assets need to be moved or disabled first :\n%depsList';
				deps = jQuery(this).data("dependents");
			}
			else if (current=='header')	{                        
				console.log('current == header');                     
				alertMsg = 'It is not possible to move ' + handle + ' to the header, since it depends on other footer assets.\nThose assets need to be moved or disabled first :\n%depsList';
				deps = jQuery(this).data("dependencies");
			}
			else if (current=='disabled') {
				console.log('current == disabled');                     
				alertMsg = 'It is not possible to disable ' + handle + ', since other active assets depend on it.\nThose assets need to be disabled first :\n%depsList';
				deps = jQuery(this).data("dependents");
			}
			else if (current=='async') {
				console.log('current == async');                     
				alertMsg = 'It is not possible to load ' + handle + ' asynchronously, since other assets depend on it.\nThose assets need to be made asynchronous first :\n%depsList';
				deps = jQuery(this).data("dependents");
			}
			checkDeps(jQuery(this), prev, current, deps, alertMsg);
			
		} // end if location modified
			
		function checkDeps(obj, prevLoc, newLoc, deps, msg) {
			var depsList='';
			var tableCell=obj.closest('td') ; 
			console.log('In checkDeps function, change to ', newLoc, 'deps are ', deps);	
			
			jQuery.each(deps, function(key, value) {
    		console.log(key, value);
    		var depLocation = tableCell.closest('.enqueued-assets').find('.location select[id="' + value + '"]').val();
    		console.log(depLocation);	
				switch(newLoc) {
					case "footer":
						issue=(depLocation=="header");
						break;
					case "header":
						issue=(depLocation=="footer");
						break;
					case "disabled":
						issue=(depLocation!="disabled");
						break;
				}
    		if (issue) {
    			depsList=depsList + '\u2022 ' + value + '\n';
    		};
			}); // end dependencies loop
			console.log(depsList);
			
			if (depsList!='') {
				msg = msg.replace('%depsList', depsList);
				alert(msg);
				obj.val(prevLoc);
				tableCell.removeClass( "modified" );
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