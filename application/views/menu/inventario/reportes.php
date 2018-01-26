<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Reportes</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="col-md-1">
        Ubicaci&oacute;n
    </div>
    <div class="col-md-3">
        <select id="locales" class="form-control" name="local">
            <?php if(isset($locales)) {
                foreach($locales as $local){
                    ?>
                    <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                <?php }
            } ?>
        </select>
        <br>
    </div>
    <div class="table-responsive" id="tabla">


    </div>

<input type="hidden" value="<?= $tipo ?>" id="tipo">

</div>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>

<!-- /.modal-dialog -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">
    $(function () {
        $.ajax({
            url: '<?= base_url()?>inventario/view_reporte',
            data: {'id_local': $("#locales").val(), tipo:$("#tipo").val() },
            type: 'POST',
            success: function (data) {
                // $("#query_consul").html(data.consulta);

                $("#tabla").html(data);
            }
        })
        TablesDatatables.init();
        $("#locales").on("change",function(){

            $("#cargando_modal").modal({
                show: true,
                backdrop: 'static'
            });
            // $("#hidden_consul").remove();
            $.ajax({
                url: '<?= base_url()?>inventario/view_reporte',
                data: {'id_local': $("#locales").val(), tipo:$("#tipo").val() },
                type: 'POST',
                success: function (data) {
                    // $("#query_consul").html(data.consulta);

                    $("#tabla").html(data);
                    $("#cargando_modal").modal('hide');
                }
            })
        });
        });
    </script>