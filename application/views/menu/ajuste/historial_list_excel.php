<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=entradas_salidas.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de Entradas y Salidas</h4>
<h4 style="text-align: center; margin: 0;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
    <tr>
        <th>Id</th>
        <th>Fecha</th>
        <th>Movimiento</th>
        <th>Operaci&oacute;n</th>
        <th>Documento</th>
        <th>Num. Documento</th>
        <th>Ubicaci&oacute;n</th>
        <th>Estado</th>
        <th>Usuario</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($lists as $list): ?>
        <tr>
            <td><?= $list->id ?></td>
            <td><?= $list->fecha ?></td>
            <td><?= $list->io == 1 ? 'ENTRADA' : 'SALIDA' ?></td>
            <td><?= get_sunat_operacion($list->operacion) ?></td>
            <td><?= get_sunat_documento($list->documento) ?></td>
            <td><?= $list->serie . ' - ' . $list->numero ?></td>
            <td><?= $list->local_nombre ?></td>
            <td><?= $list->estado ?></td>
            <td><?= $list->nombre ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
