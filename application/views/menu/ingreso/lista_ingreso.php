<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered tableStyle" id="tablaresultado">
        <thead>
        <tr>
            <th width="2%">ID</th>
            <th width="2%">Doc.</th>
            <th width="5%">Documento</th>
            <th width="4%">Fecha Registro</th>
            <th width="10%">Proveedor</th>
            <th width="5%">Responsable</th>
            <th width="10%">Local</th>
            <th width="2%">Tipo Pago</th>
            <th width="5%">Total</th>
            <?php if (valueOption('ACTIVAR_SHADOW') == 1): ?>
                <th> Facturaci&oacute;n</th>
            <?php endif; ?>
            <th width="5%">Estado</th>
            <th width="7%">Ver</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($ingresos) > 0) {
            //var_dump($ingresos);
            foreach ($ingresos as $ingreso) {
                ?>
                <tr>
                    <td align="center" style="white-space: normal;"><?php echo $ingreso->id_ingreso; ?></td>
                    <td align="center" style="white-space: normal;">
                        <?php
                        if ($ingreso->tipo_documento == "FACTURA") echo "FA";
                        if ($ingreso->tipo_documento == 2) echo "NC";
                        if ($ingreso->tipo_documento == "BOLETA DE VENTA") echo "BO";
                        if ($ingreso->tipo_documento == 4) echo "GR";
                        if ($ingreso->tipo_documento == 5) echo "PCV";
                        if ($ingreso->tipo_documento == "NOTA DE VENTA") echo "NV";
                        ?>
                    </td>
                    <td align="center" style="white-space: normal;"><?php echo $ingreso->documento_serie . "-" . $ingreso->documento_numero ?></td>
                    <td align="center" style="white-space: normal;"><span
                            style="display: none;"><?= date('YmdHis', strtotime($ingreso->fecha_registro)) ?></span><?= date('d-m-Y H:i:s', strtotime($ingreso->fecha_registro)) ?>
                    </td>
                    <td align="center" style="white-space: normal;"><?= $ingreso->proveedor_nombre ?></td>
                    <td align="center" style="white-space: normal;"><?= $ingreso->username ?></td>
                    <td align="center" style="white-space: normal;"><?= $ingreso->local_nombre ?></td>
                    <td align="center" style="white-space: normal;"><?= $ingreso->pago ?></td>
                    <td align="center" style="white-space: normal;"><?= $ingreso->simbolo . " " . $ingreso->total_ingreso ?></td>

                    <?php
                    $pertenece = "'INGRESONORMAL'";
                    /*color del estatus, la coloco por defecto en danger, que es cuando esta en pendiente*/
                    $etiqueta = 'label-danger';
                    $status = "PENDIENTE";
                    $facturado = "PENDIENTE";

                    $etiqueta_facturar = 'label-danger';

                    if (($ingreso->ingreso_status == "COMPLETADO") and (valueOption('ACTIVAR_SHADOW') == 1)) {
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if (($ingreso->ingreso_status == "COMPLETADO") and (valueOption('ACTIVAR_SHADOW') == 0)) {
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if ($ingreso->ingreso_status == "FACTURADO" and (valueOption('ACTIVAR_SHADOW') == 1)) {
                        $etiqueta_facturar = 'label-success';
                        $pertenece = "'INGRESOCONTABLE'";
                        $facturado = "FACTURADO";
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if ($ingreso->ingreso_status == "CERRADO") {
                        $etiqueta_facturar = 'label-success';
                        $facturado = "NO APLICA";
                        $status = "CERRADO";
                        $etiqueta = 'label-success';
                    }


                    if (isset($anular)) {

                        if ($ingreso->facturado == 1) {
                            $facturado = "FACTURADO";
                        }
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    } ?>

                    <?php if (valueOption('ACTIVAR_SHADOW') == 1): ?>
                        <td align='center'>
                            <label class="label <?= $etiqueta_facturar ?>"> <?= $facturado ?> </label>
                        </td>
                    <?php endif; ?>
                    <td align="center" style="white-space: normal;"><label class="label <?= $etiqueta ?>"> <?= $status ?></label></td>


                    <td class="actions" style="white-space: nowrap;">
                        <div class="btn-group">
                            <a class="btn btn-default" data-toggle="tooltip"
                               title="Ver" data-original-title="Ver"
                               href="#"
                               onclick="ver('<?= $ingreso->id_ingreso ?>','<?= $ingreso->local_id ?>','INGRESO');">
                                <i class="fa fa-search"></i>
                            </a>

                            <?php if (isset($anular)): ?>
                                <a class="btn" style="background-color:#e74c3c; color: #ffffff" data-toggle="tooltip"
                                   title="Anular" data-original-title="Anular"
                                   href="#"
                                   onclick="mostrar('<?= $ingreso->id_ingreso ?>','<?= $ingreso->local_id ?>');">
                                    <i class="fa fa-remove"></i>
                                </a>
                            <?php endif; ?>


                            <?php if ($ingreso->ingreso_status == "PENDIENTE"): ?>
                                <a class="btn btn-primary" data-toggle="tooltip"
                                   style=""
                                   title="Valorizar Ingreso" data-original-title="Valorizar Ingreso"
                                   href="#" onclick="editaringreso('<?= $ingreso->id_ingreso ?>');">
                                    <i class="fa fa-money"></i>
                                </a>
                            <?php endif ?>


                            <!-- se va a mostrar la opcion de facturar, solo cuando este habilitada la opcion de facturar
                            ingreso,
                            cuando el ingreso este en estatus completado, y en facturacion pendiente-->
                            <?php if ((valueOption('ACTIVAR_SHADOW') == 1) && ($ingreso->ingreso_status == "COMPLETADO")): ?>
                                <a class="btn" data-toggle="tooltip"
                                   style="background-color: #1493D1; border-color:#15b3ab; color: #ffffff"
                                   title="Facturar Ingreso" data-original-title="Facturar Ingreso"
                                   href="#" onclick="editaringreso('<?= $ingreso->id_ingreso ?>', 'facturar');">
                                    <i class="fa fa-building-o fa-fw"></i>
                                </a>

                            <?php endif; ?>

                            <?php if (valueOption('ACTIVAR_SHADOW') == 1): ?>
                                <a style="display:none; background-color:#f0ad4e; color: #ffffff"
                                   class="btn cerrar_ingreso"
                                   data-toggle="tooltip"
                                   title="Cerrar Ingreso" data-original-title="Cerrar Ingreso"
                                   href="#" onclick="cerrar_ingreso('<?= $ingreso->id_ingreso ?>');">
                                    <i class="fa fa-unlock"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php }
        }

        /*$ingresos_cerrados_normales son los ingresos con estatus cerrado en la tabla ingresos*/
        if (isset($ingresos_cerrados_normales)) {


            foreach ($ingresos_cerrados_normales as $ingreso) {
                ?>
                <tr>
                    <td align="center"><?php echo $ingreso->id_ingreso; ?></td>
                    <td align="center">
                        <?php
                        if ($ingreso->tipo_documento == "FACTURA") echo "FA";
                        if ($ingreso->tipo_documento == 2) echo "NC";
                        if ($ingreso->tipo_documento == "BOLETA DE VENTA") echo "BO";
                        if ($ingreso->tipo_documento == 4) echo "GR";
                        if ($ingreso->tipo_documento == 5) echo "PCV";
                        if ($ingreso->tipo_documento == "NOTA DE VENTA") echo "NV";
                        ?>
                    </td>
                    <td align="center"><?php echo $ingreso->documento_serie . "-" . $ingreso->documento_numero ?></td>
                    <td align="center"><?= date('d-m-Y H:i:s', strtotime($ingreso->fecha_registro)) ?></td>
                    <td align="center"><?php
                        if ($ingreso->fecha_emision == null) {
                            echo "--";
                        } else {
                            echo date('d-m-Y', strtotime($ingreso->fecha_emision));
                        } ?></td>
                    <td align="center"><?= $ingreso->proveedor_nombre ?></td>
                    <td align="center"><?= $ingreso->username ?></td>
                    <td align="center"><?= $ingreso->local_nombre ?></td>
                    <td align="center"><?= $ingreso->pago ?></td>
                    <td align="center"><?= $ingreso->simbolo . " " . $ingreso->total_ingreso ?></td>

                    <?php

                    /*color del estatus, la coloco por defecto en danger, que es cuando esta en pendiente*/
                    $etiqueta = 'label-danger';
                    $status = PENDIENTE;
                    $facturado = "PENDIENTE";

                    $etiqueta_facturar = 'label-danger';

                    if (($ingreso->ingreso_status == "COMPLETADO") and (valueOption('ACTIVAR_SHADOW') == 1)) {
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if (($ingreso->ingreso_status == "COMPLETADO") and (valueOption('ACTIVAR_SHADOW') == 0)) {
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if ($ingreso->ingreso_status == "FACTURADO" and (valueOption('ACTIVAR_SHADOW') == 1)) {

                        $facturado = "FACTURADO";
                        $status = "COMPLETADO";
                        $etiqueta = 'label-success';
                    }

                    if ($ingreso->ingreso_status == "CERRADO") {
                        $etiqueta_facturar = 'label-success';
                        $facturado = "NO APLICA";
                        $status = "CERRADO";
                        $etiqueta = 'label-success';
                    }


                    if (isset($anular)) {

                        if ($ingreso->facturado == 1) {
                            $facturado = "FACTURADO";
                        }
                        $status = "ANULADO";
                        $etiqueta = 'label-danger';
                    }

                    if ((valueOption('ACTIVAR_SHADOW') == 1)): ?>
                        <td align='center'>
                            <label class="label <?= $etiqueta_facturar ?>"> <?= $facturado ?> </label>
                        </td>
                    <?php endif; ?>
                    <td align="center"><label class="label <?= $etiqueta ?>"> <?= $status ?></label></td>


                    <td class="actions" style="white-space: nowrap;">
                        <div class="btn-group">
                            <?php
                            $pertenece = "'INGRESONORMAL'";
                            echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Ver" data-original-title="Ver"
                                            href="#" onclick="ver(' . $ingreso->id_ingreso . ',' . $ingreso->local_id . ',' . $pertenece . ');">'; ?>
                            <i class="fa fa-search"></i>
                            </a>

                            <?php
                            if (isset($anular)) {
                                echo '<a class="btn" style="background-color:#e74c3c; color: #ffffff" data-toggle="tooltip"
                                            title="Anular" data-original-title="Anular"
                                            href="#" onclick="mostrar(' . $ingreso->id_ingreso . ',' . $ingreso->local_id . ');">'; ?>
                                <i class="fa fa-remove"></i>
                                </a>
                            <?php } ?>


                            <?php
                            if ($ingreso->ingreso_status == "PENDIENTE") {
                                echo '<a class="btn btn-primary" data-toggle="tooltip"
                                style=""
                                            title="Valorizar Ingreso" data-original-title="Valorizar Ingreso"
                                            href="#" onclick="editaringreso(' . $ingreso->id_ingreso . ');">'; ?>
                                <i class="fa fa-money"></i>
                                </a>
                            <?php } ?>


                            <?php

                            /*se va a mostrar la opcion de facturar, solo cuando este habilitada la opcion de facturar ingreso,
                            cuando el ingreso este en estatus completado, y en facturacion pendiente*/
                            if ((valueOption('ACTIVAR_SHADOW') == 1)
                                and ($ingreso->ingreso_status == "COMPLETADO")
                            ) {
                                $facturar = "'facturar'";
                                echo '<a class="btn" data-toggle="tooltip" style="background-color: #1493D1; border-color:#15b3ab; color: #ffffff"
                                            title="Facturar Ingreso" data-original-title="Facturar Ingreso"
                                            href="#" onclick="editaringreso(' . $ingreso->id_ingreso . ',' . $facturar . ');">'; ?>
                                <i class="fa fa-building-o fa-fw"></i>
                                </a>

                            <?php }

                            if ((valueOption('ACTIVAR_SHADOW') == 1)) {
                                echo '<a style="display:none; background-color:#f0ad4e; color: #ffffff"  class="btn cerrar_ingreso" data-toggle="tooltip"
                                            title="Cerrar Ingreso" data-original-title="Cerrar Ingreso"
                                            href="#" onclick="cerrar_ingreso(' . $ingreso->id_ingreso . ');">'; ?>
                                <i class="fa fa-unlock"></i>
                                </a>

                            <?php } ?>

                        </div>
                    </td>


                </tr>
            <?php }


        }


        ?>

        </tbody>
    </table>

</div>


<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" method="post" action="<?= $ruta ?>grupo/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Anular Ingreso</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea anular el ingreso seleccionado?</p>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Serie</label>
                            <input type="text" id="documento_serie" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label>Numero</label>
                            <input type="text" id="documento_numero" class="form-control">
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id_ingreso_cerrar">
                    <input type="hidden" name="id_ingreso" id="id_ingreso">
                    <input type="hidden" name="local_id" id="local_id">
                </div>
                <div class="modal-footer">
                    <input type="button" id="" class="btn btn-primary" value="Confirmar" onclick="anular()">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<div class="modal fade" id="ingresomodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>


<div id="valorizar_ingreso" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
            </button>

            <h3></h3>
        </div>
        <div class="modal-body" id="ingresomodalbody">

        </div>

    </div>
</div>

<div id="load_div" style="display: none;">
    <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(function () {

        TablesDatatables.init(3);

    });


    function editaringreso(id, facturar) {

        $("#load_div").show();
        $("#ingresomodalbody").html('');
        /*este metodo llamado editaringreso, es usado tanto para facturar ingreso, como para valorizar el documento,
         * solo que uno envia el parametro,   y el otro no*/
        //$("#load_div").modal('show');
        if (facturar != undefined) {
            facturar = "SI";
        } else {
            facturar = "NO";
        }
        $.ajax({
            url: '<?php echo base_url()?>ingresos',
            data: {'idingreso': id, 'editar': 1, 'costos': 'true', 'facturar': facturar},
            type: 'post',
            success: function (data) {
                $('#ingresomodal').html($("#valorizar_ingreso").html());
                $("#ingresomodalbody").html(data);
            },
            complete: function () {
                $("#load_div").hide();

            }

        });

        $('#ingresomodal').html($("#load_div").html());
        $("#ingresomodal").modal('show');

    }

    function ver(id, local, ingreso) {
        $("#ver").load('<?= base_url()?>ingresos/form/' + id + '/' + local + '/' + ingreso);
        $('#ver').modal('show');

    }

    function mostrar(id, local) {

        $('#borrar').modal('show');
        $("#id_ingreso").attr('value', id);
        $("#local_id").attr('value', local);
        $("#documento_serie").val("");
        $("#documento_numero").val("");
    }

    function generar() {

        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();

        // $("#hidden_consul").remove();

        $.ajax({
            url: '<?= base_url()?>ingresos/anular_ingreso',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta
            },
            type: 'POST',
            success: function (data) {
                // $("#query_consul").html(data.consulta);
                if (data.length > 0)
                    $("#tabla").html(data);
                $("#tablaresult").dataTable();
            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }


    function anular() {

        if($("#documento_serie").val() == "" || $("#documento_numero").val() == ""){
            show_msg('warning', 'Complete la serie y numero del documento');
            return false;
        }
        var id = $("#id_ingreso").val();
        var local = $("#local_id").val();
        $.ajax({
            url: '<?php echo base_url()?>ingresos/anular_ingreso',
            data: {
                'id': id,
                'local': local,
                'serie': $("#documento_serie").val(),
                'numero': $("#documento_numero").val()
            },
            type: 'POST',
            success: function (data) {
                if (data.error_prod == undefined) {
                    $("#borrar").modal('hide');
                    var growlType = 'success';
                    $.bootstrapGrowl('<h4>Ingreso Anulado!</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $.ajax({
                        url: '<?php echo base_url()?>ingresos/devolucion',
                        success: function (data) {
                            $(".modal-backdrop").remove();
                            $('#page-content').html(data);
                        }

                    });
                }
                else {
                    $("#borrar").modal('hide');
                    $.bootstrapGrowl('<h4>' + data.error_prod + '</h4>', {
                        type: 'warning',
                        delay: 2500,
                        allow_dismiss: true
                    });
                }

            },
            error: function (data) {
                var growlType = 'error';
                $.bootstrapGrowl('<h4>Ocurrio un Error!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        })
    }
</script>