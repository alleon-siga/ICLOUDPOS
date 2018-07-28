$(document).ready(function(){
	$('#btnEnviar').on('click', function(){
		$("#msjEnviar").html($("#loading").html());
		$.ajax({
			url: ruta + 'cotizar/enviarCotizacion',
			type: 'POST',
			data: $('#form1').serialize(),
			success: function(data){
				$("#msjEnviar").html('');
				mensaje('success', '<p>Cotizaci&oacute;n enviada con exito.</p>');
				$('#correoModal').modal('hide');
			},
			error: function () {
				mensaje('danger', '<p>Error inesperado.</p>');	
			}
		});
	});
});