<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

    h4, h5 {
        margin: 0px;
    }

    table tfoot tr td {
        font-weight: bold;
    }
</style>
<h4 style="text-align: center;">Cuentas por pagar</h4>
<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th># Compra</th>
            <th># Comprobante</th>
            <th>Proveedor</th>
            <th>Fecha emisi&oacute;n</th>
            <th>Importe compra</th>
            <th>Inicial</th>
            <th>Importe abonado</th>
            <th>Pendiente de pago</th>
            <th>D&iacute;as Transcurridos</th>
            <th>Tipo</th>
            <?php if($local=="TODOS"){ ?>
            <th>Local</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php $mon = ''; ?>
    <?php foreach ($lists as $p): ?>
        <tr>
            <td><?= $p->ingreso_id ?></td>
            <td>
            <?php
                $doc = '';
                if ($p->documento_nombre == 'FACTURA') $doc = "FA";
                if ($p->documento_nombre == 'NOTA CREDITO') $doc = "NC";
                if ($p->documento_nombre == 'BOLETA DE VENTA') $doc = "BO";
                if ($p->documento_nombre == 'GUIA DE REMISION') $doc = "GR";
                if ($p->documento_nombre == 'PEDIDO COMPRA-VENTA') $doc = "PCV";
                if ($p->documento_nombre == 'NOTA PEDIDO') $doc = "NP";

                if($p->documento_numero != '')
                    echo $doc . ' ' . $p->documento_serie . '-' . sumCod($p->documento_numero, 6);
                else
                    echo '<span style="color: #0000FF">NO FACTURADO</span>';
            ?>
            </td>
            <td><?= $p->proveedor_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($p->fecha_emision)) ?></td>
            <td><?= $p->simbolo . ' ' . number_format($p->total_ingreso, 2) ?></td>
            <td><?= $p->simbolo . ' ' . number_format($p->inicial, 2) ?></td>
            <td><?= $p->simbolo . ' ' . number_format($p->monto_debito, 2) ?></td>
            <td><?= $p->simbolo . ' ' . number_format($p->monto_cuota - $p->monto_debito, 2) ?></td>
            <td><?= $p->dias_transcurridos ?></td>
            <td><?= $p->tipo_ingreso ?></td>
            <?php if($local=="TODOS"){ ?>
            <td><?= $p->local_nombre; ?></td>
            <?php } ?>
        </tr>
    <?php $mon = $p->simbolo ?>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><?= $mon.' '.number_format($ingreso_totales->total_monto_venta, 2) ?></td>
            <td></td>
            <td><?= $mon.' '.number_format($ingreso_totales->total_monto_debito, 2) ?></td>
            <td><?= $mon.' '.number_format($ingreso_totales->total_monto_cuota - $ingreso_totales->total_monto_debito, 2) ?></td>
            <td></td>
            <td></td>
            <?php if($local=="TODOS"){ ?>
            <td></td>
            <?php } ?>
        </tr>
    </tfoot>
</table>
