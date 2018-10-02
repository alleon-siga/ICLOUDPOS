$(function () {
    $("#my-table tr:last").on('click','.btnNuevo',function(){
        //var tr = $(this).closest('tr');
        var clone = $(this).clone();
        /*clone.find('.tdCodigo').text('');
        clone.find('.tdNombre').text('');*/
         $(this).after(clone);
    });
    /*$('#my-table').Tabledit({
        columns: {
            identifier: [6, 'identify'],
            editable: [[2, 'Cantidad'], [4, 'Precio']]
        },
        buttons: {
          edit: {
            class: 'btn btn-sm btn-default',
            html: '<i class="fa fa-pencil" aria-hidden="true"></i>',
            action: 'edit'
          },
          delete: {
            class: 'btn btn-sm btn-danger',
            html: '<i class="fa fa-trash" aria-hidden="true"></i>',
            action: 'delete'
          },
          save: {
            class: 'btn btn-sm btn-success',
            html: '<i class="fa fa-save" aria-hidden="true"></i>'
          },
          confirm: {
            class: 'btn btn-sm btn-danger',
            html: '<i class="fa fa-save" aria-hidden="true"></i>'
          }
        },
        restoreButton: false,
        url: $('#ruta').val() + 'facturador/venta/editarVentaContable/',
        onSuccess: function(rpta) {
            if(rpta == 'edit'){
                mensaje('success', '<h4>Actualizado correctamente</h4>');
            }else if(rpta == 'delete'){
                mensaje('success', '<h4>Eliminado correctamente</h4>');
            }
        }
    });*/

    /*$('#my-table').on('keyup', 'input', function(e){
        var tr = $(this).closest('tr');
        var cantidad = parseFloat(tr.find('input[name="Cantidad"]').val());
        var precio = parseFloat(tr.find('input[name="Precio"]').val());
        var impuesto = parseFloat(tr.find('.impuesto').text());
        var simbolo = $('#txtMoneda').val();
        var subtotal = cantidad * precio;
        if(e.target.value != ''){
            tr.find('.importe').text(simbolo + ' ' + subtotal.toFixed(2));
        }else{
            tr.find('.importe').text(simbolo + ' ' + '0.00');
        }
        var subtotalSI = parseFloat(subtotal / ((impuesto / 100) + 1));
        var impuesto = subtotal - subtotalSI;
        $('#tdSubtotal').text(simbolo + ' ' + subtotalSI.toFixed(2));
        //$('#tdDescuento').text(simbolo + ' ' + );
        $('#tdImpuesto').text(simbolo + ' ' + impuesto.toFixed(2));
        $('#tdTotal').text(simbolo + ' ' + subtotal.toFixed(2));
    });*/
    $("#my-table").on('click','.btnDelete',function(){
        var tr = $(this).closest('tr');

        /*$.ajax({
            url : $('#ruta').val() + 'facturador/venta/editarVentaContable/',
            type: 'POST',
            data: {
                'venta_id': tr.find('input[name="Id"]').data('venta'),
                'producto_id' : tr.find('input[name="Id"]').val(),
                'unidad_id' : tr.find('input[name="Id"]').data('unidad'),

            }
        });*/
    });
    
    $('#my-table').on('click', '.costoContable', function(e){
        var tr = $(this).closest('tr');
        var precio = 0;

        if(tr.find('input[name="chkCostoContable"]').prop('checked') == true){
            if(tr.find('input[name="chkCostoContable"]').val()!=''){
                precio = parseFloat(tr.find('input[name="chkCostoContable"]').val());
            }
        }else{
            precio = parseFloat(tr.find('input[name="Precio"]').data('precio'));
        }

        var cantidad = parseFloat(tr.find('input[name="Cantidad"]').val());
        var simbolo = tr.find('input[name="Id"]').data('simbolo');
        var subtotal = cantidad * precio;
        tr.find('input[name="Precio"]').val(precio.toFixed(2));
        tr.find('.importe').text(simbolo + ' ' + subtotal.toFixed(2));
        tr.find('input[name="Id"]').data('importe', subtotal);
        calcularPie();
    });

    $('#my-table').on('keyup', '.Cantidad', function(e){
        var tr = $(this).closest('tr');
        calcular(tr, e);
        calcularPie();
    });

    $('#my-table').on('keyup', '.Precio', function(e){
        var tr = $(this).closest('tr');
        calcular(tr, e);
        calcularPie();
    });        
});

function calcular(tr, e)
{
    var cantidad = parseFloat(tr.find('input[name="Cantidad"]').val());
    var precio = parseFloat(tr.find('input[name="Precio"]').val());
    var simbolo = $('#txtMoneda').val();
    var impuesto = parseFloat(tr.find('input[name="Id"]').data('impuesto'));
    var subtotal = 0;

    tr.find('input[name="Precio"]').val(precio.toFixed(2));
    if(isNaN(cantidad) || isNaN(precio)){
        subtotal = 0.00;
    }else{
        subtotal = cantidad * precio;
    }
    tr.find('.importe').text(simbolo + ' ' + subtotal.toFixed(2));
    tr.find('input[name="Id"]').data('importe', subtotal);
}

function calcularPie()
{
    var impuesto = subtotal = total = 0;
    var simbolo = $('#txtMoneda').val();
    var tipo_impuesto = $('#txtTipoImpuesto').val();

    $('#my-table tbody tr').each(function(){
        importe = parseFloat($(this).find('input[name="Id"]').data('importe'));
        tasa = parseFloat($(this).find('input[name="Id"]').data('impuesto')); //18

        if(tipo_impuesto=='1'){ //incluye impuesto
            importe2 = importe / ((tasa / 100) + 1);
            impuesto2 = importe - importe2;
        }else if(tipo_impuesto=='2'){ //agregar impuesto
            importe2 = importe;
            importeCI = importe * ((tasa / 100) + 1);
            impuesto2 = importe2 - importeCI;
            importe = impuesto2 + importeCI;
        }

        subtotal += importe2;

        total += importe;
        impuesto += impuesto2;
    });
    $('#tdSubtotal').text(simbolo + ' ' + subtotal.toFixed(2));
    $('#tdImpuesto').text(simbolo + ' ' + impuesto.toFixed(2));
    $('#tdTotal').text(simbolo + ' ' + total.toFixed(2));
}