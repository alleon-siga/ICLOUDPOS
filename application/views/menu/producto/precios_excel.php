<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=utilidades_productos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border='1'>
    <tbody>
    <tr style="background-color: cyan;">
        <th style="text-align: left;">CODIGO</th>
        <th style="text-align: left;" colspan="3">NOMBRE</th>
        <th style="text-align: left;">MARCA</th>
        <th style="text-align: left;">GRUPO</th>
        <th style="text-align: left;">FAMILIA</th>
        <th style="text-align: left;">LINEA</th>
    </tr>
    <?php foreach ($productos as $p): ?>
        <tr>
            <td><?= $p->producto_codigo_interno?></td>
            <td colspan="3"><?= $p->producto_nombre ?></td>
            <td><?= $p->nombre_marca ?></td>
            <td><?= $p->nombre_grupo ?></td>
            <td><?= $p->nombre_familia ?></td>
            <td><?= $p->nombre_linea ?></td>
        </tr>
        <tr>
            <th colspan="2"></th>
            <th style="background-color: cyan;">Unidades</th>
            <th style="background-color: cyan;">Precio Unit.</th>
            <th style="background-color: cyan;">Precio Venta</th>
        </tr>
        <?php foreach ($p->precios as $precio): ?>
            <tr>
                <td></td>
                <td><?= $precio->nombre_unidad ?></td>
                <td style="text-align: right;"><?= $precio->unidades ?></td>
                <td style="text-align: right;"><?= $precio->precio ?></td>
                <td style="text-align: right;"><?= number_format($precio->unidades * $precio->precio, 2) ?></td>
                <td colspan="3"></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>

