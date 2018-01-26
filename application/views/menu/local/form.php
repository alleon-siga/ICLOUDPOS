<form name="formagregar" action="<?= base_url() ?>local/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($local['int_local_id'])) echo $local['int_local_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Local</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="local_nombre" id="local_nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($local['local_nombre'])) echo $local['local_nombre']; ?>">
                        </div>

                    </div>

                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Pais</label>
                        </div>
                        <div class="col-md-10">
                            <select name="pais_id" id="id_pais" required="true" class="form-control"
                                    onchange="region.actualizarestados();">
                                <option value="">Seleccione</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option
                                        value="<?php echo $pais['id_pais'] ?>" <?php if (isset($local['int_local_id']) and $pais['id_pais'] == $spais['id_pais']) echo 'selected' ?>><?= $pais['nombre_pais'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Provincia</label>
                        </div>
                        <div class="col-md-10">

                            <select name="estados_id" id="estado_id" required="true" class="form-control"
                                    onchange="region.actualizardistritos();">
                                <option value="">Seleccione</option>
                                <?php if (isset($local['int_local_id'])): ?>
                                    <?php foreach ($estados as $estado): ?>
                                        <option
                                            value="<?php echo $estado['estados_id'] ?>" <?php if (isset($local['int_local_id']) and $estado['estados_id'] == $sestado['estados_id']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Ciudad</label>
                        </div>
                        <div class="col-md-10">

                            <select name="ciudad_id" id="ciudad_id" required="true" class="form-control"
                                    onchange="region.actualizarbarrio();">
                                <option value="">Seleccione</option>
                                <?php if (isset($local['int_local_id'])): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option
                                            value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($local['int_local_id']) and $ciudad['ciudad_id'] == $sciudad['ciudad_id']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Distrito</label>
                        </div>
                        <div class="col-md-10">
                            <select name="distrito_id" id="distrito_id" required="true" class="form-control">
                                <option value="">Seleccione</option>
                                <?php if (isset($local['int_local_id'])): ?>
                                    <?php foreach ($distritos as $distrito): ?>
                                        <option
                                            value="<?php echo $distrito['id'] ?>" <?php if (isset($local['int_local_id']) and $distrito['id'] == $sdistrito['id']) echo 'selected' ?>><?= $distrito['nombre'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Dirección</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="direccion" id="direccion" required="true"
                                   class="form-control"
                                   value="<?php if (isset($local['direccion'])) echo $local['direccion']; ?>">
                        </div>

                    </div>

                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Telefono</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="telefono" id="telefono" required="true"
                                   class="form-control"
                                   value="<?php if (isset($local['telefono'])) echo $local['telefono']; ?>">
                        </div>

                    </div>

                </div>


                <!--<div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Estado</label>
                        </div>
                        <div class="col-md-10">
                            <select name="local_status" id="local_status" required="true" class="form-control">
                                <option
                                    value="1" <?= isset($local['local_status']) && $local['local_status'] == '1' ? 'selected' : '' ?>>
                                    SI
                                </option>
                                <option
                                    value="0" <?= isset($local['local_status']) && $local['local_status'] == '0' ? 'selected' : '' ?>>
                                    NO
                                </option>
                            </select>
                        </div>
                    </div>
                </div>-->


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Principal</label>
                        </div>
                        <div class="col-md-10">
                            <select name="principal" id="principal" required="true" class="form-control">
                                <option value="1"  <?= isset($local['principal']) && $local['principal'] == '1' ? 'selected' : ''?>>SI</option>
                                <option value="0" <?= isset($local['principal']) && $local['principal'] == '0' ? 'selected' : 'selected="selected"'?>>NO</option>
                            </select>
                        </div>
                    </div>
                </div>


            </div>


            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
        <!-- /.modal-content -->
</form>