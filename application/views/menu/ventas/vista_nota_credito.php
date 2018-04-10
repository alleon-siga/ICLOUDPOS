<table class="table table-bordered">
    <thead>
    <tr>
        <th>Id</th>
        <th>Fecha</th>
        <th>Documento</th>
        <th>Cantidad</th>
        <th>UM</th>
        <th>Venta documento</th>
        <th>Venta Numero</th>
        <th>Fecha venta</th>
        <th>Venta estado</th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($data as $dato) { ?>
		<tr>
			<td><?= $dato->venta_id ?></td>
			<td><?= $dato->fecha ?></td>
			<td><?= $dato->documento ?></td>
			<td><?= $dato->cantidad ?></td>
			<td><?= $dato->um ?></td>
			<td><?= $dato->venta_documento ?></td>
			<td><?= $dato->venta_numero ?></td>
			<td><?= $dato->fecha_venta ?></td>
			<td><?= $dato->venta_estado ?></td>
		</tr>
<?php } ?>
    </tbody>
 </table>