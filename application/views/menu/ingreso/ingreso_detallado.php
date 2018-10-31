<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Reportes</li>
    <li><a href="">Ingreso Detallado</a></li>
</ul>
<link rel="stylesheet" href="<?=$ruta?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">

            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2">
                        <label> Ubicaci&oacute;n: </label>
                    </div>
                    <div class="col-md-3">
                        <select id="locales" class="form-control campos" name="locales">
                            <option value="TODOS"> Todos</option>
                            <?php if (isset($locales)) {
                                foreach ($locales as $local) {
                                    ?>
                                    <option <?php if($this->session->userdata('id_local')==$local['int_local_id']) echo "selected";  ?> value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                                <?php }
                            } ?>

                        </select>

                    </div>


                    <div class="col-md-2">
                        <label> Proveedor:  </label>
                    </div>
                    <div class="col-md-3">
                        <select id="proveedor" class="form-control campos" name="proveedor">
                            <option value="TODOS"> Todos</option>
                            <?php if (isset($proveedores)) {
                                foreach ($proveedores as $proveedor) {
                                    ?>
                                    <option value="<?= $proveedor['id_proveedor']; ?>"> <?= $proveedor['proveedor_nombre'] ?> </option>

                                <?php }
                            } ?>
                        </select>

                    </div>

                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-md-2">
                    <label> Desde:</label>
                </div>
                <div class="col-md-3">
                    <input type="text" name="fecha_desde"  id="fecha_desde" value="<?= date('d-m-Y')?>" required="true" class="form-control fecha campos input-datepicker ">
                </div>
                <div class="col-md-2">
                    <label>   Hasta:</label>
                </div>
                <div class="col-md-3">
                    <input type="text" name="fecha_hasta" id="fecha_hasta"  value="<?= date('d-m-Y')?>" required="true" class="form-control fecha campos input-datepicker">
                </div>

            </div>
            <br>

        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <div class="block-title">

            </div>



            <div id="lstTabla" class="table-responsive"></div>
        </div>

        <div class="block-section">


            <div id="pp_excel">
                <form action="<?php echo $ruta; ?>exportar/toExcel_ingresodetalle" name="frmExcel"
                      id="frmExcel" method="post">
                    <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
                    <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
                    <input type="hidden" name="proveedor1" id="proveedor1" class='input-small'>
                    <input type="hidden" name="local1" id="local1" class='input-small'>
                    <div id="abrir_local_excel" ></div>
                </form>
            </div>
            <a href="#" onclick="generar_reporte_excel();" class=' btn btn-md btn-success'
               title="Exportar a Excel"><i class="fa fa-file-excel-o"></i></a>

            <div id="pp_pdf">
                <form name="frmPDF" id="frmPDF"
                      action="<?php echo $ruta; ?>exportar/toPDF_ingresodetalle" target="_blank"
                      method="post">
                    <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
                    <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
                    <input type="hidden" name="proveedor2" id="proveedor2" class='input-small'>
                    <input type="hidden" name="local2" id="local2" class='input-small'>
                    <div id="abrir_local_pdf" ></div>
                </form>
            </div>
            <a href="#" onclick="generar_reporte_pdf();" class='btn btn-md btn-danger'
               title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i> </a>

        </div>


    </div>
</div>

<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<!-- /.modal-dialog -->
<script type="text/javascript">
    function buscaringresos() {

        $("#cargando_modal").modal('show');

        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var proveedor = $("#proveedor").val();

        // $("#hidden_consul").remove();
        document.getElementById('fecIni1').value = $("#fecha_desde").val();
        document.getElementById('fecFin1').value = $("#fecha_hasta").val();
        document.getElementById('fecIni2').value = $("#fecha_desde").val();
        document.getElementById('fecFin2').value = $("#fecha_hasta").val();
        document.getElementById('proveedor2').value = $("#proveedor").val();
        document.getElementById('proveedor1').value = $("#proveedor").val();
        document.getElementById('local1').value = $("#locales").val();
        document.getElementById('local2').value = $("#locales").val();
        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresodetallado',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'proveedor': proveedor
            },
            type: 'POST',
            success: function (data) {

                // $("#query_consul").html(data.consulta);
                if (data.length > 0)
                    $("#lstTabla").html(data);
            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })

    };

    function generar_reporte_excel() {

        document.getElementById("frmExcel").submit();
    }

    function generar_reporte_pdf() {
        document.getElementById("frmPDF").submit();
    }

    $(function () {
        //CONFIGURACIONES INICIALES
        App.sidebar('close-sidebar');

        $('select').chosen();

        $("#pp_excel").hide();
        $("#pp_pdf").hide();

        buscaringresos();
        $(".campos").on("change",function(){
            buscaringresos();
        });
    });
</script>
