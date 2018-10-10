<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
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
<h4 style="text-align: center;">Relacion de Comprobantes</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
