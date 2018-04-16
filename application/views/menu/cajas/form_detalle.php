<div class="modal-dialog" style="width: 95%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#dialog_form').modal('hide');"
                    aria-hidden="true">&times;
            </button>
            <h4 class="modal-title">Movimientos de caja</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-7">
                    <h4>Nombre de la caja: <?= $cuenta->descripcion ?></h4>
                    <h5>Fecha: <?= $fecha_ini . ' a ' . $fecha_fin ?></h5>
                </div>
                <div class="col-md-5">
                    <h5>Moneda: <?= $cuenta->nombre ?></h5>
                    <h5>Responsable: <?= $cuenta->usuario_nombre ?></h5>
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Operacion</th>
                    <th>Usuario</th>
                    <th>Forma de Pago</th>
                    <th>Numero</th>
                    <th>Observacion</th>
                    <th>Ingreso</th>
                    <th>Egreso</th>
                    <th>Saldo</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <?php
                    $total_ingreso = 0;
                    $total_egreso = 0;
                    ?>
                    <?php $saldo_anterior = isset($cuenta_movimientos[0]) ? $cuenta_movimientos[0]->saldo_old : 0 ?>
                    <td colspan="9" style="font-weight: bold;">SALDO ANTERIOR (<?= $fecha_ini ?>)</td>
                    <td style="font-weight: bold;"><?= $cuenta->simbolo . ' ' . number_format($saldo_anterior, 2) ?></td>
                </tr>
                <?php foreach ($cuenta_movimientos as $mov): ?>
                    <tr>
                        <td><?= $mov->id ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($mov->created_at)) ?></td>
                        <td><?= $mov->operacion_nombre ?></td>
                        <td><?= $mov->usuario_nombre ?></td>
                        <td><?= $mov->medio_pago_nombre ?></td>
                        <td><?= $mov->numero ?></td>
                        <td><?= $mov->ref_val ?></td>
                        <?php if ($mov->movimiento == 'INGRESO'): ?>
                            <?php $saldo_anterior += $mov->saldo ?>
                            <?php $total_ingreso += $mov->saldo ?>
                            <td style="color: #0d70b7;"><?= $mov->simbolo ?> <?= number_format($mov->saldo, 2) ?></td>
                            <td></td>
                        <?php elseif ($mov->movimiento == 'EGRESO'): ?>
                            <?php $saldo_anterior -= $mov->saldo ?>
                            <?php $total_egreso += $mov->saldo ?>
                            <td></td>
                            <td style="color: #ff0000;"><?= $mov->simbolo ?> <?= number_format($mov->saldo, 2) ?></td>
                        <?php endif; ?>
                        <td><?= $mov->simbolo ?> <?= number_format($saldo_anterior, 2, '.', ',') ?></td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="7" style="font-weight: bold; color: #00CC00;">SALDO FINAL (<?= $fecha_fin ?>)</td>
                    <td style="font-weight: bold; color: #0d70b7;"><?= $cuenta->simbolo . ' ' . number_format($total_ingreso, 2) ?></td>
                    <td style="font-weight: bold; color: #ff0000;"><?= $cuenta->simbolo . ' ' . number_format($total_egreso, 2) ?></td>
                    <td style="font-weight: bold; color: #00CC00;"><?= $cuenta->simbolo . ' ' . number_format($saldo_anterior, 2) ?></td>
                </tr>
                </tbody>
            </table>
            <h4 class="text-right">
                SALDO ANTERIOR - SALDO
                FINAL: <?= $cuenta->simbolo . ' ' . number_format($total_ingreso - $total_egreso, 2) ?></h4>

        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6 text-left">
                    <button id="exportar_excel" type="button" class="btn btn-default" title="Exportar Excel">
                        <i class="fa fa-file-excel-o"></i>
                    </button>

                    <button id="exportar_pdf" type="button" class="btn btn-default" title="Exportar Pdf">
                        <i class="fa fa-file-pdf-o"></i>
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <a href="#" class="btn btn-warning" onclick="$('#dialog_form').modal('hide');">Cerrar</a>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            $('#exportar_excel').on('click', function (e) {
                e.preventDefault();
                exportar_excel();
            });

            $('#exportar_pdf').on('click', function (e) {
                e.preventDefault();
                exportar_pdf();
            });


        });

        function exportar_pdf() {

            var data = {
                fecha_ini: $("#fecha_ini").val(),
                fecha_fin: $("#fecha_fin").val()
            };

            var win = window.open('<?= base_url()?>cajas/caja_detalle_pdf/<?=$desglose_id?>?data=' + JSON.stringify(data), '_blank');
            win.focus();
        }

        function exportar_excel() {
            var data = {
                fecha_ini: $("#fecha_ini").val(),
                fecha_fin: $("#fecha_fin").val()
            };

            var win = window.open('<?= base_url()?>cajas/caja_detalle_excel/<?=$desglose_id?>?data=' + JSON.stringify(data), '_blank');
            win.focus();
        }
    </script>




