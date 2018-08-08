<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=estado_cuenta.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
  <tr>
  	<td style="font-weight: bold;text-align: center; font-size:1.5em; background-color:#BA5A41; color: #fff;" colspan="9" >ESTADO DE CUENTAS</td>
  </tr>
  <tr>
  	<td colspan="9" ></td>
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

    <td style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
    <td><?php echo date("Y-m-d H:i:s")?> </td>
  </tr>
  <tr>
  	<td colspan="9" ></td>
  </tr>
</table>
<table border="1">
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
	<?php
	if(count($estado_cuenta)>0):?>
		<?php foreach ($estado_cuenta as $v):?>
			<tr>
                <td style="text-align: center;">
                    <?php
                    if($v->TipoDocumento==1) echo "FA";
                    if($v->TipoDocumento==2) echo "NC";
                    if($v->TipoDocumento==3) echo "BO";
                    if($v->TipoDocumento==4) echo "GR";
                    if($v->TipoDocumento==5) echo "PCV";
                    if($v->TipoDocumento==6) echo "NV";
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
		<?php endforeach;?>
	<?php else :?>
	<?php endif;?>
	</tbody>

</table>