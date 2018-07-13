$(document).ready(function () {
	$('#cargando_modal').modal('hide');
	TablesDatatables.init(0);

	$("#cerrar_pago_modal").on('click', function (){
	    $("#pago_modal").modal('hide');
	});
});

function imprimir(id, local_origen){
    $.bootstrapGrowl('<p>IMPRIMIENDO TRASPASO</p>', {
        type: 'success',
        delay: 2500,
        allow_dismiss: true
    });

    $("#imprimir_frame").attr('src', url + 'traspaso/imprimir/' + id + '/' + local_origen);
}

function ver(id){
    $("#verModal").html($("#loading").html());
    $("#verModal").load(url + 'traspaso/verDetalle/'+id);
    $('#verModal').modal('show');
}