<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Ingresos y Salidas de Productos</a></li>
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

    <div class="form-group row">
        <div class="col-md-2">
            Desde
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_desde"  id="fecha_desde" value="<?= date('d-m-Y')?>" required="true" class="form-control fecha campos input-datepicker ">
        </div>
        <div class="col-md-2">
            Hasta
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_hasta" id="fecha_hasta"  value="<?= date('d-m-Y')?>" required="true" class="form-control fecha campos input-datepicker">
        </div>

    </div>
    <div class="form-group row">
        <div class="col-md-2">
            Buscar Producto
        </div>
        <div class="col-md-4">
            <select class="form-control campos" id="producto">
                <option value="">Seleccione</option>
                <?php
                    foreach($productos as $producto){

                        echo "<option value=".$producto['id_producto'].">".$producto['producto_nombre']."</option>";

                    }
                ?>
            </select>
        </div>
    </div>
    <br>

    <div class="box-body" id="tabla">
     <div class="table-responsive" id="productostable">
        <table class='table table-striped dataTable table-bordered table-responsive' id="table" style="width: 100%;">
            <thead>
            <tr>
                <th>Fecha</th>
                <th>C&oacute;digo Producto</th>
                <th>Productos</th>
                <th>Cantidad Ingresada</th>
                <th>Precio Compra</th>
                <th>Cantidad Salida</th>
                <th>Precio Salida</th>
                <th>Stock Final</th>

            </tr>
            </thead>
            <tbody id="tbody">

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                    </tr>

            </tbody>
        </table>

        </div>
    </div>


</div>


<script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
<script type="text/javascript">


</script>

<!-- Load and execute javascript code used only in this page -->
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>

<script>
    $(function () {
        TablesDatatables.init();
        get_ingresos();

        $(".campos").on("change", function () {

            get_ingresos();

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

    });

    function get_ingresos() {
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var producto = $("#producto").val();


        $.ajax({
            url: '<?php echo $ruta ?>ingresosYsalidas/get_ingresos',
            data: {
                'producto': producto,
                'desde': fercha_desde,
                'hasta': fercha_hasta


            },
            type: 'POST',
            success: function (data) {
                if (data.length > 0) {
                    $("#tabla").html(data);
                }
                $("#tablaresultado").dataTable();
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }
</script>
