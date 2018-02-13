<input type="hidden" id="cotizacion_id" value="<?= $cotizar->id ?>">
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Validar cotizacion</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-2"><label class="control-label">Documento:</label></div>
                        <div class="col-md-2"><?= $cotizar->documento_nombre ?></div>

                        <div class="col-md-2"><label class="control-label">Cotizacion Nro:</label></div>
                        <div class="col-md-2"><?= sumCod($cotizar->id, 6) ?></div>

                        <div class="col-md-2"><label class="control-label">Almacen:</label></div>
                        <div class="col-md-2"><?= $cotizar->local_nombre ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= getCodigoNombre() ?></th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>UM</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th>Cantidad Minima</th>
                            <th>En Almacen Min</th>
                            <th>Validado (<?= $cotizar->local_nombre ?>)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $visible = 'inline'; ?>
                        <?php foreach ($cotizar->detalles as $detalle): ?>
                            <tr class="<?= $detalle->cantidad_almacen_minima - $detalle->cantidad_minima < 0 ? 'danger' : 'success' ?>">
                                <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                <td><?= $detalle->producto_nombre ?></td>
                                <td><?= $detalle->cantidad ?></td>
                                <td><?= $detalle->unidad_nombre ?></td>
                                <td style="text-align: right"><?= $cotizar->moneda_simbolo . " " .$detalle->precio ?></td>
                                <td style="text-align: right"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->importe, 2) ?></td>
                                <td><?= $detalle->cantidad_minima ?></td>
                                <td><?= $detalle->cantidad_almacen_minima . ' ' . $detalle->um_min ?></td>
                                <td><?= $detalle->cantidad_almacen_minima - $detalle->cantidad_minima > 0 ? 'SI' : 'NO' ?></td>

                                <?php if ($detalle->cantidad_almacen_minima - $detalle->cantidad_minima < 0) $visible = 'none'; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <span style="display: <?= $visible ?>">
                        <input type="button" id="crear_venta" class="btn btn-primary" value="Crear Venta">
                        </span>
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

        $('#crear_venta').on('click', function () {
            $('#barloadermodal').modal('show');
            $.ajax({
                url: '<?= base_url()?>/venta_new/index/-/' + $('#cotizacion_id').val(),
                success: function (data) {
                    $('#page-content').html(data);
                    $('#barloadermodal').modal('hide');
                    $(".modal-backdrop").remove();
                }
            });
        });
    });
</script>
