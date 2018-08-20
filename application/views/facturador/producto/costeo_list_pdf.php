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
<h4 style="text-align: center;">Reporte costeo de producto</h4>
<table>
    <thead>
        <tr>
            <th>Codigo</th>
            <th>Producto</th>
            <th>Unidad</th>
            <th>Costo Real MN</th>
            <th>Costo Contable MN</th>
            <th>Costo Real ME</th>
            <th>Costo Contable ME</th>
            <th>Tipo Cambio</th>
            <th>% Precio</th>
            <th>Precio Comp. MN</th>
            <th>Precio Comp. ME</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($lists as $list):
        $porcentaje_utilidad = $list->porcentaje_utilidad;
        $contable_costo_mn = $list->contable_costo_mn;
        $tipo_cambio = $list->tipo_cambio;
        $preCompMn = (($porcentaje_utilidad / 100) * $contable_costo_mn) + $contable_costo_mn;
        if($tipo_cambio<=0){
            $preCompMe = 0;
        }else{
            $preCompMe = $preCompMn / $tipo_cambio;
        }
    ?>
        <tr>
            <td><?php echo getCodigoValue(sumCod($list->producto_id), $list->producto_codigo_interno) ?></td>
            <td><?= $list->producto_nombre ?></td>
            <td><?= $list->nombre_unidad ?></td>
            <td><?= number_format($list->costo_mn, 2) ?></td>
            <td><?= number_format($contable_costo_mn, 2) ?></td>
            <td><?= number_format($list->costo_me, 2) ?></td>
            <td><?= number_format($list->contable_costo_me, 2) ?></td>
            <td><?= number_format($tipo_cambio, 2) ?></td>
            <td><?= number_format($porcentaje_utilidad, 2) ?></td>
            <td><?= number_format($preCompMn, 2); ?></td>
            <td><?= number_format($preCompMe, 2); ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
