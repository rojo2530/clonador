/*!

 =========================================================
 * Bootstrap Wizard - v1.1.1
 =========================================================
 
 * Product Page: https://www.creative-tim.com/product/bootstrap-wizard
 * Copyright 2017 Creative Tim (http://www.creative-tim.com)
 * Licensed under MIT (https://github.com/creativetimofficial/bootstrap-wizard/blob/master/LICENSE.md)
 
 =========================================================
 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */
function check_domain (dominio){
    var parametros = {
        "dominio" : dominio,
    };
    $.ajax({
        data:  parametros,
        url:   'check.live.php',
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            $("#siguiente").prop('disabled', true);   //Deshabilitamos boton
            $("#check1").removeClass('alert alert-success');
			//$("#check1").html("Procesando, espere por favor...");
            $("#check1").append('<p><img width="40px" src="https://webempresa.io/we-installatron/images/ajax-loader.gif" /> Por favor espere...</p>');
        },
        success:  function (response) {
            console.log(response);
            if (response.check) {
               $("#check1").addClass('alert alert-success');
               $("#check1").html(response.error_txt);
			   $("#clonar").prop('disabled', false);
            }
            else {
                $("#check1").addClass('alert alert-danger');
                $("#addErrorImg").addClass('glyphicon glyphicon-exclamation-sign');
                $("#check1").html(response.error_txt);
            }
        }
    });
}
function clon_domain (){
    $.ajax({
        url: 'clonar.live.php',
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            $("#anterior").remove();
            $("#clonar").remove();
           $("#tabclon").append('<img src="assets/img/ajax-loader.gif"/> Clonando...');
        },
        success:  function (response) {
            console.log(response);
            $("#tabclon").html("Desde aquí llamamos al script de Ramon para clonar " + response.domain);
        }
    });
}





searchVisible = 0;
transparent = true;

$(document).ready(function(){

   $('#dominio_sel').change(function() {
        console.log($("#dominio_sel").val());
        if ($("#dominio_sel").val() != "") {

            $("#siguiente").prop('disabled', false);
        }
        else {
            $("#siguiente").prop('disabled', true);

        }

    });


    $("#siguiente").click(function (evento) {
    //    if ($("#dominio_sel").val() != "") {

      //  }
        //Solo permitimos hacer click al boton siguiente si estamos en la primer panel
        if ($("#about").hasClass("tab-pane active")) {

            val1 = $("#dominio_sel").val();
            console.log(val1);
            //Deshabilitamos boton siguiente
            $("#siguiente").removeClass('btn-next').addClass('btn-finish');
            //Habilitamos boton clonar
            $("#clonar").removeClass('btn-finish').addClass('btn-next');
            check_domain(val1);
        }
    });

    $("#anterior").click(function (evento) {
        $("#clonar").removeClass('btn-next').addClass('btn-finish');
        $("#siguiente").removeClass('btn-finish').addClass('btn-next');
        //Eliminamos el contenido del div del check si pulsamos el boton anterior
        $("#check1").removeClass('alert alert-danger');
        $("#check1").html("");

        if ($("#dominio_sel").val() != "") {

            $("#siguiente").prop('disabled', false);
        }
        else {
            $("#siguiente").prop('disabled', true);

        }
    });

    $("#clonar").click(function (evento) {
       clon_domain();

    });



//Cambiamos mensajes de la libreria jquery validator

jQuery.extend(jQuery.validator.messages, {
  required: "Este campo es obligatorio.",
  remote: "Por favor, rellena este campo.",
  email: "Por favor, escribe una dirección de correo válida",
  url: "Por favor, escribe una URL válida.",
  date: "Por favor, escribe una fecha válida.",
  dateISO: "Por favor, escribe una fecha (ISO) válida.",
  number: "Por favor, escribe un número entero válido.",
  digits: "Por favor, escribe sólo dígitos.",
  creditcard: "Por favor, escribe un número de tarjeta válido.",
  equalTo: "Por favor, escribe el mismo valor de nuevo.",
  accept: "Por favor, escribe un valor con una extensión aceptada.",
  maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
  minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
  rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
  range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
  max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
  min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
});





    /*  Activate the tooltips      */


    $('[rel="tooltip"]').tooltip();

    // Code for the Validator
   var $validator = $('.wizard-card form').validate({
		  rules: {
		    firstname: {
		      required: true,
		      minlength: 3
		    },
		    lastname: {
		      required: true,
		      minlength: 3
		    },
		    SelectName: {
		      required: true,
		      minlength: 3,
		    }

        }
	});

    // Wizard Initialization

	$('.wizard-card').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-previous',

        onNext: function(tab, navigation, index) {
        //	var $valid = $('.wizard-card form').valid();
        //	if(!$valid) {
        //		$validator.focusInvalid();
        //		return false;
        //	}
        },

        onInit : function(tab, navigation, index){

          //check number of tabs and fill the entire row
          var $total = navigation.find('li').length;
          $width = 100/$total;
          var $wizard = navigation.closest('.wizard-card');

          $display_width = $(document).width();

          if($display_width < 600 && $total > 3){
              $width = 50;
          }

           navigation.find('li').css('width',$width + '%');
           $first_li = navigation.find('li:first-child a').html();
           $moving_div = $('<div class="moving-tab">' + $first_li + '</div>');
           $('.wizard-card .wizard-navigation').append($moving_div);
           refreshAnimation($wizard, index);
           $('.moving-tab').css('transition','transform 0s');
       },

          onTabClick : function(tab, navigation, index){

            var $valid = $('.wizard-card form').valid();

            if(!$valid){
                return false;
            } else {
                return true;
            }
        },

        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;

            var $wizard = navigation.closest('.wizard-card');

            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $($wizard).find('.btn-next').hide();
                $($wizard).find('.btn-finish').show();
            } else {
                $($wizard).find('.btn-next').show();
                $($wizard).find('.btn-finish').hide();
            }

            button_text = navigation.find('li:nth-child(' + $current + ') a').html();

            setTimeout(function(){
                $('.moving-tab').text(button_text);
            }, 150);

            var checkbox = $('.footer-checkbox');

            if( !index == 0 ){
                $(checkbox).css({
                    'opacity':'0',
                    'visibility':'hidden',
                    'position':'absolute'
                });
            } else {
                $(checkbox).css({
                    'opacity':'1',
                    'visibility':'visible'
                });
            }

            refreshAnimation($wizard, index);
        }
  	});


    // Prepare the preview for profile picture

    $("#wizard-picture").change(function(){
        readURL(this);
    });

    $('[data-toggle="wizard-radio"]').click(function(){
        wizard = $(this).closest('.wizard-card');
        wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
        $(this).addClass('active');
        $(wizard).find('[type="radio"]').removeAttr('checked');
        $(this).find('[type="radio"]').attr('checked','true');
    });

    $('[data-toggle="wizard-checkbox"]').click(function(){
        if( $(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('[type="checkbox"]').removeAttr('checked');
        } else {
            $(this).addClass('active');
            $(this).find('[type="checkbox"]').attr('checked','true');
        }
    });

    $('.set-full-height').css('height', 'auto');

});



 //Function to show image before upload

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

 $(window).resize(function(){
    $('.wizard-card').each(function(){
        $wizard = $(this);
        index = $wizard.bootstrapWizard('currentIndex');
        refreshAnimation($wizard, index);

        $('.moving-tab').css({
            'transition': 'transform 0s'
        });
    });
});

 function refreshAnimation($wizard, index){
    total_steps = $wizard.find('li').length;
    move_distance = $wizard.width() / total_steps;
    step_width = move_distance;
    move_distance *= index;

    $wizard.find('.moving-tab').css('width', step_width);
    $('.moving-tab').css({
        'transform':'translate3d(' + move_distance + 'px, 0, 0)',
        'transition': 'all 0.3s ease-out'

    });
}

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		clearTimeout(timeout);
		timeout = setTimeout(function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		}, wait);
		if (immediate && !timeout) func.apply(context, args);
	};
};


