jQuery('html').ready(function() {
    var value = jQuery('.values_import');
    for (var i = 0, l = value.length; i < l; i++) {
        jQuery(value[i]).css('display', 'block');
    }

    for (var i = 4, l = value.length; i < l; i++) {
        jQuery(value[i]).css('display', 'none');
    }
});
/**/

jQuery('#seemore').click(function() {
    jQuery('.values_import').slideDown().css('display', 'block');
    jQuery('#seemore').css('display', 'none');
    jQuery('#seecompact').css('display', 'block');
});


jQuery('#seecompact').click(function() {
    var value = jQuery('.values_import');
    for (var i = 0, l = value.length; i < l; i++) {
        jQuery(value[i]).css('display', 'block');
    }

    for (var i = 4, l = value.length; i < l; i++) {
        jQuery(value[i]).slideUp().css('display', 'none');
    }
    jQuery('#seemore').css('display', 'block');
    jQuery('#seecompact').css('display', 'none');
});





jQuery('.phone button').click(function() {
    var count = jQuery('.phone button');
    for (var i = 0, l = count.length; i < l; i++) {
        var phone = jQuery(this).val();
        jQuery('.' + phone).css('display', 'none');
        jQuery('#' + phone).css('display', 'block');
    }
});

jQuery(document).ready(function() {
    var maxLength = 40;
    jQuery(".values_direccion a").each(function() {
        var myStr = jQuery(this).text();
        if (jQuery.trim(myStr).length > maxLength) {
            var newStr = myStr.substring(0, maxLength);
            var removedStr = myStr.substring(maxLength, jQuery.trim(myStr).length);
            jQuery(this).empty().html(newStr);
            jQuery(this).append('<a href="javascript:void(0);" class="read-more"><span>... </span>Ver +</a>');
            jQuery(this).append('<span class="more-text" style="display:none">' + removedStr + '</span>');
        }
    });
    jQuery(".read-more").click(function() {
        jQuery(this).siblings(".more-text").contents().unwrap();
        jQuery(this).remove();
    });
});

jQuery(document).ready(function() {
    jQuery("#contac_form_center select").change(function() {
        jQuery.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^]*)').exec(window.location.href);
            if (results==null){
            return null;
            }
            else{
            return results[1] || 0;
           }
        }
        jQuery('form').append('<input type="hidden" name="promo" value="' + jQuery.urlParam('promo') + '" />');
        var myString = jQuery(this).find("option:selected").val();
        var direccion = myString.substring(myString.indexOf(',') + 1);

        jQuery.ajax({
            type: "GET",
            url: home_page,
            data: "?direccion_form=" + direccion,
            success: function(data) {
                console.log(data);
                var json = JSON.parse(data);
                jQuery('form').append('<input type="hidden" name="store_id" value="' + json.store_id + '" />');
                jQuery('.text_with_image_background').html('En este centro te espera:<br />' + json.nutricionista).html();
            }
        });
    });
});