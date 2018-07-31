<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/selectize.default.css">
<style type="text/css">
    /**
     * Email Contacts
     */
    .selectize-control.contacts .selectize-input [data-value] .email {
        opacity: 0.5;
    }
    .selectize-control.contacts .selectize-input [data-value] .name + .email {
        margin-left: 5px;
    }
    .selectize-control.contacts .selectize-input [data-value] .email:before {
        content: '<';
    }
    .selectize-control.contacts .selectize-input [data-value] .email:after {
        content: '>';
    }
    .selectize-control.contacts .selectize-dropdown .caption {
        font-size: 12px;
        display: block;
        opacity: 0.5;
    }
    .label {
        margin-right: 10px !important;
    }
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Enviar cotizaci&oacute;n</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <form action="<?= base_url() ?>cotizar/enviarCotizacion" id="form1" name="form1" method="post">
                        <input type="hidden" name="idCotizacion" value="<?= $idCotizacion ?>">
                        <input type="hidden" name="tipoCliente" value="<?= $tipo_cliente ?>">
                        <div class="row">
                            <div class="col-md-12"><label class="control-label">Correo electr&oacute;nico</label></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <select id="txtMail" name="txtMail[]" class="contacts" placeholder="Escriba...">
                                    <option value="<?= $correo->email ?>"><?= $correo->email ?></option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div id="msjEnviar"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input id="btnEnviar" type="submit" class="btn btn-primary" value="Enviar">
                        <input type="button" class='btn btn-danger' value="Cerrar" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var ruta = '<?= base_url() ?>';
</script>
<script src="<?= base_url() ?>recursos/js/selectize.min.js"></script>
<script src="<?= base_url() ?>recursos/js/modalEnviarCotizacion.js"></script>