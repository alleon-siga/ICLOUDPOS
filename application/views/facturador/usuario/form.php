<form name="formagregar" action="<?= base_url() ?>facturador/usuario/registrar" id="formagregar" method="post">
    <input type="hidden" name="nUsuCodigo" value="<?php if(isset($usuario->id)) echo $usuario->id?>" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Usuario</h4>
            </div>
            <div class="modal-body">
                <div class="block-section">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label">Usuario:</label>
                            </div>
                            <div class="col-md-9">
                                <div class="controls">

                                    <input type="text"
                                           name="username"
                                           id="username"
                                           maxlength="18"
                                           class='form-control'
                                           autofocus="autofocus"
                                           required value="<?php if(isset($usuario->username)) echo $usuario->username?>">

                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label">Contrase&ntilde;a:</label>
                            </div>
                            <div class="col-md-9">

                                <input type="password"
                                       name="var_usuario_clave"
                                       id="var_usuario_clave"
                                       maxlength="20"
                                       class='form-control'
                                       >

                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label">Nombre Completo</label>
                            </div>
                            <div class="col-md-9">

                                <input type="text"
                                       name="nombre"
                                       id="nombre"
                                       maxlength="50"
                                       class="form-control"
                                       required value="<?php if(isset($usuario->username)) echo $usuario->nombre?>">

                            </div>
                        </div>
                    </div>

                    <br>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cboPersonal" class="control-label">Activo</label>
                            </div>

                            <div class="col-md-9">
                                <input type="checkbox" name="activo" <?php if(isset( $usuario->activo) and $usuario->activo==true) echo 'checked '?>>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <div class="form-actions">
                    <button type="button" id="" class="btn btn-default" onclick="usuario.guardar()" >Confirmar</button>
                    <input type="button" class='btn btn-danger'  data-dismiss="modal" value="Cancelar">
                </div>
            </div>
        </div>
    </div>
</form>