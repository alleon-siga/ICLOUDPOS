<?php $ruta = base_url(); ?>

<link rel="stylesheet" href="<?=$ruta?>recursos/css/plugins.css">
<ul class="breadcrumb breadcrumb-top">
    <li>Clientes</li>
    <li><a href="">Estado de Cuenta</a></li>
</ul>
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">

                <div class="block-section block-alt-noborder">

                    <div class="row">

                        <div class="col-md-1">
                            <label>Local</label>
                        </div>
                        <div class="col-md-3">

                            <select name="local" id="local" class='cho form-control'>
                                <option value="TODOS">TODOS</option>
                                <?php if (count($locales) > 0): ?>
                                    <?php foreach ($locales as $local): ?>
                                        <option
                                            value="<?php echo $local['int_local_id']; ?>"><?php echo $local['local_nombre']; ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label>Cliente</label>
                        </div>
                        <div class="col-md-3">
                            <select name="cboCliente" id="cboCliente" class='form-control '>
                                <option value="-1">Seleccionar</option>
                                <?php if (count($lstCliente) > 0): ?>
                                    <?php foreach ($lstCliente as $cl): ?>
                                        <?php if($cl['tipo_cliente']==1){ ?>
                                        <option
                                            value="<?php echo $cl['id_cliente']; ?>"><?php echo $cl['razon_social']; ?></option>
                                        <?php }else{ ?>
                                    <option
                                        value="<?php echo $cl['id_cliente']; ?>"><?php echo $cl['nombres'].' '.$cl['apellido_paterno'] ?></option>

                                    <?php } ?>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <?php endif; ?>
                            </select></div>

                        <div class="col-md-1">
                            <label>Estado</label>
                        </div>
                        <div class="col-md-3">
                            <select name="estado" id="estado" class='form-control '>
                                <option value="TODOS">TODOS</option>
                                <option value="PagoCancelado">Pago Cancelado</option>
                                <option value="PagoPendiente">Pago Pendiente</option>
                            </select></div>


                    </div>
                    <div class="row">
                        <div class="col-md-1">
                            <label>Desde</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="fecIni" id="fecIni"
                                   class='form-control'>
                        </div>
                        <div class="col-md-1">
                            <label>Hasta</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="fecFin" id="fecFin" class='form-control'>
                        </div>


                        <input type="button"  value="Buscar" id="btnBuscar" class="btn btn-default" >
                    </div>
                </div>
            </form>

<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <div class="block-title">

            </div>



            <div id="lstTabla" class="table-responsive"></div>
        </div>



    <div id="ec_excel">
        <form action="<?php echo $ruta; ?>exportar/toExcel_estadoCuenta" name="frmExcel"
              id="frmExcel" method="post">
            <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
            <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
            <input type="hidden" name="cboCliente1" id="cboCliente1" class='input-small'>
            <input type="hidden" name="local" id="localexcel" class='input-small'>
            <input type="hidden" name="estado" id="estadoexcel" class='input-small'>
        </form>
    </div>
    <a href="#" onclick="generar_reporte_excel();" class='tip btn-lg btn btn-default'
       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i> </a>

    <div id="ec_pdf">
        <form name="frmPDF" id="frmPDF" action="<?php echo $ruta; ?>exportar/toPDF_estadoCuenta"
              target="_blank" method="post">
            <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
            <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
            <input type="hidden" name="cboCliente2" id="cboCliente2" class='input-small'>
            <input type="hidden" name="local" id="localpdf" class='input-small'>
            <input type="hidden" name="estado" id="estadopdf" class='input-small'>
        </form>
    </div>
    <a href="#" onclick="generar_reporte_pdf();" class='btn btn-lg btn-default tip'
       title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i></a>

</div>
</div>



<script type="text/javascript">
    $(document).ready(function () {

        $('select').chosen();
        $("#fecIni").datepicker({format: 'dd-mm-yyyy'});
        $("#fecFin").datepicker({format: 'dd-mm-yyyy'});
        $("#ec_excel").hide();
        $("#ec_pdf").hide();
        $("#btnBuscar").on('click', function () {

            buscar();

        });
        buscar();
    });

    function buscar(){

        document.getElementById('fecIni1').value = $("#fecIni").val();
        document.getElementById('fecFin1').value = $("#fecFin").val();
        document.getElementById('fecIni2').value = $("#fecIni").val();
        document.getElementById('fecFin2').value = $("#fecFin").val();
        document.getElementById('cboCliente1').value = $("#cboCliente").val();
        document.getElementById('cboCliente2').value = $("#cboCliente").val();

        document.getElementById('localpdf').value = $("#local").val();
        document.getElementById('localexcel').value = $("#local").val();
        document.getElementById('estadopdf').value = $("#estado").val();
        document.getElementById('estadoexcel').value = $("#estado").val();

        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_reg_estadocuenta',
            success: function (data) {

                $("#lstTabla").html(data);
            }
        });
    }

    function generar_reporte_excel() {
        document.getElementById("frmExcel").submit();
    }

    function generar_reporte_pdf() {
        document.getElementById("frmPDF").submit();
    }
</script>

