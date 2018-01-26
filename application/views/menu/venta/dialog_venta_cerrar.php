<input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h4>Cerrar Venta</h4>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-3"><label class="control-label panel-admin-text">Correlativo Inicial:</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number"
                                   id="correlativo_inicial"
                                   value="<?= $correlativo->correlativo ?>"
                                   class="form-control">
                        </div>

                        <div class="col-md-1"></div>

                        <div class="col-md-3"><label
                                class="control-label panel-admin-text">Cantidad de Documentos:</label></div>
                        <div class="col-md-2">
                            <input type="number"
                                   id="cantidad_correlativo"
                                   value="1"
                                   class="form-control">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row-fluid">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Documento:</label></div>
                        <div class="col-md-3"><?= $venta->documento_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label
                                class="control-label"><?= 'Venta Nro' ?>
                                :</label></div>
                        <div
                            class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                        <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                        <div class="col-md-3"><?= $venta->condicion_nombre ?></div>
                    </div>


                    <?php if ($venta->condicion_id == '2'): ?>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cr&eacute;dito Deuda:</label></div>
                            <div
                                class="col-md-3">
                                <?= $venta->moneda_simbolo ?> <?= $venta_action == 'caja' ? $venta->total : $venta->credito_pendiente ?>
                            </div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Cr&eacute;dito Pagado:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->credito_pagado ?></div>
                        </div>
                    <?php endif; ?>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                        <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                        <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_nombre ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Moneda Tasa:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_tasa ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Estado:</label></div>
                        <div class="col-md-3"><?= $venta->venta_estado ?></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                        <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                    </div>

                    <hr class="hr-margin-5">
                </div>

            </div>
        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-primary"
                            type="button"
                            onclick="$('#confimar_cerrar').modal('show');"><i
                            class="fa fa-close"></i> Cerrar la Venta
                    </button>

                    <button class="btn btn-danger"
                            type="button"
                            onclick="$('#dialog_venta_cerrar').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confimar_cerrar" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 30%; top: 70px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Confirmaci&oacute;n</h4>
            </div>
            <div class="modal-body">

                <h4 style="text-align: center;">Al cerrar esta venta generara los correlativos correspondiente y ya no
                    podra volver imprimirla.
                    Estas Seguro?</h4>
            </div>
            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default"
                                id="btn_cerrar_venta"
                                type="button"><i
                                class="fa fa-check"></i> Confirmar
                        </button>

                        <button class="btn btn-danger"
                                type="button"
                                onclick="$('#confimar_cerrar').modal('hide');"><i
                                class="fa fa-close"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        $('#btn_cerrar_venta').on('click', function () {
            $('#confimar_cerrar').modal('hide');
            $.ajax({
                url: '<?= base_url("venta_new/cerrar_venta_save")?>',
                type: 'POST',
                dataType: 'json',
                data: {'venta_id': $("#venta_id").val(), 'correlativo_inicial': $("#correlativo_inicial").val(), 'cantidad_correlativo':$("#cantidad_correlativo").val()},
                success: function (data) {
                    if (data.success == '1') {
                        $.bootstrapGrowl('<h4>Completado. </h4><p>La venta ha sido cerrada.</p>', {
                            type: 'success',
                            delay: 5000,
                            allow_dismiss: true
                        });

                        $('#dialog_venta_cerrar').modal('hide');
                    }
                    else {
                        $.bootstrapGrowl('<h4>Error. </h4><p>Ha ocurrido un error inesperado.</p>', {
                            type: 'danger',
                            delay: 5000,
                            allow_dismiss: true
                        });
                    }
                },
                error: function (data) {
                    $.bootstrapGrowl('<h4>Error. </h4><p>Ha ocurrido un error inesperado.</p>', {
                        type: 'danger',
                        delay: 5000,
                        allow_dismiss: true
                    });
                },
                complete: function (data) {
                }
            });

        });
    });

</script>