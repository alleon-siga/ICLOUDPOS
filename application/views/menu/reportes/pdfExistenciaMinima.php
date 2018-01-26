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

<table>

    <tbody>
    <tr>
        <td style="font-weight: bold;text-align: left; font-size:1.5em; color: #000;"
            colspan="5"><?php echo $this->session->userdata('EMPRESA_NOMBRE'); ?>
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="5">Existencia M&iacute;nima </td>
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