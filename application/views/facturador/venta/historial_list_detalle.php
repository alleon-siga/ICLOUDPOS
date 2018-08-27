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
<input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detalles de la Venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <input type="hidden" name="txtMoneda" id="txtMoneda" value="<?= $venta->moneda_simbolo ?>">
                <input type="hidden" name="txtTipoImpuesto" id="txtTipoImpuesto" value="<?= $venta->tipo_impuesto ?>">
                <?php if ($venta->condicion_id == '1'): ?>
                    <div class="row-fluid">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                            <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2"><label class="control-label">Documento:</label>
                            </div>
                            <div class="col-md-3">
                                <?php
                                $doc = '';
                                if ($venta->documento_id == 1) $doc = "FA";
                                if ($venta->documento_id == 2) $doc = "NC";
                                if ($venta->documento_id == 3) $doc = "BO";
                                if ($venta->documento_id == 4) $doc = "GR";
                                if ($venta->documento_id == 5) $doc = "PCV";
                                if ($venta->documento_id == 6) $doc = "NV";
                                if ($venta->numero != '')
                                    echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                else
                                    echo '<span style="color: #0000FF">NO EMITIDO</span>';
                                ?>
                            </div>
                        </div>
                        <hr class="hr-margin-5">
                        <?php if ($venta->comprobante_id > 0): ?>
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Comp. Nro.:</label></div>
                                <div
                                        class="col-md-3"><?= $venta->comprobante ?></div>
                            </div>

                            <hr class="hr-margin-5">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                            <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                            <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                            <div class="col-md-3"><?= $venta->condicion_nombre ?></div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                            <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
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
                        <table class="table table-bordered" id="my-table">
                            <thead>
                            <tr>
                                <th width="10%"><?= getCodigoNombre() ?></th>
                                <th width="20%">Producto</th>
                                <th width="10%">Cantidad</th>
                                <th width="10%">UM</th>
                                <th width="10%">Precio</th>
                                <th width="10%">Subtotal</th>
                                <th width="10%">P. Contable</th>
                                <th width="10%">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($venta->detalles as $detalle): ?>
                                <tr>
                                    <td style="white-space: normal;">
                                        <?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?>
                                        <input type="hidden" name="Id" value="<?= $detalle->producto_id ?>" data-simbolo="<?= $venta->moneda_simbolo ?>" data-venta="<?= $venta->venta_id ?>" data-unidad="<?= $detalle->unidad_id_min ?>" data-moneda="<?= $venta->moneda_id ?>" data-impuesto="<?= $detalle->impuesto_porciento ?>" data-importe="<?= $detalle->importe ?>">
                                    </td>
                                    <td style="white-space: normal;"><?= $detalle->producto_nombre ?></td>
                                    <td style="white-space: normal;"><input type="number" class="form-control Cantidad" name="Cantidad" value="<?= $detalle->cantidad ?>"></td>
                                    <td style="white-space: normal;"><?= $detalle->unidad_nombre ?></td>
                                    <td style="white-space: normal;"><input type="number" class="form-control Precio" name="Precio" data-precio="<?= $detalle->precio ?>" value="<?= $detalle->precio ?>"></td>
                                    <td style="white-space: normal;" class="importe"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                    <td><input type="checkbox" class="costoContable" name="chkCostoContable" value="<?= $detalle->contable_costo ?>"></td>
                                    <td>
                                        <div class="input-group">
                                            <a class="input-group-addon btn-primary btnEditar" data-toggle="tooltip" title="Editar" href="#">
                                                <i class="fa fa-pencil"></i>
                                            </a>&nbsp;
                                            <a class="input-group-addon btn-danger btnDelete" data-toggle="tooltip" title="Eliminar" href="#">
                                                <i class="fa fa-trash-o"></i>
                                            </a>&nbsp;
                                            <a class="input-group-addon btn-default btnNuevo" data-toggle="tooltip" title="Agregar" href="#">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                                <!--<tr style="">
                                    <td style="white-space: normal;"></td>
                                    <td style="white-space: normal;"></td>
                                    <td style="white-space: normal;"></td>
                                    <td style="white-space: normal;"></td>
                                    <td style="white-space: normal;"></td>
                                    <td style="white-space: normal;"></td>
                                    <td></td>
                                    <td>
                                        <div class="input-group">
                                            <a class="input-group-addon btn-primary btnEditar" data-toggle="tooltip" title="Editar" href="#">
                                                <i class="fa fa-pencil"></i>
                                            </a>&nbsp;
                                            <a class="input-group-addon btn-danger btnDelete" data-toggle="tooltip" title="Eliminar" href="#">
                                                <i class="fa fa-trash-o"></i>
                                            </a>&nbsp;
                                            <a class="input-group-addon btn-default btnNuevo" data-toggle="tooltip" title="Agregar" href="#">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>-->
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if ($venta->condicion_id == '2'): ?>
                    <div class="row-fluid">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                            <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>
                            <div class="col-md-1"></div>
                            <div class="col-md-2"><label class="control-label">Documento:</label>
                            </div>
                            <div class="col-md-3">
                                <?php
                                $doc = '';
                                if ($venta->documento_id == 1) $doc = "FA";
                                if ($venta->documento_id == 2) $doc = "NC";
                                if ($venta->documento_id == 3) $doc = "BO";
                                if ($venta->documento_id == 4) $doc = "GR";
                                if ($venta->documento_id == 5) $doc = "PCV";
                                if ($venta->documento_id == 6) $doc = "NV";
                                if ($venta->numero != '')
                                    echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                                else
                                    echo '<span style="color: #0000FF">NO EMITIDO</span>';
                                ?>
                            </div>
                        </div>
                        <hr class="hr-margin-5">
                        <?php if ($venta->comprobante_id > 0): ?>
                            <div class="row">
                                <div class="col-md-2"><label class="control-label">Comprobante:</label></div>
                                <div class="col-md-3"><?= $venta->comprobante_nombre ?></div>

                                <div class="col-md-1"></div>

                                <div class="col-md-2"><label
                                            class="control-label">Comp. Nro.:</label></div>
                                <div
                                        class="col-md-3"><?= $venta->comprobante ?></div>
                            </div>
                            <hr class="hr-margin-5">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Fecha:</label></div>
                            <div class="col-md-3"><?= date('d/m/Y H:i:s', strtotime($venta->venta_fecha)) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Moneda:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_nombre ?></div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Tipo de Pago:</label></div>
                            <div class="col-md-3"><?= $venta->condicion_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Importe de Deuda:</label></div>
                            <div
                                    class="col-md-3">
                                <?= $venta->moneda_simbolo ?> <?= $venta->credito_pendiente ?>
                            </div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Cliente:</label></div>
                            <div class="col-md-3"><?= $venta->cliente_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Importe Inicial:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->inicial ?></div>
                        </div>
                        <hr class="hr-margin-5">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Vendedor:</label></div>
                            <div class="col-md-3"><?= $venta->vendedor_nombre ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Tipo de Cambio:</label></div>
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
                        <div class="row">
                            <div class="col-md-12 text-center">
                                Dias de Gracia: <?= $venta->periodo_gracia ?> /
                                Numero de Cuotas: <?= count($venta->cuotas) ?> /
                                Tasa de Interes: <?= $venta->tasa_interes ?>%
                            </div>
                        </div>
                        <br>
                        <table class="table table-bordered" id="my-table">
                            <thead>
                            <tr>
                                <th><?= getCodigoNombre() ?></th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>UM</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th style="display: none;">identify</th>
                                <th style="display: none;">impuesto</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($venta->detalles as $detalle): ?>
                                <tr>
                                    <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                    <td><?= $detalle->producto_nombre ?></td>
                                    <td><?= number_format($detalle->cantidad, 0) ?></td>
                                    <td><?= $detalle->unidad_nombre ?></td>
                                    <td style="text-align: right" class="precio"><?= $detalle->precio ?></td>
                                    <td style="text-align: right" class="importe"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                    <td style="display: none;"><?= $venta->venta_id.'_'.$detalle->producto_id.'_'.$detalle->unidad_id ?></td>
                                    <td style="display: none;" class="impuesto"><?= $detalle->impuesto_porciento ?></td>
                                    <td><input type="checkbox" class="" name="chkCostoContable" value="<?= $detalle->precio ?>" data-producto="<?= $detalle->producto_id ?>" data-moneda="<?= $venta->moneda_id ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <br>
                <div class="row">
                    <div class="col-md-8">
                        <?php if ($venta->condicion_id == '2'): ?>
                            <h4>Cuotas y Vencimientos</h4>
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Letra</th>
                                        <th>Vence</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($venta->cuotas as $cuota): ?>
                                    <tr>
                                        <td><?= $cuota->nro_letra ?></td>
                                        <td><?= date('d/m/Y', strtotime($cuota->fecha_vencimiento)) ?></td>
                                        <td><?= $venta->moneda_simbolo . ' ' . number_format($cuota->monto, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif ?>

                        <?php if ($venta->nota != NULL): ?>
                            <h4>Notas:</h4>
                            <?= $venta->nota ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-right">
                        <table class="totales">
                            <tr>
                                <td>Subtotal:</td>
                                <td id="tdSubtotal"><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></label></td>
                            </tr>
                            <!--<tr>
                                <td>Descuento:</td>
                                <td id="tdDescuento"><?= $venta->moneda_simbolo ?> <? //number_format($venta->descuento, 2) ?></label></td>
                            </tr>-->
                            <tr>
                                <td>Impuesto:</td>
                                <td id="tdImpuesto"><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td id="tdTotal"><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></label></td>
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
                        <input type="button" class='btn btn-danger' value="Cerrar" data-dismiss="modal">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= base_url() ?>recursos/js/facturador_historial_list_detalle.js"></script>