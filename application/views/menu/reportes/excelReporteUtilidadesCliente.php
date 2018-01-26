<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ReporteUtilidadesCliente.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="3"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>


    <tr>
        <td width="12%" colspan="3" style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            >Reporte Utilidades Cliente</td>
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
        <th>C&oacute;digo</th>
        <th>Producto</th>
        <th>Utilidad</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($ventas) > 0) {

        foreach ($ventas as $venta) {

            ?>
            <tr>
                <td><?= $venta->id_cliente ?></td>
                <td><?= $venta->razon_social ?></td>
                <td><?= $venta->suma ?></td>

            </tr>

        <?php
        }
    }?>

    </tbody>
</table>