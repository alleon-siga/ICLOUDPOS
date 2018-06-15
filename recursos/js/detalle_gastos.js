$(document).ready(function () {
	$('.idMoneda').text($('#cuenta_id').find(':selected').data('moneda'));
	$("#tblDetalleGasto").on('click','.btnDelete',function(){
 		if($('#cuerpo tr').length>1){
 			$('#load_div').show();
 			var tr = $(this).closest('tr');
 			var id = tr.find('input[name^="txtId"]').val();
	        $.ajax({
	            url: url + 'gastos/deleteDetalle/'+id,
	            type: 'POST',
	            dataType: 'json',
	            success: function (data) {
	            	$('#load_div').hide();
	            	if (data.error == undefined) {
	            		mensaje('success', "Registro eliminado correctamente");
	            		tr.remove();
	            	}else{
	            		mensaje('warning', "Error, hubo un error");
	            	}
	            },
	            error: function () {
	                alert('Error');
	            }
	        });
 		}else{
 			$('#cuerpo input').val('');
 		}
 	});

 	$("#tblDetalleGasto").on('click','.btnNuevo',function(){
 		var error = validar();
 		if(error==false){
			var tr = $(this).closest('#cuerpo tr');
			var clone = tr.clone();
			clone.find('input:not(.impuesto)').val('');
			tr.after(clone);
 		}
 	});


 	$('#closeDetalle').on('click', function(){
 		$('#detalleModal').modal('hide');
 	});

 	$("#tblDetalleGasto").on('change','input',function(){
 		let tr = $(this).closest('tr');
 		let cantidad = tr.find('input[name^="txtCant"]').val();
 		let precio = tr.find('input[name^="txtPrec"]').val();
 		let sb = parseFloat(cantidad * precio);
 		let impuesto = (tr.find('input[name^="txtImp"]').val() / 100) + 1;
 		let total = parseFloat(sb * impuesto);
 		tr.find('input[name^="txtSub"]').val(sb.toFixed(2));
 		tr.find('input[name^="txtTot"]').val(total.toFixed(2));
 	});

 	$('#updateDetalle').on('click', function(){
        $('#load_div').show();
        $.ajax({
            url: url + 'gastos/editarDetalle/',
            type: 'POST',
            dataType: 'json',
            data: $("#formagregar").serialize(),
            success: function (data) {
            	$('#load_div').hide();
            	if (data.error == undefined) {
            		mensaje('success', "Registro actualizado correctamente");
            	}else{
            		mensaje('warning', "Error, hubo un error");
            	}
            	$('#detalleModal').modal('hide');
            },
            error: function () {
                alert('Error');
            }
        });
 	});
});

function mensaje(growlType, msj){
	var growlType = 'warning';
	$.bootstrapGrowl('<h4>'+msj+'</h4>', {
	    type: growlType,
	    delay: 2500,
	    allow_dismiss: true
	});
	return true;
}

function validar(){
	var error = false;
	$('input[name^="txtDesc"]').each(function() {
		if ($(this).val() == '') {
			error = mensaje('warning', 'Debe ingresar la descripci&oacute;n');
		}
	});
	if(error==false){
		$('input[name^="txtCant"]').each(function() {
			if ($(this).val() == '' || $(this).val()<1) {
				error = mensaje('warning', 'Debe ingresar la cantidad o es inv&aacute;lido');
			}
		});
	}
	if(error==false){
		$('input[name^="txtPrec"]').each(function() {
			if ($(this).val() == '' || $(this).val()<0) {
				error = mensaje('warning', 'Debe ingresar el precio o es inv&aacute;lido');
			}
		});
	}
	if(error==false){
		$('input[name^="txtImp"]').each(function() {
			if ($(this).val() == '' || $(this).val()<0) {
				error = mensaje('warning', 'Debe ingresar el importe o es inv&aacute;lido');
			}
		});
	}
	return error;
}