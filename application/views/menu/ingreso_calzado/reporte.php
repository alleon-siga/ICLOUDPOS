<style type="text/css">
    .tableStyle td {
        font-size: 9px !important;
    }

    .table > tbody > tr:hover {
        color: #333 !important;
    }

    .tableStyle th {
        font-size: 10px !important;
    }

    .input_table, .input_table_readonly {
        width: 100%;
        height: 25px;
        font-size: 10px !important;
        border: 1px solid #DEDEDE;
        text-align: center;
    }

    .input_table_readonly {
        background-color: #DEDEDE;
    }

    .table > tbody > tr > td {
        padding: 1px !important;
    }

    .table > thead > tr > th {
        padding: 4px 1px !important;
    }

    .panel_button {
        padding: 3px 5px;
    }

    .panel_button:hover {
        border: 1px solid #DEDEDE;
    }
</style>
<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li>
        <a href="">Reporte de Calzado</a>
    </li>
</ul>

<div class="block">
    <div class="row-fluid">

        <div class="row">

            <div class="col-md-3">
                <label class="control-label">Ubicaci&oacute;n:</label>
                <select name="local_id" id="local_id" class="form-control">
                    <?php foreach ($locales as $local): ?>
                        <option value="<?= $local->local_id ?>"
                            <?= $local->local_defecto == $local->local_id ? 'selected' : '' ?>>
                            <?= $local->local_nombre ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="control-label">Serie:</label>
                <select name="serie" id="serie" class="form-control">
                    <option value="">TODOS</option>
                    <?php foreach ($series as $s): ?>
                        <option value="<?= $s->serie ?>"
                                data-rango="<?= $s->rango ?>"><?= $s->serie ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="col-md-5">
                <label class="control-label">Plantilla:</label>
                <select name="plantilla" id="plantilla" class="form-control">
                    <option value="">TODOS</option>
                    <?php foreach ($plantillas as $p): ?>
                        <option value="<?= $p->id ?>"><?= $p->nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
                <label class="control-label" style="color: #fff;">buscar</label>
                <a id="find_producto" class="btn btn-default">
                    <i class="fa fa-search"></i>
                </a>
            </div>
        </div>

        <hr style="margin-bottom: 10px; margin-top: 10px;">


        <div id="table_data" class="table-responsive">
    </div>
</div>

<script type="text/javascript">

    $(function () {

        $("select").chosen();
        $("#fecha_emision").datepicker({
            format: 'dd-mm-yyyy'
        });

        $('#find_producto').on('click', function(){
            find();
        });

    });

    function find(){

//        if($('#plantilla').val() == ''){
//            show_msg('warning', 'Seleccione una plantilla');
//            return;
//        }



        var params = {
            plantilla_id: $('#plantilla').val(),
            local_id: $('#local_id').val(),
            serie: $('#serie').val(),
        };

        $("#cargando_modal").modal('show');
        $.ajax({
            url: '<?= base_url()?>ingreso_calzado/find',
            type: 'POST',
            data: params,
            success: function (data) {

                $('#table_data').html(data);

            },
            error: function () {
                show_msg('danger', 'Error al realizar la operacion');
            },
            complete: function () {
                $("#cargando_modal").modal('hide');
            }
        });
    }

</script>