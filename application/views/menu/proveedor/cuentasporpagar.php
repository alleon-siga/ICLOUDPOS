<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Cuentas por pagar</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">

                <div class="row">
                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Ubicacion:</label>
                    </div>
                    <div class="col-md-3">

                        <select name="local_id" id="local_id" class='cho form-control'>
                            <option value="-1">TODOS</option>
                            <?php if (count($locales) > 0): ?>
                                <?php foreach ($locales as $local): ?>
                                    <option
                                            value="<?= $local->local_id; ?>"><?= $local->local_nombre ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Proveedor:</label>
                    </div>
                    <div class="col-md-3">

                        <select name="proveedor" id="proveedor" class='cho form-control'>
                            <option value="-1">TODOS</option>
                            <?php if (count($lstproveedor) > 0): ?>
                                <?php foreach ($lstproveedor as $cl): ?>
                                    <option
                                            value="<?php echo $cl['id_proveedor']; ?>"><?php echo $cl['proveedor_nombre']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="control-label panel-admin-text">Moneda:</label>
                    </div>
                    <div class="col-md-2">

                        <select name="moneda" id="moneda" class='cho form-control'>
                            <?php foreach ($monedas as $moneda): ?>
                                <option value="<?= $moneda->id_moneda ?>"
                                        data-simbolo="<?= $moneda->simbolo ?>"><?= $moneda->nombre ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2" style="display:none;">
                        <label class="control-label panel-admin-text">Documento:</label>
                    </div>
                    <div class="col-md-3" style="display:none;">

                        <select name="documento" id="documento" class='cho form-control'>
                            <option value="-1">TODOS</option>
                            <option value="BOLETA DE VENTA">BOLETA DE VENTA</option>
                            <option value="FACTURA">FACTURA</option>
                            <option value="NOTA DE PEDIDO">NOTA DE PEDIDO</option>
                        </select>
                    </div>

                    <button id="btnBuscar" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="block" id="lstTabla">


            <div class="table-responsive">

            </div>
        </div>

        <div class="block-section">


            <!--<div id="pp_excel">
                <form action="<?php echo $ruta; ?>ingresos/toExcel_cuentasPorPagar" name="frmExcel"
                      id="frmExcel" method="post">
                    <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
                    <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
                    <input type="hidden" name="proveedor1" value="-1" id="proveedor1" class='input-small'>
                </form>


            </div>
            <a href="#" onclick="generar_reporte_excel();" class='btn btn-default tip'
               title="Exportar a Excel"><i class="fa fa-file-excel-o"></i></a>

            <div id="pp_pdf">
                <form name="frmPDF" id="frmPDF"
                      action="<?php echo $ruta; ?>ingresos/toPdf_cuentasPorPagar"
                      method="post">
                    <input type="hidden" name="fecIni2" id="fecIni2" >
                    <input type="hidden" name="fecFin2" id="fecFin2" >
                    <input type="hidden" name="proveedor2" id="proveedor2" value="-1" class='input-small'>
                </form>
            </div>
            <a href="#" onclick="generar_reporte_pdf();" class='btn btn-default tip '
               title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i> </a>
        </div>-->

            <div class="col-sm-1"></div>


        </div>
    </div>


    <div class="modal fade" id="visualizarPago" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">


    </div>
    <script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
    <script>


        var verificar = 1;
        $(document).ready(function () {
            $("#pp_excel").hide();
            $("#pp_pdf").hide();

            buscar();

            $('select').chosen();
            //$(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});
            //$(".input-datepicker").datepicker('setDate', new Date());

            $("#proveedor, #documento").on('change', function () {
                $("#lstTabla").html('');
            });

            $("#btnBuscar").click(function (e) {
                e.preventDefault()
                buscar();
            });
        });


        function ver_detalle_pago(id_historial, ingreso_id) {

            $.ajax({
                type: 'POST',
                data: {'id_historial': id_historial, 'ingreso_id': ingreso_id},
                url: '<?php echo base_url();?>' + 'ingresos/imprimir_pago_pendiente',
                success: function (data) {
                    $("#visualizarPago").html(data);
                    $('#visualizarPago').modal('show');

                }
            });


        }

        function buscar() {

            $("#cargando_modal").modal('show');

            $.ajax({
                type: 'POST',
                data: $('#frmBuscar').serialize(),
                url: '<?php echo base_url();?>' + 'ingresos/lst_cuentas_porpagar',
                success: function (data) {
                    $("#lstTabla").html(data);
                    var simbolo = $("#moneda option:selected").attr('data-simbolo');
                    $(".tipo_moneda").html(simbolo);
                    $("#cargando_modal").modal('hide');

                }
            });
        }


        function generar_reporte_excel() {
            document.getElementById("frmExcel").submit();
        }

        function generar_reporte_pdf() {
            document.getElementById("frmPDF").submit();
        }


        function guardarPago1(total_ingreso, suma, id_ingreso, id_moneda, tasa_cambio) {

            lst_producto = new Array(); // Esto hay que limpiarlo por si algo falla.

            $("#guardarPagoPorPagar").attr('disabled', true)


            if ($("#cantidad_a_pagar").val() == "") {
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Debe ingresar una cantidad</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#guardarPagoPorPagar").attr('disabled', false)
                return false;

            }


            //alert(parseFloat(Math.ceil(total_ingreso - suma * 10) / 10))


            if (parseFloat($("#cantidad_a_pagar").val()) > (total_ingreso - suma).toFixed(2)) {
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Ha ingresado una cantidad mayor a la cantidad a pendiente</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#guardarPagoPorPagar").attr('disabled', false)
                return false;

            }

            if (parseFloat($("#cantidad_a_pagar").val()) == 0) {
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Debe ingresar un monto mayor a 0</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#guardarPagoPorPagar").attr('disabled', false)
                return false;

            }

            if (parseFloat($("#cantidad_a_pagar").val()) < 0) {
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Debe ingresar un monto mayor a 0</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#guardarPagoPorPagar").attr('disabled', false)
                return false;

            }

            producto.total_ingreso = total_ingreso;
            producto.suma = suma;
            producto.id_ingreso = id_ingreso;
            producto.cantidad_ingresada = parseFloat($("#cantidad_a_pagar").val());
            producto.id_moneda = id_moneda,
                producto.tasa_cambio = tasa_cambio

            lst_producto.push(producto);
            var miJSON = JSON.stringify(lst_producto);

            $.ajax({
                type: 'POST',
                data: $('#form').serialize() + '&lst_producto=' + miJSON,
                dataType: 'json',
                url: '<?= base_url()?>ingresos/guardarPago',
                success: function (data) {

                    if (data.exito != false) {

                        $.ajax({
                            type: 'POST',
                            data: $('#form').serialize() + '&lst_producto=' + miJSON,
                            //dataType:'json',
                            url: '<?= base_url()?>ingresos/cargar_vistapago_proveedor',
                            success: function (datat) {
                                $("#visualizarPago").html(datat);
                                $('#visualizarPago').modal('show');
                            }
                        })


                    } else {

                        var growlType = 'danger';
                        $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar el pago</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                        $("#guardarPagoPorPagar").attr('disabled', false)
                        return false;


                    }


                }
            })

        }

    </script>