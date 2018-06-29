<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li>Inventario</li>
    <li><a href="">Kardex Valorizado</a></li>
</ul>
<div class="block">
    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label panel-admin-text">Ubicaci&oacute;n</label>
                <select class="form-control" id="local_id">
                    <?php foreach ($locales as $local): ?>
                        <option value="<?= $local->local_id ?>"><?= $local->local_nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1"></div>

            <div class="col-md-2">
                <label class="control-label panel-admin-text">A&ntilde;o</label>
                <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="control-label panel-admin-text">Mes</label>
                <select
                        id="mes"
                        class="form-control filter-input" name="mes">
                    <option value="01" <?= date('m')=='01' ? 'selected' : ''?>>Enero</option>
                    <option value="02" <?= date('m')=='02' ? 'selected' : ''?>>Febrero</option>
                    <option value="03" <?= date('m')=='03' ? 'selected' : ''?>>Marzo</option>
                    <option value="04" <?= date('m')=='04' ? 'selected' : ''?>>Abril</option>
                    <option value="05" <?= date('m')=='05' ? 'selected' : ''?>>Mayo</option>
                    <option value="06" <?= date('m')=='06' ? 'selected' : ''?>>Junio</option>
                    <option value="07" <?= date('m')=='07' ? 'selected' : ''?>>Julio</option>
                    <option value="08" <?= date('m')=='08' ? 'selected' : ''?>>Agosto</option>
                    <option value="09" <?= date('m')=='09' ? 'selected' : ''?>>Septiembre</option>
                    <option value="10" <?= date('m')=='10' ? 'selected' : ''?>>Octubre</option>
                    <option value="11" <?= date('m')=='11' ? 'selected' : ''?>>Noviembre</option>
                    <option value="12" <?= date('m')=='12' ? 'selected' : ''?>>Diciembre</option>
                </select>
                </select>
            </div>




            <div class="col-md-1">
                <label class="control-label panel-admin-text">Dias</label>
                <input type="number" min="1" id="dia_min" name="dia_min" value="1" class="form-control">
            </div>

            <div class="col-md-1">
                <label class="control-label panel-admin-text" style="color: white;">_</label>
                <input type="number" min="1" id="dia_max" name="dia_max" value="31" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="control-label panel-admin-text" style="color: white;">_</label><br>
                <button id="btn_buscar" class="btn btn-default btn-lg">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <br>

    <div id="tabla">

    </div>


</div>


<div class="modal fade" id="detalle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div id="load_div" style="display: none;">
    <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>

<script type="text/javascript">

$(function(){
    //CONFIGURACIONES INICIALES
    App.sidebar('close-sidebar');

    get_productos();

    $("#local_id").on('change', function(){
        $("#tabla").html('');
    });

    $("#btn_buscar").on('click', function(){
        get_productos();
    });

});

function get_productos(){
    $('#tabla').html($('#load_div').html());
    var data = {
        'local_id': $('#local_id').val()
    };

    $.ajax({
        url: '<?= base_url()?>reporte/get_productos',
        data: data,
        type: 'POST',
        success: function (data) {
            $("#tabla").html(data);
        },
        error: function () {
            $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                type: 'danger',
                delay: 5000,
                allow_dismiss: true
            });
            $("#tabla").html('');
        }
    });
}


</script>
