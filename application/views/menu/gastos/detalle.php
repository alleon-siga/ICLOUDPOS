<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
	    <div class="modal-header">
	        <button type="button" class="close" onclick="$('#detalleModal').modal('hide');" aria-hidden="true">
	            &times;
	        </button>
	        <h4 class="modal-title">Detalle de gasto</h4>
	    </div>
	    <div class="modal-body">
	    	<div id="divMoneda" class="idMoneda" style="display: none"></div>
			<table class="table table-bordered" id="tblDetalleGasto">
			    <thead>
			    <tr>
			        <th>Descripci&oacute;n</th>
			        <th>Cantidad</th>
			        <th>Precio</th>
			        <th>Impuesto</th>
			        <th>Subtotal</th>
			        <th>Total</th>
			        <th></th>
			    </tr>
			    </thead>
			    <tbody id="cuerpo">
			    <?php
			    	if(!empty($detalles)){
			    		foreach ($detalles as $detalle) {
			    ?>
			    	<tr>
			    		<td>
			    			<input type="hidden" name="txtId[]" value="<?= $detalle->id ?>">
			    			<input style="width:300px;" autocomplete="off" type="text" class="form-control desc" name="txtDesc[]" value="<?= $detalle->descripcion ?>">
			    		</td>
			    		<td><input style="width:50px;" autocomplete="off"type="number" class="form-control cant" name="txtCant[]" value="<?= $detalle->cantidad ?>"></td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:70px;" autocomplete="off" type="number" class="form-control precio" name="txtPrec[]" value="<?= $detalle->precio ?>">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<input style="width:70px;" autocomplete="off" type="number" class="form-control impuesto" name="txtImp[]" value="<?= $detalle->impuesto ?>">
			    				<div class="input-group-addon">%</div>
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:100px;" readonly="true" autocomplete="off" type="number" class="form-control subt" name="txtSub[]" value="<?= $detalle->subtotal ?>">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:100px;" readonly="true" autocomplete="off" type="number" class="form-control total" name="txtTot[]" value="<?= $detalle->total ?>">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
								<a class="input-group-addon btn-danger btnDelete" data-toggle="tooltip" title="Eliminar" href="#">
									<i class="fa fa-trash-o"></i>
								</a>
				    			<a class="input-group-addon btn-default btnNuevo" data-toggle="tooltip" title="Agregar" href="#">
	                                <i class="fa fa-plus"></i>
	                            </a>
	                        </div>
			    		</td>
			    	</tr>
			    <?php
			     		} 
			     	}else{
			     ?>
			    	<tr>
			    		<td><input style="width:300px;" autocomplete="off" type="text" class="form-control desc" name="txtDesc[]" value=""></td>
			    		<td><input style="width:50px;" autocomplete="off"type="number" class="form-control cant" name="txtCant[]" value=""></td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:70px;" autocomplete="off" type="number" class="form-control precio" name="txtPrec[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<input style="width:70px;" autocomplete="off" type="number" class="form-control impuesto" name="txtImp[]" value="">
			    				<div class="input-group-addon">%</div>
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:100px;" readonly="true" autocomplete="off" type="number" class="form-control subt" name="txtSub[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
			    				<div class="input-group-addon idMoneda"></div>
			    				<input style="width:100px;" readonly="true" autocomplete="off" type="number" class="form-control total" name="txtTot[]" value="">
			    			</div>
			    		</td>
			    		<td>
			    			<div class="input-group">
								<a class="input-group-addon btn-danger btnDelete" data-toggle="tooltip" title="Eliminar" href="#">
									<i class="fa fa-trash-o"></i>
								</a>
				    			<a class="input-group-addon btn-default btnNuevo" data-toggle="tooltip" title="Agregar" href="#">
	                                <i class="fa fa-plus"></i>
	                            </a>
	                        </div>
			    		</td>
			    	</tr>			     
			     <?php	
			     	}
			     ?>
			    </tbody>
			</table>
	    </div>
	    <div class="modal-footer">
	    	<a href="#" id="updateDetalle" class="btn btn-default">Editar</a>
	        <a href="#" id="closeDetalle" class="btn btn-danger">Cancelar</a>
	    </div>
    </div>
</div>
<script src="<?= base_url() ?>recursos/js/detalle_gastos.js"></script>