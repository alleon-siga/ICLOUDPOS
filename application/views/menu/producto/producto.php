<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Productos</a></li>
</ul>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<!--
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error"
             style="display:<?php //echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Error</h4>
            <span id="errorspan"><?php //echo isset($error) ? $error : '' ?></div>
    </div>
</div>-->

<div class="block">

    <div class="row">

        <div class="btn-group" role="group" aria-label="..." align="center">


            <a class="btn btn-primary" onclick="agregar();">
                <i class="fa fa-plus"></i> Nuevo
            </a>


            <a class="btn btn-default" onclick="duplicar();">
                <i class="fa fa-angle-double-up"></i> Duplicar
            </a>

            <a class="btn btn-default" onclick="editarProducto();">
                <i class="fa fa-edit"></i> Editar
            </a>

            <a class="btn btn-default" onclick="confirmar();">
                <i class="fa fa-remove"></i> Eliminar
            </a>

            <a class="btn btn-default" onclick="columnas();">
                <i class="fa fa-columns"></i> Columnas
            </a>

            <a class="btn btn-default" onclick="ver_imagen();">
                <i class="fa fa-columns"></i> Imagen
            </a>
            <?php if (getProductoSerie() == "SI"): ?>
                <a class="btn btn-default" onclick="ver_serie();">
                    <i class="fa fa-barcode"></i> Series
                </a>
            <?php endif; ?>
        </div>

    </div>
    <br>

    <div class="table-responsive" id="productostable">
        <table class='table table-striped dataTable table-bordered table-responsive tableStyle' id="table">
            <thead>
            <tr>
                <?php if (canShowCodigo()): ?>
                    <th width="2%"><?php echo getCodigoNombre() ?></th>
                <?php endif; ?>
                <?php foreach ($columnas as $col): ?>
                    <?php
                    if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                        echo " <th width='10%'>" . $col->nombre_mostrar . "</th>";
                    }

                    ?>
                <?php endforeach; ?>
                <th width="2%">Estado</th>


            </tr>
            </thead>
            <tbody id="tbody">

            <?php foreach ($lstProducto as $pd):

                ?>

                <tr id="<?= $pd['producto_id'] ?>">
                    <?php if (canShowCodigo()): ?>
                        <td style="white-space: normal;"><?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) ?></td>
                    <?php endif; ?>
                    <?php foreach ($columnas as $col): ?>
                        <?php if (array_key_exists($col->nombre_columna, $pd) and $col->mostrar == TRUE) {
                            if ($col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                                echo "<td style='white-space: normal;'>";
                                if ($col->nombre_columna == 'producto_vencimiento')
                                    echo $pd[$col->nombre_join] != null ? date('d-m-Y', strtotime($pd[$col->nombre_join])) : '';
                                else
                                    echo $pd[$col->nombre_join];
                                echo "</td>";
                            }

                        } ?>
                    <?php endforeach; ?>

                    <td style="white-space: normal;">
                        <?php if ($pd['producto_estado'] == 0) {
                            echo "INACTIVO";
                        } else {
                            echo "ACTIVO";
                        } ?>

                    </td>


                </tr>

            <?php endforeach; ?>


            </tbody>
        </table>

    </div>


</div>


<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="imagen_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="columnas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>

<div class="modal fade" id="serie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>


<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>producto/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Producto</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el producto seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">

                </div>
                <div class="modal-footer">
                    <button type="button" id="botoneliminar" class="btn btn-primary" onclick="eliminar()">Confirmar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<div id="load_div" style="display: none;">
    <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>


<!--    Aqui iran los modales de los campos que se van a asignar dinamicamente, marca, linea etc  -->

<div class="modal fade" id="agregarmarca" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<div class="modal fade" id="agregarfamilia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="agregarlinea" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="agregarproveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    /*estos metodos llamados agregar, lo que hacen es cargar los modales de las vistas, y a su vez le asignan al boton de confirmar
     * de cada vista su funcion onclick, junto con un parametro para indicar que esta en el modulo de PRODUCTOS*/
    function agregarproveedor() {
        $("#formagregarproveedor").trigger("reset");
        $('#agregarproveedor').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_proveedor').removeAttr("onclick");
            $('#confirmar_boton_proveedor').attr("onclick", "guardar_proveedor('producto')");
        }, 10);
    }

    function agregarlinea() {
        $("#formagregarlinea").trigger("reset");
        $('#agregarlinea').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_linea').removeAttr("onclick");
            $('#confirmar_boton_linea').attr("onclick", "guardar_linea('producto')");
        }, 10);
    }

    function agregarfamilia() {
        $("#formagregarfamilia").trigger("reset");
        $('#agregarfamilia').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_familia').removeAttr("onclick");
            $('#confirmar_boton_familia').attr("onclick", "guardar_familia('producto')");

        }, 10);
    }
    function agregarmarca() {
        $("#formagregarmarca").trigger("reset");
        $('#agregarmarca').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_marca').removeAttr("onclick");
            $('#confirmar_boton_marca').attr("onclick", "guardar_marca('producto')");

        }, 10);
    }

    function agregargrupo() {
        $("#formagregargrupo").trigger("reset");
        $('#agregargrupo').modal('show');
        setTimeout(function () {
            $('#confirmar_boton_grupo').removeAttr("onclick");
            $('#confirmar_boton_grupo').attr("onclick", "guardar_grupo('producto')");

        }, 10);
    }


    function agregar() {
        $('#productomodal').html($("#load_div").html());
        $("#productomodal").load('<?= $ruta ?>producto/agregar');
        $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
    }


    function editarProducto() {
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $('#productomodal').html($("#load_div").html());
            $("#productomodal").load('<?= $ruta ?>producto/agregar/' + id);
            $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
        }
        else selectProductError();
    }

    function ver_imagen() {

        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $('#imagen_model').html($("#load_div").html());
            $("#imagen_model").load('<?= $ruta ?>producto/ver_imagen/' + id);
            $('#imagen_model').modal({show: true, keyboard: false, backdrop: 'static'});

        }
        else selectProductError();
    }

    function duplicar() {
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $('#productomodal').html($("#load_div").html());
            $("#productomodal").load('<?= $ruta ?>producto/agregar/' + id, {'duplicar': 1});
            $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
        }
        else selectProductError();
    }

    function columnas() {
        $('#columnas').html($("#load_div").html());
        $("#columnas").load('<?= $ruta ?>producto/editcolumnas');
        $('#columnas').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    function ver_serie() {
        $('#serie').html($("#load_div").html());
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $("#serie").load('<?= $ruta ?>producto/ver_serie/' + id + '/' + $("#locales").val());
            $('#serie').modal({show: true, keyboard: false, backdrop: 'static'});
        }
        else selectProductError();
    }

    function confirmar() {
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $("#id_borrar").val(id)
            $('#borrar').modal('show');
        }
        else selectProductError();

    }

    function eliminar() {
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined){
            $("#cargando_modal").modal('show');
            $.ajax({
                url: '<?= $ruta?>producto/eliminar',
                type: "post",
                dataType: "json",
                data: $('#formeliminar').serialize(),
                success: function (data) {
                    if (data.error == undefined) {
                        $('#borrar').modal('hide');
                        $.ajax({
                            url: '<?= $ruta?>producto',
                            success: function (data) {
                                $('#page-content').html(data);

                            }
                        });

                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                    } else {

                        $("#cargando_modal").modal('hide');
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);
                    }

                },
                error: function () {
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Ha ocurrido un error al realizar la operacion</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                }

            });
        }


    }

    function poner_hide(modal) {


        $("#" + modal).modal('hide');
        $('#cargando_modal').modal('hide');

    }

    function getproductosbylocal() {

        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });
        $.ajax({
            url: '<?= $ruta?>producto/getbylocal',
            data: {'local': $("#locales").val()},
            type: 'post',
            success: function (data) {

                $("#productostable").html(data);
                $("#cargando_modal").modal('hide');
            }
        })

        // return retorno;
    }

    function selectProductError() {
        $.bootstrapGrowl('<h4>Debe seleccionar un producto</h4>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });
    }

</script>

<!-- Load and execute javascript code used only in this page -->


<script>
    $(function () {



        // este codigo es para que al abrir un modal encima de otro modal no se pierda el scroll
        $('.modal').on("hidden.bs.modal", function (e) {
            if($('.modal:visible').length)
            {
                $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
                $('body').addClass('modal-open');
            }
        }).on("show.bs.modal", function (e) {
            if($('.modal:visible').length)
            {
                $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
                $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
            }
        });

        //CONFIGURACIONES INICIALES
        //App.sidebar('close-sidebar');

        //setTimeout(function () { $(window).resize(); }, 400);

        /*estas hacen el load de las vistas de los campos dinamicos*/
        $("#agregarmarca").load('<?= $ruta ?>marca/form');
        $("#agregargrupo").load('<?= $ruta ?>grupo/form');
        $("#agregarfamilia").load('<?= $ruta ?>familia/form');
        $("#agregarlinea").load('<?= $ruta ?>linea/form');
        $("#agregarproveedor").load('<?= $ruta ?>proveedor/form');


        $('#imagen_model').on('show.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_01'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove()

        });

        jQuery('#imagen_model').on('hidden.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_01'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove();// remove zoom container from DOM
        });

        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected").attr('id');
            }
        });


        TablesDatatables.init();

        /*
         NO DESOMENTAR ESTO
         $('body').keydown(function (e) {
         console.log(e.keyCode );

         if (e.keyCode == 27) {
         e.preventDefault();


         if($("#productomodal").is(':visible'))
         {
         $("#confirmarcerrar").modal('show');
         }

         }
         });*/


    });

    function confirmarcerrar() {
        $("#confirmarcerrar").modal('hide');
        $("#productomodal").modal('hide');
    }
    function cancelarcerrar() {
        $("#confirmarcerrar").modal('hide');
    }

</script>
