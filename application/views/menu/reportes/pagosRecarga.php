<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Pagos recarga</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/multiple-select.css" />
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="col-md-2">
                    <?php if (isset($locales)): ?>
                        <label class="control-label panel-admin-text">Ubicación</label>
                        <select id="local_id" class="form-control">
                            <option value="0">TODOS</option>
                            <?php foreach ($locales as $local): ?>
                                <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                        value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="control-label panel-admin-text">Fecha Registro</label>
                    <div class="row">
                        <div class="col-md-10">
                            <input type="text" id="fecha" class="form-control" readonly style="cursor: pointer;" name="fecha" value="<?= date('d/m/Y') ?> - <?= date('d/m/Y') ?>"/>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="checkbox" name="chkNoFecha" id="chkNoFecha" title="Considerar fecha" />
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Centro Poblado</label>
                    <select name="poblado_id" id="poblado_id" class='form-control'>
                        <option value="0">TODOS</option>
                        <?php foreach ($poblados as $poblado): ?>
                            <option value="<?= $poblado['id_grupos_cliente'] ?>"><?= $poblado['nombre_grupos_cliente'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Condici&oacute;n de Pago</label>
                    <input type="hidden" name="condicion_pago" id="condicion_pago" value="0">
                    <select name="estado_pago" id="estado_pago" class='form-control'>
                        <option value="0">TODOS</option>
                        <option value="1">Debe</option>
                        <option value="2">Cancelado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Usuario</label>
                    <select name="usuario_id" id="usuario_id" class='form-control'>
                    <?php if(isset($usuarios->nUsuCodigo)){ ?>
                        <option value="<?= $usuarios->nUsuCodigo ?>"><?= $usuarios->nombre ?></option>
                    <?php }else{ ?>
                        <option value="0">TODOS</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario->nUsuCodigo ?>"><?= $usuario->nombre ?></option>
                        <?php endforeach; ?>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <div style="padding-top: 30px;"></div>
                    <button id="btn_buscar" class="btn btn-default">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
            <br>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list">

                    </div>
                </div>
            </div>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#chkNoFecha').prop('checked', true);
                    var fecha = $('#fecha').val();

                    $('#estado_pago').on('change', function(){
                        if($(this).val()==1){
                            $('#chkNoFecha').prop('checked', false);
                            fecha = $('#fecha').val();
                            $('#fecha').val('');
                        }else{
                            $('#chkNoFecha').prop('checked', true);
                            $('#fecha').val(fecha);
                        }
                    });

                    $('#chkNoFecha').on('click', function(){
                        if($(this).prop('checked')==true){
                            $('#fecha').val(fecha);
                        }else{
                            fecha = $('#fecha').val();
                            $('#fecha').val('');
                        }
                    })

                    $('input[name="fecha"]').daterangepicker({
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

                    $('.ctrl').chosen();

                    $("#btn_buscar, .btn_buscar").on("click", function () {
                        getReporte();
                    });

                    $('.chosen-container').css('width', '100%');
                });

                function getReporte() {
                    $("#historial_list").html($("#loading").html());

                    var data = {
                        'local_id': $("#local_id").val(),
                        'fecha': $("#fecha").val(),
                        'estado_pago': $('#estado_pago').val(),
                        'poblado_id': $('#poblado_id').val(),
                        'usuario_id': $('#usuario_id').val()
                    };

                    $.ajax({
                        url: '<?= base_url()?>reporte/pagosRecarga/filter',
                        data: data,
                        type: 'POST',
                        success: function (data) {
                            $("#historial_list").html(data);
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                                type: 'danger',
                                delay: 5000,
                                allow_dismiss: true
                            });
                            $("#historial_list").html('');
                        }
                    });
                }
            </script>
