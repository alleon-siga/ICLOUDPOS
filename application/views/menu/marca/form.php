





<form name="formagregar" action="<?= base_url() ?>marca/guardar" method="post" id="formagregarmarca">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Marca</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Nombre
                    </div>
                    <div class="col-md-10"><input type="text" name="nombre" id="nombre_marca" required="true" class="form-control"
                                                  value="<?php if (isset($marca['nombre_marca'])) echo $marca['nombre_marca']; ?>">
                    </div>

                    <input type="hidden" name="id" id="" required="true"
                           value="<?php if (isset($marca['id_marca'])) echo $marca['id_marca']; ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmar_boton_marca" onclick="guardar_marca('marca')">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>

<script>

    function guardar_marca(retorno){
        if ($("#nombre_marca").val() == '') {
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

        /*guardo la marca*/
        $.ajax({
            url: '<?= base_url()?>marca/guardar',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            dataType:'json',
            data: $("#formagregarmarca").serialize(),
            success: function (data) {

                if (data.error == undefined) {
                    /*si retorno es producto, quiere decir que esta vista fue llamada desde el modulo de producto
                    * por lo tanto va allamar a update, y update esta en la vista de producto_form.
                    * Sino quiere decir que esta en su modulo normal, y retornara la vista nuevamente*/

                     if(retorno=='producto') {
                        update_marca(data.id,data.nombre);
                    }

                    var growlType = 'success';

                    $.bootstrapGrowl('<h4>'+data.success+'</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    retornarmarca(retorno)
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

    function retornarmarca (retorno){

        $("#agregarmarca").modal('hide');

        if(retorno=="marca") {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            return $.ajax({
                url: '<?= base_url()?>marca',
                success: function (data2) {
                    $('#page-content').html(data2);
                }

            })
        }

    }
    $(function(){



    });</script>
