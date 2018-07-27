<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Enviar cotizaci&oacute;n</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <form action="<?= base_url() ?>cotizar/enviarCotizacion" id="form1" method="post">
                        <div class="row">
                            <div class="col-md-3"><label class="control-label">Correo electr&oacute;nico</label></div>
                            <div class="col-md-9"><input name="txtMail" type="mail" class="form-control" value=""></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input id="btnEnviar" type="button" class="btn btn-primary" value="Enviar">
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
<script src="<?= base_url() ?>recursos/js/modalEnviarCotizacion.js"></script>