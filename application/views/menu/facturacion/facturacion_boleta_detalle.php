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
            <h3>Detalles del Resumen</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Tipo Comprobante:</label>
                        </div>
                        <div class="col-md-3">
                            RESUMEN
                        </div>


                        <div class="col-md-3">
                            <label class="control-label">Numero Comprobante:</label>
                        </div>
                        <div class="col-md-3"><?= 'RC-' . date('Ymd', strtotime($resumen->fecha)) . '-' . $resumen->correlativo ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Fecha de Emisi&oacute;n:</label>
                        </div>
                        <div class="col-md-3"><?= date('m/d/Y', strtotime($resumen->fecha)) ?></div>


                        <div class="col-md-3">
                            <label class="control-label">Fecha de Facturaci&oacute;n:</label>
                        </div>
                        <div class="col-md-3"><?= date('d/m/Y', strtotime($resumen->fecha_ref)) ?></div>
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3">
                            <label class="control-label">Estado del Comprobante:</label>
                        </div>
                        <div class="col-md-9">
                            <?= $resumen->nota ?>
                        </div>
                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ref. Venta</th>
                            <th>Documento</th>
                            <th>Numero</th>
                            <th>Estado</th>
                            <th>Descuento</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($boletas as $boleta): ?>
                            <tr>
                                <td><?= $boleta->id ?></td>
                                <td><?= $boleta->ref_id ?></td>
                                <td><?php
                                    if ($boleta->documento_tipo == '01') echo 'FACTURA';
                                    if ($boleta->documento_tipo == '03') echo 'BOLETA';
                                    if ($boleta->documento_tipo == '07') echo 'NOTA DE CREDITO';
                                    if ($boleta->documento_tipo == '08') echo 'NOTA DE DEBITO';
                                    ?></td>
                                <td><?= $boleta->documento_numero ?></td>
                                <td style="white-space: nowrap;">

                                    <?php
                                    $estado = '';
                                    $estado_class = '';
                                    if ($boleta->estado == 0) {
                                        $estado_class = 'label-warning';
                                        $estado = 'NO GENERADO';
                                    } elseif ($boleta->estado == 1) {
                                        $estado_class = 'label-info';
                                        $estado = 'GENERADO';
                                    } elseif ($boleta->estado == 2) {
                                        $estado_class = 'label-warning';
                                        $estado = 'ENVIADO';
                                    } elseif ($boleta->estado == 3) {
                                        $estado_class = 'label-success';
                                        $estado = 'ACEPTADO';
                                    } elseif ($boleta->estado == 4) {
                                        $estado_class = 'label-danger';
                                        $estado = 'RECHAZADO';
                                    }

                                    ?>
                                    <div
                                            class="label <?= $estado_class ?>"
                                            style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                        <?= $estado ?>
                                    </div>
                                </td>
                                <td><?= $boleta->descuento ?>%</td>
                                <td style="text-align: right;"><?= $emisor->moneda_simbolo ?> <?= number_format($boleta->total, 2) ?></td>
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
                        <input type="button" class='btn btn-danger' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>