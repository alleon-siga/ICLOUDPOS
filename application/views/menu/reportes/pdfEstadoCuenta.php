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
    <tr>
        <td style="font-weight: bold;text-align: center; font-size:1.5em;  color: #000;"
            colspan="8">ESTADO DE CUENTAS
        </td>
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
    </tr>
    <tr>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="12%">&nbsp;&nbsp;</td>
        <td width="7%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="5%">&nbsp;&nbsp;</td>
        <td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%"><?php echo date("Y-m-d H:i:s"); ?></td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th>Documento&nbsp;&nbsp;</th>
        <th>Nro. Venta</th>
        <th>Cliente</th>
        <th>Personal</th>
        <th>Fecha Reg.&nbsp;&nbsp;</th>
        <th>Fecha Canc.</th>
        <th>Monto Tot. &nbsp;&nbsp;</th>
        <th>Monto abonado</th>
        <th>Saldo Pend&nbsp;&nbsp;</th>

        <?php if($local=="TODOS"){?>
            <th>Local</th>
        <?php } ?>
        <th>Etatus de la Deuda&nbsp;&nbsp;</th>
        <th>Acci&oacute;n</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($estado_cuenta) > 0): ?>
        <?php foreach ($estado_cuenta as $v): ?>
            <tr>
                <td style="text-align: center;">
                    <?php
                    if($v->TipoDocumento==1) echo "FA";
                    if($v->TipoDocumento==2) echo "NC";
                    if($v->TipoDocumento==3) echo "BO";
                    if($v->TipoDocumento==4) echo "GR";
                    if($v->TipoDocumento==5) echo "PCV";
                    if($v->TipoDocumento==6) echo "NP";
                    ?>
                </td>
                <td style="text-align: center;"><?php echo $v->NroVenta;?></td>
                <td style="text-align: center;"><?php echo $v->Cliente;?></td>
                <td style="text-align: center;"><?php echo $v->Personal;?></td>
                <td style="text-align: center;"><?php echo date('d-m-Y', strtotime($v->FechaReg));?></td>
                <td style="text-align: center;"><?php
                    if($v->FechaCancelado==null){
                        /*fecha ultimo es el ultimo pago que sele ha realziado a un credito*/
                        if($v->fecha_ultimo== null){
                            echo "PagoCancelado";
                        }else{

                            echo date('d-m-Y', strtotime($v->fecha_ultimo));
                        }

                    }else{
                        echo date('d-m-Y', strtotime($v->FechaCancelado));

                    }
                    ?>

                </td>
                <td style="text-align: center;"><?php echo $v->Simbolo." ".$v->MontoTotal;?></td>
                <td style="text-align: center;"><?php echo $v->Simbolo." ".$v->MontoCancelado;?></td>
                <td style="text-align: center;"><?php echo $v->Simbolo." ".$v->SaldoPendiente;?></td>
                <?php if($local=="TODOS"){?>
                    <td style="text-align: center;"><?php echo $v->local; ?></td>
                <?php } ?>
                <td style="text-align: center;<?php if($v->Estado=="PagoPendiente" || $v->Estado=="Debito"){
                    echo "color:#FFFF00"; }else{ echo  "color:#32CD32";   } ?>"><?php
                    if($v->Estado=="PagoPendiente" || $v->Estado=="Debito"){
                        echo "PagoPendiente"; }else{ echo $v->Estado;   }?></td>

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>