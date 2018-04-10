<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    .totales {
        width: 100%;
        text-align: right;
    }

    .totales tr td {
        padding: 5px 0;
        font-weight: bold;
    }
</style>
<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle del Ajuste</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped tableStyle" id="tabledetail">

                    <thead>
                    <tr>

                        <th>ID</th>
                        <th>Producto</th>
                        <th>UM</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Sub total</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($detalles)) {
                        $total = 0;
                        foreach ($detalles as $detalle) {
                            $total += $detalle->cantidad * $detalle->costo_unitario;
                            ?>
                            <tr>
                                <td><?= $detalle->id ?></td>
                                <td>
                                    <?= getCodigoValue(sumCod($detalle->producto_id), $detalle->producto_codigo_interno) . ' - ' . $detalle->producto_nombre ?>
                                </td>
                                <td><?= $detalle->nombre_unidad ?></td>
                                <td><?= $detalle->cantidad ?></td>
                                <td><?= $moneda->simbolo . " " . $detalle->costo_unitario ?></td>
                                <td>
                                    <?= $moneda->simbolo . " " . number_format($detalle->costo_unitario * $detalle->cantidad, 2) ?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>

            <br>
            <div class="row">
                <div class="col-md-9">

                </div>
                <div class="col-md-3 text-right">
                    <table class="totales">
                        <tr>
                            <td>Total:</td>
                            <td><?= $moneda->simbolo ?> <?= number_format($total, 2) ?></label></td>
                        </tr>
                    </table>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>

        </div>
    </div>
    <!-- /.modal-content -->
</div>

