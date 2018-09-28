<style>
    table th {
        background-color: #f4f4f4;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }

</style>
<div class="col-md-3"><label>Total Vendido: </label> <?= $moneda->simbolo ?> <span id="total_venta"></span></div>
<div class="col-md-3"><label>Total Pagado: </label> <?= $moneda->simbolo ?> <span id="total_pago"></span></div>
<div class="col-md-3"><label>Total Saldo: </label> <?= $moneda->simbolo ?> <span id="total_saldo"></span></div>
<div class="col-md-3 text-right">
    <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
        <i class="fa fa-file-excel-o fa-fw"></i>
    </button>
    <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
        <i class="fa fa-file-pdf-o fa-fw"></i>
    </button>
</div>
<?php
$total_venta = 0;
$total_pago = 0;
$total_saldo = 0; ?>
<?php if (count($clientes) == 0 && isset($form_filter)) echo '<br><h3>No hay resultados para mostrar.</h3>' ?>
<?php foreach ($clientes as $cliente): ?>
    <table class="table table-striped dataTable table-bordered no-footer tableStyle">
        <tbody>
        <tr>
            <th>Cliente</th>
            <td colspan="4"><?= $cliente->cliente_nombre ?></td>
            <th>Total Vendido</th>
            <td colspan="2"><?= $moneda->simbolo ?> <span><?= number_format($cliente->subtotal_venta, 2) ?></span></td>
        </tr>
        <tr>
            <th>Ubicaci&oacute;n</th>
            <td colspan="4"><?= $local->local_nombre ?></td>
            <th>Total Pagado</th>
            <td colspan="2"><?= $moneda->simbolo ?> <span><?= number_format($cliente->subtotal_pago, 2) ?></span></td>
        </tr>
        <tr>
            <th>Vendedor</th>
            <td colspan="4"><?= $cliente->vendedor_nombre ?></td>
            <th>Total Saldo</th>
            <td colspan="2">
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $cliente->subtotal_venta - $cliente->subtotal_pago > 0 ? 'b-warning' : 'b-default' ?>">
                    <?= $moneda->simbolo ?>
                    <?= number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2) ?>
                </label>
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Descripci&oacute;n</th>
            <th>Venta</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th></th>
        </tr>
        <?php $actual_desglose = 0 ?>
        <?php $total_venta += number_format($cliente->subtotal_venta, 2, '.', ''); ?>
        <?php $total_pago += number_format($cliente->subtotal_pago, 2, '.', ''); ?>
        <?php $total_saldo += number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2, '.', ''); ?>
        <?php foreach ($cliente->cobranzas as $cobranza): ?>

            <tr style="background-color: #dae8e7; font-weight: bold;">
                <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
                <td>
                    <?php
                    $doc = 'NE ';
                    if ($cobranza->documento_id == 3) $doc = 'BO ';
                    if ($cobranza->documento_id == 1) $doc = 'FA ';
                    ?>

                    <?= $cobranza->documento_numero != null ?
                        $doc . $cobranza->documento_serie . ' - ' . sumCod($cobranza->documento_numero, 6) :
                        '<span style="color: blue;">NO EMITIDO</span>' ?>
                    (# Vnt: <?= $cobranza->venta_id ?>)
                </td>
                <td><?= $cobranza->condicion_pago_nombre ?></td>
                <td>
                    <?= $moneda->simbolo . ' ' . number_format($cobranza->total_deuda, 2) ?>
                </td>
                <td></td>
                <?php $actual_desglose += $cobranza->total_deuda; ?>
                <td><?= $moneda->simbolo . ' ' . number_format($actual_desglose, 2) ?></td>
                <td><?= $cobranza->condicion_pago == 1 || $cobranza->credito_estado == 'PagoCancelado' ? 'Cancelado' : 'Pendiente' ?></td>
                <td>
                    <a href="#" class="btn btn-default show_detalle" data-id="<?= $cobranza->venta_id ?>">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
            </tr>

            <?php foreach ($cobranza->detalles as $detalle): ?>
                <tr>
                    <td style="text-align: right;"><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                    <td><?= $detalle->letra ?></td>
                    <td><?= $detalle->tipo_pago_nombre ?></td>
                    <td></td>
                    <td><?= $moneda->simbolo . ' ' . number_format($detalle->monto, 2) ?></td>
                    <?php $actual_desglose -= $detalle->monto; ?>
                    <td><?= $moneda->simbolo . ' ' . number_format($actual_desglose, 2) ?></td>
                    <td colspan="2">

                        <?= $detalle->letra == 'PAGO INICIAL' && $cobranza->total_interes > 0
                            ? '<span style="font-weight: bold;">INTERES:</span> ' . $moneda->simbolo . ' ' . number_format($cobranza->total_interes, 2)
                            . ' (' . $cobranza->tasa_interes . '%)'
                            : '' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
<?php endforeach; ?>
<input type="hidden" id="input_venta" value="<?= number_format($total_venta, 2) ?>">
<input type="hidden" id="input_pago" value="<?= number_format($total_pago, 2) ?>">
<input type="hidden" id="input_saldo" value="<?= number_format($total_saldo, 2) ?>">

<script>
    $(document).ready(function () {

        $('.show_detalle').on('click', function (e) {
            e.preventDefault();

            ver($(this).attr('data-id'));
        });

        $('#exportar_excel').on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });


        $("#total_venta").html($("#input_venta").val());
        $("#total_pago").html($("#input_pago").val());
        $("#total_saldo").html($("#input_saldo").val());
    });


    function exportar_pdf() {
        var data = {
            'fecha_ini': $('#fecha_ini').val(),
            'fecha_fin': $('#fecha_fin').val(),
            'vendedor_id': $("#vendedor_id").val(),
            'cliente_id': $("#cliente_id").val(),
            'moneda_id': $("#moneda_id").val(),
            'local_id': $("#local_id").val(),
            'estado': $("#estado").val()
        };

        if ($("#incluir_fecha").prop('checked'))
            data.fecha_flag = 1;
        else
            data.fecha_flag = 0;

        var win = window.open('<?= base_url()?>reporte_ventas/cliente_estado/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }

    function exportar_excel() {
        var data = {
            'fecha_ini': $('#fecha_ini').val(),
            'fecha_fin': $('#fecha_fin').val(),
            'vendedor_id': $("#vendedor_id").val(),
            'cliente_id': $("#cliente_id").val(),
            'moneda_id': $("#moneda_id").val(),
            'local_id': $("#local_id").val(),
            'estado': $("#estado").val()
        };

        if ($("#incluir_fecha").prop('checked'))
            data.fecha_flag = 1;
        else
            data.fecha_flag = 0;
        var win = window.open('<?= base_url()?>reporte_ventas/cliente_estado/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }


    function ver(venta_id) {

        $("#detalle_modal").html($("#loading").html());
        $("#detalle_modal").modal('show');

        $.ajax({
            url: '<?=base_url("venta_new/get_venta_detalle")?>',
            type: 'POST',
            data: {'venta_id': venta_id},

            success: function (data) {
                $("#detalle_modal").html(data);
            },
            error: function () {
                alert('asd')
            }
        });
    }
</script>