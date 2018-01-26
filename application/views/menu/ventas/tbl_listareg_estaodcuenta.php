<?php $ruta = base_url(); ?>
<table class='table table-striped dataTable table-bordered no-footer' id="" name="listaEC">
<thead>
	<tr>
        <th>Documento&nbsp;&nbsp;</th>
		<th>Nro. Venta</th>
		<th>Cliente</th>
		<th>Personal</th>
		<th>Fecha Reg.&nbsp;&nbsp;</th>
        <th>Fecha Canc.</th>
		<th>Monto Tot. &nbsp;&nbsp;</th>
		<th>Monto abonado. &nbsp;</th>
		<th>Saldo Pend&nbsp;&nbsp;</th>

        <?php if($local=="TODOS"){?>
            <th>Local</th>
        <?php } ?>
		<th>Estado de la Deuda&nbsp;&nbsp;</th>
		<th>Acci&oacute;n</th>
	</tr>
</thead>

<tbody>
	<?php  
	if(count($lstVenta)>0):
        ?>


	<?php foreach ($lstVenta as $v):?>
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
	  		<td style="text-align: center;"><span style="display: none"><?= date('YmdHis', strtotime($v->FechaReg)) ?></span><?php echo date('d-m-Y', strtotime($v->FechaReg));?></td>
            <td style="text-align: center;"><?php
                if($v->Estado=="PagoPendiente" || $v->Estado=="Debito"){
                    echo "--";

                }else{

                    if($v->FechaCancelado==null){
                        /*fecha ultimo es el ultimo pago que sele ha realziado a un credito*/
                        if($v->fecha_ultimo== null){

                        }else{

                            echo date('d-m-Y', strtotime($v->fecha_ultimo));
                        }

                     }else{
                        echo date('d-m-Y', strtotime($v->FechaCancelado));

                    }

                }?>

                </td>
	  		<td style="text-align: center;"><?php echo $v->Simbolo." ".$v->MontoTotal;?></td>
	  		<td style="text-align: center;"><?php echo $v->Simbolo." ".$v->MontoCancelado;?></td>
	  		<td style="text-align: center;"><?php echo $v->Simbolo." ".$v->SaldoPendiente;?></td>
            <?php if($local=="TODOS"){?>
                <td style="text-align: center;"><?php echo $v->local; ?></td>
            <?php } ?>
			<td style="text-align: center;<?php if($v->Estado=="PagoPendiente" || $v->Estado=="Debito"){
                echo "color:#f39c12"; }else{ echo  "color:#32CD32";   } ?>">
                <?php
                if($v->Estado=="PagoPendiente" || $v->Estado=="Debito"){
                    echo "PagoPendiente"; }else{
                    if($v->Estado==""){
                        echo "PagoCancelado";
                    }else{
                        echo $v->Estado;
                    }
                     }?>
            </td>
			<td class='actions_big'>
				<div class="btn-group">
					<a class='btn btn-default tip' title="Ver Venta" onclick="visualizar(<?= $v->Venta_Id; ?>)"><i
								class="fa fa-search"></i> Historial</a>


				</div>
			</td>

		</tr>
	  <?php endforeach;?>
	  <?php else :?>
	  <?php endif;?>
</tbody>


</table>
<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
	 aria-labelledby="myModalLabel"
	 aria-hidden="true">


</div>
<script type="text/javascript">
	$(document).ready(function() {
		TablesDatatables.init(4);
	} );


	function visualizar(id) {

        $('#cargando_modal').modal('show')

		$.ajax({
			url: '<?= base_url()?>venta/verVentaCredito',
			type: 'post',
			data: {'idventa': id},
			success: function (data) {
                $('#cargando_modal').modal('hide');
				$("#visualizar_venta").html(data);
				$('#visualizar_venta').modal('show');
			}

		})
	}
</script>