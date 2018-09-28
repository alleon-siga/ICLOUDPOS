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
<h4 style="text-align: center;">Reporte de Costos Contables</h4>

<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
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
