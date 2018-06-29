<?php $ruta = base_url(); ?>
<style>
    .tcharm {
        background-color: #fff;
        border: 1px solid #dae8e7;
        width: 300px;
        padding: 0 20px;
        overflow-y: auto;
    }

    .tcharm-header {
        text-align: center;
    }

    .tcharm-body .row {
        margin: 20px 3px;
    }

    .tcharm-close {
        text-decoration: none !important;
        color: #333333;
        padding: 3px;
        border: 1px solid #fff;
        float: left;
    }

    .tcharm-close:hover {
        background-color: #dae8e7;
        color: #333333;
    }
</style>

<form id="form_filter">
    <div id="charm" class="tcharm">
        <div class="tcharm-header">

            <h3><a href="#" class="fa fa-arrow-right tcharm-close"></a> <span>Filtros Avanzados</span></h3>
        </div>

        <div class="tcharm-body">

            <div class="row">
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-default btn_buscar">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button id="btn_filter_reset" type="button" class="btn btn-warning">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-danger tcharm-trigger">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>

            </div>

            <div class="row">

            </div>

            <div class="row">
                <label class="control-label">Marca:</label>
                <select class="form-control" name="marca" id="marca">

                    <option value="">Marca</option>
                    <?php foreach ($marcas as $marca) {

                        ?>
                        <option value="<?= $marca['id_marca'] ?>"><?= $marca['nombre_marca'] ?></option>
                        <?php
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Grupo:</label>
                <select class="form-control" name="grupo" id="grupo">
                    <option value="">Grupo</option>
                    <?php foreach ($grupos as $grupo) {

                        ?>
                        <option value="<?= $grupo['id_grupo'] ?>"><?= $grupo['nombre_grupo'] ?></option>
                        <?php
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Linea:</label>
                <select class="form-control" name="linea" id="linea">

                    <option value="">Linea</option>
                    <?php foreach ($lineas as $linea) {

                        ?>
                        <option value="<?= $linea['id_linea'] ?>"><?= $linea['nombre_linea'] ?></option>
                        <?php
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Familia:</label>
                <select class="form-control" name="familia" id="familia">
                    <option value="">Familia</option>
                    <?php foreach ($familias as $familia) {

                        ?>
                        <option value="<?= $familia['id_familia'] ?>"><?= $familia['nombre_familia'] ?></option>
                        <?php
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Moneda:</label>
                <select class="form-control" name="monedas" id="monedas">

                    <?php foreach ($monedas as $moneda) {

                        ?>
                        <option
                                value="<?= $moneda['id_moneda'] ?>"><?= $moneda['nombre'] ?><?php echo (intval($moneda['tasa_soles'] > 0)) ? '- Tasa de cambio:' . $moneda['tasa_soles'] : ''; ?> </option>
                        <?php
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Usar:</label>
                <input type="radio" name="usar" class="check_change" value="ultimo_costo" checked>
                <label>Costo promedio</label>
                <input type="radio" name="usar" class="check_change" value="promedio">
            </div>
        </div>
    </div>
</form>
<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>

<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li><a href="">Valorizacion de Inventario</a></li>
</ul>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?>
        </span>
        </div>
    </div>
</div>
<div class="block">
    <div class="row">
        <div class="col-md-2">
            <label class="control-label">Almacen:</label>

        </div>

        <div class="col-md-3">
            <select class="form-control campos" id="local">
                <option value="">TODOS</option>
                <?php
                foreach ($locales as $local) {


                    echo "<option value=" . $local['int_local_id'] . ">" . $local['local_nombre'] . "</option>";

                }
                ?>
            </select>
        </div>
        <div class="col-md-5"></div>
        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>

    <div id="tabla">


    </div>
</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">


</script>

<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>

<script>
    $(function () {
        //CONFIGURACIONES INICIALES
        App.sidebar('close-sidebar');
        
        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });

        $('.btn_buscar').on('click', function () {
            get_inventario();
        });

        $("#btn_filter_reset").on('click', function(){
            $('#vendedor_id').val('0').trigger('chosen:updated');
            $('#vendedor_id').change();
            $('#atraso').val('0');
            $('#dif_deuda').val('1');
            $('#dif_deuda_value').val('0');
            get_inventario();
            //$("#cliente_id").val('0').trigger('chosen:updated');
        });

        TablesDatatables.init();
        get_inventario();

//        $(".form-control, .check_change").on("change", function () {
//            get_inventario();
//
//        });

        var t = setTimeout(function () {
            $(window).resize();
        }, 400);

        $("#tbody").selectable({
            stop: function () {
                var id = $("#tbody tr.ui-selected").attr('id');
            }
        });

    });

    function get_inventario() {
        $("#charm").tcharm('hide');

        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });

        var local = $("#local").val();
        var marca = $("#marca").val();
        var grupo = $("#grupo").val();
        var linea = $("#linea").val();
        var familia = $("#familia").val();
        var producto_nombre = $("#producto_nombre").val();
        var producto_id = $("#producto_id").val();
        var monedas = $("#monedas").val();

        var myRadio = $('input[name=usar]');
        var usar = myRadio.filter(':checked').val();


        $.ajax({
            url: '<?php echo $ruta ?>inventario/get_valorizacion_inventario',
            data: {
                'local': local,
                'marca': marca,
                'grupo': grupo,
                'linea': linea,
                'familia': familia,
                'monedas': monedas,
                'usar': usar

            },

            type: 'POST',
            success: function (data) {
                if (data.length > 0) {
                    $("#tabla").html(data);
                }
                $("#cargando_modal").modal('hide');
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
                $("#cargando_modal").modal('hide');
            }
        })
    }
</script>
