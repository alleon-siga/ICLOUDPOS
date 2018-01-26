<?php $ruta = base_url(); ?>
<!-- Load and execute javascript code used only in this page -->


<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Ventas por Cobrar</a></li>
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
<!-- END Datatables Header -->
<div class="block">

    <div class="row-fluid">


        <div class="span12">
            <div class="box">
                <div class="block-title">
                    <h2>Ventas por Cobrar</h2>
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        Ubicaci&oacute;n
                    </div>
                    <div class="col-md-3">
                        <select id="locales" class="form-control campos" name="locales">
                            <?php foreach ($locales as $local): ?>
                                <option
                                    value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <div class="box-content box-nomargin">

                    <div class="tab-content" id="tabla">


                    </div>
                </div>
            </div>
        </div>


    </div>


</div>

<div class="modal fade" id="generarventa1" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">
    <!-- TERMINAR VENTA CREDITO -->

    <?php echo isset($dialog_terminar_venta_credito) ? $dialog_terminar_venta_credito : '' ?>

</div>


<div class="modal fade" id="generarventa" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false"
     aria-hidden="true">

    <!-- TERMINAR VENTA CONTADO -->

    <?php echo isset($dialog_terminar_venta_contado) ? $dialog_terminar_venta_contado : '' ?>

</div>

<div class="modal fade" id="formgarante" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
    <!-- TERMINAR VENTA CONTADO -->

    <?php echo isset($dialog_nuevo_garante) ? $dialog_nuevo_garante : '' ?>
</div>

<div class="modal fade" id="verventa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<script src="<?php echo $ruta ?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function () {

        TablesDatatables.init();

        $("#fecha").datepicker({format: 'dd-mm-yyyy'});

        buscarventas();
        $(".campos").on("change", function () {
            buscarventas();
        });


    });


    function buscarventas() {


        var locales = $("#locales").val();

        $.ajax({
            url: '<?= base_url()?>venta/get_ventas_cobrar',
            data: {
                'id_local': locales
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
        });

    }
    function ver(idventa) {
        $('#verventa').load("<?php echo base_url() ?>" + "venta/ver_productos/" + idventa);
        $('#verventa').modal('show');
    }

    function completar_cobro(venta_id, tipo_pago) {

        if (tipo_pago == 1) {
            $.ajax({
                url: '<?= base_url()?>venta/completar_cobro',
                data: {
                    'venta_id': venta_id,
                    'tipo_pago': tipo_pago,
                    'forma_pago': $("#forma_pago").val(),
                    'importe': $("#importe").val(),
                    'vuelto': $("#vuelto").val(),
                    'num_oper': $("#num_oper").val(),
                    'tipo_tarjeta': $("#tipo_tarjeta").val(),
                    'total_pagar': $("#totApagar2").val()
                },
                type: 'POST',
                success: function (data) {
                    $("#generarventa").modal("hide");
                    $.ajax({
                        url: '<?= base_url()?>venta/cobrar',
                        success: function (data) {
                            $('#page-content').html(data);
                        }

                    });
                },
                error: function () {
                    alert('Ocurrio un error por favor intente nuevamente');
                }
            });
        }
        else if(tipo_pago == 2){

            LoadCuotas();
            var miCuotas = JSON.stringify(lst_cuotas);
            
            $.ajax({
                url: '<?= base_url()?>venta/completar_cobro',
                data: {
                    'venta_id': venta_id,
                    'tipo_pago': tipo_pago,
                    'lst_cuotas': miCuotas,
                },
                type: 'POST',
                success: function (data) {
                    $("#generarventa1").modal("hide");
                    $.ajax({
                        url: '<?= base_url()?>venta/cobrar',
                        success: function (data) {
                            $('#page-content').html(data);
                        }

                    });
                },
                error: function () {
                    alert('Ocurrio un error por favor intente nuevamente');
                }
            });
        }
    }


</script>