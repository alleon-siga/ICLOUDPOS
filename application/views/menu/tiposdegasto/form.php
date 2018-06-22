<form name="formagregar" action="<?= base_url() ?>tiposdegasto/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($tiposgasto['id_tipos_gasto'])) echo $tiposgasto['id_tipos_gasto']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Tipo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <label>Nombre</label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="nombre_tipos_gasto" id="nombre_tipos_gasto" required="true"
                               class="form-control"
                               value="<?php if (isset($tiposgasto['nombre_tipos_gasto'])) echo $tiposgasto['nombre_tipos_gasto']; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>Grupo</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control" name="grupo_gasto" id="grupo_gasto">
                            <option value="">Seleccione</option>
                           <?php
                                foreach ($grupogasto as $grupo):
                                    $selected = "";    
                                    if(isset($tiposgasto['id_grupo_gastos'])){
                                        if($tiposgasto['id_grupo_gastos'] == $grupo['id_grupo_gastos']){
                                            $selected = "selected";
                                        }
                                    }
                            ?>
                                <option value="<?= $grupo['id_grupo_gastos'] ?>" <?= $selected ?>><?= $grupo['nom_grupo_gastos'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>Tipo</label>
                    </div>
                    <div class="col-md-10">
                        <select class="form-control" name="tipo_tipos_gasto" id="tipo_tipos_gasto">
                            <option value="">Seleccione</option>
                        <?php
                        $slt1 = $slt2 = '';
                            if(isset($tiposgasto)){
                                if($tiposgasto['tipo_tipos_gasto']=='0'){
                                    $slt1 = "selected";
                                }elseif($tiposgasto['tipo_tipos_gasto']=='1'){
                                    $slt2 = "selected";
                                }
                            }
                        ?>
                            <option value="0" <?= $slt1 ?>>Variable</option>
                            <option value="1" <?= $slt2 ?>>Fijo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnGuardarTipoGasto" class="btn btn-primary" onclick="grupo.guardar('tipo_gasto')" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>