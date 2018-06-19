<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Facturaci&oacute;n</li>
    <li><a href="">Emitir Facturas</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">

            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                        <?php if (isset($locales)): ?>
                            <label class="control-label panel-admin-text">Ubicaci&oacute;n</label>
                            <select id="local_id" class="form-control filter-input">
                                <?php foreach ($locales as $local): ?>
                                    <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                            value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>

                    <div class="col-md-3">
                        <label class="control-label panel-admin-text">Fecha Facturaci&oacute;n</label>
                        <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                               name="daterange" value="<?= date('d/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                    </div>


                    <div class="col-md-3">
                        <label class="control-label panel-admin-text">Estado:</label>
                        <select id="estado" class="form-control filter-input" name="estado">
                            <option value="">TODOS</option>
                            <option value="0">NO GENERADOS</option>
                            <option value="1">GENERADOS</option>
                            <option value="2">ENVIADOS</option>
                            <option value="3">ACEPTADOS</option>
                            <option value="4">RECHAZADOS</option>
                        </select>

                    </div>


                    <div class="col-md-1">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>

                    <div class="col-md-2 text-right">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <button type="button" class="btn btn-info" onclick="$('#leyenda_modal').modal('show')">
                            <i class="fa fa-info"></i> Leyenda
                        </button>
                    </div>

                </div>
            </div>
            <br>

            <div class="row-fluid">
                <div class="span12">
                    <div id="facturacion_list" class="block">


                    </div>

                </div>
            </div>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>

            <div class="modal fade" id="leyenda_modal" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">
                <div class="modal-dialog" style="width: 60%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Leyenda</h3>
                        </div>
                        <div class="modal-body">
                            <h3>Estados del Comprobante</h3>
                            <table class="table" cellpadding="15">
                                <tr>
                                    <th>Estado</th>
                                    <th style="width: 50%;">Descripci&oacute;n</th>
                                    <th style="width: 35%;">Acciones a Realizar</th>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                                class="label label-warning"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            NO GENERADO
                                        </div>
                                    </td>
                                    <td>
                                        En este estado el comprobante electr&oacute;nico no ha sido generado correctamente.
                                        Consultar en detalles
                                        <span class="btn btn-xs btn-primary">
                                            <i class="fa fa-list"></i>
                                        </span>
                                        para mas informaci&oacute;n acerca del error causado.
                                    </td>
                                    <td>
                                        <span class="btn btn-xs btn-warning">
                                            <i class="fa fa-refresh"></i>
                                        </span> <strong>Actualizar estado.</strong>
                                        Esta acci&oacute;n intentar&aacute; volver a generar el comprobante
                                        una vez halla sido corregido el error de validaci&oacute;n.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                                class="label label-primary"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            GENERADO
                                        </div>
                                    </td>
                                    <td>
                                        En este estado el comprobante electr&oacute;nico ha sido generado correctamente
                                        y firmado con su certificado digital.
                                        Ya puede imprimir en formato A4
                                        <span class="btn btn-xs btn-primary">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </span> o formato ticket
                                        <span class="btn btn-xs btn-primary">
                                            <i class="fa fa-print"></i>
                                        </span>.
                                    </td>
                                    <td>
                                        <span class="btn btn-xs btn-default">
                                            <i class="fa fa-mail-forward"></i>
                                        </span> <strong>Emitir comprobante a SUNAT.</strong> Esta acci&oacute;n enviara su comprobante
                                        a SUNAT para ser declarado. Posteriormente recibira su aceptaci&oacute;n o rechazo del mismo.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div
                                                class="label label-warning"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            ENVIADO
                                        </div>
                                    </td>
                                    <td>
                                        Cuando el comprobante electr&oacute;nico se encuentra en estado enviado quiere decir
                                        que fue emitido pero no recibio una respuesta de aceptaci&oacute;n o rechazo por parte
                                        de la SUNAT.
                                        Consultar en detalles
                                        <span class="btn btn-xs btn-primary">
                                            <i class="fa fa-list"></i>
                                        </span>
                                        para mas informaci&oacute;n acerca del error causado. Las posibles causas de este estado
                                        puede ser errores de conexi&oacute;n con SUNAT o error de validaci&oacute;n del comprobante.
                                    </td>
                                    <td>
                                        <span class="btn btn-xs btn-warning">
                                            <i class="fa fa-refresh"></i>
                                        </span> <strong>Actualizar estado.</strong>
                                        Esta acci&oacute;n intentar&aacute; volver a enviar el comprobante. Si tras varios
                                        intentos no ha podido enviar el comprobante y recibir una aceptaci&oacute;n o rechazo
                                        consulte con el administrador del sistema.
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div
                                                class="label label-success"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            ACEPTADO
                                        </div>
                                    </td>
                                    <td>
                                        Este estado identifica a un comprobante electr&oacute;nico valido y declarado a SUNAT.
                                        Tanto el emisor como el adquiriente del servicio o producto podran consultar su comprobante de
                                        pago a traves de los distintos servicio de consulta que la SUNAT ofrece.
                                    </td>
                                    <td>
                                        <span class="btn btn-xs btn-info">
                                            <i class="fa fa-download"></i>
                                        </span> <strong>Descargar comprobante XML.</strong> Aqui podr&aacute; descargar
                                        el comprobante electr&oacute;nico en su formato digital (XML).
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div
                                                class="label label-danger"
                                                data-placement="top"
                                                style="font-size: 1em; padding: 2px; cursor: pointer; white-space: nowrap;">
                                            RECHAZADO
                                        </div>
                                    </td>
                                    <td>
                                        Este estado identifica a un comprobante electr&oacute;nico rechazado por SUNAT
                                        y por lo tanto no es un comprobante valido.
                                        Consultar en detalles
                                        <span class="btn btn-xs btn-primary">
                                            <i class="fa fa-list"></i>
                                        </span>
                                        para mas informaci&oacute;n acerca del motivo del rechazo.
                                    </td>
                                    <td>
                                        Generar un nuevo comprobante electr&oacute;nico que cumpla con los requerimientos
                                        tributarios de la SUNAT.
                                    </td>
                                </tr>
                            </table>

                        </div>

                        <div class="modal-footer" align="right">
                            <div class="row">
                                <div class="text-right">
                                    <div class="col-md-12">
                                        <input type="button" class='btn btn-danger' value="Cerrar"
                                               data-dismiss="modal">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

                    $('input[name="daterange"]').daterangepicker({
                        "locale": {
                            "format": "DD/MM/YYYY",
                            "separator": " - ",
                            "applyLabel": "Aplicar",
                            "cancelLabel": "Cancelar",
                            "fromLabel": "De",
                            "toLabel": "A",
                            "customRangeLabel": "Personalizado",
                            "daysOfWeek": [
                                "Do",
                                "Lu",
                                "Ma",
                                "Mi",
                                "Ju",
                                "Vi",
                                "Sa"
                            ],
                            "monthNames": [
                                "Enero",
                                "Febrero",
                                "Marzo",
                                "Abril",
                                "Mayo",
                                "Junio",
                                "Julio",
                                "Agosto",
                                "Septiembre",
                                "Octubre",
                                "Noviembre",
                                "Diciembre"
                            ],
                            "firstDay": 1
                        }
                    });


                    $('select').chosen();

                    get_facturacion();

                    $("#btn_buscar").on("click", function () {
                        get_facturacion();
                    });

                });

                function get_facturacion() {
                    $("#facturacion_list").html($("#loading").html());

                    var local_id = $("#local_id").val();
                    var estado = $("#estado").val();
                    var fecha = $('#date_range').val();
                    $.ajax({
                        url: '<?= base_url()?>facturacion/emision/filter',
                        data: {
                            'local_id': local_id,
                            'fecha': fecha,
                            'estado': estado
                        },
                        type: 'POST',
                        success: function (data) {
                            $("#facturacion_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#facturacion_list").html('');
                        }
                    });

                }


            </script>
