

var campos = $("#campos").val();
var contador_img = 0
var identificador = 0
var identificador_je=0;

var c;
if(campos == ""){
    c = 1;
}else{
    c = parseInt(campos)+1;
}

//var num =1;
var r = 1;
var rj = 1;
var d = 1;
var dn = 1;


$(document).ready(function () {

    $("#tipo_cliente").on('change', function(){
        
        var html = '';

        if($("#tipo_cliente").val() == '0'){
            html += '<option value="">Seleccione</option>';
            html += '<option value="2">RUC</option>';
            html += '<option value="1">DNI</option>';
        } else if($("#tipo_cliente").val() == '1'){
            html += '<option value="2">RUC</option>';
        } else {
            html += '<option value="">Seleccione</option>';
        }

        $("#tipo_iden").html(html);

    });

    if($("#idClientes").val()!=""){
        $("#cargando_modal").modal('show');
        $.ajax({
            url: base_url + 'cliente_tipo_campo/get_padres_por_cliente',
            type: "post",
            dataType: "json",
            data: {'id':$("#idClientes").val()},
            success: function (data) {
                var padre=data.padre;
                var tipoDePersona = $("#tipo_cliente").val();

               /* if(tipoDePersona==0){
                    $("#pJuridica").hide();
                    $("#label_pJuridica").hide();
                }else{

                    $("#pNatural").hide();
                    $("#label_pNatural").hide();
                }*/

                if(padre.length>0){

                    for(var i =0; i<padre.length;i++){
                        /*si es 0 es personal, sino es juridica*/
                        if(tipoDePersona==0){
                            rellenar_hijos('personaNatural','selectDuplicarN',padre[i]['padre_id'],$("#idClientes").val())
                        }else{
                            rellenar_hijos("personaJuridica","selectDuplicarJ",padre[i]['padre_id'],$("#idClientes").val());
                        }
                    }

                }
                $("#cargando_modal").modal('hide');
            },
            error: function (){
                $("#cargando_modal").modal('hide');

            }

        });

    }
    

    $(".chosen").chosen({
        allowClear: true,
        width: "100%"
    });

    // este codigo es para que al abrir un modal encima de otro modal no se pierda el scroll
    $('.modal').on("hidden.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
            $('body').addClass('modal-open');
        }
    }).on("show.bs.modal", function (e) {
        if($('.modal:visible').length)
        {
            $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
            $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
        }
    });

    //--------------- //
    $("#mapaPN").click(function(){

        $("#usPN").css("visibility","visible");

    });

    $("#cerrarMPN").click(function(){

        $("#usPN").css("visibility","hidden");

    });

    $("#mapaPJ").click(function(){

        $("#usPJ").css("visibility","visible");

    });
    $("#cerrarMPJ").click(function(){

        $("#usPJ").css("visibility","hidden");

    });

    $("#imagenContacto").change(function(){
        $("#subirImg").css("display","none");
        $("#eliminarImg").css("display","block");
    });


    $("#imagenContacto").change(function(){
        mostrarImagen(this);
    });
    $("#eliminarImg").click(function(){
        $('#vista_previa').attr('src','../recursos/img/la_foto.png');
        $('#imagenContacto').val('');
        $("#subirImg").css("display","block");
        $("#eliminarImg").css("display","none");
    });

    $("#imagenContactoPJ").change(function(){
        $("#subirImgPJ").css("display","none");
        $("#eliminarImgPJ").css("display","block");
    });


    $("#imagenContactoPJ").change(function(){
        mostrarImagenPJ(this);
    });
    $("#eliminarImgPJ").click(function(){
        $('#vista_previaPJ').attr('src','../recursos/img/la_foto.png');
        $('#imagenContactoPJ').val('');
        $("#subirImgPJ").css("display","block");
        $("#eliminarImgPJ").css("display","none");
    });
    //--------------------------------------//
    $("#imagenContactoPJ").change(function(){
        $("#subirImgPJ").css("display","none");
        $("#eliminarImgPJ").css("display","block");
    });


    $("#imagenContactoPJ").change(function(){
        mostrarImagenPJ(this);
    });
    $("#eliminarImgPJ").click(function(){
        $('#vista_previaPJ').attr('src','../recursos/img/la_foto.png');
        $('#imagenContactoPJ').val('');
        $("#subirImgPJ").css("display","block");
        $("#eliminarImgPJ").css("display","none");
    });
    //--------------------------------------//
    $("#imagenContactoPJR").change(function(){
        $("#subirImgPJR").css("display","none");
        $("#eliminarImgPJR").css("display","block");
    });


    $("#imagenContactoPJR").change(function(){
        mostrarImagenPJR(this);
    });

    $("#eliminarImgPJR").click(function(){
        $('#vista_previaPJR').attr('src','../recursos/img/la_foto.png');
        $('#imagenContactoPJR').val('');
        $("#subirImgPJR").css("display","block");
        $("#eliminarImgPJR").css("display","none");
    });

    /*esto es para buscar en la tabla cliente tipo campo, por padre solo persona natural */

    $("#duplicarJ").on('click',function(){
        buscar_hijos("opcionDuplicarJ","personaJuridica","selectDuplicarJ");
    });
    

    $("#retencion").change(function(){
        if(!$('#retencion').prop('checked')){
            $("#retencion_value").val('');
            $("#retencion_value").attr("readonly","readonly");
        }else{
            $("#retencion_value").removeAttr("readonly");
        }

    });

    $("#credito").change(function(){
        if(!$('#credito').prop('checked')){
            $("#lineaC_j").val('');
            $("#lineaC_j").attr("readonly","readonly");
        }else{
            $("#lineaC_j").removeAttr("readonly");
        }

    });

});

/*aqui termina el document.ready*/


function deletePadre(padre,contador,persona){

    /*esto es para eliminarlos dinamicamente*/
    $("#"+persona+padre+contador).parent().remove();

}

function deletePadreJ(padre,contador){
    /*esto es para eliminarlos dinamicamente*/

    if(padre==2 || padre==4 ){

        $("#personaJuridica"+padre+contador).remove();
        contador--;
    }else{

        $("#personaJuridica"+padre).remove();
    }


}

function mostrarImagenPJ(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // $('#vista_previa').prev().remove();
            $('#vista_previaPJ').css('display', 'block');
            $('#vista_previaPJ').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarImagenPJ(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // $('#vista_previa').prev().remove();
            $('#vista_previaPJ').css('display', 'block');
            $('#vista_previaPJ').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarImagenPJR(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // $('#vista_previa').prev().remove();
            $('#vista_previaPJR').css('display', 'block');
            $('#vista_previaPJR').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarImagen(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#vista_previa').prev().remove();
            $('#vista_previa').css('display', 'block');
            $('#vista_previa').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function mostrarImagen(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            // $('#vista_previa').prev().remove();
            $('#vista_previa').css('display', 'block');
            $('#vista_previa').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function verMapa(id){
    $("#usPN"+id).css("visibility","visible");
    (setTimeout(function () {

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (objPosition) {
                var lon = objPosition.coords.longitude;
                var lat = objPosition.coords.latitude;

                $('#longituds'+id).val(lon);
                $('#latituds'+id).val(lat);

                $('#uspnM'+id).locationpicker({
                    location: {latitude: $('#latituds'+id).val(), longitude: $('#longituds'+id).val()},
                    radius: 50,
                    inputBinding: {
                        latitudeInput: $('#latituds'+id),
                        longitudeInput: $('#longituds'+id),
                        locationNameInput: $('#locations'+id)
                    },
                    enableAutocomplete: true,
                    onchanged: function (currentLocation, radius, isMarkerDropped) {
                        (currentLocation.latitude + ", " + currentLocation.longitude);

                    }
                });
            }, function (objPositionError) {
                switch (objPositionError.code) {
                    case objPositionError.PERMISSION_DENIED:
                        alert("No se ha permitido el acceso a la posici�n del usuario.");
                        break;
                    case objPositionError.POSITION_UNAVAILABLE:
                        alert("No se ha podido acceder a la informaci�n de su posici�n.");
                        break;
                    case objPositionError.TIMEOUT:
                        alert("El servicio ha tardado demasiado tiempo en responder.");
                        break;
                    default:
                        alert("Error desconocido.");
                }
            }, {
                maximumAge: 75000,
                timeout: 15000
            });

        }
        else {
            alert("Su navegador no soporta la API de geolocalizaci�n.");
        }
    })(), 5000);
}
function cerrarMapa(id){
    $("#usPN"+id).css("visibility","hidden");
}
//-----------------------Mapa persona juridica--------------------//
function verMapaJ(id){
    $("#usPJS"+id).css("visibility","visible");
    (setTimeout(function () {

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (objPosition) {
                var lon = objPosition.coords.longitude;
                var lat = objPosition.coords.latitude;
                //Location google maps include FOR AND FOREACH In JavaScript And PHP --- By Ezequiel
                $('#longitudsJ'+id).val(lon);
                $('#latitudsJ'+id).val(lat);

                $('#uspjM'+id).locationpicker({
                    location: {latitude: $('#latitudsJ'+id).val(), longitude: $('#longitudsJ'+id).val()},
                    radius: 50,
                    inputBinding: {
                        latitudeInput: $('#latitudsJ'+id),
                        longitudeInput: $('#longitudsJ'+id),
                        locationNameInput: $('#locationsJ'+id)
                    },
                    enableAutocomplete: true,
                    onchanged: function (currentLocation, radius, isMarkerDropped) {
                        (currentLocation.latitude + ", " + currentLocation.longitude);

                    }
                });
            }, function (objPositionError) {
                switch (objPositionError.code) {
                    case objPositionError.PERMISSION_DENIED:
                        alert("No se ha permitido el acceso a la posici�n del usuario.");
                        break;
                    case objPositionError.POSITION_UNAVAILABLE:
                        alert("No se ha podido acceder a la informaci�n de su posici�n.");
                        break;
                    case objPositionError.TIMEOUT:
                        alert("El servicio ha tardado demasiado tiempo en responder.");
                        break;
                    default:
                        alert("Error desconocido.");
                }
            }, {
                maximumAge: 75000,
                timeout: 15000
            });

        }
        else {
            alert("Su navegador no soporta la API de geolocalizaci�n.");
        }
    })(), 5000);
}
function cerrarMapaJ(id){
    $("#usPJS"+id).css("visibility","hidden");
}

$("#mapaPN").click(function() {
    var map;
    if ($('#latitud1').val() == '0') {
        (setTimeout(function () {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (objPosition) {

                    var lon = objPosition.coords.longitude;
                    var lat = objPosition.coords.latitude;

                    $('#longitud1').val(lon);
                    $('#latitud1').val(lat);

                    $('#us1').locationpicker({
                        location: {latitude: $('#latitud1').val(), longitude: $('#longitud1').val()},
                        radius: 50,
                        inputBinding: {
                            latitudeInput: $('#latitud1'),
                            longitudeInput: $('#longitud1'),
                            locationNameInput: $('#location1')
                        },
                        enableAutocomplete: true,
                        onchanged: function (currentLocation, radius, isMarkerDropped) {
                            (currentLocation.latitude + ", " + currentLocation.longitude);

                        }
                    });
                }, function (objPositionError) {
                    switch (objPositionError.code) {
                        case objPositionError.PERMISSION_DENIED:
                            alert("No se ha permitido el acceso a la posici�n del usuario.");
                            break;
                        case objPositionError.POSITION_UNAVAILABLE:
                            alert("No se ha podido acceder a la informaci�n de su posici�n.");
                            break;
                        case objPositionError.TIMEOUT:
                            alert("El servicio ha tardado demasiado tiempo en responder.");
                            break;
                        default:
                            alert("Error desconocido.");
                    }
                }, {
                    maximumAge: 75000,
                    timeout: 15000
                });

            }
            else {
                alert("Su navegador no soporta la API de geolocalizaci�n.");
            }
            /***/
        })(), 5000);
    }
});
$("#mapaPJ").click(function() {
    if ($('#latitud2').val() == '0') {
        (setTimeout(function () {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (objPosition) {
                    var lon2 = objPosition.coords.longitude;
                    var lat2 = objPosition.coords.latitude;

                    $('#longitud2').val(lon2);
                    $('#latitud2').val(lat2);

                    $('#us2').locationpicker({
                        location: {latitude: $('#latitud2').val(), longitude: $('#longitud2').val()},
                        radius: 50,
                        inputBinding: {
                            latitudeInput: $('#latitud2'),
                            longitudeInput: $('#longitud2'),
                            locationNameInput: $('#location2')
                        },
                        enableAutocomplete: true,
                        onchanged: function (currentLocation, radius, isMarkerDropped) {
                            (currentLocation.latitude + ", " + currentLocation.longitude);

                        }
                    });
                }, function (objPositionError) {
                    switch (objPositionError.code) {
                        case objPositionError.PERMISSION_DENIED:
                            alert("No se ha permitido el acceso a la posici�n del usuario.");
                            break;
                        case objPositionError.POSITION_UNAVAILABLE:
                            alert("No se ha podido acceder a la informaci�n de su posici�n.");
                            break;
                        case objPositionError.TIMEOUT:
                            alert("El servicio ha tardado demasiado tiempo en responder.");
                            break;
                        default:
                            alert("Error desconocido.");
                    }
                }, {
                    maximumAge: 75000,
                    timeout: 15000
                });

            }
            else {
                alert("Su navegador no soporta la API de geolocalizaci�n.");
            }
        })(), 5000);
    }
});

function validar_ruc_n(){
    var consigue_ruc=false;
    $(".ruc").each(function(){
        var ruc =$(this).val();
        if($(this).val()!="") {
           // if (ruc.length<11 || ruc.length>11) {
            if (ruc.length == 11) {

                if (consigue_ruc == false) {
                    $.ajax({
                        url: base_url + 'cliente/validar_ruc',
                        type: "post",
                        dataType: "json",
                        data: {ruc: $(this).val()},
                        async: false,
                        success: function (data) {

                            if (data.valor > 0) {
                                setTimeout(function () {
                                    $(this).focus()
                                }, 1);

                                var growlType = 'warning';

                                $.bootstrapGrowl('<h4>El Ruc ingresado ya existe, por favor coloque otro</h4>', {
                                    type: growlType,
                                    delay: 2500,
                                    allow_dismiss: true
                                });
                                consigue_ruc = true

                            }

                        }
                    })
                }
            }else{
                setTimeout(function () {
                    $(this).focus()
                }, 1);
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>El RUC debe contener 11 d&iacute;gitos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                consigue_ruc = true
            }
        }else{
            setTimeout(function () {
                $(this).focus()
            }, 1);
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar el Ruc</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            consigue_ruc = true
        }


    });
    if(consigue_ruc==true){
        return consigue_ruc;
    }


    var consigue_razon=false;
    $(".razon_social").each(function(){
        if($(this).val()!="") {
            if (consigue_ruc == false) {
                $.ajax({
                    url: base_url + 'cliente/validar_razon_social',
                    type: "post",
                    dataType: "json",
                    data: {razon_social: $(this).val()},
                    async: false,
                    success: function (data) {

                        if (data.valor > 0) {
                            setTimeout(function () {
                                $(this).focus()
                            }, 1);

                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>La Razón Social ingresada ya existe</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            consigue_razon = true

                        }

                    }
                })
            }
        }else{
            setTimeout(function () {
                $(this).focus()
            }, 1);
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar la Razón Social</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            consigue_razon = true

        }

    });
    return consigue_razon;

}

function guardarcliente() {
    /*if($("#tipo_cliente").val()=='1'){
        validar_ruc_n();
    }*/

        if ($('#tipo_cliente').val() == "") {
                
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de cliente</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            return false
        }
        if ($("#razon_social_j").val() == '' && $('#tipo_cliente').val()=='1') {
            
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar una razon social</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
        }

        if ($('#tipo_iden').val() == "") {
                
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de identificacion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            return false
        }

        /*if ($("#ruc_j").val() == '') {
        
            var growlType = 'warning';
            var msj = '<h4>Debe ingresar un RUC/DNI</h4>';

            $.bootstrapGrowl(msj, {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }*/

        if($("#tipo_cliente").val()=='0'){
            if ($("#nombres").val() == '') {
               
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar nombres y apellidos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }
        }

        if($("#tipo_iden").val()==2){
            /*if ($("#ruc_j").val().length != 11) {
                
                var growlType = 'warning';
                $("#ruc_j").focus();
                $.bootstrapGrowl('<h4>El RUC debe contener 11 d&iacute;gitos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }*/

            if ($('#tipo_cliente').val() == 1){

                /*if ($("#nombres").val() == '' && $("#apellido_paterno").val() == '' && $("#apellido_materno").val() == '') {
                   
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Debe ingresar un representante</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }*/

                /*if ($("#apellidoPJuridico").val() == '') {
                   
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Debe ingresar el nombre del representante</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }*/
            }

        }
        /*else if($("#tipo_iden").val()==1){
            if ($("#ruc_j").val().length != 8) {
               
                var growlType = 'warning';
                $("#ruc_j").focus();
                $.bootstrapGrowl('<h4>El DNI debe contener 8 d&iacute;gitos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }
        }*/
            

        /*if($("#direccion_j").val() == '') {
        
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar una direcci&oacute;n</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }*/

        if ($("#distrito_id").val() == '' && $("#ciudad_id").val() == '' && $("#estado_id").val() == '') {
        
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar un Departamento/Provincia/Distrito</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        /*if ($("#correo").val() == '') {
        
            var growlType = 'warning';
            $("#correo").focus()
            $.bootstrapGrowl('<h4>Debe ingresar un correo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#telefono").val() == '') {
        
            var growlType = 'warning';
            $("#telefono").focus()
            $.bootstrapGrowl('<h4>Debe ingresar un telefono</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }*/

        if ($("#genero").val() == '') {
        
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe seleccionar un genero</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#estatus_j").val() == '') {
        
            var growlType = 'warning';
            $("#estatus").focus()
            $.bootstrapGrowl('<h4>Debe seleccionar un Estado</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#grupo_id_juridico").val() == '') {
        
            var growlType = 'warning';
            $("#estatus").focus()
            $.bootstrapGrowl('<h4>Debe seleccionar un grupo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($('#retencion').prop('checked') && $("#retencion_value").val() == '') {
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe ingresar la retencion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($('#credito').prop('checked') && $("#lineaC_j").val() == '') {
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe ingresar la linea de credito</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }


    var formData;
    if($('#tipo_cliente').val() == 1){ //juridico
        formData = $('#formagregarE').serialize();
    }else{
        formData = $('#formagregar').serialize();
    }

    $('#load_div').show()
    $.ajax({
        url: base_url + 'cliente/guardar',
        type: "post",
        dataType: "json",
        data: formData,
        success: function (data) {
            var modal = "clienteomodal";
            if (data.error == undefined) {

                var growlType = 'success';

                $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                $("#agregar").modal('hide');
                if($("#new_from_venta").val() == 1){
                    $('#dialog_new_cliente').attr('data-id', data.cliente);
                    $('#dialog_new_cliente').modal('hide');
                    $('.modal-backdrop').remove();
                    $('#load_div').hide();
                }
                else{
                    return $.ajax({
                    url: base_url+'cliente',
                    success: function(data){
                        $('#page-content').html(data);
                        $('.modal-backdrop').remove();
                        $('#load_div').hide();
                    }

                    });
                }
                

            }else {

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);
                $('.modal-backdrop').remove()
                $('#load_div').hide()
            }
            



        },
        error: function (response) {
            $('.modal-backdrop').remove()
            $('#load_div').hide()
            var growlType = 'warning';


            $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

        }


    });

}

function guardarclienteEmpresa() {
        

        if ($('#tipo_cliente').val() == "") {
                
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de cliente</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            return false
        }

        if ($("#razon_social_j").val() == '') {
            
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar una razon social</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
        }

        if ($('#tipo_iden').val() == "") {
                
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de identificacion</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            return false
        }

        if ($("#ruc_j").val() == '') {
        
            var growlType = 'warning';
            var msj = '<h4>Debe ingresar un RUC/DNI</h4>';

            $.bootstrapGrowl(msj, {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }


        if($("#tipo_iden").val()==2){
            /*if ($("#ruc_j").val().length != 11) {
                
                var growlType = 'warning';
                $("#ruc_j").focus();
                $.bootstrapGrowl('<h4>El RUC debe contener 11 d&iacute;gitos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }*/

            if ($('#tipo_cliente').val() == 1){

                if ($("#nombres").val() == '' && $("#apellido_paterno").val() == '' && $("#apellido_materno").val() == '') {
                   
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Debe ingresar un representante</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }

                if ($("#apellidoPJuridico").val() == '') {
                   
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Debe ingresar el DNI del representante</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }
            }

        }
        /*else if($("#tipo_iden").val()==1){
            if ($("#ruc_j").val().length != 8) {
               
                var growlType = 'warning';
                $("#ruc_j").focus();
                $.bootstrapGrowl('<h4>El DNI debe contener 8 d&iacute;gitos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }
        }*/
            

        if($("#direccion_j").val() == '') {
        
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar una direcci&oacute;n</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#distrito_id").val() == '' && $("#ciudad_id").val() == '' && $("#estado_id").val() == '') {
        
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar un Departamento/Provincia/Distrito</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        /*if ($("#correo").val() == '') {
        
            var growlType = 'warning';
            $("#correo").focus()
            $.bootstrapGrowl('<h4>Debe ingresar un correo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#telefono").val() == '') {
        
            var growlType = 'warning';
            $("#telefono").focus()
            $.bootstrapGrowl('<h4>Debe ingresar un telefono</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }*/

        if ($("#genero").val() == '') {
        
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe seleccionar un genero</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#estatus_j").val() == '') {
        
            var growlType = 'warning';
            $("#estatus").focus()
            $.bootstrapGrowl('<h4>Debe seleccionar un Estado</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($("#grupo_id_juridico").val() == '') {
        
            var growlType = 'warning';
            $("#estatus").focus()
            $.bootstrapGrowl('<h4>Debe seleccionar un grupo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($('#retencion').prop('checked') && $("#retencion_value").val() == '') {
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe ingresar la retencion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($('#credito').prop('checked') && $("#lineaC_j").val() == '') {
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe ingresar la linea de credito</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }


    var formData = $('#formagregar').serialize();

    $('#load_div').show()
    $.ajax({
        url: base_url + 'cliente/guardar',
        type: "post",
        dataType: "json",
        data: formData,
        success: function (data) {
            var modal = "clienteomodal";
            if (data.error == undefined) {

                var growlType = 'success';

                $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                $("#agregar").modal('hide');
                if($("#new_from_venta").val() == 1){
                    $('#dialog_new_cliente').attr('data-id', data.cliente);
                    $('#dialog_new_cliente').modal('hide');
                    $('.modal-backdrop').remove();
                    $('#load_div').hide();
                }
                else{
                    return $.ajax({
                    url: base_url+'cliente',
                    success: function(data){
                        $('#page-content').html(data);
                        $('.modal-backdrop').remove();
                        $('#load_div').hide();
                    }

                    });
                }
                

            }else {

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);
                $('.modal-backdrop').remove()
                $('#load_div').hide()
            }
            



        },
        error: function (response) {
            $('.modal-backdrop').remove()
            $('#load_div').hide()
            var growlType = 'warning';


            $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

        }


    });

}

function deleteimg(id,nombre) {

    $.ajax({
        url: base_url + 'cliente/eliminarimg',
        type: "post",
        dataType: "json",
        data: {'id': id, 'nombre': nombre},
        success: function (data) {

            if(data.error==undefined){

                var growlType = 'success';

                $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $('#vista_previa').attr('src','../recursos/img/la_foto.png');
                $('#subirImg').css('display','block');

                $("#eliminar").remove();




            }else{
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>'+data.error+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }

        },
        error: function (){

            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Por favor comuniquese con soporte</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
        }

    });
}
function deleteimgPJ(id,nombre) {

    $.ajax({
        url: base_url + 'cliente/eliminarimg',
        type: "post",
        dataType: "json",
        data: {'id': id, 'nombre': nombre},
        success: function (data) {

            if(data.error==undefined){

                var growlType = 'success';

                $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $('#vista_previaPJ').attr('src','../recursos/img/la_foto.png');
                $('#subirImgPJ').css('display','block');

                $("#eliminarImagPJ").remove();




            }else{
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>'+data.error+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }

        },
        error: function (){

            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Por favor comuniquese con soporte</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
        }

    });
}
function deleteimgR(id,nombre) {

    $.ajax({
        url: base_url + 'cliente/eliminarimgR',
        type: "post",
        dataType: "json",
        data: {'id': id, 'nombre': nombre},
        success: function (data) {

            if(data.error==undefined){

                var growlType = 'success';

                $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $('#vista_previaPJR').attr('src','../recursos/img/la_foto.png');
                $('#subirImgPJR').css('display','block');

                $("#eliminarImagR").remove();




            }else{
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>'+data.error+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }

        },
        error: function (){

            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Por favor comuniquese con soporte</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
        }

    });
}

function eliminarCampo(id) {

    $.ajax({
        url: base_url + 'cliente/eliminarC',
        type: "post",
        dataType: "json",
        data: {'id': id},
        success: function (data) {

            if(data.error==undefined){

                var growlType = 'success';

                $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#eliminar"+id).parent().parent().remove();

            }else{
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>'+data.error+'</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }

        }


    });
}

if ($('#latitud').val() == '0') {
    (setTimeout(function () {

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (objPosition) {
                var lon = objPosition.coords.longitude;
                var lat = objPosition.coords.latitude;
                alert(lat + ' y ' + lon);
                $('#longitud').val(lon);
                $('#latitud').val(lat);
                $('#us').locationpicker({
                    location: {latitude: lat, longitude: lon},
                    radius: 50,
                    inputBinding: {
                        latitudeInput: $('#latitud'),
                        longitudeInput: $('#longitud'),
                        locationNameInput: $('#location')
                    },
                    enableAutocomplete: true,
                    onchanged: function (currentLocation, radius, isMarkerDropped) {
                        (currentLocation.latitude + ", " + currentLocation.longitude);


                    }
                });
            }, function (objPositionError) {
                switch (objPositionError.code) {
                    case objPositionError.PERMISSION_DENIED:
                        alert("No se ha permitido el acceso a la posici�n del usuario.");
                        break;
                    case objPositionError.POSITION_UNAVAILABLE:
                        alert("No se ha podido acceder a la informaci�n de su posici�n.");
                        break;
                    case objPositionError.TIMEOUT:
                        alert("El servicio ha tardado demasiado tiempo en responder.");
                        break;
                    default:
                        alert("Error desconocido.");
                }
            }, {
                maximumAge: 75000,
                timeout: 15000
            });
        }
        else {
            alert("Su navegador no soporta la API de geolocalizaci�n.");
        }
    })(), 5000);

}

else {
    $('#us').locationpicker({
        location: {latitude: $('#latitud').val(), longitude: $('#longitud').val()},
        radius: 50,
        inputBinding: {
            latitudeInput: $('#latitud'),
            longitudeInput: $('#longitud'),
            locationNameInput: $('#location')
        },
        enableAutocomplete: true,
        onchanged: function (currentLocation, radius, isMarkerDropped) {
            (currentLocation.latitude + ", " + currentLocation.longitude);

        }
    });

}

//funcion para colocar una cadena tipo titulo
function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

/*esta funcion es la que agrega los item dinamicamente*/
function buscar_hijos(boton,persona,selectduplicar){

    var cont = 0;
    var id = $("#"+boton).val();
    var enviar_persona="'"+persona+"'";

    $.ajax({
        url: base_url + 'cliente_tipo_campo/get_by',
        type: "post",
        dataType: "json",
        data: { id : id},
        success: function (data) {
            var provincias = data.estados;
            var ciudades = data.ciudades;
            var distritos = data.distritos;

            var result = data.clientes;

            if (result.length > 0) {

                /*se recorre la data retornada*/
                $.each(result, function (i, val) {

                    /*se pregunta si existe para crearlo. Solo entra una sola vez, este es el encabezado*/
                    if ($("#" + persona + val.padre_id).length < 1) {

                        /*se usa para concatenar*/
                        var select = '<div class="row" id="' + persona + val.padre_id + '"><div class="form-group">' +
                            '<div class="col-md-12"><input type="hidden" name="padre[]"  value="' + val.padre_id + '" /><label><h4>'
                        var cerrar_div = '</h4></label></div></div></div>'

                        select += toTitleCase(val.tipo_campo_padre_nombre);


                        $("#" + selectduplicar).after(select + cerrar_div);

                    }


                    /*se usa un contador universal*/
                    var personal = ''

                    if (val.tipo_campo_padre_slug == "direccion") {
                        contador_universal = contadordireccion;
                    }
                    if (val.tipo_campo_padre_slug == "razon_social") {
                        contador_universal = contadorrazon_social;
                    }
                    if (val.tipo_campo_padre_slug == "representante") {
                        contador_universal = contadorrepresentante;
                    }
                    if (val.tipo_campo_padre_slug == "telefono") {
                        contador_universal = contadortelefono;
                    }
                    if (val.tipo_campo_padre_slug == "pagina_web") {
                        contador_universal = contadorpagina_web;
                    }
                    if (val.tipo_campo_padre_slug == "cumpleanos") {
                        contador_universal = contadorcumpleanos;
                    }
                    if (val.tipo_campo_padre_slug == "correo") {
                        contador_universal = contadorcorreo;
                    }


                    if ($("#" + persona + val.padre_id + contador_universal).length < 1) {
                        personal = '<div class="form-group ' + persona + val.padre_id + contador_universal + '" ' +
                        'id="' + persona + val.padre_id + contador_universal + '" ></div>'
                        $("#" + persona + val.padre_id).append(personal);
                    }

                    /*aqui solo entra la primera vez para poner el encabezado*/
                    if (cont < 1) {
                        var nombre_minuscula = val.tipo_campo_padre_nombre;
                        nombre_minuscula = nombre_minuscula.toLowerCase();

                        if (val.input_type == "text") {
                            var abrir=""
                            abrir+='<hr />'
                            abrir+= '<div class="row"><div class="col-md-11"></div>'
                            abrir+='<div class="col-md-1">'
                            abrir+='<button class="fa fa-times deletePadre" onclick="deletePadre(' + val.padre_id + ',' + contador_universal + ',' + enviar_persona + ')" aria-hidden="true" value="' + val.padre_id + contador_universal + '" ></button>'
                            abrir+='</div></div>'
                            abrir+='<div class="row"><div class="col-md-3"  style="text-align: right"><label class="control-label panel-admin-text">' + val.nombre + '</label></div>'
                            abrir+='<div class="col-md-7"><input  name="' + val.slug + '[]" '
                            if(val.slug=='ruc'){
                                abrir+=' onkeydown="return soloNumeros(event);" '
                            }

                            abrir+='class="form-control '+val.slug

                            if(val.slug=='cumpleanos'){
                                abrir+=' input-datepicker '
                            }

                            abrir+=' " type="' + val.input_type + '"  />'
                            abrir+='</div>'
                            $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir);

                            $(".input-datepicker").attr('readonly', true);


                        }

                        /*suponiendo que siempre el primer valor de la direccion sera provincia*/
                        if (val.input_type == "select") {

                            var abrir = '<hr />' +
                                '<div class="row"><div class="col-md-11"></div>' +
                                '<div class="col-md-1">' +
                                '<button class="fa fa-times deletePadre" onclick="deletePadre(' + val.padre_id + ',' + contador_universal + ',' + enviar_persona + ')" aria-hidden="true" value="' + val.padre_id + contador_universal + '" ></button>' +
                                '</div></div>' +
                                '<div class="row"><div class="col-md-3" style="text-align: right"><label class="control-label panel-admin-text">' + val.nombre + '</label></div>' +
                                '<div class="col-md-7">'

                            abrir += ' <select name="' + val.slug + '[]" id="estado_id'+contador_universal+'" required="true" class="form-control" onchange="region.actualizardistritos_dinamico('+contador_universal+');">' +
                            '<option value="">Seleccione</option>'

                            for (var e = 0; e < provincias.length; e++) {
                                var value = provincias[e]['estados_id'];
                                var nombre=provincias[e]['estados_nombre'];
                                abrir += '<option value="' + value + '" > ' + nombre + ' </option>'
                            }

                            abrir += '</select></div>'

                            $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir);
                        }

                        cont++;

                    } else {
                        /*aqui entra la segunda vez */
                        if (val.input_type == "text") {
                            $("#" + persona + val.padre_id + contador_universal + ":last").append('<div class="row"><div class="col-md-3" style="text-align: right"><label class="control-label panel-admin-text">' + val.nombre + '</label></div>' +
                            '<div class="col-md-7"><input   name="' + val.slug + '[]"  class="form-control '+val.slug+'" type="' + val.input_type + '"  />' +
                                //'<input type="hidden" name="camposT[]" value="' + val.id_tipo + '" />' +
                            '</div></div>');
                        }

                        if (val.input_type == "select") {

                            var abrir = '<div class="row"><div class="col-md-3" style="text-align: right"><label class="control-label panel-admin-text">' + val.nombre + '</label></div><div class="col-md-7">'

                            if (val.slug == "ciudad") {

                                abrir += '<select name="' + val.slug + '[]" id="ciudad_id'+contador_universal+'" required="true" class="form-control"  onchange="region.actualizarbarrio_dinamico('+contador_universal+');">' +
                                '<option value="">Seleccione</option>'
                            }

                            if (val.slug == "distrito") {

                                abrir += '<select name="' + val.slug + '[]" id="distrito_id'+contador_universal+'" required="true" class="form-control">' +
                                '<option value="">Seleccione</option>'
                            }

                            abrir += '</select></div></div>';
                            $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir)

                        }
                    }

                });

                /*aumento los respectivos contadores*/
                if (result[0]['tipo_campo_padre_slug'] == "direccion") {
                    contadordireccion++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "razon_social") {
                    contadorrazon_social++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "representante") {
                    contadorrepresentante++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "telefono") {
                    contadortelefono++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "pagina_web") {
                    contadorpagina_web++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "cumpleanos") {
                    contadorcumpleanos++;
                }
                if (result[0]['tipo_campo_padre_slug'] == "correo") {
                    contadorcorreo++;
                }

                /*reinicializo a 0*/
                cont = 0;

                /*no halle la manera en que tom el datepicker dinamico*/
                // Initialize Datepicker
                $("body").delegate(".input-datepicker", "focusin", function(){
                    $('.input-datepicker').datepicker();
                });
                $('body').on('focus',".input-datepicker", function(){
                    $(this).datepicker();
                });


            } else {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>No existen campos</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);
                return false;

            }
        }
    });

}

/*funcion que devuelve la cadena de texto con la primera letra en mayuscula*/
function MaysPrimera(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/*esta funcion es la que agrega los item dinamicamente solo cuando esats editando un cliente*/
function rellenar_hijos(persona,selectduplicar,idpadre,cliente){

    /*esta variable es para poder eliminarlos dinamicamente*/
    var enviar_persona="'"+persona+"'";

    var cont = 0;
    /*se envian el id del padre y el id del cliente*/

        $.ajax({
            url: base_url + 'cliente_tipo_campo/get_by_with_valor',
            type: "post",
            dataType: "json",
            data: { id : idpadre,cliente_id:cliente},
            success: function (data) {
                var provincias = data.estados;
                var ciudades = data.ciudades;
                var distritos = data.distritos;

                var result = data.clientes;


                if (result.length > 0) {

                    /*estas dos variables son las que determinaran a que campo_valor pertenece al padre*/
                    var referencia1 = '';
                    var referencia2 = '';


                    /*se recorre la data retornada*/
                    $.each(result, function (i, val) {


                        /*se pregunta si existe para crearlo. Solo entra una sola vez, este es el encabezado*/
                        if ($("#" + persona + val.padre_id).length < 1) {

                            /*se usa para concatenar*/
                            var select = '<div class="row" id="' + persona + val.padre_id + '"><div class="form-group">' +
                                '<div class="col-md-12"><input type="hidden" name="padre[]"  value="' + val.padre_id + '" /><label><h4>'
                            var cerrar_div = '</h4></label></div></div></div>'

                            select += toTitleCase(val.tipo_campo_padre_nombre);
                            $("#" + selectduplicar).after(select + cerrar_div);

                        }


                        /*se usa un contador universal*/
                        var personal = ''

                        /*si es la primera vez que entra por padre  */
                        if (i < 1) {
                            referencia1 = val.referencia;
                            referencia2 = val.referencia;

                            if (val.tipo_campo_padre_slug == "direccion") {
                                contador_universal = contadordireccion;
                                contadordireccion++;
                            }
                            if (val.tipo_campo_padre_slug == "razon_social") {
                                contador_universal = contadorrazon_social;
                                contadorrazon_social++;
                            }
                            if (val.tipo_campo_padre_slug == "representante") {
                                contador_universal = contadorrepresentante;
                                contadorrepresentante++;
                            }
                            if (val.tipo_campo_padre_slug == "telefono") {
                                contador_universal = contadortelefono;
                                contadortelefono++;
                            }
                            if (val.tipo_campo_padre_slug == "pagina_web") {
                                contador_universal = contadorpagina_web;
                                contadorpagina_web++;
                            }
                            if (val.tipo_campo_padre_slug == "cumpleanos") {
                                contador_universal = contadorcumpleano;
                                contadorcumpleanos++;
                            }
                            if (val.tipo_campo_padre_slug == "correo") {
                                contador_universal = contadorcorreo;
                                contadorcorreo++;
                            }
                        } else {
                            /*si no es la primera vez que entra, a referencia2 se le asigna el valor que siguio.*/
                            referencia2 = val.referencia
                        }

                        /* si es distinta quiere decir que ya paso a el siguiente item, siguiente direcion, rzon social, correo etc.*/
                        if (referencia2 != referencia1) {
                            /*reinicializo a 0 para que hagael encabezado*/
                            cont = 0;

                            /*le asigno los valores iguales para comparar en la siguiente iteracion*/
                            referencia1 = referencia2;
                            if (val.tipo_campo_padre_slug == "direccion") {
                                contador_universal = contadordireccion;
                                contadordireccion++;

                            }
                            if (val.tipo_campo_padre_slug == "razon_social") {
                                contador_universal = contadorrazon_social;
                                contadorrazon_social++;
                            }
                            if (val.tipo_campo_padre_slug == "representante") {
                                contador_universal = contadorrepresentante;
                                contadorrepresentante++;
                            }
                            if (val.tipo_campo_padre_slug == "telefono") {
                                contador_universal = contadortelefono;
                                contadortelefono++;
                            }
                            if (val.tipo_campo_padre_slug == "pagina_web") {
                                contador_universal = contadorpagina_web;
                                contadorpagina_web++;
                            }
                            if (val.tipo_campo_padre_slug == "cumpleanos") {
                                contador_universal = contadorcumpleanos;
                                contadorcumpleanos++;
                            }
                            if (val.tipo_campo_padre_slug == "correo") {
                                contador_universal = contadorcorreo;
                                contadorcorreo++;
                            }
                        }


                        if ($("#" + persona + val.padre_id + contador_universal).length < 1) {
                            personal = '<div class="form-group ' + persona + val.padre_id + contador_universal + '" ' +
                            'id="' + persona + val.padre_id + contador_universal + '" ></div>'
                            $("#" + persona + val.padre_id).append(personal);
                        }


                        /*aqui solo entra la primera vez para poner el encabezado*/
                        if (cont < 1) {

                            var nombre_minuscula = val.tipo_campo_padre_nombre;
                            nombre_minuscula = nombre_minuscula.toLowerCase();

                            if (val.input_type == "text") {

                                var abrir=""
                                abrir+='<hr />'
                                abrir+= '<div class="row"><div class="col-md-11"></div>'
                                abrir+='<div class="col-md-1">'
                                abrir+='<button class="fa fa-times deletePadre" onclick="deletePadre(' + val.padre_id + ',' + contador_universal + ',' + enviar_persona + ')" aria-hidden="true" value="' + val.padre_id + contador_universal + '" ></button>'
                                abrir+='</div></div>'
                                abrir+='<div class="row"><div class="col-md-3"  style="text-align: right"><label class="control-label panel-admin-text">' + val.nombre + '</label></div>'
                                abrir+='<div class="col-md-7"><input value="'+val.campo_valor+'"  name="' + val.slug + '[]" '
                                if(val.slug=='ruc'){
                                    abrir+=' onkeydown="return soloNumeros(event);" '
                                }

                                abrir+='class="form-control '+val.slug

                                if(val.slug=='cumpleanos'){
                                    abrir+=' input-datepicker '
                                }

                                abrir+=' " type="' + val.input_type + '"  />'
                                abrir+='</div>'
                                $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir);

                                $(".input-datepicker").attr('readonly', true);

                            }

                            /*suponiendo que siempre el primer valor de la direccion sera provincia*/
                            if (val.input_type == "select") {

                                var abrir = '<hr />' +
                                    '<div class="row"><div class="col-md-11"></div>' +
                                    '<div class="col-md-1">' +
                                    '<button class="fa fa-times deletePadre" onclick="deletePadre(' + val.padre_id + ',' + contador_universal + ',' + enviar_persona + ')" aria-hidden="true" value="' + val.padre_id + contador_universal + '" ></button>' +
                                    '</div></div>' +
                                    '<div class="row"><div class="col-md-3" style="text-align: right"><label class="panel-admin-text control-label">' + val.nombre + '</label></div>' +
                                    '<div class="col-md-7">'

                                abrir += ' <select name="' + val.slug + '[]" id="estado_id'+contador_universal+'" required="true" class="form-control" onchange="region.actualizardistritos_dinamico('+contador_universal+');">' +
                                '<option value="">Seleccione</option>'

                                for (var e = 0; e < provincias.length; e++) {
                                    var value = provincias[e]['estados_id'];
                                    var nombre=provincias[e]['estados_nombre'];
                                    abrir += '<option value="' + value + '" '
                                    if(val.campo_valor==value){
                                        abrir += 'selected'
                                    }
                                    abrir += ' > ' + nombre + ' </option>'
                                }

                                abrir += '</select></div>'

                                $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir);
                            }

                            cont++;

                        } else {
                            /*aqui entra la segunda vez */
                            if (val.input_type == "text") {
                                $("#" + persona + val.padre_id + contador_universal + ":last").append('<div class="row"><div class="col-md-3" style="text-align: right"><label class="panel-admin-text control-labell">' + val.nombre + '</label></div>' +
                                '<div class="col-md-7"><input value="' + val.campo_valor + '"  name="' + val.slug + '[]"  class="form-control '+val.slug+'" type="' + val.input_type + '"  />' +
                                    //'<input type="hidden" name="camposT[]" value="' + val.id_tipo + '" />' +
                                '</div></div>');
                            }

                            if (val.input_type == "select") {

                                var abrir = '<div class="row"><div class="col-md-3" style="text-align: right" ><label class="panel-admin-text control-label">' + val.nombre + '</label></div><div class="col-md-7">'

                                if (val.slug == "ciudad") {

                                    abrir += '<select name="' + val.slug + '[]" id="ciudad_id'+contador_universal+'" required="true" class="form-control"  onchange="region.actualizarbarrio_dinamico('+contador_universal+');">' +
                                    '<option value="">Seleccione</option>'

                                    for (var e = 0; e < ciudades.length; e++) {
                                        var value = ciudades[e]['ciudad_id'];
                                        var nombre=ciudades[e]['ciudad_nombre'];
                                        abrir += '<option value="' + value + '" '
                                        if(val.campo_valor==value){
                                            abrir += 'selected'
                                        }
                                        abrir += ' > ' + nombre + ' </option>'
                                    }
                                }

                                if (val.slug == "distrito") {

                                    abrir += '<select name="' + val.slug + '[]" id="distrito_id'+contador_universal+'" required="true" class="form-control">' +
                                    '<option value="">Seleccione</option>'
                                    for (var e = 0; e < distritos.length; e++) {
                                        var value = distritos[e]['id'];
                                        var nombre=distritos[e]['nombre'];
                                        abrir += '<option value="' + value + '" '
                                        if(val.campo_valor==value){
                                            abrir += 'selected'
                                        }
                                        abrir += ' > ' + nombre + ' </option>'
                                    }
                                }

                                abrir += '</select></div></div>';
                                $("#" + persona + val.padre_id + contador_universal + ":last").append(abrir)

                            }
                        }


                    });

                    cont = 0;

                } else {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>No existen campos</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);
                    return false;

                }
            }

    });

}

function asignar_identificador(identif) {
    identificador = identif;
}

function asignar_identificador_je(identif) {
    identificador_je = identif;
}

function asignar_imagen(con) {


    var input = $("#input_imagen" + con)

    if (input[0].files[0] && input[0].files[0]) {

        asignar_identificador(con)

        var reader = new FileReader();
        reader.onload = fileOnload;

        reader.readAsDataURL(input[0].files[0]);
    }

}



function asignar_imagen_je(con) {

    var input = $("#input_imagen_je" + con)

    if (input[0].files[0] && input[0].files[0]) {

        asignar_identificador_je(con)

        var reader = new FileReader();
        reader.onload = fileOnload_je;

        reader.readAsDataURL(input[0].files[0]);
    }


}

function fileOnload(e) {
    var result = e.target.result;

    $('#imgSalida0').attr("src", result);
}

function fileOnload_je(e) {

    var result = e.target.result;

    $('#imgSalida_je0').attr("src", result);
}

/*este borra la imagen de los productos, pero de la pestana IMAGEN*/

 function borrar_img_je(id, nombre, id_div){



     $.ajax({
         url: base_url + 'cliente/eliminarimg',
         type: "post",
         dataType: "json",
         data: {'id': id, 'nombre': nombre},
         success: function (data) {

             if (data.error == undefined) {

                 $("#div_imagen_producto_je" + id_div).remove();

                 var url=base_url+'recursos/img/la_foto.png'
                 var div_imagen=''
                 div_imagen+='<img id="imgSalida_je0" data-count="0" src="'+url+'" width="80%" '+
                 'height="150"><span class="btn btn-default" style="position:relative;width:150px;" id="subirImg_je">Subir '+
                 '<i class="fa fa-file-image-o" aria-hidden="true"></i></span>'+
                 '<input style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;width:100%;height:100%;opacity: 0;" '+
                 'type="file" onchange="asignar_imagen(0)" class="form-control input_imagen" '+
                 'data-count="0" name="userfile_je[]" accept="image/*" id="input_imagen_je0">'

                 $("#abrir_imagen_empresa").append(div_imagen);


             } else {

             }

         },
         error: function () {

         }
     })
 }



function borrar_img(id, nombre, id_div) {

    $.ajax({
        url: base_url + 'cliente/eliminarimg',
        type: "post",
        dataType: "json",
        data: {'id': id, 'nombre': nombre},
        success: function (data) {

            if (data.error == undefined) {

                $("#div_imagen_producto" + id_div).remove();

                var url=base_url+'recursos/img/la_foto.png'
                var div_imagen=''
                div_imagen+='<img id="imgSalida0" data-count="0" src="'+url+'" width="80%" '+
                'height="150"><span class="btn btn-default" style="position:relative;width:150px;" id="subirImg">Subir '+
                '<i class="fa fa-file-image-o" aria-hidden="true"></i></span>'+
                '<input style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;width:100%;height:100%;opacity: 0;" '+
                'type="file" onchange="asignar_imagen(0)" class="form-control input_imagen" '+
                'data-count="0" name="userfile[]" accept="image/*" id="input_imagen0">'

                $("#abrir_imagen_n").append(div_imagen);


            } else {

            }

        },
        error: function () {

        }
    })

}

$('.nav-pills a[href="#persona"]').on('shown.bs.tab', function(event){
    $('#tipo_cliente').attr('value','0');
    $('#tipo_iden').attr('value','1');
});

$('.nav-pills a[href="#empresa"]').on('shown.bs.tab', function(event){
   $('#tipo_cliente').attr('value','1');
   $('#tipo_iden').attr('value','2');
});