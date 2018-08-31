<?php $term = diccionarioTermino() ?>
<form name="formagregar" action="<?= base_url() ?>proveedor/guardar" method="post" id="formagregarproveedor">

    <input type="hidden" name="id" id=""
           value="<?php if (isset($proveedor['id_proveedor'])) echo $proveedor['id_proveedor']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Registrar nuevo proveedor</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"><?= $term[1]->valor.' / '.$term[0]->valor ?></label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_nrofax" id="proveedor_nrofax"  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_ruc'])) echo $proveedor['proveedor_ruc']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Razon Social</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_nombre" id="proveedor_nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_nombre'])) echo $proveedor['proveedor_nombre']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Dirección Fiscal</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_direccion1" id="proveedor_direccion1"  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_direccion1'])) echo $proveedor['proveedor_direccion1']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Tel&eacute;fono Empresa </label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_telefono1" id="proveedor_telefono1"  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_telefono1'])) echo $proveedor['proveedor_telefono1']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Correo</label>
                        </div>
                        <div class="col-md-9">
                            <input type="email" name="proveedor_email" id="proveedor_email"  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_email'])) echo $proveedor['proveedor_email']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">P&aacute;gina Web</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_paginaweb" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_paginaweb'])) echo $proveedor['proveedor_paginaweb']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Persona Contacto</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_direccion2" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_contacto'])) echo $proveedor['proveedor_contacto']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Tel&eacute;fono Contacto</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_telefono2" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_telefono2'])) echo $proveedor['proveedor_telefono2']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label panel-admin-text">Observaci&oacute;n</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="proveedor_observacion" id="" class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_observacion'])) echo $proveedor['proveedor_observacion']; ?>" autocomplete="off">
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"  class="btn btn-primary" id="confirmar_boton_proveedor" onclick="guardar_proveedor('proveedor')" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <div class="modaloader vertical">
                <div class="col-xs-12 text-center">
                    <img src="<?=base_url();?>recursos/img/circles.svg">
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
</form>

<script>

    function guardar_proveedor(retorno){

        if ($("#proveedor_nrofax").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe registrar el RUC </h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
        if ($("#proveedor_nombre").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe registrar el nombre</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
        if ($("#proveedor_direccion1").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe registrar la direccion fiscal</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
        if($("#proveedor_email").val()!="") {

            if(isEmail($("#proveedor_email").val()) == false)
            {

                $("#proveedor_email").focus()
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un formato v&aacute;lido de email</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        }

    $('#load_div').show()

        $.ajax({
            url: '<?= base_url()?>proveedor/guardar',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            dataType:'json',
            data: $("#formagregarproveedor").serialize(),
            success: function (data) {

                if (data.error == undefined) {
                    /*si retorno es producto, quiere decir que esta vista fue llamada desde el modulo de producto
                     * por lo tanto va allamar a update, y update esta en la vista de producto_form.
                     * Sino quiere decir que esta en su modulo normal, y retornara la vista nuevamente*/
                     if(retorno != 'proveedor'){
                        update_proveedor(data.id,data.nombre);
                     }
                    /*if(retorno=='producto') {
                        update_proveedor(data.id,data.nombre);
                    }else if(retorno=='gasto') {
                        update_proveedor(data.id,data.nombre);
                    }else if(retorno=='compras') {
                        update_proveedor(data.id,data.nombre);
                    }*/

                    var growlType = 'success';

                    $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });


                    retornar_proveedor(retorno)
                }else{
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $(this).prop('disabled', true);
                }
                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $('#load_div').hide()
                    }, 2000)
            },
            error: function(data){

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $('#load_div').hide()
                    }, 2000)
            }
        })



    }

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

    function retornar_proveedor (retorno){

        $("#agregarproveedor").modal('hide');

        if(retorno=="proveedor") {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            return $.ajax({
                url: '<?= base_url()?>proveedor',
                success: function (data2) {
                    $('#page-content').html(data2);
                }

            })
        }

    }

$( "input#proveedor_nrofax" ).keyup(function() {
    var input=$(this);
    RUC_DNI=$(this).val();
    var formData = new FormData();
    if (RUC_DNI.length==8) {
        formData.append('DNI', RUC_DNI);
        $.ajax({
            url: '<?= base_url()?>cliente/getDatosFromAPI_Reniec',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('div.modaloader').addClass('see');
            },
            success: function(data){
                console.log(data);
                if (data=='false') {
                    input.addClass('errorAPI');
                    $('input#proveedor_nombre').val('');
                }else{
                    input.removeClass('errorAPI');
                    var obj = $.parseJSON(data);
                    var Nombre  = obj['Nombre'];
                    var Paterno = obj['Paterno'];
                    var Materno = obj['Materno'];
                    $('input#proveedor_nombre').val(Nombre+' '+Paterno+' '+Materno);
                }
                $('div.modaloader').removeClass('see');
            },
            error: function(data){
              console.log('Error Ajax Peticion');
              console.log(data);
              $('div.modaloader').removeClass('see');
            }
        });
    }else if (RUC_DNI.length==11){
        formData.append('RUC', RUC_DNI);
        $.ajax({
            url: '<?= base_url()?>cliente/getDatosFromAPI_Sunac',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('div.modaloader').addClass('see');
            },
            success: function(data){
                console.log(data);
                if (data=='false') {
                    input.addClass('errorAPI');
                    $('input#proveedor_nombre').val('');
                    $('input#proveedor_telefono1').val('');
                    $('input#proveedor_direccion1').val('');
                }else{
                    input.removeClass('errorAPI');
                    var obj = $.parseJSON(data);
                    var RazonSocial = obj['RazonSocial'];
                    var Telefono    = obj['Telefono'];
                    var Direccion   = obj['Direccion'];
                    $('input#proveedor_nombre').val(RazonSocial);
                    $('input#proveedor_telefono1').val(Telefono);
                    $('input#proveedor_direccion1').val(Direccion);
                }
                $('div.modaloader').removeClass('see');
            },
            error: function(data){
              console.log('Error Ajax Peticion');
              console.log(data);
              $('div.modaloader').removeClass('see');
            }
        });
    }else{
        input.addClass('errorAPI');
        $('input#proveedor_nombre').val('');
        $('input#proveedor_telefono1').val('');
        $('input#proveedor_direccion1').val('');
    }
});
/*
$( "input#proveedor_nrofax" ).change(function() {

});
*/
    </script>
