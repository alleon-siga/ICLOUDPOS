<?php

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=CuentasPorPagar.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="9">CUENTAS POR PAGAR
        </td>
    </tr>
    <tr>
        <td colspan="9"></td>
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
        <td style="font-weight: bold;">Fecha Emision:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
</table>
<table border="1">
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
    <?php else : ?>
    <?php endif; ?>
    </tbody>
</table>