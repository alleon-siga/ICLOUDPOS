<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Ajuste de inventario</a></li>
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

    <button class="btn btn-default" onclick="agregar();">
        <i class="fa fa-plus "> NUEVO AJUSTE</i>
    </button>
    <form id="frmBuscar">
    <!-- Progress Bars Wizard Title -->
    <div class="col-md-1">
       <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
    </div>
    <div class="col-md-3">
        <select id="locales" name="locales" class="form-control buscar_ajuste">
            <option value="seleccione"> Seleccione</option>
            <?php if (isset($locales)) { 
            	foreach ($locales as $local) { if ($this->session->userdata('id_local') == $local['int_local_id']) {
            		?>
            	    <option selected value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
            	   <?php } else { ?>
            	    <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
            	    <?php } }
            	} ?>
            	            	
        </select>
        <br>
    </div>
    <div class="col-md-1">
        <label class="control-label panel-admin-text">Desde</label>
    </div>
    <div class="col-md-2">

        <input type="text" readonly name="fecIni" id="fecIni" value="<?= date('d-m-Y')?>" class='buscar_ajuste input-small form-control input-datepicker'>
    </div>
    <div class="col-md-1">
        <label class="control-label panel-admin-text">Hasta</label>
    </div>
    <div class="col-md-2">
        <input type="text" readonly name="fecFin" id="fecFin" value="<?= date('d-m-Y')?>" class='buscar_ajuste form-control input-datepicker'>
    </div>
    <br>
    </form>

    <?php
    echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
    ?>
    <div class="row">


        <div class="table-responsive" id="tabla">

            <table class="table dataTable table-striped dataTable table-bordered" id="tablaresultado">
                <thead>
                <tr>

                    <th style="text-align: center">N&uacute;mero</th>
                    <th style="text-align: center">Fecha</th>
                    <th style="text-align: center">Nombre</th>
                    <th style="text-align: center">Cantidad Prod.</th>
                    <th style="text-align: center">Acciones</th>

                </tr>
                </thead>
                <tbody>
                <?php if (count($ajustes) > 0) {

                    foreach ($ajustes as $ajuste) {
                        ?>
                        <tr>

                            <td style="text-align: center"><?= $ajuste->id_ajusteinventario ?></td>
                            <td style="text-align: center"><?= date('d-m-Y H:i:s', strtotime($ajuste->fecha)) ?></td>
                            <td style="text-align: center"><?= $ajuste->descripcion ?></td>
                            <td style="text-align: center"><?= $ajuste->cantidad ?></td>

                            <td style="text-align: center">
                                <div class="btn-group">
                                    <?php

                                    echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver Detalle" data-original-title="Ver Detalle"
                                            href="#" onclick="ver(' . $ajuste->id_ajusteinventario . ');">'; ?>
                                    <i class="fa fa-search"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    <?php }
                } ?>
                </tbody>
            </table>
        </div>
    </div>


    <br>

</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

    function agregar() {

    	local_id = $("#locales").val();
        $('#agregargrupo').html($("#load_div").html());
        $("#agregargrupo").load('<?= $ruta ?>inventario/addajuste/' + local_id);
        $('#agregargrupo').modal({show: true, keyboard: false, backdrop: 'static'});

    }

    function ver(id) {
        $('#verajuste').html($("#load_div").html());
        $("#verajuste").load('<?= $ruta ?>inventario/verajuste/' + id + '/' + $("#locales").val());
        $('#verajuste').modal({show: true});
    }

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>inventario/ajuste'

            })
        }

    }
</script>
<div class="modal fades" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div id="load_div" style="display: none;">
    <div class="row" id="loading" style="position: relative; top: 50px; z-index: 500000;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
</div>

<!-- /.modal-dialog -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function () {

        $('#agregargrupo').on('hidden.bs.modal', function (e) {

            $('.modal-backdrop').remove();
            buscar()
        });

        TablesDatatables.init();
        //   TablesDatatables.init();

        buscar();
        $(".buscar_ajuste").on("change", function () {

                buscar();
        });
        $('body').keydown(function (e) {

            if (e.keyCode == 27) {
                e.preventDefault();
                e.stopPropagation();
                if($("#formagregar").is(':visible')) {
                    $("#confirmarcerrar").modal('show');
                }
            }
        });
    });
    function confirmarcerrar() {
        $("#confirmarcerrar").modal('hide');
        $("#agregargrupo").modal('hide');
    }


    function buscar(){


        $("#cargando_modal").modal('show')
        $.ajax({
            url: '<?= base_url()?>inventario/ajusteinventario_by_local',
            data: $('#frmBuscar').serialize(),
            type: 'POST',
            success: function (data) {
                // $("#query_consul").html(data.consulta);

                $("#tabla").html(data);
                TablesDatatables.init();
                $("#cargando_modal").modal('hide')
            },
            error: function () {
                $("#cargando_modal").modal('hide')
            }
        })
    }

</script>
