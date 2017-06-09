        jQuery('.form_weight .wpcf7-submit').click(function($) {
            jQuery('.weight_row').css("display", "block");

            var height = Number(jQuery('input[name="height"]').val()) * 0.01;
            var height_raw = jQuery('input[name="height"]').val();
            var weight = Number(jQuery('input[name="weight"]').val());
            var img_bmi = '';
            var text_color = '';
            var text = '';
            var resultado = '';

            var BMI = weight / (height * 2);
            jQuery('.bmi_dato').html(BMI.toPrecision(4));
            jQuery('#height_orig').html(Number(jQuery('input[name="height"]').val()));
            jQuery('#weight_orig').html(Number(jQuery('input[name="weight"]').val()));
            jQuery('#target_weight_orig').html(Number(jQuery('input[name="target_weight"]').val()));

            if (BMI >= 40) {
                resultado = '<b>Usted está en un peso muy por encima de su peso óptimo.</b> Esto puede afectar tanto su calidad de vida como su salud. Recomendamos el asesoramiento de profesionales.';
                img_bmi = '/wp_content/img/obesity.jpg';
                text_color = 'red';
                text = 'Tienes sobrepeso';
            }
            if (BMI >= 30 && BMI < 40) {
                resultado = '<b>Está muy por encima de su peso óptimo.</b> Le recomendamos que solicite una cita con su asesor de Naturhouse más cercano tan pronto como sea posible. Le ayudará a perder el exceso de peso y le ayudará a recuperar su peso objetivo de acuerdo a su altura, sexo y constitución.';
                img_bmi = '/wp_content/img/obesity.jpg';
                text_color = 'red';
                text = 'Tienes sobrepeso';
            }
            if (BMI >= 25 && BMI < 30) {
                resultado = '<b>Está por encima de su peso óptimo.</b> Pida consejo en su centro Naturhouse más cercano, donde le ayudarán a perder el exceso de peso que necesita para alcanzar el peso más apropiado para su estatura, sexo y constitución.';
                img_bmi = '/wp_content/img/overweight_last.jpg';
                text_color = 'red';
                text = 'Tienes sobrepeso';
            }
            if (BMI >= 18.5 && BMI < 25) {
                resultado = '<b>Está en un peso óptimo.</b> Si desea seguir una dieta variada y equilibrada, le aconsejamos que visite su Naturhouse más cercano, donde puede recibir el asesoramiento nutricional de un profesional cualificado.';
                img_bmi = '/wp_content/img/normal_weight.jpg';
                text_color = 'green';
                text = 'Tu peso es optimo';
            }
            if (BMI < 18.5) {
                resultado = '<b>Está por debajo de su peso óptimo.</b> Consulte con un Asesor de Naturhouse que le ayudará a alcanzar el peso adecuado para su estatura, sexo y constitución.';
                img_bmi = '/wp_content/img/underweight.jpg';
                text_color = 'red';
                text = 'Estas por debajo de tu peso';
            }

            jQuery('.explicacion_BMI').html(resultado);
            jQuery('.explicacion_BMI').attr('class', 'explicacion_BMI ' + text_color);
            jQuery('#img_bmi').attr('src', img_bmi);
            jQuery('.bmi_exp').attr('class', 'bmi_exp ' + text_color);
            jQuery('.bmi_exp').html(text);

            jQuery(".bloque3").toggle();

            var target = this.hash;
            var $target = jQuery('#BMI_resultado');

            jQuery('html, body').stop().animate({
                'scrollTop': $target.offset().top
            }, 900, 'swing', function() {
                window.location.hash = '#BMI_resultado';
            });

        });