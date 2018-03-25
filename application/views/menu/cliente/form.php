<?php  $ruta= base_url(); ?>
<style>
  .datepicker{z-index:9999 !important;}
</style>
<script type="text/javascript">
    var base_url='<?php echo $ruta; ?>';
    var contador_universal=0;
    var contadordireccion=0;
    var contadorrazon_social=0;
    var contadortelefono=0;
    var contadorcorreo=0;
    var contadorrepresentante=0;
    var contadorpagina_web=0;
    var contadorcumpleanos=0;

    function vermas(){
        $('#vermas').toggle("slow");
    }

    function vermas2(){
        $('#vermas2').toggle("slow");
    }
</script>
<div class="modal-dialog modal-lg" style="width: 60%">
    <div class="modal-content" >
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title">Nuevo Cliente</h4>
        </div>
        <div class="modal-body">
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a data-toggle="tab" href="#persona">Persona</a></li>
                <li><a data-toggle="tab" href="#empresa">Empresa</a></li>
            </ul>
            <div class="tab-content">
                <div id="persona" class="tab-pane fade in active">
                    <input type="hidden" id="new_from_venta" value="<?= isset($new_from_venta) ? $new_from_venta : 0?>">
                    <form name="formagregar" onsubmit="return validarFrm(this)" action="<?= base_url() ?>cliente/guardar" method="post" id="formagregar"
          enctype="multipart/form-data">
                        <input type="hidden" name="tipo_cliente" id="tipo_cliente" value="0">
                        <input type="hidden" name="tipo_iden" id="tipo_iden" value="1">
                        <input type="hidden" name="idClientes" id="idClientes" value="<?php if (isset($cliente['id_cliente'])) echo $cliente['id_cliente']; ?>">
                        <div class="row" style="display: none;">
                            <div class="form-group">
                                <div class="col-md-8">
                                </div>
                                <div class="col-md-2" id="abrir_imagen_empresa" style="position: absolute; top:0px; right:0px;">

                                        <?php  if (empty($images)){  ?>
                                            <img id="imgSalida_je0" data-count="0"
                                                 src="<?php echo $ruta . "recursos/img/la_foto.png" ?>" width="80%"
                                                 height="150">
                                            <span
                                                class="btn btn-default" style="position:relative;width:100px;" id="subirImg_je">Subir  <i
                                                    class="fa fa-file-image-o" aria-hidden="true"></i></span>
                                            <input style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;width:100%;height:100%;opacity: 0;"  type="file" onchange="asignar_imagen_je(0)" class="form-control input_imagen"
                                                   data-count="0" name="userfile_je[]" accept="image/*"
                                                   id="input_imagen_je0">

                                        <?php
                                        }
                                        if (isset($cliente['id_cliente']) and !empty($images)): ?>


                                            <?php $ruta_imagen = "clientes/" . $cliente['id_cliente'] . "/" ?>


                                            <?php

                                            $con_image = 0;
                                            foreach ($images as $img): ?>
                                                <div  style="text-align: center; margin-bottom: 20px;"
                                                     id="div_imagen_producto_je<?= $con_image ?>">

                                                    <a href="#" class="img_show"
                                                       data-src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                        <img alt='' width='100'  height='150'
                                                             src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                    </a>
                                                    <br>
                                                    <a href="#"
                                                       onclick="borrar_img_je('<?= $cliente['id_cliente'] ?>','<?= $img ?>','<?= $con_image ?>')"
                                                       style="width: 150px; margin: 0;" id="eliminar_je" class="btn btn-raised btn-danger"><i

                                                            class="fa fa-trash-o"></i> Eliminar</a>
                                                </div>


                                                <?php
                                                $con_image++;
                                            endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">DNI</label>
                                <input type="text" name="ruc_j" value="<?php if (isset($cliente['identificacion'])) echo  $cliente['identificacion']; ?>" id="ruc_j" typeClass="DNI" class="form-control dni" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($operacion == FALSE):?>
                                    <h5><?= (isset($cliente['tipo_cliente']) && $cliente['tipo_cliente'] == 1) ? 'Jur&iacute;dico': 'Natural'?></h5>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!--<label class="control-label panel-admin-text">Razon Social</label>-->
                                <!--<input type="hidden" name="razon_social_j" value="" id="razon_social_j" class="form-control"  />-->
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                        <!--<br> -->
                        <!-- <h4>Representante del Cliente</h4>-->
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Nombre</label>
                                <input type="text" name="nombres"
                                       value="<?php if (isset($cliente['nombres'])) echo  $cliente['nombres']; ?>"
                                       id="nombres" class="form-control" data-placeholder="Nombre"  />
                            </div>
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Apellidos</label>
                                <input type="text" name="apellido_paterno"
                                       value="<?php if (isset($cliente['apellido_paterno'])) echo  $cliente['apellido_paterno']; ?>"
                                       id="apellido_paterno" class="form-control" data-placeholder="Apellido paterno"  />
                            </div>
                        </div>
                        <!--<div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Apellido Materno</label>
                                <input type="text" name="apellido_materno"
                                       value="<?php if (isset($cliente['apellido_materno'])) echo  $cliente['apellido_materno']; ?>"
                                       id="apellido_materno" class="form-control" data-placeholder="Apellido materno"  />
                            </div>
                        </div>-->
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Dirección Principal</label>
                                <input type="text"  id="direccion_j" required="true"
                                class="form-control" name="direccion_j"
                                value="<?php if (isset($cliente['direccion'])) echo $cliente['direccion']; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Telefono</label>
                                <input type="text" name="telefono"
                                value="<?php if (isset($cliente['telefono1'])) echo  $cliente['telefono1']; ?>"
                                id="telefono" class="form-control" data-placeholder="Telefono"  />
                            </div>
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Correo Electr&oacute;nico</label>
                                <input type="email" name="correo"
                                value="<?php if (isset($cliente['email'])) echo  $cliente['email']; ?>"
                                id="correo" class="form-control" data-placeholder="Correo"  />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Grupo</label>
                                <select  id="grupo_id_juridico" name="grupo_id_juridico" required="true" class="chosen form-control">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($grupos as $grupo): ?>
                                        <?php if(!isset($cliente['grupo_id'])){ $cliente['grupo_id'] = 1; } ?>
                                        <option value="<?php echo $grupo['id_grupos_cliente'] ?>" <?php  if (isset($cliente['grupo_id']) and $cliente['grupo_id'] == $grupo['id_grupos_cliente']) echo 'selected' ?>><?= $grupo['nombre_grupos_cliente'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Estado</label>
                                <select id="estatus_j" name="estatus_j" required="true" class="chosen form-control">

                                    <option value="1" <?php if (isset($cliente['cliente_status']) AND $cliente['tipo_cliente'] == 1 and $cliente['cliente_status']==1) echo "selected" ?>>ACTIVO</option>
                                    <option value="0" <?php if (isset($cliente['cliente_status']) AND $cliente['tipo_cliente'] == 1 and $cliente['cliente_status']==0) echo "selected" ?>>INACTIVO</option>

                                </select> 
                            </div>
                        </div>
                        <br>
                        <button type="button" class="btn btn-info" onclick="vermas()">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Ver m&aacute;s
                        </button>
                        <div id="vermas" style="display: none;">
                            <h4>Datos Adicionales</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">Departamento</label>
                                    <select name="estado_id" id="estado_id" required="true" class="chosen form-control" onchange="region.actualizardistritos();">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($estados as $estado): ?>
                                        <?php if(!isset($cliente['provincia'])){ $cliente['provincia'] = '1'; } ?>
                                        <option value="<?php echo $estado['estados_id'] ?>" <?php if (isset($cliente['provincia']) and $estado['estados_id'] == $cliente['provincia']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">Provincia</label>
                                    <select name="ciudad_id" id="ciudad_id" required="true" class="chosen form-control" onchange="region.actualizarbarrio();">
                                        <option value="">Seleccione</option>
                                <?php //if (isset($cliente['id_cliente'])): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <?php $cliente['ciudad'] = '2'; ?>
                                        <option value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($cliente['ciudad']) and $ciudad['ciudad_id'] == $cliente['ciudad']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php //endif ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text" >Distrito</label>
                                    <select name="distrito_id" id="distrito_id" required="true" class="chosen form-control">
                                        <option value="">Seleccione</option>
                                        <?php if (isset($cliente['id_cliente'])): ?>
                                            <?php foreach ($distritos as $distrito): ?>
                                        <option value="<?php echo $distrito['id'] ?>" <?php if (isset($cliente['distrito']) and $distrito['id'] == $cliente['distrito']) echo 'selected' ?>><?= $distrito['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">Genero</label>
                                    <select id="genero" name="genero" class="form-control">
                                        <option value=""></option>
                                        <?php if(!isset($cliente['genero'])){ ?>
                                        <?php $cliente['genero'] = '1'; ?>
                                        <?php } ?>
                                        <option value="1" <?=(isset($cliente['genero']) && $cliente['genero'] == '1' ? 'selected' : '')?>>Masculino</option>
                                        <option value="2" <?=(isset($cliente['genero']) && $cliente['genero'] == '2' ? 'selected' : '')?>>Femenino</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="retencion" id="retencion" value="1" <?= isset($cliente['agente_retension']) && $cliente['agente_retension'] == 1 ? 'checked' : ''?>>
                                    <label class="control-label panel-admin-text" style="cursor: pointer;" for="retencion">Retenci&oacute;n?</label> 
                                    <input type="number" <?php if(!(isset($cliente['agente_retension_valor']) AND $cliente['tipo_cliente'] == 1)){ echo "readonly"; } ?>  
                                    class="form-control"  autocomplete="on"
                                    id="retencion_value" name="retencion_value" 
                                    value="<?php if(isset($cliente['agente_retension_valor']) AND $cliente['tipo_cliente'] == 1 ){ echo $cliente['agente_retension_valor']; } ?>" />
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="credito" id="credito" value="1" 
                                        <?= isset($cliente['linea_credito']) && $cliente['linea_credito'] != NULL ? 'checked' : ''?>>
                                    <label class="control-label panel-admin-text" style="cursor: pointer;" for="lineaC_j">L&iacute;nea de Cr&eacute;dito</label>
                                    <input type="number"
                                           value="<?php if (isset($cliente['linea_credito'])) echo $cliente['linea_credito']; ?>"
                                           id="lineaC_j" name="lineaC_j" class="form-control"
                                           <?php if(!(isset($cliente['linea_credito']) AND $cliente['linea_credito'] != NULL)){ echo "readonly"; } ?> />
                                </div>
                            </div>
                                    <!--<div class="row">
                                        <div class="form-group">
                                            <div class="col-md-10">
                                             <label class="control-label panel-admin-text" >Direcci&oacute;n Google Maps</label>
                                                <input type="text"
                                                       value="<?php if (isset($cliente['direccion_maps']) AND $cliente['tipo_cliente'] == 1) echo  $cliente['direccion_maps']; ?>"
                                                       id="location2" name="location2" class="form-control" autocomplete="on" />
                                            </div>
                                            <div class="col-md-2">
                                            <label class="control-label panel-admin-text" style="color: white;">A</label><br>
                                                <span class="btn btn-default" id="mapaPJ" name="mapaPJ">Ver Mapa</span>
                                            </div>
                                        </div>
                                    </div>-->
                                        <div class="row" id="selectDuplicarJ" style="display: none;">
                                        <h4>Mas Informaci&oacute;n</h4>
                                            <div class="form-group">
                                                <div class="col-md-3"  style="text-align: right">
                                                    <label class="control-label panel-admin-text">A&ntilde;adir Campo</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <select  class="form-control" id="opcionDuplicarJ">
                                                        <option value="0">Seleccione</option>
                                                        <?php foreach($clientes_tipo_padre as $row){

                                                            echo "<option value=".$row['tipo_campo_padre_id'].">".$row['tipo_campo_padre_nombre']."</option>";

                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="btn btn-default" id="duplicarJ" value="">A&ntilde;adir</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="usPJ" style="width: 80%; height: 400px; top:10%; left:10%; visibility:hidden; position: absolute;" class="panel panel-default">
                                            <span style="float: right; color:red; cursor: pointer;" id="cerrarMPJ"><b>CERRAR</b></span>
                                            <div id="us2"  style="width: 100%; height:100%;">

                                            </div>
                                        </div>
                                        <!--Lat.: <input type="text" id="latitud2" required readonly
                                                     value="<?php if (isset($cliente['latitud'])) echo $cliente['latitud']; else echo '0'; ?>"/>
                                        Long.: <input type="text" id="longitud2" required readonly
                                                      value="<?php if (isset($cliente['longitud'])) echo $cliente['longitud']; else echo '0'; ?>"/>-->
                                        <script>
                                            // $('.selectpicker').selectpicker();
                                        </script>
                        </div>                                  
                    </form>
                </div>
                <div id="empresa" class="tab-pane fade">
                    <input type="hidden" id="new_from_venta" value="<?= isset($new_from_venta) ? $new_from_venta : 0?>">
                    <form name="formagregarE" onsubmit="return validarFrm(this)" action="<?= base_url() ?>cliente/guardar" method="post" id="formagregarE"
          enctype="multipart/form-data">
                        <input type="hidden" name="tipo_cliente" id="tipo_cliente" value="1">
                        <input type="hidden" name="tipo_iden" id="tipo_iden" value="2">
                        <input type="hidden" name="idClientes" id="idClientes" value="<?php if (isset($cliente['id_cliente'])) echo $cliente['id_cliente']; ?>">
                        <div class="row" style="display: none;">
                            <div class="form-group">
                                <div class="col-md-8">
                                </div>
                                <div class="col-md-2" id="abrir_imagen_empresa" style="position: absolute; top:0px; right:0px;">

                                        <?php  if (empty($images)){  ?>
                                            <img id="imgSalida_je0" data-count="0"
                                                 src="<?php echo $ruta . "recursos/img/la_foto.png" ?>" width="80%"
                                                 height="150">
                                            <span
                                                class="btn btn-default" style="position:relative;width:100px;" id="subirImg_je">Subir  <i
                                                    class="fa fa-file-image-o" aria-hidden="true"></i></span>
                                            <input style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;width:100%;height:100%;opacity: 0;"  type="file" onchange="asignar_imagen_je(0)" class="form-control input_imagen"
                                                   data-count="0" name="userfile_je[]" accept="image/*"
                                                   id="input_imagen_je0">

                                        <?php
                                        }
                                        if (isset($cliente['id_cliente']) and !empty($images)): ?>


                                            <?php $ruta_imagen = "clientes/" . $cliente['id_cliente'] . "/" ?>


                                            <?php

                                            $con_image = 0;
                                            foreach ($images as $img): ?>
                                                <div  style="text-align: center; margin-bottom: 20px;"
                                                     id="div_imagen_producto_je<?= $con_image ?>">

                                                    <a href="#" class="img_show"
                                                       data-src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                        <img alt='' width='100'  height='150'
                                                             src="<?php echo $ruta . $ruta_imagen . $img; ?>">
                                                    </a>
                                                    <br>
                                                    <a href="#"
                                                       onclick="borrar_img_je('<?= $cliente['id_cliente'] ?>','<?= $img ?>','<?= $con_image ?>')"
                                                       style="width: 150px; margin: 0;" id="eliminar_je" class="btn btn-raised btn-danger"><i

                                                            class="fa fa-trash-o"></i> Eliminar</a>
                                                </div>


                                                <?php
                                                $con_image++;
                                            endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">RUC</label>
                                <input type="text" name="ruc_j" value="<?php if (isset($cliente['identificacion'])) echo  $cliente['identificacion']; ?>" id="ruc_j" class="form-control ruc" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Razon Social</label>
                                <input type="text" name="razon_social_j" value="<?php if (isset($cliente['razon_social'])) echo  $cliente['razon_social']; ?>" id="razon_social_j" class="form-control"  />
                            </div> 
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php if($operacion == FALSE):?>
                                    <h5><?= (isset($cliente['tipo_cliente']) && $cliente['tipo_cliente'] == 1) ? 'Jur&iacute;dico': 'Natural'?></h5>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Dirección Principal</label>
                                <input type="text"  id="direccion_j" required="true"
                                class="form-control" name="direccion_j"
                                value="<?php if (isset($cliente['direccion'])) echo $cliente['direccion']; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label panel-admin-text">Representante</label>
                                <input type="text" name="apellidoPJuridico"
                               value="<?php if (isset($cliente['dni'])) echo  $cliente['dni']; ?>"
                               id="apellidoPJuridico" class="form-control" data-placeholder="Nombre"  />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Telefono</label>
                                <input type="text" name="telefono"
                                value="<?php if (isset($cliente['telefono1'])) echo  $cliente['telefono1']; ?>"
                                id="telefono" class="form-control" data-placeholder="Telefono"  />
                            </div>
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Correo Electr&oacute;nico</label>
                                <input type="email" name="correo"
                                value="<?php if (isset($cliente['email'])) echo  $cliente['email']; ?>"
                                id="correo" class="form-control" data-placeholder="Correo"  />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Grupo</label>
                                <select  id="grupo_id_juridico" name="grupo_id_juridico" required="true" class="chosen form-control">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($grupos as $grupo): ?>
                                        <option
                                            value="<?php echo $grupo['id_grupos_cliente'] ?>" <?php  if (isset($cliente['grupo_id']) and $cliente['grupo_id'] == $grupo['id_grupos_cliente']) echo 'selected' ?>><?= $grupo['nombre_grupos_cliente'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label panel-admin-text">Estado</label>
                                <select id="estatus_j" name="estatus_j" required="true" class="chosen form-control">

                                    <option value="1" <?php if (isset($cliente['cliente_status']) AND $cliente['tipo_cliente'] == 1 and $cliente['cliente_status']==1) echo "selected" ?>>ACTIVO</option>
                                    <option value="0" <?php if (isset($cliente['cliente_status']) AND $cliente['tipo_cliente'] == 1 and $cliente['cliente_status']==0) echo "selected" ?>>INACTIVO</option>

                                </select> 
                            </div>
                        </div>
                                <!--<div class="row">-
                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">Tipo de Cliente</label>
                                        <select id="tipo_cliente" name="tipo_cliente" class="form-control" 
                                            style="display: <?= $operacion == TRUE ? 'block' : 'none'?>;">
                                            <?php if(!isset($cliente['tipo_cliente'])): ?>
                                                <option value="">Seleccione</option>
                                            <?php endif; ?>
                                            <option value="0" <?= (isset($cliente['tipo_cliente']) && $cliente['tipo_cliente'] == 0) ? 'selected' : ''?>>
                                                Natural
                                            </option>
                                            <option value="1" <?= (isset($cliente['tipo_cliente']) && $cliente['tipo_cliente'] == 1) ? 'selected': ''?>>
                                                Jur&iacute;dico
                                            </option>
                                        </select>
                                    <?php if($operacion == FALSE):?>
                                        <h5><?= (isset($cliente['tipo_cliente']) && $cliente['tipo_cliente'] == 1) ? 'Jur&iacute;dico': 'Natural'?></h5>
                                    <?php endif;?>
                                </div>-->
                                <!--<div class="col-md-4">
                                <label class="control-label panel-admin-text">Razon Social</label>
                                    <input type="text" name="razon_social_j"
                                                   value="<?php if (isset($cliente['razon_social'])) echo  $cliente['razon_social']; ?>"
                                                   id="razon_social_j" class="form-control"  />
                                </div> -->
                                <!--<div class="col-md-2">
                                <label class="control-label panel-admin-text">Identificaci&oacute;n</label>
                                <select id="tipo_iden" name="tipo_iden" class="form-control">
                                    
                                    <?php if(isset($cliente['tipo_cliente'])): ?>
                                        <?php if($cliente['tipo_cliente'] == 0): ?>
                                            <option value="2" <?= isset($cliente['ruc']) && $cliente['ruc'] == 2 ? 'selected' : ''?>>RUC</option>
                                            <?php if($operacion == TRUE):?>
                                                <option value="1" <?= isset($cliente['ruc']) && $cliente['ruc'] == 1 ? 'selected' : ''?>>DNI</option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($cliente['tipo_cliente'] == 1): ?>
                                            <option value="2">RUC</option>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <option value="">Seleccione</option>
                                    <?php endif; ?>
                                </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">RUC/DNI</label>
                                    <input type="text" name="ruc_j" onkeydown="return soloNumeros(event);"
                                                   value="<?php if (isset($cliente['identificacion'])) echo  $cliente['identificacion']; ?>"
                                                   id="ruc_j"  class="form-control" />
                                </div> 
                            </div>
                            <br>-->
                            <!--<h4>Representante del Cliente</h4>-->

                            <!--<div class="row">
                                
                                <div class="col-md-3">
                                <label class="control-label panel-admin-text">Nombre</label>
                                    <input type="text" name="nombres"
                                           value="<?php if (isset($cliente['nombres'])) echo  $cliente['nombres']; ?>"
                                           id="nombres" class="form-control" data-placeholder="Nombre"  />
                                </div>

                                <div class="col-md-3">
                                <label class="control-label panel-admin-text">Apellido Paterno</label>
                                    <input type="text" name="apellido_paterno"
                                           value="<?php if (isset($cliente['apellido_paterno'])) echo  $cliente['apellido_paterno']; ?>"
                                           id="apellido_paterno" class="form-control" data-placeholder="Apellido paterno"  />
                                </div>

                                <div class="col-md-3">
                                <label class="control-label panel-admin-text">Apellido Materno</label>
                                    <input type="text" name="apellido_materno"
                                           value="<?php if (isset($cliente['apellido_materno'])) echo  $cliente['apellido_materno']; ?>"
                                           id="apellido_materno" class="form-control" data-placeholder="Apellido materno"  />
                                </div>


                                <div class="col-md-2">
                                <label class="control-label panel-admin-text">DNI</label>
                                    <input type="text" name="apellidoPJuridico"
                                                   value="<?php if (isset($cliente['dni'])) echo  $cliente['dni']; ?>"
                                                   id="apellidoPJuridico" class="form-control" data-placeholder="Nombre"  />
                                </div>
                            </div>
                            <br>-->
                        <br>
                        <button type="button" class="btn btn-info" onclick="vermas2()">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Ver m&aacute;s
                        </button>
                        <div id="vermas2" style="display: none">
                            <h4>Datos Adicionales</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">Departamento</label>
                                    <select name="estado_id" id="estado_id" required="true" class="chosen form-control"
                                            onchange="region.actualizardistritos();">
                                        <option value="">Seleccione</option>

                                            <?php foreach ($estados as $estado): ?>
                                                <option
                                                    value="<?php echo $estado['estados_id'] ?>" <?php if (isset($cliente['provincia']) and $estado['estados_id'] == $cliente['provincia']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                            <?php endforeach ?>

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">Provincia</label>
                                    <select name="ciudad_id" id="ciudad_id" required="true" class="chosen form-control"
                                            onchange="region.actualizarbarrio();">
                                        <option value="">Seleccione</option>
                                        <?php if (isset($cliente['id_cliente'])): ?>
                                            <?php foreach ($ciudades as $ciudad): ?>
                                                <option
                                                    value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($cliente['ciudad']) and $ciudad['ciudad_id'] == $cliente['ciudad']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text" >Distrito</label>
                                    <select name="distrito_id" id="distrito_id" required="true" class="chosen form-control">
                                        <option value="">Seleccione</option>
                                        <?php if (isset($cliente['id_cliente'])): ?>
                                            <?php foreach ($distritos as $distrito): ?>
                                                <option
                                                    value="<?php echo $distrito['id'] ?>" <?php if (isset($cliente['distrito']) and $distrito['id'] == $cliente['distrito']) echo 'selected' ?>><?= $distrito['nombre'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                <label class="control-label panel-admin-text">Genero</label>
                                    <select id="genero" name="genero" class="form-control">
                                        <option value=""></option>
                                        <?php if(!isset($cliente['genero'])){ ?>
                                        <?php $cliente['genero'] = '1'; ?>
                                        <?php } ?>
                                        <option value="1" <?=(isset($cliente['genero']) && $cliente['genero'] == '1' ? 'selected' : '')?>>Masculino</option>
                                        <option value="2" <?=(isset($cliente['genero']) && $cliente['genero'] == '2' ? 'selected' : '')?>>Femenino</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="retencion" id="retencion" value="1" 
                                        <?= isset($cliente['agente_retension']) && $cliente['agente_retension'] == 1 ? 'checked' : ''?>>
                                     <label class="control-label panel-admin-text" style="cursor: pointer;" for="retencion">Retenci&oacute;n?</label> 
                                   
                                    <input type="number" <?php if(!(isset($cliente['agente_retension_valor']) AND $cliente['tipo_cliente'] == 1)){ echo "readonly"; } ?>  
                                            class="form-control"  autocomplete="on"
                                           id="retencion_value" name="retencion_value" 
                                           value="<?php if(isset($cliente['agente_retension_valor']) AND $cliente['tipo_cliente'] == 1 ){ echo $cliente['agente_retension_valor']; } ?>" />
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="credito" id="credito" value="1" 
                                        <?= isset($cliente['linea_credito']) && $cliente['linea_credito'] != NULL ? 'checked' : ''?>>
                                    <label class="control-label panel-admin-text" style="cursor: pointer;" for="lineaC_j">L&iacute;nea de Cr&eacute;dito</label>
                                    <input type="number"
                                           value="<?php if (isset($cliente['linea_credito'])) echo $cliente['linea_credito']; ?>"
                                           id="lineaC_j" name="lineaC_j" class="form-control"
                                           <?php if(!(isset($cliente['linea_credito']) AND $cliente['linea_credito'] != NULL)){ echo "readonly"; } ?> />
                                </div>
                            </div>
                                <!--<div class="row">
                                    <div class="form-group">
                                        <div class="col-md-10">
                                         <label class="control-label panel-admin-text" >Direcci&oacute;n Google Maps</label>
                                            <input type="text"
                                                   value="<?php if (isset($cliente['direccion_maps']) AND $cliente['tipo_cliente'] == 1) echo  $cliente['direccion_maps']; ?>"
                                                   id="location2" name="location2" class="form-control" autocomplete="on" />
                                        </div>
                                        <div class="col-md-2">
                                        <label class="control-label panel-admin-text" style="color: white;">A</label><br>
                                            <span class="btn btn-default" id="mapaPJ" name="mapaPJ">Ver Mapa</span>
                                        </div>
                                    </div>
                                </div>-->
                                    <br>
                                    <div class="row" id="selectDuplicarJ" style="display: none;">
                                    <h4>Mas Informaci&oacute;n</h4>
                                        <div class="form-group">
                                            <div class="col-md-3"  style="text-align: right">
                                                <label class="control-label panel-admin-text">A&ntilde;adir Campo</label>
                                            </div>
                                            <div class="col-md-4">
                                                <select  class="form-control" id="opcionDuplicarJ">
                                                    <option value="0">Seleccione</option>
                                                    <?php foreach($clientes_tipo_padre as $row){

                                                        echo "<option value=".$row['tipo_campo_padre_id'].">".$row['tipo_campo_padre_nombre']."</option>";

                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="btn btn-default" id="duplicarJ" value="">A&ntilde;adir</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="usPJ" style="width: 80%; height: 400px; top:10%; left:10%; visibility:hidden; position: absolute;" class="panel panel-default">
                                        <span style="float: right; color:red; cursor: pointer;" id="cerrarMPJ"><b>CERRAR</b></span>
                                        <div id="us2"  style="width: 100%; height:100%;">

                                        </div>
                                    </div>
                                    <!--Lat.: <input type="text" id="latitud2" required readonly
                                                 value="<?php if (isset($cliente['latitud'])) echo $cliente['latitud']; else echo '0'; ?>"/>
                                    Long.: <input type="text" id="longitud2" required readonly
                                                  value="<?php if (isset($cliente['longitud'])) echo $cliente['longitud']; else echo '0'; ?>"/>-->
                                    <script>
                                        // $('.selectpicker').selectpicker();
                                    </script>   
                                </div>                                 
                    </form>
                </div>   
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="" class="btn btn-primary" onclick="guardarcliente(); ">Guardar</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button> <!-- grupo.guardar();; -->
        </div>
    </div>
    <div class="modaloader vertical">
        <div class="col-xs-12 text-center">
            <img src="<?php echo $ruta; ?>recursos/img/circles.svg">
        </div>
    </div>
</div>
<script src="<?php echo $ruta; ?>recursos/js/cliente.js"></script>
<script type="text/javascript">
$( "input#ruc_j.dni" ).keyup(function() {
    var input=$(this);
    DNI=$(this).val();
    if (DNI.length==8) {
        var formData = new FormData();
        formData.append('DNI', DNI);
        $.ajax({
            url: '<?= base_url()?>cliente/getDatosFromAPI_Reniec',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('div.modaloader').addClass('see');
            },
            success: function(data){
                console.log(data);
                if (data=='false') {
                    input.addClass('errorAPI');
                    $('#formagregar input#nombres').val('');
                    $('#formagregar input#apellido_paterno').val('');
                }else{
                    input.removeClass('errorAPI');
                    var obj = $.parseJSON(data);
                    var Nombre  = obj['Nombre'];
                    var Paterno = obj['Paterno'];
                    var Materno = obj['Materno'];
                    $('#formagregar input#nombres').val(Nombre);
                    $('#formagregar input#apellido_paterno').val(Paterno+' '+Materno);
                }
                $('div.modaloader').removeClass('see');
            },
            error: function(data){
              console.log('Error Ajax Peticion');
              console.log(data);
            }
        });
    }else{
       input.addClass('errorAPI');
        $('#formagregar input#nombres').val('');
        $('#formagregar input#apellido_paterno').val('');
    }
});
$( "input#ruc_j.ruc" ).keyup(function() {
    var input=$(this);
    RUC=$(this).val();
    if (RUC.length==11) {
        var formData = new FormData();
        formData.append('RUC', RUC);
        $.ajax({
            url: '<?= base_url()?>cliente/getDatosFromAPI_Sunac',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('div.modaloader').addClass('see');
            },
            success: function(data){
                console.log(data);
                if (data=='false') {
                    input.addClass('errorAPI');
                    $('#formagregarE input#razon_social_j').val('');
                    $('#formagregarE input#telefono').val('');
                    $('#formagregarE input#direccion_j').val('');
                }else{
                    input.removeClass('errorAPI');
                    var obj = $.parseJSON(data);
                    var RazonSocial = obj['RazonSocial'];
                    var Telefono    = obj['Telefono'];
                    var Direccion   = obj['Direccion'];
                    $('#formagregarE input#razon_social_j').val(RazonSocial);
                    $('#formagregarE input#telefono').val(Telefono);
                    $('#formagregarE input#direccion_j').val(Direccion);
                }
                $('div.modaloader').removeClass('see');
            },
            error: function(data){
              console.log('Error Ajax Peticion');
              console.log(data);
            }
        });
    }else{
       input.addClass('errorAPI');
        $('#formagregarE input#razon_social_j').val('');
        $('#formagregarE input#telefono').val('');
        $('#formagregarE input#direccion_j').val('');
    }
});
    region.actualizardistritos();
</script>