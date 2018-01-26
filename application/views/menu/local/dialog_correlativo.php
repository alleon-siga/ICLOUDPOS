<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Configuraci&oacute;n de los Correlativos</h4>
        </div>
        <div class="modal-body panel-venta-left">

            <h4>Ubicaci&oacute;n: <span id="local_nombre_almacen"></span></h4>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label panel-admin-text">Documento</label>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label panel-admin-text">Serie</label>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label panel-admin-text">Correlativo</label>
                    </div>
                </div>
            </div>

            <?php foreach ($documentos as $documento): ?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text docs"
                                   data-id="<?= $documento->id_doc ?>"
                                   style="font-size: 12px; text-align: left;">
                                <?= $documento->des_doc ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="serie_<?= $documento->id_doc ?>"
                                   class="form-control serie_class"
                                   value="000">
                        </div>
                        <div class="col-md-4">
                            <input type="number" id="next_<?= $documento->id_doc ?>"
                                   class="form-control next_class"
                                   min="1"
                                   step="1"
                                   value="1">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>


        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">

                    <button class="btn btn-default" data-imprimir="0" style="margin-bottom:5px"
                            type="button"
                            id="guardar_correlativo"
                            onclick="save_correlativos();">
                    <i class="fa fa-save"></i> Guardar
                    </button>

                    <button class="btn btn-danger" style="margin-bottom:5px"
                            type="button"
                            onclick="$('#dialog_correlativo').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {


    });

</script>