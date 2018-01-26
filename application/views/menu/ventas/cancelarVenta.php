<?php $ruta = base_url(); ?>
<!-- Load and execute javascript code used only in this page -->


<script type="text/javascript">


</script>


<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Anular Ventas</a></li>
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

        <form name="formeliminar" method="post" id="formeliminar" action="<?= $ruta ?>venta/anular_venta">
        <div class="span12">
            <div class="box">
                <div class="block-title">
                    <h2><strong>Anular</strong> Ventas</h2>
                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Ubicaci&oacute;n:</label>
                    </div>
                    <div class="col-md-3">
                        <select id="locales" class="form-control campos" name="locales">

                            <?php if (isset($locales)) {
                                foreach ($locales as $local) {
                                    ?>
                                    <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                                <?php }
                            } ?>

                        </select>

                    </div>
                </div>

                <div class="box-content box-nomargin">

                    <div class="tab-content" id="tabla">

                        <div class="table-responsive">
                            <table class='table table-striped dataTable table-bordered' id="tablaresult">
                                <thead>
                                <tr>
                                    <th>Secci&oacute;n</th>
                                    <th>Documento</th>
                                    <th>Num Document.</th>
                                    <th>Fecha </th>
                                    <th>Cliente</th>
                                    <th>Condici&oacute;n</th>
                                    <th>Vendedor</th>
                                    <th>Moneda</th>
                                    <th>Tip. Camb. </th>
                                    <th>Total</th>
                                    <th>Ver</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="anular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">

                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="titulo"></h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="col-md-2">
                                    Motivo
                                </div>
                                <div class="col-md-10">
                                    <input type="text" name="motivo" id="motivo" required="true" class="form-control"
                                        >
                                    <input type="hidden" name="id" id="id" required="" class="form-control"
                                        >
                                    <input type="hidden" name="local" id="local" required="" class="form-control"
                                        >
                                </div>

                            </div>

                            </div>

                        <div class="modal-footer">
                            <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>


        </div>
        <div class="modal fade" id="modal_notas_credito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">

                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Notas de Credito</h4>
                        </div>

                        <div class="modal-body ">
                            <div id="botones_impresion_notas_credito"></div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>


        </div>


    </form>

        <div class="modal fade" id="verventa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">


        </div>
    </div>
</div>
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function(){

        TablesDatatables.init();

        $("#fecha").datepicker({format: 'dd-mm-yyyy'});

        buscarventas();
        $(".campos").on("change",function(){
            buscarventas();
        });
    });

    function buscarventas() {


        var locales = $("#locales").val();

        $.ajax({
            url: '<?= base_url()?>venta/get_ventas_cancelar',
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
        })

    };
    function ver(idventa) {
        $('#verventa').load("<?php echo base_url() ?>"+"venta/ver_productos/"+idventa);
        $('#verventa').modal('show');
    }

    function anular(documento,idventa) {
        $("#motivo").focus()
        $("#titulo").html('')
        $("#titulo").append("Anular Venta "+documento)
        $("#local").val($('#locales').val())
        $("#id").val(idventa)
        $('#anular').modal('show');
        //$("#id").attr('value', id);
    }
    var totales;
    var url_fifi = "<?php echo base_url() ?>"
    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>venta/cancelar'

            })
        },
        guardar : function () {

           // alert($("venta").length)
          /*var  total =  $('input[name="venta[]"]:checked').length > 0;
          totales = $('input[name="venta[]"]:checked');*/
          
           if ($("#motivo").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
           /* if(total==false){
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar una Venta</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;

            }*/
            $("#id"+$("#id").val()).append('<input type="text" name="locales[]" class="form-control check_venta" value="'+$("#local").val()+'">'
            +'<input type="text" name="venta[]" class="form-control check_venta" value="'+$("#id").val()+'">')

            App.formSubmitAjax($("#formeliminar").attr('action'), this.ajaxgrupo, 'anular', 'formeliminar');
        }
    }


</script>