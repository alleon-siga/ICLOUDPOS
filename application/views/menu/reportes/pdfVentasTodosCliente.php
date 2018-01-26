<style type="text/css">
    table{
        width: 100%;
        border-color: #111 1px solid;
    }
    thead , th{
        background: #585858;
        /* #e7e6e6*/
        border-color: #111 1px solid;
        color:#fff;
    }
    tbody tr{
        border-color: #111 1px solid;
    }
</style>
<table>
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em; color: #000;"
            colspan="8" >VENTAS POR CLIENTE</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
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
        <td width="20%">&nbsp;&nbsp;</td>
        <td width="18%">&nbsp;&nbsp;</td>
        <td width="10%" style="font-weight: bold;text-align: center;"></td>
        <td width="10%" style="text-align: center;"></td>
        <td width="5%" style="font-weight: bold; text-align: center;"></td>
        <td width="14%" style="text-align: center;"></td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("Y-m-d H:i:s");?></td>
    </tr>
    <tr>
        <td colspan="8" ></td>
    </tr>
</table>
<table>

        <thead><tr>
            <th>N&uacute;mero de Venta</th>
            <th>Fecha</th>
            <th>Vendedor</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Sub total</th>
            <th>Impuesto</th>
            <th>Total</th></tr>
    </thead>
    <tbody>
    <?php if (count($total) > 0){
        foreach ($total as $rows) { ?>
            <tr>
                <td >  <?= $rows->venta_id ?>  </td>
                <td ><?= date('d-m-Y H:i:s', strtotime($rows->fecha)) ?> </td>
                <td ><?=  $rows->username ?></td>;
                <td ><?=  $rows->razon_social ?></td>
                <td><?=  $rows->venta_status ?></td>
                <td><?=  $rows->simbolo." ".$rows->sub_total ?></td>
                <td><?=  $rows->simbolo." ".$rows->impuesto ?></td>
                <td><?= $rows->simbolo." ".$rows->totalizado ?></td>
            </tr>;
        <?php }
     } ?>
    </tbody>
</table>