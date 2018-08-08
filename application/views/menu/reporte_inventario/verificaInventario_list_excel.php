<?php
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=verifica_inventario.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<h4 style="text-align: center; margin: 0;">Reporte de verificaci&oacute;n de inventario</h4>
<table border="1">
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Total compras</th>
            <th>Total ventas</th>
            <th>Compras - Ventas</th>
            <th>Stock Actual</th>
            <th>Diferencia</th>
            <th>Observaci&oacute;n</th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($lists as $dato):
        $compraVenta = $dato->compra - $dato->venta;
        $diferencia = $compraVenta - $dato->stock;
        $mensaje = $color = '';
        if($compraVenta!=$dato->stock){
            $mensaje = "Inconsistente";
            $color = "red";
        }

        if($compraVenta==$dato->stock && $inconsistencia=='1'){
            continue;
        }        
?>
        <tr>
            <td style="text-align: right; color:<?= $color ?>"><?= $dato->producto_id ?></td>
            <td style="text-align: left; color:<?= $color ?>"><?= $dato->producto_nombre ?></td>
            <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->compra, 0) ?></td>
            <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->venta, 0) ?></td>
            <td style="text-align: right; color:<?= $color ?>"><?= number_format($compraVenta, 0) ?></td>
            <td style="text-align: right; color:<?= $color ?>"><?= number_format($dato->stock, 0) ?></td>
            <td style="text-align: left; color:<?= $color ?>"><?= $diferencia ?></td>
            <td style="text-align: left; color:<?= $color ?>"><?= $mensaje ?></td>
        </tr>
<?php
    endforeach;
?>
    </tbody>
</table>
