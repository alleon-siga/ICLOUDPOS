<form name="formagregar" action="<?= base_url() ?>metodosdepago/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($metodospago['id_metodo'])) echo $metodospago['id_metodo']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo M&eacute;todo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_metodo" id="nombre_metodo" required="true"
                                   class="form-control"
                                   value="<?php if (isset($metodospago['nombre_metodo'])) echo $metodospago['nombre_metodo']; ?>">
                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="marca.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>