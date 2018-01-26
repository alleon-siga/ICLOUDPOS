<style type="text/css">
    table {
        width: 100%;
    }

    thead, th {
        background-color: #CED6DB;
        color: #000;
    }
    tbody tr{
        border-color: #111;
    }

    .title {
        font-weight: bold;
        text-align: center;
        font-size: 1.5em;
        color: #000;
    }
</style>
<?php

$totalutilidad=0;
$totalvalorizacion=0; ?>
<table>

    <tbody>
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>

    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="5">Reporte Utilidades Producto</td>
    </tr>
    <tr>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("d-m-Y H:i:s"); ?></td>
        <td></td>
        <td></td>
        <td></td>

    </tr>

    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th>C&oacute;digo</th>
        <th>Producto</th>
        <th>Utilidad</th>
        <th>Valorizaci&oacute;n</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0) {

        foreach ($ventas as $venta) {
            $totalutilidad=$totalutilidad+$venta->suma;
            $totalvalorizacion=$totalvalorizacion+$venta->valorizacion;
            ?>
            <tr>
                <td><?= $venta->id_producto ?></td>
                <td><?= $venta->producto_nombre ?></td>
                <td><?= $venta->suma ?></td>
             <td><?= $venta->valorizacion ?></td></tr>

        <?php
        }
    }?>
    <tr >
        <td >Total</td>
        <td></td>
        <td><?= $totalutilidad ?></td>
        <td><?= $totalvalorizacion ?></td></tr>
    </tbody>
</table>