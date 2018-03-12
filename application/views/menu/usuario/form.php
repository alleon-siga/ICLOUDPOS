<form name="formagregar" action="<?= base_url() ?>usuario/registrar" id="formagregar" method="post">
    <input type="hidden" name="nUsuCodigo" value="<?php if(isset($usuario->nUsuCodigo)) echo $usuario->nUsuCodigo?>" >
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

                  <!--  <div class="row">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label for="cboPersonal" class="control-label">Administrador</label>

                            </div>

                            <div class="col-md-10">
                                <input type="checkbox" name="admin" <?php if(isset($usuario->admin) and $usuario->admin==true) echo 'checked '?>>
                            </div>
                        </div>
                    </div>-->

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
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label class="control-label">Identificacion</label>
                            </div>
                            <div class="col-md-9">

                                <input type="number"
                                       name="identificacion"
                                       id="nombre"
                                       maxlength="20"
                                       class="form-control"
                                       required value="<?php if(isset($usuario->identificacion)) echo $usuario->identificacion?>">

                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cboPersonal" class="control-label">Grupo</label>

                            </div>

                            <div class="col-md-9">
                                <select name="grupo" id="grupo" class='form-control'>
                                    <option value="">Seleccione</option>
                                    <?php if (count($grupos) > 0): ?>
                                        <?php foreach ($grupos as $grupo): ?>
                                            <option
                                                value="<?php echo $grupo['id_grupos_usuarios']; ?>" <?php if(isset($usuario->grupo) and $usuario->grupo==$grupo['id_grupos_usuarios']) echo 'selected'?>><?php echo $grupo['nombre_grupos_usuarios']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cboPersonal" class="control-label">Ubicaci&oacute;n</label>

                            </div>

                            <div class="col-md-9">
                                <div >
                                <br>
                                   <?php $index = 1; $checked = ""; $opt = ""; foreach ($locales as $local) { ?>
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   <?php if (isset($usu_almacen) && count($usu_almacen) > 0) { $checked = ""; foreach ($usu_almacen as $usu)
                                   {
                                      if ($usu->local_id == $local["int_local_id"]){
                                      	$checked = "checked";
                                      	if ($usuario->id_local == $local["int_local_id"]){
                                      	    $opt .= "<option selected value='".$local["int_local_id"]."'>".$local["local_nombre"]."</option>";
                                      	}else{
                                      		$opt .= "<option value='".$local["int_local_id"]."'>".$local["local_nombre"]."</option>";
                                      	}
                                      	break;
                                      }
                                   }

                                   }
                                   	?>
                                   <input type="checkbox" onClick="CargarDefecto(this,'<?php echo $local["local_nombre"] ?>')" <?php echo $checked; ?> value="<?php echo $local["int_local_id"]?>" id="<?php echo $local["int_local_id"]?>" name="chlocales[]">&nbsp;
                                   	   <?php echo $local["local_nombre"];
                                   	   if ($index >= 3){
                                   	   	  echo "<br>";
                                   	   	  $index = 1;
                                   	   }else {$index++;}
                                    } ?>

                                </div><br>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="cboPersonal" class="control-label">Defecto:</label>

                            </div>

                            <div class="col-md-9">
                                <select id="id_local" name="id_local" name="grupo" class='form-control'>
                                  <?php  echo $opt; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <br>

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

                    <br>

                    <div class="row">
                      <div class="form-group">
                        <div class="col-md-3">
                                <label class="control-label panel-admin-text">Comisión:</label>
                        </div>

                        <div class="col-md-9">
                          <div class="input-group">
                                <input type="text"
                                     class='form-control'
                                     name="comision" id="comision" value="<?php if(isset($usuario->porcentaje_comision)) echo $usuario->porcentaje_comision?>"
                                     onkeydown="return soloDecimal4(this, event);">
                                <div class="input-group-addon">%</div>
                          </div>
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