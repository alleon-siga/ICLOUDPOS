<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=pago_pendiente.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;"
            colspan="9">LISTA DE CUENTAS POR COBRAR
        </td>
    </tr>
    <tr>
        <td colspan="8"></td>
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


        <td style="font-weight: bold;">Fecha Emision:</td>
        <td><?php echo date("Y-m-d H:i:s") ?> </td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table border="1">
    <thead>
    <tr>
        <th>Documento</th>
        <th>Nro Venta</th>
        <th>Cliente</th>
        <th class='tip' title="Fecha Registro">Fecha Reg.</th>
        <?php $md = get_moneda_defecto(); ?>
        <th class='tip' title="Monto Credito Solicitado">Monto Cred <?php echo $md->simbolo ?></th>
        <th class='tip' title="Monto Cancelado">Monto Abonado <?php echo $md->simbolo ?></th>
        <th class='tip' title="Monto Cancelado">Restante <?php echo $md->simbolo ?></th>

        <th class='tip' title="Total">Días de atraso a hoy <?= date('d-m-Y') ?></th>
        <?php if($local=="TODOS"){?>
            <th>Local</th>
        <?php } ?>
        <th>Estatus de la deuda</th>

    </tr>
    </thead>
    <tbody>
    <?php if (count($pago_pendiente) > 0): ?>
        <?php foreach ($pago_pendiente as $v): ?>
            <tr>
                <td style="text-align: center;"><?php echo $v->TipoDocumento; ?></td>
                <td style="text-align: center;"><?php echo $v->NroVenta; ?></td>
                <td><?php echo $v->Cliente; ?></td>
                <td style="text-align: center;"><?php echo date("d-m-Y", strtotime($v->FechaReg)) ?></td>

                <td style="text-align: center;"><?php echo $v->MontoTotal; ?></td>
                <td style="text-align: center;"><?php echo $v->MontoCancelado; ?></td>
                <td style="text-align: center;"><?php echo $v->MontoTotal - $v->MontoCancelado; ?></td>
                <td style="text-align: center;"><?php
                    $days = (strtotime(date('d-m-Y')) - strtotime($v->FechaReg)) / (60 * 60 * 24);
                    echo floor($days);
                    ?></td>
                <?php if($local=="TODOS"){?>
                    <td style="text-align: center;"><?php echo $v->local; ?></td>
                <?php } ?>
                <td style="text-align: center;"><?php echo $v->Estado; ?></td>

            </tr>
        <?php endforeach; ?>
    <?php else : ?>
    <?php endif; ?>
    </tbody>
</table>