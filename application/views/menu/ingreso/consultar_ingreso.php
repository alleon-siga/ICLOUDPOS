<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li><a href="">Reporte de Ingreso</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-2">
           <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
        </div>
        <div class="col-md-4">
            <select id="locales" class="form-control campos cho" name="locales">
                <option value="seleccione"> Seleccione</option>
                <?php if(isset($locales)) {
                    foreach($locales as $local){
                        ?>
                        <option <?php if($this->session->userdata('id_local')==$local['int_local_id']) {
                            echo "selected"; } ?> value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

            </select>

        </div>

        <div class="col-md-2">
            <label class="control-label panel-admin-text">Estado Ingreso:</label>
        </div>
        <div class="col-md-4">
            <select id="status" class="form-control campos cho" name="status">
                <option value="PENDIENTE">PENDIENTE</option>

            </select>

        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            <label class="control-label panel-admin-text">Desde:</label>
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_desde" id="fecha_desde" required="true" readonly style="cursor: pointer;" class="form-control fecha campos">
        </div>
        <div class="col-md-2">
            <label class="control-label panel-admin-text">Hasta:</label>
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_hasta" id="fecha_hasta" required="true" readonly style="cursor: pointer;" class="form-control fecha campos">
        </div>

    </div>

    <div class="form-group row" id="div_estado_facturacion"  style="display: <?= valueOption('ACTIVAR_SHADOW') == 1 ? 'block' : 'none'?>;">
        <div class="col-md-2">
            <label class="control-label panel-admin-text">Estado Facturaci&oacute;n:</label>
        </div>
        <div class="col-md-4">
            <select id="estado_facturacion" class="form-control campos cho tooltip" name="estado_facturacion"
                    title="Completados son aquellos facturados o que no fue necesarios facturarlos" disabled>

                <option value="seleccione">Seleccione</option>
                    <option value="<?= FACTURACION_PENDIENTE ?>" title="Tooltip">PENDIENTE</option>
                    <option value="FACTURADO">COMPLETADO</option>

            </select>

        </div>
    </div>



    <div class="" id="tabla">


    </div>

    <div id="ec_excel">
        <form action="<?php echo $ruta; ?>ingresos/excel" name="frmExcel"
              id="frmExcel" method="post">
            <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
            <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
            <input type="hidden" name="local" id="localexcel" class='input-small'>
            <input type="hidden" name="estado" id="estadoexcel" class='input-small'>
            <input type="hidden" name="estado_facturacion" id="estado_facturacion_excel" class='input-small'>
            <input type="hidden" name="id_ingreso" value="" id="iddetalle" class='input-small'>
            <input type="hidden" name="ingreso_tipo" value="" id="ingreso_tipo" class='input-small'>
        </form>
    </div>
    <a href="#" onclick="generar_reporte_excel(0);" class='tip btn-lg btn btn-default'
       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i> </a>

    <div id="ec_pdf">
        <form name="frmPDF" id="frmPDF" action="<?php echo $ruta; ?>ingresos/pdf"
              target="_blank" method="post">
            <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
            <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
            <input type="hidden" name="local" id="localpdf" class='input-small'>
            <input type="hidden" name="estado" id="estadopdf" class='input-small'>
            <input type="hidden" name="estado_facturacion" id="estado_facturacion_pdf" class='input-small'>
            <input type="hidden" name="id_ingreso" value="" id="iddetalle_pdf" class='input-small'>
            <input type="hidden" name="ingreso_tipo" value="" id="ingreso_tipo_pdf" class='input-small'>
        </form>
    </div>
    <a href="#" onclick="generar_reporte_pdf(0);" class='btn btn-lg btn-default tip'
       title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i></a>
</div>


<div class="modal fade" id="modal_cerrar_ingreso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Cerrar Ingreso</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea cerrar el ingreso seleccionado?</p>
                </div>
                <div class="modal-footer">
                    <input type="button" id="" class="btn btn-primary" value="Confirmar" onclick="guardar_cerrar_ingreso()">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>



<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<!-- /.modal-dialog -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">
    $(function () {
        $('.uitip').tooltip();

        $("select").chosen({width: '100%'});
        $("#ec_excel").hide();
        $("#ec_pdf").hide();
        TablesDatatables.init();
        $(".fecha").datepicker({
            format: 'dd-mm-yyyy'
        });

        $("#status").on("change",function(){

            if($(this).val()=="COMPLETADO"){



                $("#estado_facturacion").attr('disabled', false).trigger("chosen:updated");

                $("#estado_facturacion [value='seleccione']")
                    .remove();

                $("#estado_facturacion")
                    .trigger('chosen:updated');
            }else{


                $("#estado_facturacion").append("<option value='seleccione'>Seleccione</option>");

                $('#estado_facturacion')
                    .find('option:last-child').prop('selected', true)
                    .end().trigger('chosen:updated');
                $("#estado_facturacion").attr('disabled', true).trigger("chosen:updated");
            }
        });


        $(".campos").on("change",function(){

            getINgresos();
        });
        getINgresos();
    });


    function guardar_cerrar_ingreso(){

        var ingreso_contable=false;
        if( $("#estado_facturacion").val()=="FACTURADO"){
            ingreso_contable=true;
        }


            if($("#id_ingreso_cerrar").val()!="") {
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url()?>ingresos/cerrar_ingreso',
                    data: {'idingreso': $("#id_ingreso_cerrar").val(), 'ingreso_contable':ingreso_contable},
                    dataType: 'JSON',
                    headers: {
                        Accept: 'application/json'
                    },
                    success: function (data) {

                        if (data.success == true) {

                            var growlType = 'success';
                            $.bootstrapGrowl('<p><h5>El ingreso se ha cerrado</h5></p>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $("#modal_cerrar_ingreso").modal('hide');
                            getINgresos();

                        }else{
                            var growlType = 'danger';
                            $.bootstrapGrowl('<p><h5>Ha ocurrido un error al cerrar el ingreso</h5></p>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            return false;
                        }
                    },
                    error: function () {
                        var growlType = 'danger';
                        $.bootstrapGrowl('<h4>Error</h4> <p><h5>Ha ocurrido un error</h5></p>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                        return false;
                    }
                })
            }else{

                var growlType = 'success';
                $.bootstrapGrowl('<h4>Debe selecionar un Ingreso!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                return false;
            }
    }

    function cerrar_ingreso(id){
        $("#id_ingreso_cerrar").val(id)
        $("#modal_cerrar_ingreso").modal('show');
    }

    function generar_reporte_excel(id_detalle,tipo) {
        if(id_detalle!=0){
            $("#iddetalle").val(id_detalle)
        }

        if(tipo){
            $("#ingreso_tipo").val(tipo)
        }

        document.getElementById("frmExcel").submit();
    }

    function generar_reporte_pdf(id_detalle,tipo) {

        if(id_detalle!=0){
            $("#iddetalle_pdf").val(id_detalle)
        }
        if(tipo){
            $("#ingreso_tipo_pdf").val(tipo)
        }
        document.getElementById("frmPDF").submit();
    }


    function ver(id, local,ingreso) {
            $("#ver").load('<?= base_url()?>ingresos/form/' + id + '/' + local+'/'+ingreso);
        $('#ver').modal('show');

    }

    function getINgresos(){
        var fercha_desde=$("#fecha_desde").val();
        var fercha_hasta=$("#fecha_hasta").val();
        var locales=$("#locales").val();
        var status = $("#status").val();
        var facturacion = $("#estado_facturacion").val();
        // $("#hidden_consul").remove();


        document.getElementById('fecIni1').value = $("#fecha_desde").val();
        document.getElementById('fecFin1').value = $("#fecha_hasta").val();

        document.getElementById('fecIni2').value = $("#fecha_desde").val();
        document.getElementById('fecFin2').value = $("#fecha_hasta").val();

        document.getElementById('localpdf').value = $("#locales").val();
        document.getElementById('localexcel').value = $("#locales").val();

        document.getElementById('estadopdf').value = $("#status").val();
        document.getElementById('estadoexcel').value = $("#status").val();

        /*les asigno el estado de facturacion*/
        document.getElementById('estado_facturacion_pdf').value = $("#estado_facturacion").val();
        document.getElementById('estado_facturacion_excel').value = $("#estado_facturacion").val();

        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresos',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'status': status,
                'estado_facturacion':facturacion
            },
            type: 'POST',
            success: function (data) {
                // $("#query_consul").html(data.consulta);
                if (data.length > 0)
                    $("#tabla").html(data);
                $("#tablaresult").dataTable();

                if($("#status").val()=="COMPLETADO" &&   $("#estado_facturacion").val()!="FACTURADO"){
                    $(".cerrar_ingreso").show();

                }else{
                    $(".cerrar_ingreso").hide();
                }

            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })

    }

</script>
