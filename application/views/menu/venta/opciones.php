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
    <!--    <button type="button" id="imprimir">Imprimir</button>-->
    <?= form_open_multipart(base_url() . 'venta_new/opciones/save', array('id' => 'formguardar')) ?>
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

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Visualizar Cr&eacute;dito:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="VISTA_CREDITO" id="" class='' value="SIMPLE"
                    <?php echo validOption("VISTA_CREDITO", 'SIMPLE', 'AVANZADO') ? 'checked' : '' ?>> Simple
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="VISTA_CREDITO" id="" class='' value="AVANZADO"
                    <?php echo validOption("VISTA_CREDITO", 'AVANZADO', 'AVANZADO') ? 'checked' : '' ?>> Avanzado
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Habilitar Uso de Comprobante:</label>
        </div>
        <div class="col-md-8">
            <div class="form-control">
                <input type="radio" name="COMPROBANTE" id="" class='' value="1"
                    <?php echo validOption("COMPROBANTE", '1', '0') ? 'checked' : '' ?>> SI
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="COMPROBANTE" id="" class='' value="0"
                    <?php echo validOption("COMPROBANTE", '0', '0') ? 'checked' : '' ?>> NO
            </div>
        </div>
    </div>

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
            <label class="control-label panel-admin-text">Informacion de interes:</label>
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


    <?= form_close() ?>
</div>

<div class="row form-group">
    <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>

</div>


</div>
<script>


    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>venta_new/opciones'

            })
        },
        guardar: function () {

            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
            //App.formSubmitAjax($("#formguardar").attr('action'), this.reloadOpciones, null, 'formguardar');
        },
        reloadOpciones: function () {
            window.location.href = '<?= base_url()?>venta_new/opciones';
        }
    }

    $(function () {


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
</script>
