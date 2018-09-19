<?php $md = get_moneda_defecto() ?>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Id.Dev.</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>UM</th>
        <th>Precio</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
<?php $Subtotal = 0; ?>    	
<?php foreach ($data as $dato) { ?>
		<tr>
			<td><?= $dato->id ?></td>
			<td><?= $dato->producto_nombre ?></td>
			<td><?= $dato->cantidad ?></td>
			<td><?= $dato->nombre_unidad ?></td>
			<td><?= $md->simbolo.' '.number_format($dato->precio,2) ?></td>
			<td><?= $md->simbolo.' '.number_format($dato->cantidad * $dato->precio,2) ?></td>
		</tr>
<?php $Subtotal += ($dato->cantidad * $dato->precio) ?>
<?php } ?>
    </tbody>
    <tfoot>
    	<tr>
    		<td align="right" colspan="5">Subtotal</td>
    		<td><?= $md->simbolo.' '.number_format($Subtotal,2) ?></td>
    	</tr>
    </tfoot>
 </table>