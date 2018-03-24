<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    onclick="javascript:$('#visualizar_venta').hide();">&times;
            </button>
            <h3>Visualizar Venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label panel-admin-text">Fecha Emision:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="fec_emision"
                                       value="<?= date('d/m/Y', strtotime($ventas[0]['fechaemision'])) ?>"
                                       id="fec_emision" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="nro_venta" class="control-label panel-admin-text">Venta Nro:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='form-control' name="nro_venta"
                                   id="nro_venta"
                                   value="<?= sumCod($ventas[0]['venta_id'], 6) ?>"
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label panel-admin-text">Cliente:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='form-control' name="Cliente"
                                   value="<?= $ventas[0]['cliente'] ?>" id="Cliente"
                                   readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label panel-admin-text">Monto Deuda:</label>
                        </div>

                        <div class="col-md-3">
                            <div class="input-prepend input-append input-group">
                                <label id="lblSim3" class="input-group-addon"><?= $ventas[0]['simbolo'] ?></label>
                                <input type="text" class='input-square input-small form-control'
                                       value="<?= $ventas[0]['montoTotal'] ?>"
                                       name="dec_credito_montocuota"
                                       id="monto_cuota" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Detalle Productos</h3>
                        </div>
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">

                                <table id="table" class="table dataTable dataTables_filter table-striped tableStyle">
                                    <thead>
                                    <th>ID Producto</th>
                                    <th>Producto</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($ventas as $venta): ?>
                                        <tr>
                                            <td align="center"><?= $venta['producto_id'] ?></td>
                                            <td><?= $venta['nombre'] ?></td>
                                            <td align="center"><?= $venta['nombre_unidad'] ?></td>
                                            <td align="center"><?= number_format($venta['cantidad'], 0) ?></td>
                                            <td align="right"><?= $venta['simbolo'] . ' ' . number_format($venta['preciounitario'], 2) ?></td>
                                            <td align="right"><?= $venta['simbolo'] . ' ' . number_format($venta['importe'], 2) ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1">
                            <label for="monto_total" class="control-label panel-admin-text">Inicial:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend input-append input-group">
                                <label id="lblSim3" class="input-group-addon"><?= $ventas[0]['simbolo'] ?></label>
                                <input type="text" class='input-square input-small form-control' name="monto_total"
                                       id="monto_total" value="<?= number_format($ventas[0]['inicial'], 2) ?>" readonly>
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
                                    <th># Unico</th>
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
                                        $idletra = $pago->nro_letra;
                                        ?>
                                        <tr>
                                            <td align="center"><?php echo $idletra; ?><input type="hidden"
                                                                                             id="val<?php echo $i; ?>"
                                                                                             value="<?php echo $idletra; ?>">
                                            </td>
                                            <td style="width: 120px;">
                                                <input style="width: 100%;" value="<?= $pago->numero_unico ?>"
                                                       type="text" data-id="<?= $pago->id_credito_cuota ?>"
                                                       class="numero_unico">
                                            </td>
                                            <td align="center"><?= date("d/m/Y", strtotime($pago->fecha_vencimiento)) ?></td>
                                            <td align="right"> <?= $ventas[0]["simbolo"] . " " . number_format($pago->monto, 2) ?></td>
                                            <td align="right"><?php if ($pago->monto_restante == null) {
                                                    echo $ventas[0]["simbolo"] . " " . number_format($pago->monto, 2);
                                                } else {
                                                    echo $ventas[0]["simbolo"] . " " . number_format($pago->monto_restante, 2);
                                                } ?></td>
                                            <?php if ($pago->ispagado) { ?>
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
                            <h3>Historial de pagos</h3>
                        </div>
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">
                                <table id="table" class="table dataTable dataTables_filter table-striped tableStyle">
                                    <thead>
                                    <th>N° Cuota</th>
                                    <th>Vencimiento</th>
                                    <th>Pagado</th>
                                    <th>Forma de Pago</th>
                                    <th>Banco/Tarjeta</th>
                                    <th>Operacion</th>
                                    <th>Importe Abonado</th>
                                    <th>Saldo Cuota</th>
                                    <th>Acci&oacute;n</th>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (count($historial) > 0) {
                                        foreach ($historial as $row): ?>
                                            <tr>
                                                <td align="center"><?= $row['nro_letra']; ?></td>
                                                <td align="center"><?= date("d/m/Y", strtotime($row['fecha_vencimiento'])) ?></td>
                                                <td align="center"><?= date("d/m/Y", strtotime($row['fecha_abono'])) ?></td>
                                                <td align="center"><?= $row['nombre_metodo']; ?></td>
                                                <td align="center"><?php
                                                    if ($row['id_metodo'] == '4') echo $row['banco_nombre'];
                                                    elseif ($row['id_metodo'] == '7') echo $row['tarjeta_nombre'];
                                                    else echo '-' ?></td>
                                                <td align="center"><?= $row['id_metodo'] != '3' ? $row['nro_operacion'] : '-'; ?></td>
                                                <td align="right">
                                                    <?php echo $ventas[0]["simbolo"] . " " . number_format($row['monto_abono'], 2); ?>
                                                </td>
                                                <td align="right"><?php
                                                    echo number_format($row['monto_restante'], 2);
                                                    ?></td>
                                                <td class='actions_big'>
                                                    <div class="btn-group">
                                                        <a class='btn btn-xs btn-default tip' title="Ver Venta"
                                                           onclick="visualizar_monto_abonado(<?= $row['id_credito_cuota'] ?>,<?= $row['id_venta'] ?>,<?= $row['abono_id'] ?>)"><i
                                                                    class="fa fa-search"></i> Imprimir </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach;
                                    } ?>
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

    <div class="modal fade" id="visualizar_cada_historial" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">


    </div>

    <style>
        .border-green {
            border: 1px solid green;

        }

        .border-red {
            border: 1px solid red;

        }
    </style>

    <script>
        var timer = [];
        var delay = (function () {
            return function (id, callback, ms) {
                timer[id] = 0;
                clearTimeout(timer);
                timer[id] = setTimeout(callback, ms);
            };
        })();

        $(function () {

//            $('.numero_unico').on('keydown', function(){
//                $('.numero_unico').off('keyup');
//            })


            $('.numero_unico').on('keyup', function (e) {
                var input = $(this);
                input.removeClass('border-green');
                input.removeClass('border-red');
                delay(input.attr('data-id'), function () {
                    $.ajax({
                        url: '<?= base_url("venta/update_numero_unico")?>/' + input.attr('data-id'),
                        type: 'POST',
                        headers: {
                            Accept: 'application/json'
                        },
                        data: {
                            'numero': input.val()
                        },
                        success: function (data) {
                            if (data.success == 1) {
                                input.addClass('border-green');
                            }
                        },
                        error: function () {
                            input.addClass('border-red')
                        },
                        complete: function () {
                            setTimeout(function () {
                                input.removeClass('border-green');
                                input.removeClass('border-red');
                            }, 4000);
                        }
                    });
                }, 1000);
            });

        })
        function cerrar_detalle_historial() {

            $('#visualizar_cada_historial').modal('hide');
        }


    </script>
