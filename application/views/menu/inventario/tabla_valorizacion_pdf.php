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
<h4 style="text-align: center;">Reporte de Valorizaci&oacute;n de inventario</h4>
<h5>EMPRESA: <?= valueOption('EMPRESA_NOMBRE') ?></h5>
<h5>DIRECCI&Oacute;N: <?= $local_direccion ?></h5>
<h5>SUCURSAL: <?= $local_nombre ?></h5>
<table>
    <thead>
        <tr>
            <th><?= getCodigoNombre() ?></th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Grupo</th>
            <th>Unidad</th>
            <th>Moneda</th>
            <th>Precio de venta</th>
            <th>Costo de compra</th>
            <th>Stock Actual</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    <?php

    $total=0;
    foreach ($productos as $producto) {
        ?>
        <tr>
            <td><?= getCodigoValue($producto['producto_id'], $producto['producto_codigo_interno']) ?></td>
            <td><?= $producto['producto_nombre'] ?></td>
            <td><?= $producto['nombre_marca'] ?></td>
            <td><?= $producto['nombre_grupo'] ?></td>
            <td><?= $producto['nombre_unidad'] ?></td>
            <td><?= $moneda_nombre ?></td>
            <td><?php
                if (isset($operacion)) {
                    $precio = $producto['precio'];
                    $string = ' $precio$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");
                    eval("\$result = ($string);");
                    echo number_format($result, 2);
                } else {
                    echo number_format($producto['precio'], 2);
                }
                ?>
            </td>
            <td>

                <?php
                if (isset($operacion)) {
                    $precio = $producto['costo'];
                    $string = ' $precio$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");
                    eval("\$result = ($string);");

                    echo number_format($result, 2);
                } else {
                    echo number_format($producto['costo'], 2);
                }
                ?>
            </td>
            <td><?=  number_format($producto['stock'],2) ?></td>
            <td><?php $subtotal = $producto['stock'] * $producto['costo'];


                if (isset($operacion)) {
                    $precio = $subtotal;
                    $string = ' $precio$operacion$tasa_soles ';
                    eval("\$string = \"$string\";");
                    eval("\$result = ($string);");

                    echo number_format($result, 2);
                } else {
                    echo number_format($subtotal, 2);
                }

                $total = $subtotal + $total;
                ?></td>

        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
