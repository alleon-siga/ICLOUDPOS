<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/selectize.default.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/selectize.app.css">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Enviar venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <form action="<?= base_url() ?>venta/enviarVenta" id="form1" name="form1" method="post">
                        <input type="hidden" name="idVenta" value="<?= $idVenta ?>">
                        <input type="hidden" name="tipoCliente" value="<?= $tipo_cliente ?>">
                        <input type="hidden" name="razon_social" value="<?= $razon_social ?>">
                        <input type="hidden" name="idFacturacion" value="<?= $facturacion->id ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Asunto</label>
                                <input id="txtAsunto" name="txtAsunto" type="text" class="form-control" value="<?= valueOption('EMPRESA_NOMBRE')." te ha enviado un nuevo comprobante de venta"; ?>">
                            </div>
                        </div>
                        <?php if(!empty($facturacion->id)){ ?>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="checkbox" class="chk" id="nv" name="tipo[]" value="NV">
                                <label for="nv" class="control-label panel-admin-text" style="cursor: hand;">Nota Venta</label>
                            </div>
                            <div class="col-md-6">
                                <input type="checkbox" class="chk" id="ce" name="tipo[]" value="CE"> 
                                <label for="ce" class="control-label panel-admin-text" style="cursor: hand;">Comprobante electr&oacute;nico</label>
                            </div>
                        </div>
                        <br>
                        <?php }else{ ?>
                            <input type="hidden" name="tipo[]" value="NV">
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Correo electr&oacute;nico</label>
                                <select id="txtMail" name="txtMail[]" class="contacts" placeholder="Escriba...">
                                    <option value="<?= $email ?>"><?= $email ?></option>
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
<script src="<?= base_url() ?>recursos/js/modalEnviarVenta.js"></script>
