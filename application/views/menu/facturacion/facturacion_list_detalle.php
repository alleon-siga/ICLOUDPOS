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
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles del Comprobante</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Tipo Comprobante:</label>
                        </div>
                        <div class="col-md-3">
                            <?php
                            if ($facturacion->documento_tipo == '01') echo 'FACTURA';
                            if ($facturacion->documento_tipo == '03') echo 'BOLETA';
                            if ($facturacion->documento_tipo == '07') echo 'NOTA DE CREDITO';
                            if ($facturacion->documento_tipo == '08') echo 'NOTA DE DEBITO';
                            ?>
                        </div>


                        <div class="col-md-3">
                            <label class="control-label">Numero Comprobante:</label>
                        </div>
                        <div class="col-md-3"><?= $facturacion->documento_numero ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Fecha de Facturaci&oacute;n:</label>
                        </div>
                        <div class="col-md-3"><?= date('m/d/Y', strtotime($facturacion->fecha)) ?></div>


                        <div class="col-md-3">
                            <label class="control-label">Venta Referencia:</label>
                        </div>
                        <div class="col-md-3"><?= $facturacion->ref_id ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Cliente Identificaci&oacute;n:</label>
                        </div>
                        <div class="col-md-3">
                            <?php
                            if ($facturacion->cliente_tipo == 1) echo 'DNI: ';
                            elseif ($facturacion->cliente_tipo == 6) echo 'RUC: ';
                            ?>
                            <?= $facturacion->cliente_identificacion ?>
                        </div>


                        <div class="col-md-3">
                            <label class="control-label">Cliente Nombre:</label>
                        </div>
                        <div class="col-md-3"><?= $facturacion->cliente_nombre ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <?php if ($facturacion->documento_tipo == '07' || $facturacion->documento_tipo == '08'): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="control-label">Comprobante Afectado:</label>
                            </div>
                            <div class="col-md-3">
                                <?= $facturacion->documento_mod_numero ?>
                            </div>


                            <div class="col-md-3">
                                <label class="control-label">Motivo Afectado:</label>
                            </div>
                            <div class="col-md-3"><?= $facturacion->motivo_nota ?></div>
                        </div>

                        <hr class="hr-margin-5">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Estado del Comprobante:</label>
                        </div>
                        <div class="col-md-9">
                            <?= $facturacion->nota ?>
                        </div>
                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>C&oacute;digo</th>
                            <th>Producto</th>
                            <th>UM</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Impuesto</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($facturacion->detalles as $detalle): ?>
                            <tr>
                                <td><?= $detalle->id ?></td>
                                <td><?= $detalle->producto_codigo ?></td>
                                <td><?= $detalle->producto_descripcion ?></td>
                                <td><?= $detalle->um ?></td>
                                <td><?= $detalle->cantidad ?></td>
                                <td><?= $detalle->precio ?></td>
                                <td><?= $detalle->impuesto ?></td>
                                <td><?= $emisor->moneda_simbolo . " " . number_format($detalle->cantidad * $detalle->precio, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <br>
                <div class="row">
                    <div class="col-md-8">

                    </div>
                    <div class="col-md-4 text-right">
                        <table class="totales">
                            <tr>
                                <td>Subtotal:</td>
                                <td><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->subtotal, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Impuesto:</td>
                                <td><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->impuesto, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td><?= $emisor->moneda_simbolo ?> <?= number_format($facturacion->total, 2) ?></label></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-danger' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>