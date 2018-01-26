<?php $ruta = base_url(); ?>

<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post">
    <input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 80%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="row">

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h4>Movimiento de Inventario</h4>
                        </div>
                        <div class="col-md-7">
                            <h4><?= $producto['producto_nombre'] ?></h4>
                        </div>
                    </div>


                </div>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            Seleccione el tipo de movimiento
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="buscar_select">
                                <option value="TODOS"> TODOS</option>
                                <option value="ENTRADA"> ENTRADA</option>
                                <option value="SALIDA"> SALIDA</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if ($local != "TODOS"): ?>
                    <?php $local_id = $local['int_local_id']; ?>
                    <?php echo $local['local_nombre']; ?>
                <?php else: ?>
                    <?php $local_id = "TODOS"; ?>
                    Todas las Ubicaciones
                <?php endif; ?>

                <br>

                <div class="table-responsive">
                    <table class="table table-striped tableStyle table-bordered" id="tablaresult">
                        <thead>
                        <tr>

                            <th>Fecha y Hora</th>
                            <th>Movimiento</th>
                            <th>Tipo</th>
                            <th>N&uacute;mero</th>
                            <th>Referencia</th>
                            <th>Encargado</th>
                            <th>UM</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <?php  if ($local == "TODOS"): ?>
                                <th>Local</th>
                            <?php endif; ?>
                            <th>Importe</th>
                        </tr>
                        </thead>
                        <tbody id="columnas">

                        <?php

                        foreach ($movimientos as $arreglo): ?>
                            <tr>
                                <td style="text-align: center"><span
                                        style="display: none"><?= date('YmdHi', strtotime($arreglo->date)) ?></span><?= date('d-m-Y H:i', strtotime($arreglo->date)) ?></td>
                                <td style="text-align: center"><?= $arreglo->operacion ?></td>
                                <td style="text-align: center"><?= $arreglo->tipo ?></td>
                                <td style="text-align: center"><?= $arreglo->numero ?></td>
                                <td style="text-align: center"><?php
                                    if( $arreglo->tipo=="TRASPASO"){

                                    if($arreglo->operacion=="SALIDA"){

                                        echo "Local de origen: ".$arreglo->localuno. ", local de destino: ".$arreglo->localreferencia;
                                    }else{

                                        echo "Local de origen: ".$arreglo->localreferencia. ", local de destino: ".$arreglo->localuno;
                                    }

                                    }else{
                                        echo $arreglo->referencia;
                                    }
                                     ?></td>
                                <td style="text-align: center"><?= $arreglo->encargado ?></td>
                                <td style="text-align: center"><?= $arreglo->um ?></td>
                                <?php if ($arreglo->operacion == "ENTRADA"): ?>
                                    <td style="text-align: center"><?= $arreglo->cantidad ?></td>
                                    <td style="text-align: center"></td>
                                <?php elseif ($arreglo->operacion == "SALIDA"): ?>
                                    <td></td>
                                    <td style="text-align: center"><?= $arreglo->cantidad ?></td>
                                <?php endif; ?>
                                <?php if ($local == "TODOS"): ?>
                                    <td style="text-align: center"><?= $arreglo->local_nombre ?></td>
                                <?php endif; ?>
                                <td style="text-align: right"><?= $arreglo->importe!="" ? $arreglo->simbolo." ". number_format($arreglo->importe,2) : $arreglo->importe ?></td>
                            </tr>
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" id="generar_pdf_url"
                               value="<?= $ruta ?>inventario/pdfMovimiento/<?= $producto['producto_id'] ?>/<?= $local_id ?>">
                        <a target="_blank"
                           href="<?= $ruta ?>inventario/pdfMovimiento/<?= $producto['producto_id'] ?>/<?= $local_id ?>"
                           id="generar_pdf"
                           class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
                           data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

                        <input type="hidden" id="generar_excel_url"
                               value="<?= $ruta ?>inventario/excelMovimiento/<?= $producto['producto_id'] ?>/<?= $local_id ?>">

                        <a id="generar_excel"
                           href="<?= $ruta ?>inventario/excelMovimiento/<?= $producto['producto_id'] ?>/<?= $local_id ?>"
                           class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
                           data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>


                    </div>

                </div>
            </div>


            <!-- /.modal-content -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Salir</button>

            </div>
        </div>


</form>


<script type="text/javascript">

    $(function () {



        // Apply the search


        $("#fecha").datepicker({format: 'dd-mm-yyyy'});


        $("#select").chosen({
            placeholder: "Seleccione el producto",
            allowClear: true
        });
        $("#locales_in").chosen({
            placeholder: "Seleccione el producto",
            allowClear: true
        });

        $('#buscar_select').on("change", function () {


            table.columns(1)
                .search('')
                .draw();
            table.columns(1)
                .search('')
                .draw();

            if ($('#buscar_select').val() == 'ENTRADA') {
                table.columns(1)
                    .search($('#buscar_select').val(), true)
                    .draw();
            }
            if ($('#buscar_select').val() == 'SALIDA') {

                table.columns(1)
                    .search($('#buscar_select').val(), true)
                    .draw();
            }

            $("#generar_pdf").attr('href', $("#generar_pdf_url").val() + "/" + $(this).val());
            $("#generar_excel").attr('href', $("#generar_excel_url").val() + "/" + $(this).val());


        });


        $('#select').on("change", function () {
            if ($(this).val() != "seleccione") {


                $("#maxima").remove();
                $("#minima").remove();
                $.ajax({
                    url: '<?= base_url()?>inventario/get_unidades_has_producto',
                    type: 'POST',
                    headers: {
                        Accept: 'application/json'
                    },
                    data: {'id_producto': $(this).val()},
                    success: function (data) {

                        $("#fraccion").attr('max', data.unidades[0].unidades);
                        $("#existencia").css("display", "block");
                        $("#cantidad").val("");
                        $("#fraccion").val("");
//data.unidades[data.unidades.length -1].unidades
                        $("#unidad_maxima").append("<div id='maxima'><div class='col-md-5'> Unidad Maxima " + data.unidades[0].nombre_unidad + "</div></div> ");
                        $("#unidad_minima").append("<div id='minima'><div class='col-md-5'> Unidad Minima " + data.unidades[data.unidades.length - 1].nombre_unidad + "</div></div> ");

                        $("#maximahidden").val(data.unidades[0].nombre_unidad);


                    }
                })

            }
        });


    });

    function remover(id) {

        $("#" + id).remove();

    }


    function add_productos() {
        $("#tablaresult").css("display", "block");
        ///var table = $('#tablaresult').DataTable();


        var maxima = $("#maximahidden");
        var fraccion = $("#fraccion");
        var cantidad = $("#cantidad");

        var id = $("#select").val();
        var nombre = $("#select option:selected").html();
        if (id != "seleccione" && $("#cantidad").val() != "") {

            $("#columnas").append('<tr id="' + id + '"><td class="center" width="10%">' + id + '<input type="hidden" name="id_producto[]" value="' + id + '"> </td>' +
                '<td class="center" width="40%">' + nombre + '<input type="hidden" name="nombre_producto[]" value="' + nombre + '"></td>' +
                '<td width="20%" id="unidad_medida_td"' + id + '">' + maxima.val() + '</td><td width="10%">' + cantidad.val() + '<input type="hidden" name="cantidad_producto[]" value="' + cantidad.val() + '"></td>' +
                '<td width="10%">' + fraccion.val() + '<input type="hidden" name="fraccion_producto[]" value="' + fraccion.val() + '"></td>' +
                '<td> <div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Remover" data-original-title="Remover" onclick="remover(' + id + ')"> <i class="fa fa-trash-o"></i> </a></div></td>' +
                '</tr>');
            cantidad.val('');
            fraccion.val('');

        }

    }
</script>
