<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/selectize.default.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/selectize.app.css">
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
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Asunto</label>
                                <input id="txtAsunto" name="txtAsunto" type="text" class="form-control" value="<?= "COTIZACION ".valueOption('EMPRESA_NOMBRE'); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Correo electr&oacute;nico</label>
                                <select id="txtMail" name="txtMail[]" class="contacts" placeholder="Escriba...">
                                    <option value="<?= $correo->email ?>"><?= $correo->email ?></option>
                                </select>
                            </div>
                        </div>
                    </form>
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