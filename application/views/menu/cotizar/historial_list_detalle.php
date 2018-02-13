<input type="hidden" id="id" value="<?= $cotizar->id ?>">
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles de la Cotizacion</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row">
                        <div class="col-md-3"><label class="control-label">Documento Solicitado:</label></div>
                        <div class="col-md-3"><?= $cotizar->documento_nombre ?></div>

                        <div class="col-md-1"></div>

<!--                        <div class="col-md-2"><label-->
<!--                                    class="control-label">--><?//= 'Cotizacion Nro' ?>
<!--                                :</label></div>-->
<!--                        <div-->
<!--                                class="col-md-3">--><?//= sumCod($cotizar->id, 6) ?><!--</div>-->
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3"><label class="control-label">Fecha Emision:</label></div>
                        <div class="col-md-3"><?= date('d/m/Y', strtotime($cotizar->created)) ?></div>

                        <div class="col-md-1"></div>

<!--                        <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->condicion_nombre ?><!--</div>-->
                    </div>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3"><label class="control-label">Fecha Vencimiento:</label></div>
                        <div class="col-md-3"><?= date('d/m/Y', strtotime($cotizar->fecha)) ?></div>

                        <div class="col-md-1"></div>

                        <!--                        <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>-->
                        <!--                        <div class="col-md-3">--><?//= $cotizar->condicion_nombre ?><!--</div>-->
                    </div>

<!---->
<!--                    --><?php //if ($cotizar->condicion_id == '2'): ?>
<!--                        <hr class="hr-margin-5">-->
<!--                        <div class="row">-->
<!--                            <div class="col-md-2"><label class="control-label">Tipo de Credito:</label></div>-->
<!--                            <div class="col-md-3">-->
<!--                                --><?//= get_tipo_credito($cotizar->credito_periodo) ?>
<!--                            </div>-->
<!---->
<!--                            <div class="col-md-1"></div>-->
<!--                            --><?php //if ($cotizar->credito_periodo == 5): ?>
<!--                                <div class="col-md-2"><label class="control-label">Credito Dias:</label></div>-->
<!--                                <div class="col-md-3">--><?//= $cotizar->periodo_per ?><!--</div>-->
<!--                            --><?php //endif; ?>
<!--                        </div>-->
<!--                    --><?php //endif; ?>

                    <hr class="hr-margin-5">

                    <div class="row">
                        <div class="col-md-3"><label class="control-label">Cliente:</label></div>
                        <div class="col-md-3"><?= $cotizar->cliente_nombre ?></div>

                        <div class="col-md-1"></div>

<!--                        <div class="col-md-2"><label class="control-label">Vendedor:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->vendedor_nombre ?><!--</div>-->
                    </div>

<!--                    <hr class="hr-margin-5">-->
<!---->
<!--                    <div class="row">-->
<!--                        <div class="col-md-2"><label class="control-label">Moneda:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->moneda_nombre ?><!--</div>-->
<!---->
<!--                        <div class="col-md-1"></div>-->
<!---->
<!--                        <div class="col-md-2"><label class="control-label">Moneda Tasa:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->moneda_tasa ?><!--</div>-->
<!--                    </div>-->
<!---->
<!--                    <hr class="hr-margin-5">-->
<!---->
<!--                    <div class="row">-->
<!--                        <div class="col-md-2"><label class="control-label">Estado:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->estado ?><!--</div>-->
<!---->
<!--                        <div class="col-md-1"></div>-->
<!---->
<!--                        <div class="col-md-2"><label class="control-label">Cotizacion Total:</label></div>-->
<!--                        <div class="col-md-3">--><?//= $cotizar->moneda_simbolo . " " . $cotizar->total ?><!--</div>-->
<!--                    </div>-->

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
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($cotizar->detalles as $detalle): ?>
                            <tr>
                                <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                <td><?= $detalle->producto_nombre ?></td>
                                <td><?= $detalle->cantidad ?></td>
                                <td><?= $detalle->unidad_nombre ?></td>
                                <td style="text-align: right"><?= $detalle->precio ?></td>
                                <td style="text-align: right"><?= $cotizar->moneda_simbolo . " " . number_format($detalle->importe, 2) ?></td>
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
                        <input type="button" class='btn btn-default' value="Cerrar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
