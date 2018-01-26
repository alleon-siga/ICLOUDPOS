<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=ExistenciaMinima.xls");
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
        <td width="12%" colspan="5" style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            >Existencia M&iacute;nima</td>
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
        <td width="18%" colspan="5" style="font-weight: bold;">Fecha Emisi&oacute;n: <?php echo date("Y-m-d H:i:s"); ?></td>
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
        <th><?php echo getCodigoNombre() ?></th>
        <th>Nombre </th>
        <th>Stock M&iacute;nimo </th>
        <th>Existencia</th>
        <th>Fracci&oacute;n</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($inventarios) > 0) {
        foreach ($inventarios as $inventario) {
            ?>
            <tr>
                <td class="center"><?php echo getCodigoValue(sumCod($inventario->producto_id), $inventario->producto_codigo_interno) ?></td>
                <td class="center"><?= $inventario->producto_nombre ?></td>
                <td class="center"><?= $inventario->producto_stockminimo ?></td>
                <td><?= $inventario->cantidad ?></td>
                <td><?= $inventario->fraccion ?></td>

            </tr>
        <?php }
    } ?>
    </tbody>
</table>