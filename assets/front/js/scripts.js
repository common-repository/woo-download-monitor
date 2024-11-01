jQuery(document).ready(function($) {
	
   $(document).on('woocommerce_variation_has_changed', function() {
		
		product_id		= $('form.variations_form').find("input[name=product_id]" ).val();
		arr_attributes 	= [];
		
		$( $('form.variations_form').find(".variations select" ) ).each(function() {
			
			if( $(this).val().length != 0 ){
				
				arr_attributes.push({
					attribute_name	: $(this).attr('name'),
					attribute_value	: $(this).val()
				});
			}
			
		});
		
		if( $('form.variations_form').find(".variations select" ).size() != arr_attributes.length ) return;
		
		$.ajax(
            {
		type: 'POST',
		context: this,
		url:wdm_ajax.wdm_ajaxurl,
		async: false,
		data: {
			"action"	 : "wdm_ajax_get_downloads_for_variable_products",
			"product_id" : product_id,
			"attributes" : arr_attributes,
		},
		success: function(data) {

			$('.wdm_file_container').fadeOut(400).html( data ).fadeIn(800);
		}
            });
		
	   
   })
   
   
   
   $(document).on('click', '.wdm_file_download', function() {

	   	var href 		= $(this).attr('href');
		var product_id 	= getUrlParameter(href,'wdm_download');
		var download_id = getUrlParameter(href,'file_id');
        var what_return = "false";

        $(this).parent().find('.wdm_download_error').hide('slow','swing');

        $.ajax(
            {
                type: 'POST',
                context: this,
                url:wdm_ajax.wdm_ajaxurl,
                async: false,
                data: {
                    "action"		: "wdm_ajax_check_download_validity",
                    "product_id"	: product_id,
                    "download_id"	: download_id,
                },
                success: function(data) {

                    console.log( data );

                    if( data.length == 0 ) what_return = "true";
                    else $(this).parent().append( data );
                }
            });

        if( what_return == "false" ) return false;
        else return true;
    })





});

function getUrlParameter(full_url,sParam) {

	var arr_full_url = full_url.split("?");
    var sPageURL = decodeURIComponent(arr_full_url[1]),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};




//
// $.when(
//     $.ajax(
//         {
//             type: 'POST',
//             context: this,
//             url:wdm_ajax.wdm_ajaxurl,
//             data: {
//                 "action"		: "wdm_ajax_check_download_validity",
//                 "product_id"	: product_id,
//                 "download_id"	: download_id,
//             },
//             success: function(data) {
//
//                 console.log( data.length );
//
//                 if( data.length == 0 ) what_return = "true";
//                 else {
//                     $(this).parent().append( data );
//                 }
//             }
//         })
// ).done(function () {
//
//     console.log( what_return );
//
//     if( what_return == "false" ) return false;
//     else return true;
// });