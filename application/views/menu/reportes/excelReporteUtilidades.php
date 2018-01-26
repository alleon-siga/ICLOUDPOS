<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ReporteUtilidades.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>
    <tr>
        <td width="12%" colspan="10" style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            >Reporte Utilidades</td>
        <td width="12%">&nbsp;&nbsp;</td>

        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
    </tr>

    <tr>
        <td width="18%" colspan="5" style="font-weight: bold;">Fecha Emisi&oacute;n: <?php echo date("d-m-Y H:i:s"); ?></td>
        <td width="25%"></td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>


    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th > Fecha y Hora</th>
        <th>C&oacute;digo</th>
        <th>N&uacute;mero</th>
        <th>Cantidad</th>
        <th>Producto</th>
        <th>Cliente</th>
        <th>Vendedor</th>
        <th>Costo</th>
        <th>Precio</th>
        <th>Utilidad</th>


    </tr>
    </thead>
    <tbody>
    <?php

    $total_utilidad=0;
    $total_costo=0;
    $total_precio=0;
    if (count($ventas) > 0) {

        foreach ($ventas as $venta) {
            $total_utilidad=$total_utilidad+$venta->detalle_utilidad;
            $total_costo=$total_costo+$venta->detalle_costo_promedio;
            $total_precio=$total_precio+$venta->precio ;

            ?>
            <tr>
                <td><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>
                <td><?= $venta->venta_id ?></td>
                <td><?= $venta->documento_Serie . " " . $venta->documento_Numero ?></td>
                <td><?= $venta->cantidad ?></td>
                <td><?= $venta->producto_nombre ?></td>
                <td><?= $venta->razon_social ?></td>
                <td><?= $venta->nombre ?></td>
                <td><?= $venta->detalle_costo_promedio ?></td>
                <td><?= $venta->precio ?></td>
                <td><?= $venta->detalle_utilidad ?></td>
            </tr>

        <?php
        }
    }?>
    <tr>
    </tr>
    <tr  >
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="border-top: 1px #000 dashed">Totales</td>



        <td style="border-top: 1px #000 dashed"><?= $total_costo ?></td>
        <td style="border-top: 1px #000 dashed"><?= $total_precio ?></td>
        <td style="border-top: 1px #000 dashed"><?= $total_utilidad ?></td>

    </tr>

    </tbody>
</table>