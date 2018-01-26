<?php $ruta = base_url(); ?>

    <table class='table table-striped dataTable table-bordered no-footer' id="tablaresultado" style="overflow:scroll">
        <thead>
        <tr>

            <th>Documento </th>
            <th>Num Document.</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Condici&oacute;n</th>
            <th>Vendedor</th>
            <th>Moneda</th>
            <th>Tip. Camb. </th>
            <th>Total</th>
            <th>Formato</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0): ?>

            <?php
             foreach

            ($ventas as $venta): ?>

                <tr>
                    <td ><?php
                        if($venta->id_doc==1) echo "FA";
                        if($venta->id_doc==2) echo "NC";
                        if($venta->id_doc==3) echo "BO";
                        if($venta->id_doc==4) echo "GR";
                        if($venta->id_doc==5) echo "PCV";
                        if($venta->id_doc==6) echo "NP";
                        ?></td>
                    <td ><?= $venta->serie." - ".$venta->numero_documento ?></td>
                    <td ><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>
                    <td ><?= $venta->razon_social ?></td>
                    <td ><?= $venta->venta_status ?></td>
                    <td ><?= $venta->nombre_condiciones ?></td>
                    <td ><?= $venta->username ?></td>
                    <td><?= $venta->nombre?></td>
                    <td><?= $venta->tasa_cambio ?></td>
                    <td><?= $venta->simbolo." ". $venta->total ?></td>
                    <td>

                        <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                           title="Ver" data-original-title="Ver"
                           href="#" onclick="ver(<?= $venta->venta_id ?>,<?= $venta->id_documento?>,<?= $venta->condicion_pago?>,'<?= $venta->venta_status ?>')" >
                            <i class="fa fa-search"></i>
                        </a>


                    </td>




                </tr>
            <?php endforeach ?>
        <?php endif; ?>

        </tbody>
    </table>




<a target="_blank" href="<?= $ruta?>venta/pdfHistorialVentas/<?php if(isset($local)) echo $local; else echo 0;?>/<?php if(isset($fecha_desde)) echo date("Y-m-d", strtotime($fecha_desde)); else echo 0;?>/<?php if(isset($fecha_hasta)) echo date("Y-m-d", strtotime($fecha_hasta)); else echo 0;?>/<?php if(isset($estatus)) echo $estatus; else echo 0;?>"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a target="_blank" href="<?= $ruta?>venta/excelHistorialVentas/<?php if(isset($local)) echo $local; else echo 0;?>/<?php if(isset($fecha_desde)) echo date("Y-m-d", strtotime($fecha_desde)); else echo 0;?>/<?php if(isset($fecha_hasta)) echo date("Y-m-d", strtotime($fecha_hasta)); else echo 0;?>/<?php if(isset($estatus)) echo $estatus; else echo 0;?>"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
           aria-labelledby="myModalLabel"
           aria-hidden="true">


</div>

<div class="modal fade" id="verimprimir" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init(3);

    });

    function ver(idventa,documento,condicion_pago,estatus){

        <?php if ($this->session->userdata('GENERAR_FACTURACION') == "SI") { ?>

        /*se carga la vista donde se imprimen los formatos*/
        $.ajax({
            url:  '<?php echo $ruta.'venta/vista_impresion_formatos'; ?>',
            type: 'POST',
            data: {'idventa':idventa,'estatus':estatus,'condicion_pago': condicion_pago, 'id_documento':documento},

            success:function(data){

                $("#verimprimir").html(data);
                $("#verimprimir").modal({show:true});


            }
        });



        <?php }else{  ?>
        /*se carga la vista donde se ve el detalle de la venta (el ticket)*/
        cargaData_Impresion(idventa)
        <?php } ?>

    }

    function generar(){

        var fecha_desde=$("#fecha_desde").val();
        var fecha_hasta=$("#fecha_hasta").val();
        var locales=$("#locales").val();
        var estatus=$("#estatus").val();
        // $("#hidden_consul").remove();
        $("#agregargrupo").load('<?= $ruta ?>venta/pdf/'+locales+'/'+fecha_desde+'/'+fecha_hasta+'/'+estatus);
        TablesDatatables.init();
    }

    function cargaData_Impresion(id_venta){

        $.ajax({
            url:  '<?php echo $ruta.'venta/verVenta'; ?>',
            type: 'POST',
            data: "idventa="+id_venta,

            success:function(data){

                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal({show:true,  keyboard: false,  backdrop:'static'});


            }
        });



    }
</script>