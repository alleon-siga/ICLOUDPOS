<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Reporte de Venta</a></li>
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
                <option value=""> Seleccione</option>
                <?php if (isset($locales)) {
                    foreach ($locales as $local) {
                        ?>
                        <option <?php if($this->session->userdata('id_local')==$local['int_local_id']) echo "selected";  ?> value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

            </select>

        </div>


        <div class="col-md-2">
            <label> Estatus:  </label>
        </div>
        <div class="col-md-3">
            <select id="estatus" class="form-control campos" name="estatus">
                <option value=""> SELECCIONE</option>
                <option value="COMPLETADO"> COMPLETADO</option>
                <option value="EN ESPERA"> EN ESPERA</option>
                <option value="ANULADO"> ANULADO</option>
                <option value="DEVUELTO"> DEVUELTO</option>
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


    </div>
</div>

<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<!-- /.modal-dialog -->
<script type="text/javascript">
     function buscarventas() {

        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var estatus = $("#estatus").val();

        // $("#hidden_consul").remove();


      $.ajax({
            url: '<?= base_url()?>venta/get_ventas',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'estatus': estatus
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
        $('select').chosen();

        $("#pp_excel").hide();
        $("#pp_pdf").hide();

        TablesDatatables.init();
        buscarventas();
        $(".campos").on("change",function(){
            buscarventas();
        });

    });


</script>
