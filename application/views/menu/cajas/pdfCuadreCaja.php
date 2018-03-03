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
    $total_ingresos = 0;
    $total_egresos = 0;

    ?>
    <div style="padding-left: 10px; padding-right: 10px; height: 99%; width: 98.5%;">
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
                    <span>Almacen: </span><?php echo $local_nombre ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Usuario: </span><?php echo $usuario_nombre ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:left; width: 100%;">
                    <span>Moneda: </span><?php echo $moneda_nombre ?>
                </td>
            </tr>
        </table>

        <p><strong>INGRESOS</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <?php foreach ($ingresos[$moneda['id_moneda']] as $ingreso): ?>
                <?php $total_ingresos += $ingreso['saldo']; ?>
                <tr>
                    <td style="width: 60%;"><?= $ingreso['metodo']['id_metodo'] == '9' ? 'TRANSF. BANCO' : $ingreso['metodo']['nombre_metodo'] ?></td>
                    <td style="text-align: right;"><?= $moneda['simbolo'] . ' ' . ($ingreso['saldo']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td style="width: 60%; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] . ' ' . $total_ingresos ?></td>
            </tr>
        </table>
        <p><strong>EGRESOS</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <?php foreach ($egresos[$moneda['id_moneda']] as $egreso): ?>
                <?php $total_egresos += $egreso['saldo']; ?>
                <tr>
                    <td style="width: 60%;"><?= $egreso['metodo']['id_metodo'] == '9' ? 'TRANSF. BANCO' : $egreso['metodo']['nombre_metodo'] ?></td>
                    <td style="text-align: right;"><?= $moneda['simbolo'] . ' ' . ($egreso['saldo']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td style="width: 60%; font-weight: bold;">TOTAL</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] . ' ' . $total_egresos ?></td>
            </tr>
        </table>
        <p><strong>RESUMEN</strong></p>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 60%; font-weight: bold;">SALDO DEL DIA</td>
                <td style="text-align: right; font-weight: bold;"><?= $moneda['simbolo'] . ' ' . ($total_ingresos - $total_egresos) ?></td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>
