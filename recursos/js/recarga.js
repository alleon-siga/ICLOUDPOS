$(document).ready(function(){
	var ruta = $("#base_url").val();

	$("#cliente_new").on('click', function (e) {
	    e.preventDefault();
	    $('#dialog_new_cliente').attr('data-id', '');
	    $("#dialog_new_cliente").html($("#loading").html());
	    $('#dialog_new_cliente').modal('show');
	    $("#dialog_new_cliente").load(ruta + 'cliente/form/' + '-1');
	});

    $("#dialog_new_cliente").on('hidden.bs.modal', function () {
        var dni = $('#dialog_new_cliente').attr('data-id');
        if (dni != '') {
            $.ajax({
                headers: {
                    Accept: 'application/json'
                },
                url: ruta + 'venta_new/update_cliente',
                success: function (data) {
                    var selected = 1;
                    var template = '';
                    for (var i = 0; i < data.clientes.length; i++) {
                        if (dni == data.clientes[i].id_cliente)
                            selected = data.clientes[i].id_cliente;

                        template += '<option value="' + data.clientes[i].id_cliente + '" data-ruc="' + data.clientes[i].ruc + '">' + data.clientes[i].razon_social + '</option>';
                    }
                    $("#cliente_id").html(template);

                    $("#cliente_id").val(selected).trigger("chosen:updated");
                    $("#cliente_id").change();
                }
            });
        }
    });

    $("#terminar_venta").on('click', function (e) {
        e.preventDefault();
        $("#dialog_venta_contado").html($("#loading").html());
        $('#dialog_venta_contado').modal('show');
        $("#dialog_venta_contado").load(ruta + 'venta_new/dialog_venta_contado', function(){
            let num = parseFloat($('#total_importe').val());
            $('#vc_total_pagar').attr('value', num.toFixed(2));
            $('#vc_importe2').attr('value', $('#vc_importe').val());
            $('#vc_vuelto2').attr('value', $('#vc_vuelto').val());
        }); 
    });    

    $('.date-picker').datepicker({
        weekStart: 1,
        format: 'dd/mm/yyyy'
    });

    /*$('#terminar_venta').on('click', function(){
        $.ajax({
            url: ruta + 'venta_new/save_recarga',
            data: $('#frmRecarga').serialize(),
            type: 'POST',
            success: function(){

            }
        });
    });*/
});
