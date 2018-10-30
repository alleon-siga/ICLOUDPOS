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
<?php foreach ($datos as $dato): ?>
    <div style="page-break-before: always;">
        <table cellpadding="0" cellspacing="10">
            <tr>
                <td style="text-transform: uppercase; text-align: center;"
                    colspan="2"><?= valueOption('EMPRESA_NOMBRE', '') ?></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">ALMACEN ORIGEN:</td>
                <td style="text-transform: uppercase;">ALMACEN DESTINO (Punto de venta):</td>
            </tr>
            <tr>
                <td style="border: 1px solid #0b0b0b;"><?= $dato['detalles'][0]->ref_val ?></td>
                <td style="border: 1px solid #0b0b0b;"><?= $dato['detalles'][0]->local_nombre ?></td>
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
                    <?= $dato['head']->serie != null ? $dato['head']->serie . ' - ' : '' ?>
                    <?= sumCod($dato['head']->venta_id, 6) ?>
                </td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">
                    Fecha: <?= date('d/m/Y h:i a', strtotime($dato['head']->fecha)) ?></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;"><?= ($dato['head']->tipo_cliente == '1') ? $term[1]->valor : $term[0]->valor ?>
                    : <?= $dato['head']->identificacion ?></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">Cliente: <?= $dato['head']->razon_social ?></td>
            </tr>
            <tr>
                <td style="text-transform: uppercase;">Vendedor: <?= $dato['head']->username ?></td>
            </tr>
        </table>
        <br>
        <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000;">Producto</td>
                <td style="border-bottom: 1px solid #000000; border-top: 1px solid #000000; text-align: right;">
                    Cantidad
                </td>
            </tr>
            <?php $i = 0; ?>
            <?php foreach ($dato['detalles'] as $d): ?>
                <tr>
                    <td colspan="2"
                        style="<?= $i++ != 0 ? 'border-top: 1px dashed #0b0b0b;' : '' ?>"><?= $d->producto_nombre ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right"><?= $d->producto_cualidad == "PESABLE" ? $d->cantidad : number_format($d->cantidad, 0) . " " . $d->nombre_unidad ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <br><br><br><br>
        <div style="border-bottom: 1px dashed #0b0b0b; text-align: left; width: 20%; float: left;"></div>
        <div style="border-bottom: 1px dashed #0b0b0b; text-align: right; width: 20%; float: right;"></div>
    </div>
<?php endforeach; ?>
<script>
  this.print()
</script>