<?php $ruta = base_url(); ?>
<table class='table table-striped dataTable dataTable-noheader table-condensed table-bordered' id="tbLista" name="tbLista">
	<thead>
		<tr>
			<th>Nro_Documento</th>
			<th>Documento &nbsp;</th>
			<th>Fecha Registro&nbsp; </th>
			<th>Fecha Emisión</th>
			<th>Proveedor&nbsp;</th>
			<th>Responsable&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if(count($lstCompra)>0):?>
		<?php foreach ($lstCompra as $c):?>
		 	<tr>
		  		<td><?php echo $c->Nro_Documento;?></td>
		  		<td><?php echo $c->Documento;?></td>
		  		<td><?php echo $c->FecRegistro;?></td>
		  		<td><?php echo $c->FecEmision;?></td>
		  		<td><?php echo $c->RazonSocial;?></td>
		  		<td><?php echo $c->Responsable;?></td>
			</tr>
		  <?php endforeach;?>
		  <?php else :?>
		  <?php endif;?>
	</tbody>
</table>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tbLista').dataTable( {
			"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
				"sLengthMenu": "_MENU_ filas por pagina"
			}
		} );
	} );
</script>