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
  		colspan="8" >LISTA DE CUENTAS POR PAGAR</td>
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

	<th class='tip' title="Total">Días de atraso a hoy <?= date('d-m-Y')?></th>
    <?php if($local=="TODOS"){?>
        <th>Local</th>
    <?php } ?>
	<th>Status de la deuda</th>

</tr>
</thead>
<tbody>
	<?php if(count($pago_pendiente)>0):?>
	<?php foreach ($pago_pendiente as $v):?>
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
	  <?php endforeach;?>
	  <?php endif;?>
	</tbody>
</table>