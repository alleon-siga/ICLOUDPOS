<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=utilidad_venta.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<?php $md = get_moneda_defecto() ?>
<h4 style="text-align: center; margin: 0;">Reporte de Costo Contable</h4>
<h5 style="margin: 0;">EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5 style="margin: 0;">DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5 style="margin: 0;">SUCURSAL: <?= $local_nombre ?></h5>
<table border="1">
    <thead>
        <tr>
            <th>Cod</th>
            <th>Producto</th>
            <th>Marca</th>
            <th>UM</th>
            <th>Costo Real S/</th>
            <th>Costo Contable S/</th>
            <th>Costo Real $</th>
            <th>Costo Contable $</th>
            <th>Tipo de Cambio</th>
            <th>% Precio</th>
            <th>Precio Comp S/</th>                
            <th>Precio Comp $</th>
        </tr>
    </thead>
    <tbody>
      <?php
            foreach ($lists as $ingreso):
                ?>
        <tr>
           
                <td><?= $ingreso->producto_codigo_interno ?></td>
                <td><?= $ingreso->producto_nombre ?></td>
                <td><?= $ingreso->producto_marca != NULL ? $ingreso->producto_marca : "Sin Marca" ?></td>
                <td><?= $ingreso->nombre_unidad ?></td>
                <td><?= number_format($ingreso->costo_real, 2) ?></td>
                <td><?= number_format($ingreso->contable_costo, 2) ?></td>
                <td><?= number_format($ingreso->costo_real_d, 2) ?></td>
                <td><?= number_format($ingreso->costo_contable_d, 2) ?></td>
                <td><?= $ingreso->tipo_cambio ?></td>
                <td><?= number_format($ingreso->porcentaje_utilidad, 2) ?></td>
                <td><?= number_format($ingreso->precio_compra_s, 2) ?></td>
                <td><?= number_format($ingreso->precio_compra_d, 2) ?></td> 
            </tr>
            <?php
        endforeach;
        ?>
    </tbody>
</table>
