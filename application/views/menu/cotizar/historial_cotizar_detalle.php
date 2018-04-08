<?php $ruta = base_url(); ?>
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

                    <table class="table table-bordered" id="my-table">
                        <thead>
                        <tr>
                            <th><?= getCodigoNombre() ?></th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>UM</th>
                            <th>Moneda</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th style="display: none">Cantidad Minima</th>
                            <th>En Almacen Min</th>
                            <th>Validado (<?= $cotizar->local_nombre ?>)</th>
                            <th style="display: none">identify</th>
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
                                <td><?= $cotizar->moneda_nombre ?></td>
                                <td style="text-align: right"><?= number_format($detalle->precio, 2) ?></td>
                                <td style="text-align: right"><?= number_format($detalle->importe, 2) ?></td>
                                <td style="display: none"><?= $detalle->cantidad_minima ?></td>
                                <td><?= $detalle->cantidad_almacen_minima . ' ' . $detalle->um_min ?></td>
                                <td><?= $detalle->cantidad_almacen_minima - $detalle->cantidad_minima > 0 ? 'SI' : 'NO' ?></td>
                                <td style="display: none;"><?= $detalle->detalle_id.'|'.$detalle->cotizacion_id ?></td>
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
<script type="text/javascript" src="<?= $ruta ?>recursos/js/jquery.tabledit.min.js"></script>
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

        $('#my-table').Tabledit({
            columns: {
                identifier: [10, 'identify'],
                editable: [[2, 'Cantidad'], [5, 'Precio']]
            },
            buttons: {
              edit: {
                class: 'btn btn-sm btn-default',
                html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
                action: 'edit'
              },
              delete: {
                class: 'btn btn-sm btn-danger',
                html: '<i class="fa fa-trash" aria-hidden="true"></i>',
                action: 'delete'
              },
              save: {
                class: 'btn btn-sm btn-success',
                html: 'Guardar'
              },
              confirm: {
                class: 'btn btn-sm btn-danger',
                html: 'Confirmar'
              }
            },
            restoreButton: false,
            url: '<?= base_url()?>cotizar/editarCotizacion/',
            onSuccess: function(rpta) {
                

                $.ajax({
                    url: '<?php echo $ruta . 'cotizar/get_cotizar_validar/'?>',
                    type: 'POST',
                    data: {'id': $('#cotizacion_id').val()},

                    success: function (data) {

                        if(rpta=='edit'){
                    $.bootstrapGrowl('<h4>Cotizaci&oacute;n actualizada</h4>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });                
                }else if(rpta=='delete'){
                    $.bootstrapGrowl('<h4>Cotizaci&oacute;n eliminada</h4>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });
                }
                
                        $("#dialog_cotizar_detalle").html(data);
                    },
                    error: function () {
                        alert('Error inesperado')
                    }
                });
                //$("#dialog_cotizar_detalle").modal('hide');
            }
        });
    });
</script>
