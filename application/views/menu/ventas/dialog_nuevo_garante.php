<div class="modal-dialog" style="width: 40%">
    <div class="modal-content">
        <div class="modal-body">
            <h4>Nuevo Garante</h4>
            <div id="mensaje_garante"></div>
        </div>
        <div class="modal-body">
            <form action="#" id="form_nuevo_garante">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="dni" class="control-label">DNI:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="number" name="dni" id="dni"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="nombre_full" class="control-label">Nombre Completo:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="nombre_full" id="nombre_full"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="direccion" class="control-label">Direccion:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="direccion" id="direccion"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="referencia_direcc" class="control-label">Referencia
                                Direcc:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="refe_direccion" id="refe_direccion"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="celular" class="control-label">Celular:</label>
                        </div>
                        <div class="col-md-5">
                            <input type=text name="celular" id="celular"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="correo" class="control-label">Correo:</label>
                        </div>
                        <div class="col-md-5">
                            <input type=text name="correo" id="correo"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="ocupacion" class="control-label">Ocupacion:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="centro_traba" id="centro_traba"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="direc_trab" class="control-label">Direccion Trabajo:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="direc_trab" id="direc_trab"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label for="nombre_conyu" class="control-label">Nombre Conyuge:</label>
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="nombre_conyu" id="nombre_conyu"
                                   class='form-control' autofocus="autofocus"
                                   value="">
                        </div>
                    </div>
                </div>

        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default" type="button" id="btnGuardarGarante"
                            onclick="GuardarGarante()"><i
                            class="fa fa-save"></i>Guardar


                    </button>
                    <button class="btn btn-default" type="button" onclick="$('#formgarante').modal('hide');"><i
                            class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function GuardarGarante() {
        //console.log($("#form_nuevo_garante").serialize());
        $.ajax({
            url: ruta + 'venta/saveGuardar',
            dataType: 'json',
            type: 'POST',
            //dataType: 'json',
            //data: {'dni': $("#dni").val(),'nombre_full': $("#nombre_full").val(), 'direccion': $("#direccion").val(), 'refe_direccion': $("#refe_direccion").val(), 'celular': $("#celular").val(), 'centro_traba': $("#centro_traba").val(), 'direc_trab': $("#direc_trab").val(), 'nombre_conyu': $("#nombre_conyu").val() },
            data: "dni=" + $("#dni").val() + "&nombre_full=" + $("#nombre_full").val() + "&direccion=" + $("#direccion").val() + "&refe_direccion=" + $("#refe_direccion").val() + "&centro_traba=" + $("#centro_traba").val() + "&direc_trab=" + $("#direc_trab").val() + "&nombre_conyu=" + $("#nombre_conyu").val() + "&correo=" + $("#correo").val() + "&celular=" + $("#celular").val(),
            // data:$("#form_nuevo_garante").serialize(),
            success: function (data) {
                if (data.html) {
                    //console.log();
                    $("#dni,#nombre_full,#direccion,#refe_direccion,#centro_traba,#direc_trab,#nombre_conyu,#correo,#celular").val("");
                    $("#garante").html(data.html);
                    $("#formgarante").modal('hide');
                    $("#garante_menesaje").removeClass("hide");
                    $("#garante_menesaje").html("<center class='alert alert-success'>" + data.message + "</center>");
                    $('#garante').trigger("chosen:updated");

                } else {
                    $("#mensaje_garante").html("<center class='alert alert-danger'>" + data.message + "</center>");
                }
            }
        });
    }
</script>