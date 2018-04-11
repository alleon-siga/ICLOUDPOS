<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    onclick="javascript:$('#visualizar_venta').hide();">&times;
            </button>
            <h3>Visualizar Cuenta por Pagar</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row">
                    <div class="form-group">


                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Fecha Emision:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="fec_emision"
                                       value="<?= date("d/m/Y", strtotime($ingreso->fecha_emision)) ?>"
                                       id="fec_emision" readonly>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <label for="nro_venta" class="control-label">N&uacute;mero del Ingreso:</label>
                        </div>
                        <div class="col-md-3">

                            <input type="text" class='form-control' name="nro_venta"
                                   id="nro_venta"
                                   value="<?= $ingreso->documento_serie . "-" . $ingreso->documento_numero ?>"
                                   readonly>

                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Proveedor:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='form-control' name="Cliente"
                                   value="<?= $ingreso->proveedor_nombre ?>" id="Cliente"
                                   readonly>
                        </div>

                    </div>

                    <!--
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Nro Cuota:</label>
                        </div>

                        <div class="col-md-3">
                            <input type="text" class='input-square input-small form-control'
                                   value=""
                                   name="nro_cuota"
                                   id="nro_cuota" readonly>
                        </div>
                    </div> -->


                </div>
                <div class="row">
                    <!--
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Monto Total del Ingreso:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='input-square input-small form-control'
                                   value=""
                                   name="dec_credito_montocuota"
                                   id="monto_cuota" readonly>
                        </div>
                    </div>

                </div> -->

                </div>
                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Detalle Productos</h3>
                        </div>
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">
                                <table id="table" class="table table-striped table-bordered tableStyle">
                                    <thead>
                                    <th><?= getCodigoNombre() ?></th>
                                    <th>Producto</th>
                                    <th>UM</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    </thead>

                                    <tbody>
                                    <?php if (count($ingreso_detalles > 0)) {
                                        foreach ($ingreso_detalles as $row): ?>
                                            <tr>
                                                <td><?= getCodigoValue(sumCod($row->producto_id, 4), $row->producto_codigo_interno) ?></td>
                                                <td><?= $row->producto_nombre ?></td>
                                                <td><?= $row->nombre_unidad ?></td>
                                                <td><?= number_format($row->cantidad, 2, ',', '.') ?></td>
                                                <td><?= $ingreso->simbolo . ' ' . number_format($row->precio, 2, ',', '.') ?></td>
                                                <td><?= $ingreso->simbolo . ' ' . number_format($row->total_detalle, 2, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach;

                                    }
                                    ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="monto_total" class="control-label">Monto Total:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="monto_total"
                                       id="monto_total"
                                       value="<?= $ingreso->simbolo . ' ' . number_format($ingreso->total_ingreso, 2, ',', '.'); ?>"
                                       readonly>
                            </div>

                        </div>

                        <div class="col-md-2">
                        </div>

                        <div class="col-md-2">
                            <label for="monto_total" class="control-label">Monto Inicial:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="monto_total"
                                       id="monto_total"
                                       value="<?= $ingreso->simbolo . ' ' . number_format($credito->inicial, 2, ',', '.'); ?>"
                                       readonly>
                            </div>

                        </div>
                    </div>
                </div>
                <br>

                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Cronograma de Pagos</h3>
                        </div>
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">
                                <table id="table" class="table dataTable dataTables_filter table-striped tableStyle">
                                    <thead>
                                    <th>N° Cuota</th>
                                    <th>Vencimiento</th>
                                    <th>Total</th>
                                    <th>Pago Pendiente</th>
                                    <th>Estado</th>
                                    </thead>
                                    <tbody>
                                    <?php
                                    //un contador
                                    $i = 1;
                                    foreach ($cronogramas as $pago) {
                                        $idletra = $pago->letra;
                                        ?>
                                        <tr>
                                            <td align="center"><?php echo $idletra; ?><input type="hidden"
                                                                                             id="val<?php echo $i; ?>"
                                                                                             value="<?php echo $idletra; ?>">
                                            </td>
                                            <td align="center">
                                                <input type="text" class="form-control cambiar_fecha" readonly style="width: 100px; padding: 2px 2px; cursor: pointer; color: #2CA8E4; text-align: center; border: 1px solid #2CA8E4;" value="<?= date('d-m-Y', strtotime($pago->fecha_vencimiento)) ?>" data-id="<?= $pago->id ?>"></td>
                                            <td align="right"> <?= $ingreso->simbolo . " " . number_format($pago->monto, 2) ?></td>
                                            <td align="right"><?= $ingreso->simbolo . " " . number_format($pago->monto - $pago->monto_pagado, 2) ?></td>
                                            <?php if ($pago->pagado) { ?>
                                                <td align="center">
                                                    Pago Realizado
                                                </td>
                                            <?php } else {
                                                ?>
                                                <td align="center">
                                                    Pendiente
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        $i++;
                                    } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Historial de Pago</h3>
                        </div>
                        <div class="box-body">
                            <div id="lstTabla" class="table-responsive">
                                <table id="table_resultado" class="table table-striped table-bordered tableStyle">
                                    <thead>
                                    <th>Fecha Pagado</th>
                                    <th>Monto Pagado</th>
                                    <th>Metodo de Pago</th>
                                    <th>Banco</th>
                                    <th>Operacion</th>
                                    <th>Acci&oacute;n</th>
                                    </thead>

                                    <tbody>
                                    <?php foreach ($pagos_ingreso as $detalle): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i:s', strtotime($detalle->pagoingreso_fecha)) ?></td>
                                            <td><?= number_format($detalle->pagoingreso_monto, 2) ?></td>
                                            <td><?= $detalle->nombre_metodo ?></td>
                                            <td><?= $detalle->tipo_metodo == 'BANCO' ? $detalle->banco_nombre : '-' ?></td>
                                            <td><?= $detalle->operacion != '' ? $detalle->operacion : '' ?></td>
                                            <td></td>
                                        </tr>
                                    <?php endforeach; ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <a href="#" class="btn btn-danger" data-dismiss="modal"
                   onclick="javascript:$('#visualizar_venta').hide();">Salir</a>
            </div>
        </div>
    </div>
</div>
<script>
    var fecha_flag = true;

    $(document).ready(function () {
        $('.cambiar_fecha').datepicker({
            weekStart: 1,
            format: 'dd-mm-yyyy'
        });

        $('.cambiar_fecha').on('change', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (fecha_flag)
                cambiar_fecha($(this).attr('data-id'), $(this).val());
            $(this).datepicker('hide');
        });
    });

    function cambiar_fecha(id, fecha) {
        fecha_flag = false;
        $.ajax({
            url: '<?php echo base_url('ingresos/cambiar_fecha'); ?>',
            type: 'POST',
            data: {"id": id, 'fecha': fecha},
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                //$("#btn_buscar").click();
            },
            complete: function () {
                fecha_flag = true;
            }
        });
    }
</script>