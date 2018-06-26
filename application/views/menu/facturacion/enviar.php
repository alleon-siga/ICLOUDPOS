<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Facturaci&oacute;n</li>
    <li><a href="">Emitir Facturas</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
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
                        <div id="fecha_block">
                            <label class="control-label panel-admin-text">Fecha Facturaci&oacute;n</label>
                            <input type="text" id="date_range" class="form-control" readonly style="cursor: pointer;"
                                   name="daterange" value="<?= date('d/m/Y') ?>"/>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <label class="control-label panel-admin-text">Estado:</label>
                        <select id="estado" class="form-control filter-input" name="estado">
                            <option value="1">GENERADOS</option>
                            <option value="2">ENVIADOS</option>
                        </select>

                    </div>


                    <div class="col-md-1">
                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>
                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>

                    <div class="col-md-2 text-right">
                        <!--                        <label class="control-label panel-admin-text" style="color: #fff;">.</label><br>-->
                        <!--                        <button type="button" class="btn btn-info" onclick="$('#leyenda_modal').modal('show')">-->
                        <!--                            <i class="fa fa-info"></i> Leyenda-->
                        <!--                        </button>-->
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

            <div class="modal fade" id="resumen_modal" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
                 aria-hidden="true">
                <div class="modal-dialog" style="width: 45%">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Estado del progreso de emisi&oacute;n</h3>
                        </div>
                        <div class="modal-body">
                            <div id="mensaje_estado"></div>
                            <div class="progress">
                                <div id="mensaje_progress" class="progress-bar" role="progressbar" aria-valuenow="0"
                                     aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                    0%
                                </div>
                            </div>

                            <div id="mensaje_box" style="border: 1px solid #d3d3d3; height: 200px; overflow-y: scroll;">

                            </div>
                        </div>

                        <div class="modal-footer" align="right">
                            <div class="row">
                                <div id="cerrar_block" class="text-right">
                                    <div class="col-md-12">
                                        <input type="button" id="btn_cerrar" disabled="disabled" class='btn btn-danger'
                                               value="Cerrar"
                                               data-dismiss="modal">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">

                $(function () {

                    $('input[name="daterange"]').datepicker({format: 'dd/mm/yyyy'});


                    $('select').chosen();

                    get_facturacion();

                    $("#btn_buscar").on("click", function () {
                        get_facturacion();
                    });

                    $('#resumen_modal').on('hide.bs.modal', function (e) {
                        get_facturacion();
                    });

                    $('#estado').on('change', function () {
                        if ($(this).val() == 1) {
                            $('#fecha_block').show();
                        }
                        else {
                            $('#fecha_block').hide();
                        }
                    });

                });

                function get_facturacion() {
                    $("#facturacion_list").html($("#loading").html());

                    var local_id = $("#local_id").val();
                    var estado = $("#estado").val();
                    var fecha = $('#date_range').val();
                    $.ajax({
                        url: '<?= base_url()?>facturacion/enviar/filter',
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
