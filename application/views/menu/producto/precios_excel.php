<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=utilidades_productos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tbody>
    <tr>
        <th style="text-align: left; border-top: 1px solid black; border-left: 1px solid black;background-color: cyan;">CODIGO</th>
        <th style="text-align: left; border-top: 1px solid black;background-color: cyan;" colspan="3">NOMBRE</th>
        <th style="text-align: left; border-top: 1px solid black;background-color: cyan;">MARCA</th>
        <th style="text-align: left; border-top: 1px solid black;background-color: cyan;">GRUPO</th>
        <th style="text-align: left; border-top: 1px solid black;background-color: cyan;">FAMILIA</th>
        <th style="text-align: left; border-top: 1px solid black;background-color: cyan; border-right: 1px solid black;">LINEA</th>
    </tr>
    <?php foreach ($productos as $p): ?>
        <tr>
            <td style="text-align: center;  border-top: 1px solid black; border-left: 1px solid black; font-weight: bold;"><?= $p->producto_codigo_interno?></td>
            <td style=" border-top:1px solid black; font-weight: bold;" colspan="3"><?= $p->producto_nombre ?></td>
            <td style=" border-top:1px solid black; font-weight: bold;"><?= $p->nombre_marca ?></td>
            <td style=" border-top:1px solid black; font-weight: bold;"><?= $p->nombre_grupo ?></td>
            <td style=" border-top:1px solid black; font-weight: bold;"><?= $p->nombre_familia ?></td>
            <td style=" border-top:1px solid black; border-right: 1px solid black; font-weight: bold;"><?= $p->nombre_linea ?></td>
        </tr>
        <tr>
            <th style="border-left: 1px solid black;" colspan="2"></th>
            <th style="background-color: cyan;">Unidades</th>
            <th style="background-color: cyan;">Precio Unit.</th>
            <th style="background-color: cyan;">Precio Venta</th>
            <th colspan="3" style="border-right: 1px solid black;"></th>
        </tr>
        <?php foreach ($p->precios as $precio): ?>
            <tr>
                <td style="border-left: 1px solid black;"></td>
                <td><?= $precio->nombre_unidad ?></td>
                <td style="text-align: center;"><?= $precio->unidades ?></td>
                <td style="text-align: center;"><?= $precio->precio ?></td>
                <td style="text-align: center;"><?= number_format($precio->unidades * $precio->precio, 2) ?></td>
                <td colspan="3" style="border-right: 1px solid black;"></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>

