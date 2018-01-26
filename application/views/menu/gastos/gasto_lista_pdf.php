<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
    }
</style>
<h2 style="text-align: center;">Gastos</h2>

<table border="0">
<tr>
    <td style="border: 0px;">Ubicaci&oacute;n: <?= $local['local_nombre'] ?></td>
    <td style="border: 0px;">Desde: <?= $fecha_ini ?></td>
    <td style="border: 0px;">Hasta: <?= $fecha_fin ?></td>
</tr>
</table>
<br>
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
        <th>Moneda</th>
        <th>Total</th>
        <th>Usuario</th>
        <th>Fecha Registro</th>
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
                        <td><?= date("d/m/Y", strtotime($gastos['fecha'] ))?></td>
                        <td><?= $gastos['nombre_tipos_gasto'] ?></td>
                        <td><?= $gastos['proveedor_id'] != NULL ? $gastos['proveedor_nombre'] : $gastos['trabajador'] ?></td>
                        <td><?= $gastos['descripcion'] ?></td>
                        <td>Soles</td>
                        <td><?= number_format($gastos['total'], 2) ?></td>
                        <td><?= $gastos['responsable'] ?></td>
                        <td><?= date("d/m/Y", strtotime($gastos['fecha_registro'] ))?></td>
                        <td><?= $gastos['status_gastos'] == 1 ? 'Activado' : 'Eliminado' ?></td>
                    </tr>
                <?php }
            } ?>
    </tbody>
</table>

<h4 style="text-align: right;">Importe: <?= $md->simbolo?> <?=number_format($gastos_totales->total, 2)?></h4>