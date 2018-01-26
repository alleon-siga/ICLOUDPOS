<?php $ruta = base_url(); ?>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>

<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Generar ventas</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="block-title">
        <h2><strong>Cronograma</strong> de Pago</h2>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <form method="post" id="frmVenta" action="#" class='form-horizontal form-bordered'>


                    <div class="box-content">

                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-1">
                                    <label for="nro_venta" class="control-label">Nro Venta:</label>
                                </div>


                                <input type="text" name="codVenta" id="codVenta" style="display:none;"
                                       class="form-control">


                                <?php if ($nro_venta != '') { ?>
                                    <div class="col-md-3">
                                        <input type="text" class='input-square input-medium'
                                               name="nro_venta" id="nro_venta" readonly>
                                    </div>
                                <?php } else { ?>
                                    <div class="col-md-4">
                                        <select name="nro_venta"  id="nro_venta" class="form-control" >
                                            <?php foreach($ventas as $venta){
                                                ?>
                                                <option value="<?=$venta->venta_id?>"><?=$venta->nombre_tipo_documento." ".$venta->documento_Serie."-".$venta->documento_Numero?></option>
                                            <?php
                                            }?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <a class="btn btn-default" data-placement="bottom"
                                           style="cursor: pointer;"
                                           onclick="buscar_venta();">Buscar</a>

                                    </div>
                                    <div class="col-md-3">

                                        <input type="text" name="nombre" id="nombre" class='input-square form-control'>
                                    </div>
                                <?php } ?>


                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-1">
                                <label for="fec_primer_pago" class="control-label">Fecha 1er
                                    Pago:</label>
                            </div>
                            <div class="col-md-3">
                                <div class="input-prepend input-append input-group">

                                    <input type="text" name="fec_prim_pago" id="fec_prim_pago"
                                           class='input-square input-small datepicker-dropdown form-control '
                                           data-date-format="dd/mm/yyyy">
                                    <span class="input-group-addon">dd/mm/yyyy </span>
                                </div>

                            </div>

                            <div class="col-md-1">
                                <label for="nroDia" class="control-label">Fecha Paga cada:</label>
                            </div>
                            <div class="col-md-3">
                                <div class="input-prepend input-append">
                                    <input type="text" name="nro_cuota" id="nro_cuota"
                                           style="display:none;">
                                    <input type="text" name="monto_cuota" id="monto_cuota"
                                           style="display:none;">
                                    <input type="number" class='input-square form-control input-small'
                                           name="nroDia" id="nroDia"
                                           onkeydown="return soloNumeros(event);"
                                           maxlength="6"><span class="add-on"> Días.</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <a class="btn btn-deault" data-placement="bottom"
                                   onclick="generar_cronograma();">Calcular</a>

                            </div>
                        </div>

                    </div>
                    <div class="row-fluid">
                        <div class="block">
                            <div class="box-head">
                                <h3>Detalle Cronograma</h3>
                            </div>
                            <div class="box-content box-nomargin">
                                <div id="lstTabla">


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-right">
                        <div class="col-md-12">
                            <div class="form-actions">
                                <input class="btn btn-default" value="Cancelar" type="reset"/>
                                <button class="btn btn-success" id="btnRealizarVenta">Generar Cronograma
                                </button>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    var lst_cronograma = new Array();

    $(document).ready(function () {

        $("#nro_venta").chosen();
        $("#fec_prim_pago").datepicker({
            startDate: '-1d',
            format: 'dd/mm/yyyy'
        });



        $("#lstTabla").hide();
        $("#nombre").hide();

        $("#btnRealizarVenta").click(function (e) {
            e.preventDefault();
            var miJSON = JSON.stringify(lst_cronograma);
            $.ajax({
                type: 'POST',
                data: 'lst_cronograma=' + miJSON,
                url: '<?php echo base_url();?>' + 'venta/registrar_cronogramapago',
                success: function (msj) {
                    if (msj == 'guardo') {

                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>Se ha generado el cronograma de pago</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                        $.ajax({
                            url: '<?= base_url()?>venta/cronograma_pago',
                            success: function (data) {
                                $('#page-content').html(data);
                            }

                        })


                    } else {
                        var growlType = 'error';

                        $.bootstrapGrowl('<h4>Ha ocurrido un error al generar el cronogrma  de pago</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);

                        return false;
                    }
                }
            });
            return false;
        });

    });

    function generar_cronograma() {

        lst_cronograma = new Array();
        var nro_cuota = $("#nro_cuota").val();
        var monto_cuota = $('#monto_cuota').val();
        var sumarDias = parseInt($("#nroDia").val());
      // var fechaPrimero = new Date($('#fec_prim_pago').val());

       var  from = $('#fec_prim_pago').val().split("/");
        var fechaPrimero = new Date(from[2], from[1] - 1, from[0])

        if (sumarDias > 0 && fechaPrimero.length != 0) {


            for (i = 1; i <= nro_cuota; i++) {

                var fechaSiguiente = new Date(fechaPrimero);
                fechaSiguiente.setDate(fechaSiguiente.getDate() + sumarDias);
                var anio = fechaPrimero.getFullYear();
                var mes = fechaPrimero.getMonth() + 1;
                var dia = fechaPrimero.getDate();

                var cronograma = {};
                cronograma.Codigo = $("#codVenta").val();
                cronograma.NroCuota = i;
                cronograma.monto_cuota = monto_cuota;
                cronograma.FechaInicio = (dia + "/" + mes) + "/" + fechaPrimero.getFullYear();
                cronograma.FechaFin = fechaSiguiente.getDate() + "/" + (fechaSiguiente.getMonth() + 1) + "/" +
                fechaSiguiente.getFullYear();

                lst_cronograma.push(cronograma);

                fechaPrimero = fechaSiguiente;
            }


            $("#lstTabla").show();
            var $tabla = $("#lstTabla");
            $tabla.find("table").remove();
            $tabla.append('<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
            '<thead><tr><th>Codigo</th><th>NroCuota</th><th>FechaInicio</th><th>FechaFin</th>' +
            '</th><th>Monto Cuota</th></tr>' +
            '</thead></table>');
            var tbody = $('<tbody></tbody>');
            jQuery.each(lst_cronograma, function (i, value) {
                tbody.append(
                    '<tr><td style="text-align: center;">' + value["Codigo"] +
                    '</td><td >' + value["NroCuota"] +
                    '</td><td style="text-align: center;">' + value["FechaInicio"] +
                    '</td><td style="text-align: center;">' + value["FechaFin"] +
                    '</td><td style="text-align: center;">' + value["monto_cuota"] +
                    '</tr>'
                );
            });

            $tabla.find("table").append(tbody);
        } else {
            $("#msj_stock").show();
        }
    }

    function del_cronograma() {
        var id = $("#codVenta").val();
        lst_cronograma.length = 0;
        $("#lstTabla").show();
        var $tabla = $("#lstTabla");
        $tabla.find("table").remove();
        $tabla.append('<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
        '<thead><tr><th>Codigo</th><th>NroCuota</th><th>FechaInicio</th><th>FechaFin</th><th>Monto Cuota</th></tr>' +
        '</thead></table>');
        var tbody = $('<tbody></tbody>');
        $tabla.find("table").append(tbody);
    }

    function buscar_venta() {
        var nro_venta = $("#nro_venta").val();
        $.ajax({
            type: 'POST',
            data: {'nro_venta': nro_venta, 'validar_cronograma': true},
            dataType: "json",
            url: '<?php echo base_url();?>' + 'venta/buscar_NroVenta_credito',
            success: function (msj) {


                if (msj.error != undefined) {
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + msj.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                    return false;
                }

                jQuery.each(msj, function (key, value) {
                    document.getElementById('codVenta').value = value["venta_id"];
                    document.getElementById('nro_cuota').value = value["int_credito_nrocuota"];
                    document.getElementById('monto_cuota').value = value["dec_credito_montocuota"];
                    document.getElementById('nombre').value = value["razon_social"];
                    document.getElementById('nombre').readOnly = true;
                    $("#nombre").show();
                });
            }
        });
        return false;
    }

</script>
