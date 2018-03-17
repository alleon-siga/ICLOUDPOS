<ul class="breadcrumb breadcrumb-top">
    <li><a href="#">Ventas</a></li>
    <li><a href="#">Ofertas</a></li>

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
    <?= form_open_multipart(base_url() . 'venta_new/ofertas/save', array('id' => 'formguardar')) ?>


    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Vencimiento de la Oferta de Venta:</label>
        </div>

        <div class="col-md-8">
            <input type="text" id="FECHA_VENTA_PROMO"
                   name="FECHA_VENTA_PROMO" class="form-control" readonly style="cursor: pointer;"
                   value="<?= valueOption("FECHA_VENTA_PROMO", date('d/m/Y')) ?>">
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-4">
            <label class="control-label panel-admin-text">Oferta de Ventas:</label>
        </div>

        <div class="col-md-8">
            <textarea type="text" name="VENTA_PROMO" rows="10" id="VENTA_PROMO"
                      class='form-control textarea-editor'>
                <?= valueOption("VENTA_PROMO", '') ?>
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
                url: '<?= base_url()?>venta_new/ofertas'

            })
        },
        guardar: function () {

            App.formSubmitAjax($("#formguardar").attr('action'), this.ajaxgrupo, null, 'formguardar');
            //App.formSubmitAjax($("#formguardar").attr('action'), this.reloadOpciones, null, 'formguardar');
        },
        reloadOpciones: function () {
            window.location.href = '<?= base_url()?>venta_new/ofertas';
        }
    }

    $(function () {

        $('#FECHA_VENTA_PROMO').datepicker({
            format: 'dd/mm/yyyy'
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
</script>
