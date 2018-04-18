<input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">

<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Facturar Venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-4"><label class="control-label">Venta No.:</label></div>
                        <div class="col-md-6"><?= sumCod($venta->venta_id, 6) ?></div>
                    </div>
                    <hr class="hr-margin-5">
                    <div class="row">
                        <div class="col-md-4"><label class="control-label">Fecha Documento:</label></div>
                        <div class="col-md-6"><?= date('m/d/Y') ?></div>
                    </div>
                    <hr class="hr-margin-5">
                    <div class="row">
                        <div class="col-md-4"><label class="control-label">Documento:</label></div>
                        <div class="col-md-6">
                            <select id="cboDoc" class="form-control">
                            <?php foreach ($comprobante as $dato): ?>
                                <option value="<?= $dato->id_doc ?>" <?php if($venta->documento_id == $dato->id_doc){ echo "selected"; } ?>>
                                    <?= $dato->des_doc ?></option>
                            <?php endforeach; ?>
                                
                            </select>
                        </div>
                    </div>
                    <hr class="hr-margin-5">
                    <div class="row">
                        <div class="col-md-4"><label class="control-label">Documento Numero:</label></div>
                        <div class="col-md-6" id="docNum"><?= $venta->next_correlativo ?></div>
                    </div>
                    <hr class="hr-margin-5">
                    <?php if ($venta->comprobante_id > 0): ?>
                        <div class="row">
                            <div class="col-md-4"><label class="control-label">Comprobante:</label></div>
                            <div class="col-md-6"><?= $venta->comprobante_nombre ?></div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-4"><label class="control-label">Comprobante Numero:</label></div>
                            <div class="col-md-6"><?= $venta->comprobante ?></div>
                        </div>
                        <hr class="hr-margin-5">
                    <?php endif; ?>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" id="facturar_btn" class='btn btn-default' value="Facturar">

                        <input type="button" class='btn btn-danger' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<script>

    $(function () {

        $("#confirm_venta_text").html($("#loading").html());

        $('#facturar_btn').on('click', function () {
            $.ajax({
                url: '<?php echo base_url() . 'venta_new/facturar_venta'; ?>',
                type: 'POST',
                data: {
                    'venta_id': '<?= $venta->venta_id ?>',
                    'iddoc': $('#cboDoc').val()
                },
                success: function () {
                    $('#dialog_venta_confirm').modal('hide');
                    $('#dialog_venta_facturar').modal('hide');
                    $(".modal-backdrop").remove();
                    $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta facturada con exito.</p>', {
                        type: 'success',
                        delay: 5000,
                        allow_dismiss: true
                    });
                    get_ventas();
                },
                error: function () {
                    alert('Error inesperado');
                }
            });
        })

        $('#cboDoc').on('change', function(){
            $.ajax({
                url: '<?= base_url() ?>venta_new/getDocumentoNumero',
                type: 'POST',
                data: {
                    'iddoc': $('#cboDoc').val(),
                    'local_id': '<?= $venta->local_id ?>'
                },
                success: function(data){
                    $('#docNum').text(data);
                }
            });
        });
    });


</script>
