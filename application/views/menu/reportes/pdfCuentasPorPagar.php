<style type="text/css">
    table {
        width: 100%;
        border-color: #111 1px solid;
    }

    thead, th {
        background: #585858;
        /* #e7e6e6*/
        border-color: #111 1px solid;
        color: #fff;
    }

    tbody tr {
        border-color: #111 1px solid;
    }
</style>

<table>
    <tbody>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="10">CUENTAS POR PAGAR
        </td>
    </tr>
    <tr>
        <td colspan="10"></td>
    </tr>

    <tr>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("Y-m-d H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="10"></td>
    </tr>
    </tbody>
</table>
<table>
    <thead>
    <tr>
        <th class='tip' title="Documento"> ID Ingreso</th>
        <th class='tip' title="Documento"> Documento</th>
        <th>Proveedor</th>
        <th class='tip' title="Fecha Registro">Fecha Reg.</th>
        <th class='tip' title="Tipo Doc">Tipo de Doc.</th>
        <?php $md = get_moneda_defecto(); ?>
        <th class='tip' title="Total">Monto Ingreso <?php echo $md->simbolo ?></th>
        <th class='tip' title="Total">Monto abonado <?php echo $md->simbolo ?></th>
        <th class='tip' title="Total">Monto Deudor <?php echo $md->simbolo ?></th>
        <th class='tip' title="Total">Días de atraso a hoy <?= date('d-m-Y') ?></th>
        <th class='tip' title="Estatus">Estatus</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($cuentas) > 0): ?>
        <?php foreach ($cuentas as $row):
            ?>
            <tr>
                <td><?php echo $row['id_ingreso']; ?></td>
                <td style="text-align: center;"><?php echo $row['documento_serie'] . "-" . $row['documento_numero'] ?></td>
                <td><?php echo $row['proveedor_nombre']; ?></td>
                <td style="text-align: center;"><span
                        style="display: none"><?= date('YmdHis', strtotime($row['fecha_registro'])) ?></span><?php echo date("d-m-Y", strtotime($row['fecha_registro'])); ?>
                </td>
                <td style="text-align: center;"><?php echo $row['tipo_documento']; ?></td>
                <td style="text-align: center;"><?php echo number_format($row['suma_detalle'], 2); ?></td>
                <td style="text-align: center;">
                    <?php
                    if (!isset($row['abonado'])) {
                        echo "0.00";
                    } elseif ($row['abonado'] < $row['suma_detalle']) {
                        echo number_format($row['abonado'], 2);

                    } elseif (isset($row['abonado']) && $row['abonado'] >= $row['suma_detalle']) {
                        echo number_format($row['abonado'], 2);;

                    } ?></td>
                <td style="text-align: center;">
                    <?php

                    if (!isset($row['abonado'])) {

                        echo number_format($row['suma_detalle'], 2);
                    } elseif ($row['abonado'] < $row['suma_detalle']) {

                        echo number_format($row['pagoingreso_restante'], 2);

                    } elseif (isset($row['abonado']) && $row['abonado'] >= $row['suma_detalle']) { ?>

                        0.00


                    <?php }

                    ?> </td>
                <td style="text-align: center;"><?php

                    if (!isset($row['abonado'])) {

                        $days = (strtotime(date('d-m-Y')) - strtotime($row['fecha_registro'])) / (60 * 60 * 24);
                        echo floor($days);
                    } elseif ($row['abonado'] < $row['suma_detalle']) {
                        $days = (strtotime(date('d-m-Y')) - strtotime($row['fecha_registro'])) / (60 * 60 * 24);
                        echo floor($days);

                    } elseif (isset($row['abonado']) && $row['abonado'] >= $row['suma_detalle']) { ?>

                        0.00


                    <?php }


                    ?></td>
                <td style="text-align: center;">
                    <?php

                    if (!isset($row['abonado'])) {

                        echo "PENDIENTE";
                    } elseif ($row['abonado'] < $row['suma_detalle']) {
                        echo "PENDIENTE";

                    } elseif (isset($row['abonado']) && $row['abonado'] >= $row['suma_detalle']) { ?>

                        PAGO CANCELADO


                    <?php }


                    ?>

                </td>

            </tr>
            <?php

        endforeach; ?>

    <?php endif; ?>
    </tbody>
</table>