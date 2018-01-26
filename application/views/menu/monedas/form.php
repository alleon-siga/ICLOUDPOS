<form name="formagregar" action="<?= base_url() ?>monedas/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($monedas['id_moneda'])) echo $monedas['id_moneda']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Moneda</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Moneda</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_moneda" id="nombre_moneda" required="true"
                                   class="form-control"
                                   value="<?php if (isset($monedas['nombre'])) echo $monedas['nombre']; ?>">
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Simbolo</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="simbolo" id="simbolo" required="true"
                                   class="form-control"
                                   value="<?php if (isset($monedas['simbolo'])) echo $monedas['simbolo']; ?>">
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Pais</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="pais" id="pais" required="true"
                                   class="form-control"
                                   value="<?php if (isset($monedas['pais'])) echo $monedas['pais']; ?>">
                        </div>

                    </div>
                </div>


                <?php if ($monedas['id_moneda'] != MONEDA_DEFECTO): ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label>Estado</label>
                            </div>
                            <div class="col-md-10">
                                <select id="status_moneda" name="status_moneda" class="form-control">
                                    <option value="1" <?= isset($monedas['status_moneda']) && $monedas['status_moneda'] == 1 ? "selected" : '' ?> >
                                        Activa
                                    </option>
                                    <option value="0" <?= isset($monedas['status_moneda']) && $monedas['status_moneda'] == 0 ? "selected" : '' ?>>
                                        Inactiva
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h4>Tasa de conversi&oacute;n a <?= $moneda_defecto->nombre ?></h4>
                    <div class="row">
                        <div class="form-group">

                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-addon nuevo_simbolo"><?php if (isset($monedas['simbolo'])) echo $monedas['simbolo']; ?></div>
                                    <input type="number" name="un_" id="un_" required="true"
                                           class="form-control"
                                           value="1" readonly>
                                </div>
                            </div>

                            <div class="col-md-4" style="text-align: center;"><label>Equivale a</label></div>

                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-addon"><?= $moneda_defecto->simbolo ?></div>
                                    <input type="number" name="tasa_soles" id="tasa_soles" required="true"
                                           class="form-control"
                                           value="<?php if (isset($monedas['tasa_soles'])) echo $monedas['tasa_soles']; ?>">
                                </div>
                            </div>

                            <div class="col-md-2" style="display: none;">
                                <select id="operacion" name="operacion" class="form-control">
                                    <option value="/" <?php if (isset($monedas['ope_tasa'])) echo "selected"; ?> >
                                        Dividir
                                    </option>
                                    <option value="*" <?php if (isset($monedas['ope_tasa'])) echo "selected"; ?>>
                                        Multiplicar
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>


                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="marca.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>

<script>
    $(function () {
        $('#simbolo').on('keyup', function () {
            $('.nuevo_simbolo').html($(this).val());
        })
    })
</script>
