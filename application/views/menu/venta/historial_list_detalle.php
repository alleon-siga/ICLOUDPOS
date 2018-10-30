<?php
$ruta = base_url();
?>
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
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                &times;
            </button>
            <h3>Detalles de la Venta <?= $venta_action == 'caja' ? 'a Cobrar' : '' ?></h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <?php if ($venta->condicion_id == '1'): ?>
                    <div class="row-fluid">
                        <div class="row">
                            <div class="col-md-2"><label class="control-label">Venta Nro:</label></div>
                            <div class="col-md-3"><?= sumCod($venta->venta_id, 6) ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label
                                        class="control-label">Documento:</label>
                            </div>
                            <div
                                    class="col-md-3">
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
                            <div class="col-md-3" <?=$venta->venta_estado=="ANULADO"?'style="color:red !important;"':''?>><?= $venta->venta_estado ?></div>

                            <div class="col-md-1"></div>

                            <div class="col-md-2"><label class="control-label">Venta Total:</label></div>
                            <div class="col-md-3"><?= $venta->moneda_simbolo . " " . $venta->total ?></div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle;" width="5%"><?= getCodigoNombre() ?></th>
                                <th style="vertical-align: middle;" width="36%">Producto</th>
                                <th width="8%">Cantidad<br>Vendida</th>
                                <th width="8%">Cantidad<br>Devuelta</th>
                                <th style="vertical-align: middle;" width="8%">Cantidad</th>
                                <th style="vertical-align: middle;" width="15%">UM</th>
                                <th style="vertical-align: middle;" width="10%">Precio</th>
                                <th style="vertical-align: middle;" width="10%">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($venta->detalles as $detalle): ?>
                                <tr>
                                    <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                    <td><?= $detalle->producto_nombre ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad : number_format($detalle->cantidad, 0) ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad_devuelta : number_format($detalle->cantidad_devuelta, 0) ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad - $detalle->cantidad_devuelta : number_format($detalle->cantidad - $detalle->cantidad_devuelta, 0) ?></td>
                                    <td><?= $detalle->unidad_nombre ?></td>
                                    <td style="text-align: right"><?= $detalle->precio ?></td>
                                    <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                </tr>
                            <?php endforeach; ?>
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

                            <div class="col-md-2"><label
                                        class="control-label">Documento:</label>
                            </div>
                            <div
                                    class="col-md-3">
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
                                <?= $venta->moneda_simbolo ?> <?= $venta_action == 'caja' ? $venta->total : $venta->credito_pendiente ?>
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
                            <div class="col-md-3"  <?=$venta->venta_estado=="ANULADO"?'style="color:red !important;"':''?> ><?= $venta->venta_estado?></div>

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
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle;" width="5%"><?= getCodigoNombre() ?></th>
                                <th style="vertical-align: middle;" width="36%">Producto</th>
                                <th width="8%">Cantidad<br>Vendida</th>
                                <th width="8%">Cantidad<br>Devuelta</th>
                                <th style="vertical-align: middle;" width="8%">Cantidad</th>
                                <th style="vertical-align: middle;" width="15%">UM</th>
                                <th style="vertical-align: middle;" width="10%">Precio</th>
                                <th style="vertical-align: middle;" width="10%">Subtotal</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($venta->detalles as $detalle): ?>
                                <tr>
                                    <td><?= getCodigoValue($detalle->producto_id, $detalle->producto_codigo_interno) ?></td>
                                    <td><?= $detalle->producto_nombre ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad : number_format($detalle->cantidad, 0) ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad_devuelta : number_format($detalle->cantidad_devuelta, 0) ?></td>
                                    <td><?= $detalle->producto_cualidad == "PESABLE" ? $detalle->cantidad - $detalle->cantidad_devuelta : number_format($detalle->cantidad - $detalle->cantidad_devuelta, 0) ?></td>
                                    <td><?= $detalle->unidad_nombre ?></td>
                                    <td style="text-align: right"><?= $detalle->precio ?></td>
                                    <td style="text-align: right"><?= $venta->moneda_simbolo . " " . $detalle->importe ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <br>
                <div class="row">
                    <div class="col-md-8">
                        <?php if (isset($notas_credito) && count($notas_credito) > 0): ?>
                            <h4>Anulaciones</h4>
                            <?php foreach ($notas_credito as $nc): ?>
                                <h5>
                                    <a href="#"
                                       onclick="ver_nc(<?= $nc->id ?>)"><?= 'NC ' . $nc->serie . ' - ' . $nc->numero ?></a>
                                    <br><br>
                                    <span style="color: red">Fecha y hora de anulaci&oacute;n: <b><?= date('d/m/Y H:i', strtotime($nc->fecha)) . '</b> Anulado por: ' . '<b>' . $nc->nombre . '</b>' ?></span>
                                </h5>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->subtotal, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Descuento:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->descuento, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Impuesto:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->impuesto, 2) ?></label></td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></label></td>
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
                        <input type="button" class='btn btn-danger' data-dismiss="modal" value="Cerrar">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>

  function ver_nc (nc_id) {
    $('#barloadermodal').modal('show')
    $.ajax({
      url: '<?php echo $ruta ?>venta_new/get_nota_credito/',
      type: 'POST',
      data: {'nc_id': nc_id},
      success: function (data) {
        $('#nc_modal').html(data)
        $('#nc_modal').modal('show')
      },
      error: function () {
        show_msg('danger', 'Ha ocurrido un error inesperado')
      },
      complete: function () {

        $('#barloadermodal').modal('hide')
      }
    })
  }
</script>

