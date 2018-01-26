<?php $ruta = base_url(); ?>
<input id="base_url" type="hidden" value="<?= $ruta ?>">

<ul class="breadcrumb breadcrumb-top">
    <li>Compras </li>
    <li> <a href="">Consultar Compras </a> </li>
</ul>

<div class="block">

    <div class="row">
        <div class="form-group">

            <div class="col-md-1">
                <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
            </div>
            <div class="col-md-3">
                <?php if (isset($locales)): ?>
                    <select id="local_id" class="form-control">
                        <?php foreach ($locales as $local): ?>
                            <option <?php if ($this->session->userdata('id_local') == $local['int_local_id']) echo "selected"; ?>
                                value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

            </div>

            <div class="col-md-1">

            </div>

            <div class="col-md-2">
                <label class="control-label panel-admin-text">Estado:</label>
            </div>

            <div class="col-md-2">
                <select
                    id="estado"
                    class="form-control" name="estado">
                    <option value="COMPLETADO">COMPLETADO</option>
                    <option value="PENDIENTE">PENDIENTE</option>
                </select>

            </div>
            <div class="col-md-2">

            </div>

            <div class="col-md-2">
                <button id="btn_buscar" class="btn btn-default btn-lg">
                    <i class="fa fa-search"></i>
                </button>
            </div>

        </div>
    </div>
        <br>

        <div class="row">

            <div class="col-md-1">
                <label class="control-label panel-admin-text">Periodo:</label>
            </div>

            <div class="col-md-1">
                <input type="number" id="year" name="year" value="<?=date('Y')?>" class="form-control">
            </div>

            <div class="col-md-2">
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
            </div>


            <div class="col-md-1">

            </div>


            <div class="col-md-2">
                <label class="control-label panel-admin-text">Rango de Dias:</label>
            </div>
            <div class="col-md-1">
                <input type="number" min="1" id="dia_min" name="dia_min" value="1" class="form-control">
            </div>

            <div class="col-md-1">
                <input type="number" min="1" id="dia_max" name="dia_max" value="31" class="form-control">
            </div>

            <br><br>

        </div>


    <div id="tabla">

    </div>

    <div class="modal fade" id="ver_compra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


    <div class="modal fade" id="ingresomodal" style="width: 85%; overflow: auto;
      margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
    </div>

    <div id="load_div" style="display: none;">
        <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
            <div class="col-md-12 text-center">
                <div class="loading-icon"></div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">

    $(document).ready(function(){

        get_ingresos();

        $("#local_id, #estado, #mes").on('change', function(){
            $("#tabla").html('');
        });

        $("#year, #dia_min, #dia_max").bind('keyup change click', function(){
            $("#tabla").html('');
        });

        $("#btn_buscar").on('click', function(){
            get_ingresos();
        });
    });

    function get_ingresos(){
        $('#tabla').html($('#load_div').html());
            var data = {
                'local_id': $('#local_id').val(),
                'estado': $('#estado').val(),
                'mes': $('#mes').val(),
                'year': $('#year').val(),
                'dia_min': $('#dia_min').val(),
                'dia_max': $('#dia_max').val()
            };

            $.ajax({
            url: '<?= base_url()?>ingresos/lista_compra',
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