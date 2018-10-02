<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/spectrum.css">
<ul class="breadcrumb breadcrumb-top">
    <li><a href="#">Ventas</a></li>
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

    <?= form_open_multipart(base_url() . 'venta_new/opciones/save', array('id' => 'formguardar')) ?>
    <h3>Configuraciones de venta</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Cobrar en Caja:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="COBRAR_CAJA" id="" class='' value="1"
                    <?php echo validOption("COBRAR_CAJA", '1', '0') ? 'checked' : '' ?>> SI
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="COBRAR_CAJA" id="" class='' value="0"
                    <?php echo validOption("COBRAR_CAJA", '0', '0') ? 'checked' : '' ?>> NO
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Documento de venta por defecto:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <?php foreach($documentos as $doc) { ?>
                <input type="radio" name="DOCUMENTO_DEFECTO" value="<?= $doc->id_doc ?>" <?php echo validOption("DOCUMENTO_DEFECTO", $doc->id_doc, '0') ? 'checked' : '' ?>> <?= $doc->des_doc ?>
                &nbsp;&nbsp;&nbsp;
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Botones para guardar venta:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
        <?php
            $boton = json_decode(valueOption("BOTONES_VENTA"));
            $arr = array('Guardar', 'Guardar & imprimir', 'Guardar & detalles');
            foreach ($boton as $clave => $valor) {
        ?>
                <input type="checkbox" class="BOTONES_VENTA" name="BOTONES_VENTA[]" value="<?= $valor ?>" <?php echo ($valor=='1')? 'checked' : '' ?>> <?= $arr[$clave] ?>&nbsp;&nbsp;&nbsp;
        <?php
            } 
        ?>
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Configurar nombre de producto:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
        <?php
            $boton = json_decode(valueOption("NOMBRE_PRODUCTO"));
            $arr = array('Grupo', 'Familia', 'Linea', 'Modelo', 'Marca', 'Codigo interno');
            foreach ($boton as $clave => $valor) {
        ?>
                <input type="checkbox" class="NOMBRE_PRODUCTO" name="NOMBRE_PRODUCTO[]" value="<?= $valor ?>" <?php echo ($valor=='1')? 'checked' : '' ?>> <?= $arr[$clave] ?>&nbsp;&nbsp;&nbsp;
        <?php
            } 
        ?>
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Mostrar embalaje en impresi&oacute;n:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="EMBALAJE_IMPRESION" id="" class='' value="1"
                    <?php echo validOption("EMBALAJE_IMPRESION", '1', '0') ? 'checked' : '' ?>> SI
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="EMBALAJE_IMPRESION" id="" class='' value="0"
                    <?php echo validOption("EMBALAJE_IMPRESION", '0', '0') ? 'checked' : '' ?>> NO
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">N&uacute;mero de decimales en productos:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="NUMERO_DECIMALES" id="" class='' value="2"
                    <?php echo validOption("NUMERO_DECIMALES", '2', '2') ? 'checked' : '' ?>> 2
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="NUMERO_DECIMALES" id="" class='' value="4"
                    <?php echo validOption("NUMERO_DECIMALES", '4', '2') ? 'checked' : '' ?>> 4
            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Valor en el comprobante</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="VALOR_COMPROBANTE" id="" value="NOMBRE" <?php echo validOption("VALOR_COMPROBANTE", 'NOMBRE', 'NOMBRE') ? 'checked' : '' ?>>
                Nombre
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="VALOR_COMPROBANTE" id="" value="DESCRIPCION" <?php echo validOption("VALOR_COMPROBANTE", 'DESCRIPCION', 'NOMBRE') ? 'checked' : '' ?>>
                Descripci&oacute;n
            </div>
        </div>
    </div>
    <h3>Cr&eacute;dito</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Saldo Inicial (%):</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="CREDITO_INICIAL" required="true" id="CREDITO_INICIAL"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("CREDITO_INICIAL", '0') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Tasa de Interes (%):</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="CREDITO_TASA" required="true" id="CREDITO_TASA"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("CREDITO_TASA", '0') ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">M&aacute;ximo de Cuotas:</label>
        </div>

        <div class="col-md-8">
            <input type="text" name="CREDITO_CUOTAS" required="true" id="CREDITO_CUOTAS"
                   class='form-control'
                   maxlength="100"
                   value="<?= valueOption("CREDITO_CUOTAS", '10') ?>">
        </div>
    </div>

    <!--<div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Visualizar Cr&eacute;dito:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="VISTA_CREDITO" id="" class='' value="SIMPLE"
                    <?php //echo validOption("VISTA_CREDITO", 'SIMPLE', 'AVANZADO') ? 'checked' : '' ?>> Simple
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="VISTA_CREDITO" id="" class='' value="AVANZADO"
                    <?php //echo validOption("VISTA_CREDITO", 'AVANZADO', 'AVANZADO') ? 'checked' : '' ?>> Avanzado
            </div>
        </div>
    </div>-->
    <input type="hidden" name="VISTA_CREDITO" value="AVANZADO">

    <input type="hidden" name="COMPROBANTE" value="0"> <!-- Desarrollado solo para ramon -->
    <!--<div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Habilitar Uso de Comprobante:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="COMPROBANTE" id="" class='' value="1"
                    <?php //echo validOption("COMPROBANTE", '1', '0') ? 'checked' : '' ?>> SI
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="COMPROBANTE" id="" class='' value="0"
                    <?php //echo validOption("COMPROBANTE", '0', '0') ? 'checked' : '' ?>> NO
            </div>
        </div>
    </div>-->
    <?php if (validOption('ACTIVAR_SHADOW', 1)): ?>
        <div class="row form-group">
            <div class="col-md-4">
                <label class="control-label panel-admin-text">Costo Contable Aumento (%):</label>
            </div>

            <div class="col-md-8">
                <input type="text" name="COSTO_AUMENTO" required="true" id="COSTO_AUMENTO"
                       class='form-control'
                       maxlength="100"
                       value="<?= valueOption("COSTO_AUMENTO", '5') ?>">
            </div>
        </div>
    <?php endif; ?>

    <h3>Cotizaci&oacute;n</h3>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Cabecera de Cotizaci&oacute;n:</label>
        </div>

        <div class="col-md-8">
            <textarea type="text" name="COTIZACION_INFORMACION" rows="5" id="COTIZACION_INFORMACION"
                      class='form-control textarea-editor'>
                <?= valueOption("COTIZACION_INFORMACION", '') ?>
            </textarea>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Condiciones de Cotizacion:</label>
        </div>

        <div class="col-md-8">
            <textarea type="text" name="COTIZACION_CONDICION" rows="5" id="COTIZACION_CONDICION"
                      class='form-control textarea-editor'>
                <?= valueOption("COTIZACION_CONDICION", '') ?>
            </textarea>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Informacion pie de pagina:</label>
        </div>
        <div class="col-md-8">
            <textarea type="text" name="COTIZACION_PIE_PAGINA" rows="5" id="COTIZACION_PIE_PAGINA"
                      class='form-control textarea-editor'>
                <?= valueOption("COTIZACION_PIE_PAGINA", '') ?>
            </textarea>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Colores de formato:</label>
        </div>
        <div class="col-md-8">
        <?php
            $boton = json_decode(valueOption("COTIZACION_COLOR_FORMATO"));
            $arr = array('Nombre de Empresa', 'Bordes');
            $i = 1;
            foreach ($boton as $clave => $valor) {
        ?>
                <?= $arr[$clave] ?>
                <input id='colorpicker<?= $i ?>' class="form-control" name="COTIZACION_COLOR_FORMATO[]" value="<?= $valor ?>" />&nbsp;&nbsp;&nbsp;
        <?php
                $i++;
            } 
        ?>
        </div>
    </div>
    <?= form_close() ?>
</div>

<div class="row form-group">
    <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
</div>
</div>
<script src="<?= $ruta ?>recursos/js/spectrum.js"></script>
<script>
    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>venta_new/opciones'

            })
        },
        guardar: function () {
            $('.BOTONES_VENTA').prop('checked', true);
            $('.NOMBRE_PRODUCTO').prop('checked', true);
            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
            //App.formSubmitAjax($("#formguardar").attr('action'), this.reloadOpciones, null, 'formguardar');
        },
        reloadOpciones: function () {
            window.location.href = '<?= base_url()?>venta_new/opciones';
        }
    }

    $(function () {
        $('.BOTONES_VENTA, .NOMBRE_PRODUCTO').on('click', function(){
            if($(this).prop('checked')){
                $(this).attr('value', '1');
            }else{
                $(this).attr('value', '0');
            }
        });

        $('.textarea-editor').wysihtml5({
            "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
            "emphasis": true, //Italics, bold, etc. Default true
            "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": false, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false //Button to change color of font
        });
    })

    $("#colorpicker1, #colorpicker2").spectrum({
        preferredFormat: "hex3",
        showInput: true,
        showPalette: true,
        palette: [["red", "rgba(0, 255, 0, .5)", "rgb(0, 0, 255)"]]
    });
</script>
