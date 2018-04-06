<ul class="breadcrumb breadcrumb-top">
    <li><a href="#">Configuraci&oacute;n</a></li>
    <li><a href="#">Opciones</a></li>

</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Error</h4>
            <span id="errorspan"><?php //echo isset($error) ? $error : '' ?></div>
    </div>
</div>
<div class="row block">

    <?= form_open_multipart(base_url() . 'opciones/index/save', array('id' => 'formguardar', 'method' => 'post')) ?>
    <h3>Generales</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Nombre de la empresa:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="EMPRESA_NOMBRE" required="true" id="EMPRESA_NOMBRE"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("EMPRESA_NOMBRE", 'TEAYUDO') ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Identificaci&oacute;n de la empresa:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="EMPRESA_IDENTIFICACION" required="true" id="EMPRESA_IDENTIFICACION"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("EMPRESA_IDENTIFICACION", '') ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Correo de la empresa:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="EMPRESA_CORREO" required="true" id="EMPRESA_CORREO"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("EMPRESA_CORREO", '') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Contacto de la empresa:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="EMPRESA_CONTACTO" required="true" id="EMPRESA_CONTACTO"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("EMPRESA_CONTACTO", '') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Telefono de la empresa:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="EMPRESA_TELEFONO" required="true" id="EMPRESA_TELEFONO"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("EMPRESA_TELEFONO", '') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Logo de la empresa:</label>
        </div>

        <div class="col-md-6">
            <div class="input-prepend input-append input-group">
                <span class="input-group-addon"><i class="fa fa-folder"></i> </span>
                <input type="file" onchange="asignar_imagen(0)" class="form-control input_imagen"
                       data-count="0" name="userfile[]" accept="image/*"
                       id="input_imagen0">

            </div>
        </div>

        <div class="col-md-2">
            <img id="imgSalida0" data-count="0"
                 src="" height="100"
                 width="100">

        </div>
    </div>

    <!--    -->
    <!--    <div class="row form-group">-->
    <!--        <div class="col-md-4">-->
    <!--            <label class="control-label panel-admin-text">Empresa logo:</label>-->
    <!--        </div>-->
    <!---->
    <!--        <div class="col-md-8">-->
    <!--            <input type="file" name="EMPRESA_LOGO" required="true" id="EMPRESA_LOGO"-->
    <!--                   class='form-control'-->
    <!--                   value="">-->
    <!--        </div>-->
    <!--    </div>-->

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Código Identificativo:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="CODIGO_DEFAULT" id="" class='' value="INTERNO"
                    <?php echo validOption("CODIGO_DEFAULT", 'INTERNO', 'INTERNO') ? 'checked' : '' ?>>
                Código Interno
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="CODIGO_DEFAULT" id="" class='' value="AUTO"
                    <?php echo validOption("CODIGO_DEFAULT", 'AUTO', 'INTERNO') ? 'checked' : '' ?>> Código
                Autogenerado
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Valor Unico de Producto:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="VALOR_UNICO" id="" class='' value="NOMBRE"
                    <?php echo validOption("VALOR_UNICO", 'NOMBRE', 'NOMBRE') ? 'checked' : '' ?>> Nombre
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="VALOR_UNICO" id="" class='' value="MODELO"
                    <?php echo validOption("VALOR_UNICO", 'MODELO', 'NOMBRE') ? 'checked' : '' ?>> Modelo
            </div>
        </div>
    </div>


    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Precio Base de Ingreso:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="PRECIO_INGRESO" id="" class='' value="IMPORTE"
                    <?php echo validOption("PRECIO_INGRESO", 'IMPORTE', 'COSTO') ? 'checked' : '' ?>> SubTotal
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="PRECIO_INGRESO" id="" class='' value="COSTO"
                    <?php echo validOption("PRECIO_INGRESO", 'COSTO', 'COSTO') ? 'checked' : '' ?>> Costo Unitario
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Habilitar Serie de Producto:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="PRODUCTO_SERIE" id="" class='' value="1"
                    <?php echo validOption("PRODUCTO_SERIE", '1') ? 'checked' : '' ?>> Si
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="PRODUCTO_SERIE" id="" class='' value="0"
                    <?php echo validOption("PRODUCTO_SERIE", '0') ? 'checked' : '' ?>> No
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Costo del Ingreso (%):</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="INGRESO_COSTO" required="true" id="INGRESO_COSTO"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("INGRESO_COSTO", '0') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Utilidad del Ingreso (%):</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="INGRESO_UTILIDAD" required="true" id="INGRESO_UTILIDAD"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("INGRESO_UTILIDAD", '0') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Habilitar Opci&oacute;n de pago anticipado:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="PAGOS_ANTICIPADOS" id="" class='' value="1"
                    <?php echo validOption("PAGOS_ANTICIPADOS", '1') ? 'checked' : '' ?>> Si
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="PAGOS_ANTICIPADOS" id="" class='' value="0"
                    <?php echo validOption("PAGOS_ANTICIPADOS", '0') ? 'checked' : '' ?>> No
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Host de impresion cliente:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="HOST_IMPRESION" required="true" id="HOST_IMPRESION"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("HOST_IMPRESION", 'http://localhost:8080') ?>">
        </div>
    </div>


    <h3>Facturaci&oacute;n</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Activar Facturaci&oacute;n Ventas:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="ACTIVAR_FACTURACION_VENTA" id="" class='' value="1"
                    <?php echo validOption("ACTIVAR_FACTURACION_VENTA", '1') ? 'checked' : '' ?>> Si
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="ACTIVAR_FACTURACION_VENTA" id="" class='' value="0"
                    <?php echo validOption("ACTIVAR_FACTURACION_VENTA", '0') ? 'checked' : '' ?>> No
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Activar Facturaci&oacute;n Ingreso:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="ACTIVAR_FACTURACION_INGRESO" id="" class='' value="1"
                    <?php echo validOption("ACTIVAR_FACTURACION_INGRESO", '1') ? 'checked' : '' ?>> Si
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="ACTIVAR_FACTURACION_INGRESO" id="" class='' value="0"
                    <?php echo validOption("ACTIVAR_FACTURACION_INGRESO", '0') ? 'checked' : '' ?>> No
            </div>
        </div>
    </div>

    <h3>Shadow Stock</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Activar Shadow Stock:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="ACTIVAR_SHADOW" id="" class='' value="1"
                    <?php echo validOption("ACTIVAR_SHADOW", '1') ? 'checked' : '' ?>> Si
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="ACTIVAR_SHADOW" id="" class='' value="0"
                    <?php echo validOption("ACTIVAR_SHADOW", '0') ? 'checked' : '' ?>> No
            </div>
        </div>
    </div>


    <?= form_close() ?>
</div>

<div class="row form-group">
    <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>

</div>


</div>
<script>

    $(function () {
        $('#imgSalida0').attr('src', '<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", '')) ?>?' + new Date().getTime());
    });

    var contador_img = 0
    var identificador = 0

    function guardar_form() {
        var formData = new FormData($("#formguardar")[0]);
        $.ajax({
            url: '<?= base_url()?>opciones/index/save',
            type: "post",
            dataType: "json",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                show_msg('success', 'La configuracion se ha guardado correctamente');

                $("#barloadermodal").modal('show');
                $.ajax({
                    url: '<?= base_url()?>opciones/index?' + new Date().getTime(),
                    cache: false,
                    success: function (data) {
                        $("#barloadermodal").modal('hide');
                        $(".modal-backdrop").remove();
                        $('#page-content').html(data);
                        $('#imgSalida0').attr('src', '<?= base_url('recursos/img/logo/' . valueOption("EMPRESA_LOGO", '')) ?>?' + new Date().getTime());
                        $('#EMPRESA_NOMBRE').focus();
                    }
                });

            },
            error: function (response) {
                alert('Error inesperado');

            }

        });
    }

    function asignar_identificador(identif) {
        identificador = identif;
    }

    function fileOnload(e) {
        var result = e.target.result;
        $('#imgSalida' + identificador).attr("src", result);

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

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>opciones'

            })
        },
        guardar: function () {

            guardar_form();

//            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
            //App.formSubmitAjax($("#formguardar").attr('action'), this.reloadOpciones, null, 'formguardar');
        },
        reloadOpciones: function () {
            window.location.href = '<?= base_url()?>opciones';
        }
    }
</script>
