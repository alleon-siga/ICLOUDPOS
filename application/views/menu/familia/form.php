<form name="formagregar" action="<?= base_url() ?>familia/guardar" method="post" id="formagregarfamilia">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Familia</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissable" id="error"
                             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                            <h4><i class="icon fa fa-check"></i> Error</h4>
                            <span id="errorspan"><?php echo isset($error) ? $error : '' ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-2">
                        Nombre
                    </div>

                    <div class="col-md-10">
                        <input type="text" name="nombre" id="nombre_familia" required="true" class="form-control"
                               value="<?php if (isset($familia['nombre_familia'])) echo $familia['nombre_familia']; ?>">
                    </div>
                    <input type="hidden" name="id" id="" required="true"
                           value="<?php if (isset($familia['id_familia'])) echo $familia['id_familia']; ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmar_boton_familia" onclick="guardar_familia('familia')">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>


<script>

    function guardar_familia(retorno){
        if ($("#nombre_familia").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
            $('#load_div').show()

        $.ajax({
            url: '<?= base_url()?>familia/guardar',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            dataType:'json',
            data: $("#formagregarfamilia").serialize(),
            success: function (data) {
                /*si retorno es producto, quiere decir que esta vista fue llamada desde el modulo de producto
                 * por lo tanto va allamar a update, y update esta en la vista de producto_form.
                 * Sino quiere decir que esta en su modulo normal, y retornara la vista nuevamente*/
                if (data.error == undefined) {

                    if(retorno=='producto') {
                        update_familia(data.id,data.nombre);
                    }

                    var growlType = 'success';

                    $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    retornarfamilia(retorno)
                }else{
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $(this).prop('disabled', true);
                }
                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $('#load_div').hide()
                    }, 2000)
            },
            error: function(data){

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);
                    setTimeout(function () {
                        //$(".alert-danger").css('display','none');
                        $('#load_div').hide()
                    }, 2000)

            }
        })



    }

    function retornarfamilia (retorno){

        $("#agregarfamilia").modal('hide');

        if(retorno=="familia") {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            return $.ajax({
                url: '<?= base_url()?>familia',
                success: function (data2) {
                    $('#page-content').html(data2);
                }

            })
        }

    }
    $(function(){



    });</script>
