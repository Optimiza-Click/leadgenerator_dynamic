/*
 * SimpleModal Basic Modal Dialog
 */

jQuery(function($) {
    // Load dialog on page load
    //$('#basic-modal-content').modal();

    // Load dialog on click
    jQuery('#consulta').click(function(e) {
        jQuery('#basic-modal-content').modal();

        return false;
    });
});

jQuery(function($) {
    jQuery('#text_cp').on('keypress', function(e) {
        if (e.which === 13) {
            var cp = jQuery('#text_cp').val();
            window.location.href = "/tienda/?cp=" + cp;
        }
    });
});


jQuery(function($) {
    jQuery('#provincia').change(function(e) {
        var provincia = $("#provincia").val();
        jQuery("#basic-modal-content").fadeOut("slow");
        jQuery.ajax({
            type: "GET",
            url: "/",
            data: "?category_id=" + provincia,
            success: function(data) {
                var str2 = "http";
                if (data.indexOf(str2) != -1) {
                    jQuery(location).attr('href', data);
                } else {
                    jQuery("#basic-modal-content").fadeOut("slow", function() {
                        jQuery('.message_prov').text('¿Cuál es tu localidad?');
                        var $site = $("#localidad");
                        jQuery('#local').css("display", "block");
                        jQuery('#provincia').css("display", "none");
                        jQuery.each(JSON.parse(data), function(key, value) {
                            $site.append(jQuery("<option></option>")
                                .attr("value", value.term_id).text(value.name));
                        });
                    });
                    jQuery("#basic-modal-content").fadeIn("slow");
                }
            }
        });
    });
});


jQuery(function($) {
    jQuery('#localidad').change(function(e) {
        var localidad = $("#localidad").val();
        jQuery.ajax({
            type: "GET",
            url: "/nutricionista-para-adelgazar/",
            data: "?local_id=" + localidad,
            success: function(data) {
                var str2 = "http";
                var obj = jQuery.parseJSON(data);
                if (obj.value == true) {
                    jQuery('.message_prov').text('¿Conoces tu codigo postal?').css('font-size', '25px');
                    jQuery(".codigo_postal a").attr("class", obj.url);
                    jQuery('.codigo_postal').css("display", "block");
                    jQuery('.provincia_select').css("display", "none");
                } else {
                    jQuery(location).attr('href', obj.url);
                }
            }
        });
    });
});


jQuery(function($) {
    jQuery('#promo_provincia').change(function(e) {
        var provincia = $("#promo_provincia").val();
        jQuery("#basic-modal-content").fadeOut("slow");
        jQuery.ajax({
            type: "GET",
            url: "/",
            data: "?promo_id=" + provincia,
            success: function(data) {
                var str2 = "http";
                if (data.indexOf(str2) != -1) {
                    jQuery(location).attr('href', data);
                } else {
                    jQuery("#basic-modal-content").fadeOut("slow", function() {
                        jQuery('.message_prov').text('¿Cuál es tu localidad?');
                        var $site = $("#localidad");
                        jQuery('#local').css("display", "block");
                        jQuery('#provincia').css("display", "none");
                        jQuery.each(JSON.parse(data), function(key, value) {
                            $site.append(jQuery("<option></option>")
                                .attr("value", value.term_id).text(value.name));
                        });
                    });
                    jQuery("#basic-modal-content").fadeIn("slow");
                }
            }
        });
    });
});

jQuery(function($) {
    jQuery('#come_back').click(function(e) {
        jQuery('.message_prov').text('Dinos tu provincia:');
        jQuery('#local').css("display", "none");
        jQuery('#provincia').animate({ height: 'toggle' }).css("display", "block");
    });
});

jQuery(function($) {
    jQuery('#categories_display').click(function(e) {
        var url = jQuery(".codigo_postal a").attr("class");
        window.location.href = url;
    });
});


jQuery(function($) {
    jQuery('.promo').change(function(e) {
        var url = jQuery(".promo").val();
        window.location.href = url;
    });
});