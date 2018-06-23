<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Stock</a></li>
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
        <!-- <div class="col-md-1">
             <a class="btn btn-primary" onclick="agregar();">
                 <i class="fa fa-plus "> Nuevo</i>
             </a>
         </div>
         <div class="col-md-1">
             <a class="btn btn-default" onclick="duplicar();">
                 <i class="fa fa-angle-double-up "> Duplicar</i>
             </a>
         </div>-->
        <!--<div class="col-md-1">
            <a class="btn btn-default" onclick="confirmar();">
                <i class="fa fa-remove"> Eliminar</i>
            </a>
        </div>
        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="columnas();">
                <i class="fa fa-columns "> Columnas</i>
            </a>
        </div>-->
    </div>
    <br>

    <div class="row">
        <div class="col-md-2">
            <label class="control-label panel-admin-text">Ubicaci&oacute;n Inventario:</label>
        <?php if (count($locales) == 1): ?>
            <input type="hidden" id="locales" value="<?= $locales[0]['int_local_id'] ?>">
            <h4><?php echo $locales[0]['local_nombre'] ?></h4>
        <?php else: ?>
            <select class="form-control" id="locales">
                    <option value="TODOS">TODOS</option>
                <?php foreach ($locales as $local) { ?>
                    <option value="<?= $local['int_local_id'] ?>"
                        <?=$local_selected == $local['int_local_id'] ? 'selected' : ''?>><?= $local['local_nombre'] ?></option>
                <?php } ?>

            </select>
        <?php endif; ?>
        </div>
        <div class="col-md-2">
            <div style="padding-top: 30px;"></div>
            <div class="form-group">
                <div id="detalle_div" style="display: <?=$local_selected == false ? 'block' : 'none'?>;">
                    <?php if (count($locales) > 1): ?>
                    <input type="checkbox" name="mostrar_detalles" id="mostrar_detalles" <?=$detalle_checked==1 ? 'checked' : ''?>>
                    <label for="mostrar_detalles" style="cursor: pointer;">Mostrar Detalles</label>
                    <?php endif; ?>
                </div>
                <input type="checkbox" name="unidades_minimas" id="unidades_minimas" <?=$unidadMinima==1 ? 'checked' : ''?>>
                <label for="unidades_minimas" style="cursor: pointer;">Unidades m&iacute;nimas</label>
            </div>
        </div>
        <div class="col-md-5">
            <div style="padding-top: 30px;"></div>
            <button id="buscar_stock" class="btn btn-default btn-md">
                <i class="fa fa-search"></i>
            </button>
            <a class="btn btn-default" onclick="unidadesycostos();">
                <i class="fa fa-list-ol"> Unidades y costos</i>
            </a>
            <a class="btn btn-warning" onclick="ver_imagen();">
                <i class="fa fa-columns "> Ver Imagen</i>
            </a>
            <?php if (getProductoSerie() == "SI"): ?>
                <a class="btn btn-default" onclick="ver_serie();">
                    <i class="fa fa-barcode"> Ver Series</i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <br>
    <div class="table-responsive" id="productostable">
        <table class='table table-striped dataTable table-bordered table-responsive tableStyle' id="table" style="width: 100%;">
            <thead>
            <tr>
                <?php if (canShowCodigo()): ?>
                    <th><?php echo getCodigoNombre() ?></th>
                <?php endif; ?>
                <?php foreach ($columnas as $col): ?>
                    <?php
                    if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                        echo " <th>" . $col->nombre_mostrar . "</th>";
                    }

                    ?>
                <?php endforeach; ?>
                <th>UM</th>
                <th>Cantidad</th>
            <?php if($unidadMinima==0){ ?>
                <th>Fracci&oacute;n</th>
            <?php } ?>
                <th>Estado</th>
                <?php if($local_selected == false && $detalle_checked == 1):?>
                    <th>Ubicaci&oacute;n</th>
                <?php endif;?>
            </tr>
            </thead>
            <tbody id="tbody">

            <?php foreach ($lstProducto as $pd):

                ?>

                <tr id="<?= $pd['producto_id'] ?>">
                    <?php if (canShowCodigo()): ?>
                        <td><?php echo getCodigoValue(sumCod($pd['producto_id']), $pd['producto_codigo_interno']) ?></td>
                    <?php endif; ?>
                    <?php foreach ($columnas as $col): ?>
                        <?php if (array_key_exists($col->nombre_columna, $pd) and $col->mostrar == TRUE) {
                            if ($col->nombre_columna != 'producto_estado' && $col->nombre_columna != 'producto_codigo_interno' && $col->nombre_columna != 'producto_id') {
                                echo "<td>";
                                if ($col->nombre_columna == 'producto_vencimiento')
                                    echo $pd[$col->nombre_join] != null ? date('d-m-Y', strtotime($pd[$col->nombre_join])) : '';
                                else
                                    echo $pd[$col->nombre_join];
                                echo "</td>";
                            }
                        } ?>
                    <?php endforeach; ?>
                    <td>
                <?php if($unidadMinima == 0){ ?>
                    <?php echo $pd['nombre_unidad']; ?>
                <?php }else{ ?>
                    <?php echo !empty($pd['nombre_fraccion'])? $pd['nombre_fraccion'] : $pd['nombre_unidad']; ?>
                <?php } ?>
                    </td>
                    <td id="cantidad_prod_<?php echo $pd['producto_id'] ?>">
                <?php if($unidadMinima == 0){ ?>
                    <?php if($pd['producto_cualidad']=='MEDIBLE'){ ?>
                        <?php echo bcdiv($pd['cantidad'],1,0); ?>    
                    <?php }else{ ?>
                        <?php echo $pd['cantidad']; ?>
                    <?php } ?>
                <?php }else{ ?>
                    <?php if($pd['producto_cualidad']=='MEDIBLE'){ ?>
                        <?php echo bcdiv(($pd['unidades'] * $pd['cantidad']) + $pd['fraccion'],1,0); ?>    
                    <?php }else{ ?>
                        <?php echo ($pd['unidades'] * $pd['cantidad']) + $pd['fraccion']; ?>
                    <?php } ?>
                <?php } ?>
                    </td>
                    <?php if($unidadMinima == 0){ ?>
                    <td>
                        <?php if ($pd['fraccion'] != null) {
                            echo $pd['fraccion'];
                            if ($pd['nombre_fraccion'] != "") {
                                echo " " . $pd['nombre_fraccion'];
                            }
                        } ?>
                    </td>
                    <?php } ?>
                    <td>
                        <?php if ($pd['producto_estado'] == 0) {
                            echo "INACTIVO";
                        } else {
                            echo "ACTIVO";
                        } ?>

                    </td>
                    <?php if($local_selected == false && $detalle_checked == 1):?>
                        <td><?=$pd['local_nombre']?></td>
                    <?php endif;?>

                </tr>

            <?php endforeach; ?>


            </tbody>
        </table>

    </div>
    <a href="#" id="pdf" target="_blank"
       class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <a href="#" id="excel"
       class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

</div>


<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="columnas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>

<div class="modal fade" id="imagen_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


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
                    <p>Est&aacute; seguro que desea eliminar el producto seleccionado</p>
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


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">
    function selectProductError() {
        $.bootstrapGrowl('<h4>Debe seleccionar un producto</h4>', {
            type: 'warning',
            delay: 2500,
            allow_dismiss: true
        });
    }





    function unidadesycostos() {
        $("#cargando_modal").modal('show');
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $("#productomodal").load('<?= $ruta ?>producto/verunidades/' + id, [], function () {
                $("#cargando_modal").modal('hide');
                $('#productomodal').modal('show');
            });
        }
        else {
            $("#cargando_modal").modal('hide');
            selectProductError()
        };

    }

    function ver_imagen() {
        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {

            $("#imagen_model").load('<?= $ruta ?>producto/ver_imagen/' + id, [], function () {
                $("#cargando_modal").modal('hide');
                $('#imagen_model').modal('show');
            });
        }
        else {
            $("#cargando_modal").modal('hide');
            selectProductError();
        }
    }

    function ver_serie() {
        $("#serie").html('');
        var id = $("#tbody tr.ui-selected").attr('id');
        if (id != undefined) {
            $("#serie").load('<?= $ruta ?>producto/ver_serie/' + id + '/' + $("#locales").val() + '/' + '1');
            $('#serie').modal({show: true, keyboard: false, backdrop: 'static'});
        }
        else selectProductError();
    }


    function getproductosbylocal() {
        $("#cargando_modal").modal({
            show: true,
            backdrop: 'static'
        });
        $.ajax({
            url: '<?= $ruta?>producto/getbylocal',
            data: {'local': $("#locales").val(), 'stock': '1'},
            type: 'post',
            success: function (data) {

                $("#productostable").html(data);
                $("#cargando_modal").modal('hide');

            }
        })
    }

</script>

<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>

<script>$(function () {
        TablesDatatables.init();


        $("#locales, #mostrar_detalles, #unidades_minimas").on('change', function(){
            $("#productostable").hide();
            $("#pdf").hide();
            $("#excel").hide();

            if($("#locales").val() != "TODOS"){
                $("#detalle_div").hide();
            }
            else{
                $("#detalle_div").show();
            }
        });

        $("#pdf").click(function (e) {
            var url = '<?=base_url('producto/pdf_stock')?>';
            if($("#locales").val() != "TODOS"){
                url += '/' + $('#locales').val() + '/0';
            }else{
                if($("#mostrar_detalles").prop('checked')){
                    url += '/0/1';
                }else{
                    url += '/0/0';    
                }
            }
            if($("#unidades_minimas").prop('checked')){
                url += '/1';
            }else{
                url += '/0';
            }
            $("#pdf").attr('href', url);
            return true;
        });

        $("#excel").click(function (e) {
            var url = '<?=base_url('producto/excel_stock')?>';
            if($("#locales").val() != "TODOS"){
                url += '/' + $('#locales').val() + '/0';
            }else{
                if($("#mostrar_detalles").prop('checked')){
                    url += '/0/1';
                }else{
                    url += '/0/0';    
                }
            }
            if($("#unidades_minimas").prop('checked')){
                url += '/1';
            }else{
                url += '/0';
            }

            $("#excel").attr('href', url);
            return true;
        });

        $("#buscar_stock").on('click', function(){
            var url = '<?=base_url('producto/stock')?>';

            $("#cargando_modal").modal({
                show: true,
                backdrop: 'static'
            });
            if($("#locales").val() != "TODOS"){
                url += '/' + $('#locales').val() + '/0';
            }else{
                if($("#mostrar_detalles").prop('checked')){
                    url += '/0/1';
                }else{
                    url += '/0/0';    
                }
            }
            if($("#unidades_minimas").prop('checked')){
                url += '/1';
            }else{
                url += '/0';
            }
            $.ajax({
                url: url,
                success: function (data) {

                    $('#page-content').html(data);
                    $("#cargando_modal").modal('hide');
                }
            });
        });



        $('#imagen_model').on('show.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_01'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove()

        });

        jQuery('#imagen_model').on('hidden.bs.modal', function (e) {

            jQuery.removeData(jQuery('#img_01'), 'elevateZoom');//remove zoom instance from image
            jQuery('.zoomContainer').remove();// remove zoom container from DOM
        });

        /*Daniel Contreras | Para evitar tamaños diferentes entre thead y tbody al iniciar la pagina */
        var t = setTimeout(function () {
            $(window).resize();
        }, 400);

        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected").attr('id');


            }
        });


    });</script>
