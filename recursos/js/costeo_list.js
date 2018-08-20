$(document).ready(function () {
    //TablesDatatables.init(0);

    $('#exportar_excel').on('click', function () {
        var data = {
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open(ruta + 'facturador/producto/costeo/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    });

    $("#exportar_pdf").on('click', function () {
        var data = {
            'producto_id': $("#producto_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val()
        };

        var win = window.open(ruta + 'facturador/producto/costeo/excel?data=' + JSON.stringify(data), '_blank');
        win.focus();
    });

    //Textbox de la cabecera para escribir toda la columna
    $('#txtAllCostoContMn, #txtAllCostoContMe, #txtAllPorcPrecio, #txtAllTipoCambio').on('keyup', function(e){
        var id = $(this).attr('id');
        if(id == 'txtAllCostoContMn'){
            $('input[name="txtCostoContMn"]').val($(this).val());
        }else if(id == 'txtAllCostoContMe'){
            $('input[name="txtCostoContMe"]').val($(this).val());
        }else if(id == 'txtAllPorcPrecio'){
            $('input[name="txtPorcPrecio"]').val($(this).val());
        }else{
            $('input[name="txtTipoCambio"]').val($(this).val());
        }

        var porcentaje_utilidad = costoContableMn = costoContableMe = tipo_cambio = precioCompMe = 0
        $('#tabla tbody tr').each(function(){
            porcentaje_utilidad = parseFloat($(this).find('input[name="txtPorcPrecio"]').val());
            costoContableMn = parseFloat($(this).find('input[name="txtCostoContMn"]').val());
            costoContableMe = parseFloat($(this).find('input[name="txtCostoContMe"]').val());
            tipo_cambio = parseFloat($(this).find('input[name="txtTipoCambio"]').val());
            precioCompMe = 0;

            if(id == 'txtAllCostoContMn'){
                if(tipo_cambio>0)
                    costoContableMe = costoContableMn / tipo_cambio;
                else
                    costoContableMe = 0;
                $(this).find('input[name="txtCostoContMe"]').val(costoContableMe.toFixed(2));
            }else if(id == 'txtAllCostoContMe'){
                if(tipo_cambio>0)
                    costoContableMn = costoContableMe * tipo_cambio;
                else
                    costoContableMn = 0
                $(this).find('input[name="txtCostoContMn"]').val(costoContableMn.toFixed(2));
            }else if(id == 'txtAllTipoCambio'){
                if(tipo_cambio>0)
                    costoContableMe = costoContableMn / tipo_cambio;
                else
                    costoContableMe = 0;
                $(this).find('input[name="txtCostoContMe"]').val(costoContableMe.toFixed(2));
            }

            precioCompMn = ((porcentaje_utilidad / 100) * costoContableMn) + costoContableMn;
            if(tipo_cambio>0) 
                precioCompMe = precioCompMn / tipo_cambio;

            if(porcentaje_utilidad!='' || costoContableMn!='' || costoContableMe!='' || tipo_cambio!=''){
                $(this).find('.preCompMn').text(precioCompMn.toFixed(2));
                $(this).find('.preCompMe').text(precioCompMe.toFixed(2));
            }else{
                $(this).find('.preCompMn').text('0.00');
                $(this).find('.preCompMe').text('0.00');
            }
        });
    });

    //Permite actualizar el calculo por fila
    $('#tabla').on('keyup','input',function(e){
        var tr = $(this).closest('tr');
        var porcentaje_utilidad = parseFloat(tr.find('input[name="txtPorcPrecio"]').val());
        var costoContableMn = parseFloat(tr.find('input[name="txtCostoContMn"]').val());
        var costoContableMe = parseFloat(tr.find('input[name="txtCostoContMe"]').val());
        var tipo_cambio = parseFloat(tr.find('input[name="txtTipoCambio"]').val());
        var precioCompMe = 0;

        if(e.target.name == 'txtCostoContMn'){
            if(tipo_cambio>0)
                costoContableMe = costoContableMn / tipo_cambio;
            else
                costoContableMe = 0;
            tr.find('input[name="txtCostoContMe"]').val(costoContableMe.toFixed(2));
        }else if(e.target.name == 'txtCostoContMe'){
            if(tipo_cambio>0)
                costoContableMn = costoContableMe * tipo_cambio;
            else
                costoContableMn = 0
            tr.find('input[name="txtCostoContMn"]').val(costoContableMn.toFixed(2));
        }else if(e.target.name == 'txtTipoCambio'){
            if(tipo_cambio>0)
                costoContableMe = costoContableMn / tipo_cambio;
            else
                costoContableMe = 0;
            tr.find('input[name="txtCostoContMe"]').val(costoContableMe.toFixed(2));
        }

        precioCompMn = ((porcentaje_utilidad / 100) * costoContableMn) + costoContableMn;
        if(tipo_cambio>0) 
            precioCompMe = precioCompMn / tipo_cambio;        

        if(e.target.value != ''){
            tr.find('.preCompMn').text(precioCompMn.toFixed(2));
            tr.find('.preCompMe').text(precioCompMe.toFixed(2));
        }else{
            tr.find('.preCompMn').text('0.00');
            tr.find('.preCompMe').text('0.00');
        }
    });
});

//Preparar datos para el envio al servidor
function prepareCosto(){
    var productos = [];
    
    $('#tabla tbody tr').each(function(){
        var producto = {};
        var contable_costo = 0;
        producto.producto_id = $(this).find('input[name="txtIdProducto"]').val();
        producto.contable_costo_mn = $(this).find('input[name="txtCostoContMn"]').val();
        producto.contable_costo_me = $(this).find('input[name="txtCostoContMe"]').val();
        producto.porcentaje_utilidad = $(this).find('input[name="txtPorcPrecio"]').val();
        producto.tipo_cambio = $(this).find('input[name="txtTipoCambio"]').val();
        productos.push(producto);
    });
    return JSON.stringify(productos);
}