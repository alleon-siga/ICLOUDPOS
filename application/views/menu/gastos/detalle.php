<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
	    <div class="modal-header">
	        <button type="button" class="close" onclick="$('#detalleModal').modal('hide');" aria-hidden="true">
	            &times;
	        </button>
	        <h4 class="modal-title">Detalle de gasto</h4>
	    </div>
	    <div class="modal-body">
	    	<div id="divMoneda" class="idMoneda" style="display: none"></div>
			<table class="table table-bordered">
			    <thead>
			    <tr>
			        <th>Descripci&oacute;n</th>
			        <th>Cantidad</th>
			        <th>Precio</th>
			        <th>Impuesto</th>
			        <th>Subtotal</th>
			        <th></th>
			    </tr>
			    </thead>
			    <tbody id="detalle">
			    	<tr id="fila_1">
			    		<td><input autocomplete="off" type="text" class="form-control" name="txtDesc[]" value=""></td>
			    		<td><input autocomplete="off"type="number" class="form-control" name="txtCant[]" value=""></td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input autocomplete="off" type="number" class="form-control" name="txtPrec[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input autocomplete="off" type="number" class="form-control" name="txtImp[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input autocomplete="off" type="number" class="form-control" name="txtSub[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
								<a class="input-group-addon btn-danger" data-toggle="tooltip" title="Eliminar" href="#" onclick="eliminar(1)">
									<i class="fa fa-trash-o"></i>
								</a>
				    			<a class="input-group-addon btn-default" data-toggle="tooltip" title="Agregar" href="#" onclick="agregarDetalle2()">
	                                <i class="fa fa-plus"></i>
	                            </a>
	                        </div>
			    		</td>
			    	</tr>
			    </tbody>
			</table>
	    </div>
	    <div class="modal-footer">
	        <a href="#" class="btn btn-danger" onclick="$('#detalleModal').modal('hide');">Cerrar</a>
	    </div>
    </div>
</div>
<script type="text/javascript">
	var contador=2;
	function agregarDetalle2(){
		let moneda = $('#divMoneda').text();
		let fila = '<tr id="fila_'+ contador +'">';
		fila += '<td><input autocomplete="off" type="text" class="form-control" name="txtDesc[]" value=""></td>';
		fila += '<td><input autocomplete="off" type="number" class="form-control" name="txtCant[]" value=""></td>';
		fila += '<td><div class="input-group"><div class="input-group-addon idMoneda">'+moneda+'</div><input autocomplete="off" type="number" class="form-control" name="txtPrec[]" value=""></div></td>';
		fila += '<td><div class="input-group"><div class="input-group-addon idMoneda">'+moneda+'</div><input autocomplete="off" type="number" class="form-control" name="txtImp[]" value=""></div></td>';
		fila += '<td><div class="input-group"><div class="input-group-addon idMoneda">'+moneda+'</div><input autocomplete="off" type="number" class="form-control" name="txtSub[]" value=""></div></td>';
		fila += '<td><div class="input-group">';
		fila += '<a class="input-group-addon btn-danger" data-toggle="tooltip" title="Eliminar" href="#" onclick="eliminar('+contador+')"><i class="fa fa-trash-o"></i></a>';
		fila += '<a class="input-group-addon add btn-default" data-toggle="tooltip" title="Agregar" href="#" onclick="agregarDetalle2()"><i class="fa fa-plus"></i></a>';
		fila += '</div></td>';
		fila += "</tr>";
		$('#detalle').append(fila);
		contador++;
	}

	function eliminar(contador){
		if($('#detalle tr').length>1){
			$('#fila_'+contador).remove();	
		}
	}
</script>