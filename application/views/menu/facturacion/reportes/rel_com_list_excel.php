<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=producto_vendido.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Relacion de Comprobates</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th># Venta</th>
            <th>Fec. Venta</th>
            <th>Fec. Fact.</th>
            <th>Documento</th>
            <th>Tipo Doc. Mod.</th>
            <th>Nro. Doc. Mod.</th>
            <th>Nro. Doc. Fact. Elec.</th>
            <th>Nro. de Venta</th>
            <th>SubTotal</th>
            <th>Impuesto</th>
            <th>Total</th>
            <th># Doc. Cliente</th>
            <th>Nom. Cliente</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lists as $list): ?>
            <tr class="info" style="font-weight: bold;">
                <td><?= $list->venta_id ?></td>
                <td><?= date('d/m/Y', strtotime($list->Fec_Venta)) ?></td>
                <td><?= date('d/m/Y', strtotime($list->FecFacturacionElectr)) ?></td>
                <td><?= $list->documento ?></td>
                <td><?= $list->documento_mod_tipo ?></td>
                <td><?= $list->documento_mod_numero ?></td>                
                <td><?= $list->documento_numero ?></td>
                <td><?= $list->numero ?></td>
                <td><?= $list->subtotal ?></td>
                <td><?= $list->impuesto ?></td>
                <td><?= $list->total ?></td>
                <td><?= $list->cliente_identificacion ?></td>
                <td><?= $list->cliente_nombre ?></td>
                <td>
                    <?php
                    $estado = '';
                    $estado_class = '';
                    if ($list->Estado == "NO GENERADO") {
                        $estado_class = 'label-warning';
                        $estado = 'NO GENERADO';
                    } elseif ($list->Estado == "GENERADO") {
                        $estado_class = 'label-info';
                        $estado = 'GENERADO';
                    } elseif ($list->Estado == "ENVIADO") {
                        $estado_class = 'label-warning';
                        $estado = 'ENVIADO';
                    } elseif ($list->Estado == "ACEPTADO") {
                        $estado_class = 'label-success';
                        $estado = 'ACEPTADO';
                    } elseif ($list->Estado == "RECHAZADO") {
                        $estado_class = 'label-danger';
                        $estado = 'RECHAZADO';
                    }
                    ?>
                    <div title="Descripci&oacute;n del Estado" data-content="<?= $list->nota ?>"
                         data-toggle="popover"
                         class="label <?= $estado_class ?>"
                         data-placement="top"
                         style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                        <?= $estado ?>
                    </div>

                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
