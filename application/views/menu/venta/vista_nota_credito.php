<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>
            <h4 class="modal-title">Nota de cr&eacute;dito</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4"><?= $notas_credito->serie . '-' . sumCod($notas_credito->numero, 8) ?></div>
                <div class="col-md-4"><?= date('d/m/Y H:i:s', strtotime($notas_credito->fecha)) ?></div>
                <div class="col-md-4"><?= $notas_credito->nombre ?></div>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th><?= getCodigoNombre() ?></th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>UM</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
                <?php $Subtotal = 0; ?>
                <?php foreach ($notas_credito->detalles as $detalle): ?>
                    <tr>
                        <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                        <td><?= $detalle->producto_nombre ?></td>
                        <td><?= $detalle->cantidad ?></td>
                        <td><?= $detalle->um ?></td>
                        <td><?= $venta->moneda_simbolo . ' ' . number_format($detalle->precio, 2) ?></td>
                        <td><?= $venta->moneda_simbolo . ' ' . number_format($detalle->cantidad * $detalle->precio, 2) ?></td>
                    </tr>
                    <?php $Subtotal += ($detalle->cantidad * $detalle->precio) ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td align="right" colspan="5">Total</td>
                    <td><?= $venta->moneda_simbolo . ' ' . number_format($Subtotal, 2) ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary btn_venta_imprimir imprimir" type="button" data-nombre="nota_credito">
                <i class="fa fa-print"></i> Imprimir
            </button>
            <a href="#" class="btn btn-danger" data-dismiss="modal">Cerrar</a>
        </div>
    </div>
</div>
