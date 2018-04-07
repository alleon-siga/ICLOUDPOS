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
<h4 style="text-align: center;">Reporte de Entradas y Salidas</h4>
<h4 style="text-align: center;">Desde <?= date('d/m/Y', strtotime($fecha_ini)) ?>
    al <?= date('d/m/Y', strtotime($fecha_fin)) ?></h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table class='table table-striped dataTable table-bordered no-footer tableStyle' style="overflow:scroll">
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
