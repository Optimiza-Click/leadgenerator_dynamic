jQuery(document).ready(function($)
{
    var selected = jQuery('#prueba').text();
    if(selected.length != 0) {
        var arr = unserialize(selected);
        console.log(arr);
        jQuery.each( arr, function( i, val ) {
            jQuery('.select2 option[value=' + val + ']').attr('selected','selected');
        });

        jQuery('.select2').select2({
                tags: true
	    });
    } else {
         jQuery('.select2').select2({
		    tags: true
	    });
    }
    
});