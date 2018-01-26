<?php $ruta = base_url(); ?>
<style>
    .tableStyle th {
        font-size: 11px !important;
        padding: 2px 2px;
        text-align: center;
        vertical-align: middle;
    }

    .tableStyle td {
        font-size: 10px !important;
    }
</style>

<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Movimiento de Inventario</a></li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i>
            </button>
            <h4><i class="icon fa fa-ban"></i> Error</h4>
            <?php echo isset($error) ? $error : '' ?></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i>
            </button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <?php echo isset($success) ? $success : '' ?>
        </div>
    </div>
</div>

<div class="block">


    <br>
    <div class="row">
        <div class="form-group">
            <div class="col-md-4">
                <label class="panel-admin-text">Ubicaci&oacute;n Inventario</label>
            </div>
            <div class="col-md-4">
                <select class="form-control" id="locales">
                    <option value="TODOS">Todos</option>
                    <?php foreach ($locales as $local): ?>
                        <?php if ($local['int_local_id'] == $local_selected): ?>
                            <option selected
                                    value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                        <?php else: ?>
                            <option value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </select>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning alert-dismissable" style="padding: 2px; padding-right: 30px; padding-top: 7px; margin-bottom: 0;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i>
                    </button>
                    Los Productos que no aparescan aqui es porque aun no ha registrado un movimiento.
                </div>
            </div>
        </div>
    </div>
    <br>

    <div class="table-responsive">

        <table class='table table-striped dataTable tableStyle table-bordered' id="table">
            <thead>
            <tr>
                <th><?php echo getCodigoNombre() ?></th>
                <th>Nombre</th>
                <th>UM</th>
                <th>Cantidad</th>
                <th>Fracci&oacute;n</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($lstProducto as $pd): //var_dump($pd); ?>


                <tr>
                    <td>
                        <?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) ?>
                    </td>
                    <td>
                        <?php echo $pd['producto_nombre'] . " " . $pd['producto_descripcion'] ?>

                    </td>
                    <td id="um_<?php echo $pd['producto_id']?>" data-um="<?php echo $pd['nombre_unidad']=='' ? '0' : '1'?>">
                        <?php echo $pd['nombre_unidad']; ?>

                    </td>
                    <td>
                        <?php echo $pd['cantidad']; ?>

                    </td>
                    <td>
                        <?php echo $pd['fraccion']; ?>

                    </td>
                    <td class='actions_big'>
                        <div class="btn-group">

                            <a class='btn btn-default btn-default tip' data-toggle="tooltip"
                               title="Ver" data-original-title="Ver" title="Ver"
                               onclick="ver(<?php echo $pd['producto_id'] ?>)">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>


            </tbody>
        </table>
    </div>


</div>


<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">


    function ver(id) {
        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });
        if($("#um_" + id).attr('data-um')=='1') {
            var local = $("#locales").val();

            $("#ver").load('<?= $ruta ?>inventario/formMovimiento/' + id + '/' + local);



            setTimeout(function () {
                $("#cargando_modal").modal('hide');

            }, 1000);

            setTimeout(function () {
                $('#ver').modal('show');

            }, 1500);


        }
        else{
            $.bootstrapGrowl('<h4>Este producto no tiene una Unidad definida.</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            $("#cargando_modal").modal('hide');
        }



    }


    $("#locales").change(function (e) {
        e.preventDefault();
        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });
        $.ajax({
            type: "GET",
            url: '<?php echo site_url('inventario/movimiento')?>' + '/' + $("#locales").val(),
            //data: 'page=' + url,	//with the page number as a parameter
            dataType: "html",	//expect html to be returned
            success: function (msg) {

                if (parseInt(msg) != 0)	//if no errors
                {

                    $('#page-content').html(msg);	//load the returned html into pageContet
                    // $('#loading').css('visibility', 'hidden');	//and hide the rotating gif
                    //inizializePlugins();
                    //$('#barloadermodal').modal('hide');
                }
                setTimeout(function () {
                    $("#cargando_modal").modal('hide');

                }, 1);
            }

        });
    });

</script>

<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function () {
        TablesDatatables.init();
    });</script>
