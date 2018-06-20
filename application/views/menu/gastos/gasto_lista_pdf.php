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
</style>
<h4>Reporte de Gastos</h4>
<h5>Fecha: <?= date('d/m/Y', strtotime($fecha_ini)) . ' a ' . date('d/m/Y', strtotime($fecha_fin)) ?></h5>
<h5>Moneda: <?= $moneda->nombre ?></h5>
<?php $md = get_moneda_defecto(); ?>
<table cellpadding="3" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Local</th>
        <th>Fecha</th>
        <th>Tipo de Gasto</th>
        <th>Persona Afectada</th>
        <th>Descripci&oacute;n</th>
        <th>Total</th>
        <th>Usuario</th>
        <th>Fecha Registro</th>
        <th>Condici&oacute;n</th>
        <th>Estado</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($gastoss) > 0) {
        foreach ($gastoss as $gastos) {
            ?>
            <tr>
                <td class="center"><?= $gastos['id_gastos'] ?></td>
                <td><?= $gastos['local_nombre'] ?></td>
                <td>
                    <span style="display: none;"><?= date("YmdHis", strtotime($gastos['fecha'])) ?></span><?= date("d/m/Y", strtotime($gastos['fecha'])) ?>
                </td>
                <td><?= $gastos['nombre_tipos_gasto'] ?></td>
                <td><?= $gastos['proveedor_id'] != NULL ? $gastos['proveedor_nombre'] : $gastos['trabajador'] ?></td>
                <td><?= $gastos['descripcion'] ?></td>
                <td><?= $gastos['simbolo'] . ' ' . number_format($gastos['total'], 2) ?></td>
                <td><?= $gastos['responsable'] ?></td>
                <td><?= date("d/m/Y", strtotime($gastos['fecha_registro'])) ?></td>
                <td><?= $gastos['nombre_condiciones'] ?></td>
                <td><?= $gastos['status_gastos'] == 1 ? 'Pendiente' : 'Confirmado' ?></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>
<h4 style="text-align: right;">Importe: <?= $moneda->simbolo ?> <?= number_format($gastos_totales->total, 2) ?></h4>