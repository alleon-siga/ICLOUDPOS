<form name="formagregar" action="<?= base_url() ?>categorias/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="id" required="true"
           value="<?php if (isset($categoria['id'])) echo $categoria['id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Categoria</h4>
            </div>
            <div class="modal-body">

              <div class="row">
                  <div class="form-group">
                     <div class="col-md-4">
                          <label>Tipo </label>
                      </div>
                      <div class="col-md-8">
                          <select id="tipo_id" name="tipo_id" class="form-control">
                              <option value="">Seleccione</option>
                              <?php foreach ($tipo as $tipos): ?>
                                  <option
                                      value="<?php echo $tipos['id'] ?>"
                                      <?php if (isset($categoria['pl_tipo_id']) and $categoria['pl_tipo_id'] == $tipos['id']) echo 'selected' ?>>
                                      <?= $tipos['tipo'] ?>
                                  </option>
                              <?php endforeach ?>
                          </select>
                      </div>
                  </div>
              </div>

              <div class="row">
                  <div class="form-group">
                      <div class="col-md-4">
                          <label>Descripcion</label>
                      </div>
                      <div class="col-md-8">
                          <input type="text" name="valor" id="valor" required="true"
                                 class="form-control"
                                 value="<?php if (isset($categoria['valor'])) echo $categoria['valor']; ?>">
                      </div>

                  </div>
              </div>
          </div>

            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
            <!-- /.modal-content -->
</form>
