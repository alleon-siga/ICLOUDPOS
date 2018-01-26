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
<div style="border: 2px solid #000;padding:5px; height: 98%; width: 98.5%;">
    <?php foreach ($monedas as $moneda): ?>
        <?php
        $total_referencia = 0.0;
        $total_ingresos = 0.0;
        $total_salida = 0.0;

        ?>
        <table style="width: 100%;">

            <tr>
                <td style=" height: 80px; font-size:1em; color: #111; padding-right: 0px; text-align: right; text-transform: uppercase; width: 100%;">
                    <h2>CUADRE DE CAJA</h2>
                </td>
                <td rowspan="2"></td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Fecha: </span><?php echo $this->input->post('fecha', true) . " " . date('H:i:s'); ?></td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Almacen: </span><?php echo $local_nombre["local_nombre"] ?></td>
            </tr>
        </table>
        <br>
        <p><strong>REFERENCIA</strong></p>
        <table style="border-collapse: collapse; text-align: center; border: 1px solid #000;">
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">VENTAS POR CONTADO</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $ventas_contado[$moneda['id_moneda']]->total + $ventas_contado_tarjeta[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_referencia += $total ?>
                </td>
            </tr>
            <tr style="border: 1px solid #000;">
                <td>VENTAS POR CRÉDITO</td>
                <td style="text-align: right;">
                    <?php $total = $ventas_credito[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_referencia += $total ?>
                </td>
            </tr>
            <tr>
                <td>TOTAL</td>
                <td style="text-align: right;"><?php echo $moneda['simbolo'] . " " . number_format($total_referencia, 2) ?></td>
            </tr>
        </table>
        <p><strong>INGRESO</strong></p>
        <table style="border-collapse: collapse; text-align: center; border: 1px solid #000;">
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">VENTAS POR EFECTIVO</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $ventas_contado[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_ingresos += $total ?>
                </td>
            </tr>

            <tr style="border: 1px solid #000;">
                <td style="width:70%;">VENTAS POR TARJETA</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $ventas_contado_tarjeta[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_referencia += $total ?>
                </td>
            </tr>

            <tr style="border: 1px solid #000;">
                <td>COBRO POR CUOTAS</td>
                <td style="text-align: right;">
                    <?php $total = $cobro_cuotas[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_ingresos += $total ?>
                </td>
            </tr>
            <!--INGRESO DE CAJAS HAY QUE IMPLEMENTARLO-->
            <!--
            <tr style="border: 1px solid #000;">
                <td>INGRESO DE CAJA</td>
                <td style="text-align: right;">0.00</td>
            </tr>-->
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">TOTAL INGRESO</td>
                <td style="width:30%; text-align: right;"><?php echo $moneda['simbolo'] . " " . number_format($total_ingresos, 2) ?></td>
            </tr>
        </table>
        <p><strong>SALIDA</strong></p>
        <table style="border-collapse: collapse; text-align: center; border: 1px solid #000;">
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">GASTOS</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $gastos[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_salida += $total ?>
                </td>
            </tr>
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">PAGOS A PROVEEDORES</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $pagos_proveedores[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_salida += $total ?>
                </td>
            </tr>

            <tr style="border: 1px solid #000;">
                <td style="width:70%;">COMPRAS AL CONTADO</td>
                <td style="width:30%; text-align: right;">
                    <?php $total = $compra_contado[$moneda['id_moneda']]->total; ?>
                    <?php echo $moneda['simbolo'] . " " . number_format($total, 2) ?>
                    <?php $total_salida += $total ?>
                </td>
            </tr>
            <!--SALIDA DE CAJAS HAY QUE IMPLEMENTARLO-->
            <!--
            <tr style="border: 1px solid #000;">
                <td>SALIDA DE CAJA</td>
                <td style="text-align: right;">0.00</td>
            </tr>-->
            <tr style="border: 1px solid #000;">
                <td style="width:70%;">TOTAL SALIDA</td>
                <td style="width:30%; text-align: right;"><?php echo $moneda['simbolo'] . " " . number_format($total_salida, 2) ?></td>
            </tr>
        </table>
        <br>
        <table style="border-collapse: collapse; text-align: center; border: 1px solid #000;">
            <tr>
                <td style="width:70%; text-align: center;"><strong>TOTAL DE CAJA</strong></td>
                <td style="width:30%; padding-left: 0px; font-weight: bold; text-align: right;"><strong><?php echo $moneda['simbolo'] . " " . number_format($total_ingresos - $total_salida, 2) ?></strong></td>
            </tr>
        </table>
        <br><br><br><br><br>
        <br>
    <?php endforeach; ?>
</div>