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

    h4 {
        margin: 0px;
    }
</style>
<h2 style="text-align: center;">Registro de Ventas</h2>

<h4>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h4>
<h4>FECHA: <?= date('d/m/Y', strtotime($fecha_ini))?> - <?= date('d/m/Y', strtotime($fecha_fin))?></h4>
<h4>UBICACI&Oacute;N: <?= $local_nombre ?></h4>

<table cellpadding="3" cellspacing="0">
    <thead>
    <tr>
        <th># Venta</th>
        <th>Fecha</th>
        <th># Comprobante</th>
        <th>Identificaci&oacute;n</th>
        <th>Cliente</th>
        <th>Vendedor</th>
        <th>Condici&oacute;n</th>
        <th>Estado</th>
        <th>Tip. Cam.</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0): ?>
        <?php $total = 0; ?>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->venta_id ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>>
                    <span style="display: none;"><?= date('YmdHis', strtotime($venta->venta_fecha)) ?></span>
                    <?= date('d/m/Y H:i', strtotime($venta->venta_fecha)) ?>
                </td>
                <td><?php
                    $doc = '';
                    if ($venta->documento_id == 1) $doc = "FA";
                    if ($venta->documento_id == 2) $doc = "NC";
                    if ($venta->documento_id == 3) $doc = "BO";
                    if ($venta->documento_id == 4) $doc = "GR";
                    if ($venta->documento_id == 5) $doc = "PCV";
                    if ($venta->documento_id == 6) $doc = "NP";
                    if ($venta->numero != '')
                        echo $doc . ' ' . $venta->serie . '-' . sumCod($venta->numero, 6);
                    else
                        echo '<span style="color: #0000FF">NO FACTURADO</span>';
                    ?>
                </td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->ruc ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->cliente_nombre ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->vendedor_nombre ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->condicion_nombre ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->venta_estado ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red;"' : '' ?>><?= $venta->moneda_tasa ?></td>
                <td <?= $venta->venta_estado == 'ANULADO' ? 'style="color: red; text-align: right;"' : 'style="text-align: right;"' ?>><?= $venta->moneda_simbolo ?> <?= number_format($venta->total, 2) ?></td>

            </tr>
        <?php 
            if($venta->venta_estado != 'ANULADO'){
                $total += $venta->total;         
            }
        ?>
        <?php endforeach ?>
            <tr>
                <td colspan="9" align="right">Total</td>
                <td style="text-align: right;"><?= $venta->moneda_simbolo ?> <?= number_format($total, 2) ?></td>
            </tr>
    <?php endif; ?>

    </tbody>
</table>
