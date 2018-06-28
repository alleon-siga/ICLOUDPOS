<?php $term = diccionarioTermino() ?>
<style>
    @media print {
        html, body {
            width: 100%;
            margin: 0;
            font-size: 9pt;
        }

        table {
            border: 0px;
            width: 100%;
            font-family: Verdana, Arial, sans-serif;
        }

        table tbody td {
            font-size: 8pt;
            text-transform: uppercase;
            padding: 2px;
        }

        table thead td {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;

            padding: 3px 2px;
        }
    }
</style>
<div>
    <table cellpadding="0" cellspacing="10">
        <tr>
            <td style="text-transform: uppercase; text-align: center;" colspan="2"><?= valueOption('EMPRESA_NOMBRE', '') ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">ALMACEN ORIGEN:</td>
            <td style="text-transform: uppercase;">ALMACEN DESTINO:</td>
        </tr>
        <tr>
            <td style="border: 1px solid #0b0b0b;"><?= $venta[0]->local_nombre ?></td>
            <td style="border: 1px solid #0b0b0b;"><?= $venta[0]->ref_val ?></td>
        </tr>
    </table>
    <br>
    <table style="border: 0px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase; text-align: center;">TRASPASO ENTRE ALMACENES</td>
        </tr>
    </table>
    <hr>
    <table style="border: 0px;"
           cellpadding="0" cellspacing="0">
        <tr>
            <td style="text-transform: uppercase;">
                Venta Nro:
                <?= $venta[0]->serie != null ? $venta[0]->serie . ' - ' : '' ?>
                <?= sumCod($venta[0]->venta_id, 6) ?>
            </td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">
                Fecha: <?= date('d/m/Y h:i a', strtotime($venta[0]->fecha)) ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;"><?= ($venta[0]->tipo_cliente == '1') ? $term[1]->valor : $term[0]->valor ?>
                : <?= $venta[0]->identificacion ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Cliente: <?= $venta[0]->razon_social ?></td>
        </tr>
        <tr>
            <td style="text-transform: uppercase;">Vendedor: <?= $venta[0]->username ?></td>
        </tr>
    </table>
    <br>
    <table cellpadding="0" cellspacing="0">
        <tbody>
        <tr>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Producto</td>
            <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">Cantidad</td>
        </tr>
        <?php $i = 0; ?>
        <?php foreach ($venta as $detalle): ?>
            <tr>
                <td colspan="2"
                    style="<?= $i++ != 0 ? 'border-top: 1px dashed #0b0b0b;' : '' ?>"><?= $detalle->producto_nombre ?></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align: right"><?= number_format($detalle->cantidad, 0) . " " . $detalle->nombre_unidad ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br><br><br><br>
    <div style="border-bottom: 1px dashed #0b0b0b; text-align: left; width: 20%; float: left;"></div>
    <div style="border-bottom: 1px dashed #0b0b0b; text-align: right; width: 20%; float: right;"></div>
</div>
<script>
    this.print();
</script>