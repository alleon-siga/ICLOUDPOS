<?php $ruta = base_url(); ?>

<style type="text/css">
    @page {
        size: 80mm 200mm;
        width: 80mm;
        max-width: 80mm;
        min-height: 200mm;
        margin: 0;

        font-family: georgia, serif;
        font-size: 2px;
        color: blue;
        height: auto;
        border: 1px #000000;
        /* width: 80mm;*/
        min-height: 200mm;
        margin: 0;
        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-inside: avoid;
    }

    @media print {

        table {
            page-break-inside: avoid
        }

        #tabla_resumen_productos thead tr {
            border-top: 1px #000 dashed;
            border-bottom: 1px #000 dashed;
        }

        #tabla_resumen_productos thead tr th {
            border-top: 1px #000 dashed;
            border-bottom: 1px #000 dashed;
            font-size: 12px !important;
        }

        #tabla_resumen_productos tbody tr td {
            border-top: 0px #000 dashed;
            border-bottom: 0px #000 dashed;
            font-size: 10px !important;
        }

        #totales_ {
            font-size: 10px !important;
        }

    }

    table {
        width: 100%;
    }

    th {
        background: #e7e6e6;
    }

    td {
        text-align: left;
    }

    #header {
        width: 100%;
    }

    #resume, #total {
        border: #fff 0px solid;
        padding: 10px;
    }

    #resume td.impar, .upbold {
        font-weight: bold;
        text-transform: uppercase;
    }
</style>
<?php foreach ($monedas as $moneda): ?>
    <?php
    $total_ingresos = $venta_contado[$moneda['id_moneda']]->total
        + $venta_inicial[$moneda['id_moneda']]->total
        + $venta_credito[$moneda['id_moneda']]->total
        + $cobranza_cuota[$moneda['id_moneda']]->total;

    $total_egresos = $compra_contado[$moneda['id_moneda']]->total
        + $pagos_cuota[$moneda['id_moneda']]->total
        + $gasto[$moneda['id_moneda']]->total;
    ?>
    <div style="padding-left: 10px; padding-right: 10px; height: 99%; width: 98.5%;">
        <table style="width: 100%;">

            <tr>
                <td style=" height: 80px; font-size:1em; color: #111; padding-right: 0px; text-align: right; text-transform: uppercase; width: 100%;">
                    <h2>CORTE DE CAJA</h2>
                </td>
                <td rowspan="2"></td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Fecha: </span><?php echo $this->input->post('fecha', true) . " " . date('H:i:s'); ?></td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Almacen: </span><?php echo $local_nombre ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Usuario: </span><?php echo $usuario_nombre ?>
                </td>
            </tr>
        </table>

        <p><strong>INGRESOS</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 60%;">VENTAS AL CONTADO</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $venta_contado[$moneda['id_moneda']]->total != NULL ? number_format($venta_contado[$moneda['id_moneda']]->total, 2) : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%;">VENTAS CUOTA INICIALES</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $venta_inicial[$moneda['id_moneda']]->total != NULL ? number_format($venta_inicial[$moneda['id_moneda']]->total, 2) : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%;">VENTAS AL CREDITO</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $venta_credito[$moneda['id_moneda']]->total != NULL ? number_format($venta_credito[$moneda['id_moneda']]->total, 2) : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%;">COBRANZAS DE CUOTAS</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $cobranza_cuota[$moneda['id_moneda']]->total != NULL ? number_format($cobranza_cuota[$moneda['id_moneda']]->total, 2) : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] ?> <?= number_format($total_ingresos, 2) ?></td>
            </tr>
        </table>
        <p><strong>EGRESOS</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 60%;">COMPRAS AL CONTADO</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $compra_contado[$moneda['id_moneda']]->total != NULL ? $compra_contado[$moneda['id_moneda']]->total : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%;">PAGOS A PROVEEDORES</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $pagos_cuota[$moneda['id_moneda']]->total != NULL ? $pagos_cuota[$moneda['id_moneda']]->total : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%;">GASTOS</td>
                <td style="text-align: right;"><?= $moneda['simbolo'] ?> <?= $gasto[$moneda['id_moneda']]->total != NULL ? $gasto[$moneda['id_moneda']]->total : 0 ?></td>
            </tr>
            <tr>
                <td style="width: 60%; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] ?> <?= number_format($total_egresos, 2) ?></td>
            </tr>
        </table>

        <br>
        <p><strong>CORTE DE CAJA (INGRESOS - EGRESOS)</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 60%; font-weight: bold;">SALDO</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] ?> <?= number_format($total_ingresos - $total_egresos, 2) ?></td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>
