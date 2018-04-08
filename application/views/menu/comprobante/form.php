<form name="form_comprobante" action="<?= base_url() ?>comprobante/guardar" method="post" id="form_comprobante">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?= isset($comprobante) ? 'Editar Comprobante' : 'Nuevo Comprobante' ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden"
                       name="id"
                       id="id"
                       required="true"
                       value="<?= isset($comprobante) ? $comprobante->id : '' ?>">
                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Nombre</label>
                    <div class="col-md-6">
                        <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                required="true"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->nombre : '' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Serie</label>
                    <div class="col-md-6">
                        <input
                                type="text"
                                name="serie"
                                id="serie"
                                required="true"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->serie : '' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Desde</label>
                    <div class="col-md-6">
                        <input
                                type="number"
                                name="desde"
                                id="desde"
                                required="true"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->desde : '' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Hasta</label>
                    <div class="col-md-6">
                        <input
                                type="number"
                                name="hasta"
                                id="hasta"
                                required="true"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->hasta : '' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Longitud</label>
                    <div class="col-md-6">
                        <input
                                type="number"
                                name="longitud"
                                id="longitud"
                                required="longitud"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->longitud : '' ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">N&uacute;mero actual</label>
                    <div class="col-md-6">
                        <input
                                type="number"
                                name="actual"
                                id="actual"
                                required="numero actual"
                                class="form-control"
                                value="<?= isset($comprobante) ? $comprobante->num_actual : '0' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-md-offset-2">Estado</label>
                    <div class="col-md-6">
                        <select id="estado" name="estado" class="form-control">
                            <option value="1" <?= isset($comprobante) && $comprobante->estado == 1 ? 'selected' : '' ?>>
                                Activo
                            </option>
                            <option value="2" <?= isset($comprobante) && $comprobante->estado == 2 ? 'selected' : '' ?>>
                                Inactivo
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="guardar_form">
                    Guardar
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>

<script>

    $(function () {

        $('#guardar_form').on('click', function () {
            if ($('#nombre').val() == '') {
                show_msg('warning', 'El campo nombre es requerido');
                return false;
            }

            if ($('#serie').val() == '') {
                show_msg('warning', 'El campo serie es requerido');
                return false;
            }

            if ($('#desde').val() == '') {
                show_msg('warning', 'El campo desde serie es requerido');
                return false;
            }

            if ($('#hasta').val() == '') {
                show_msg('warning', 'El campo hasta serie es requerido');
                return false;
            }

            if ($('#longitud').val() == '') {
                show_msg('warning', 'El campo longitud serie es requerido');
                return false;
            }

            if ($('#actual').val() == '') {
                show_msg('warning', 'El campo longitud numero actual es requerido');
                return false;
            }            

            if ($('#desde').val() >= $('#hasta').val()) {
                show_msg('warning', 'El campo desde tiene que ser menor que el hasta');
                return false;
            }

            $('#load_div').show();

            $.ajax({
                url: '<?= base_url()?>comprobante/guardar',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                dataType: 'json',
                data: $("#form_comprobante").serialize(),
                success: function (data) {
                    if (data.success != undefined) {
                        show_msg('success', data.success);
                        $('#form_modal').modal('hide');
                        $.ajax({
                            url: '<?= base_url()?>comprobante',
                            success: function (data) {
                                $('#load_div').hide();
                                $(".modal-backdrop").remove();
                                $('#page-content').html(data);
                            }
                        });
                    }
                    else {
                        show_msg('danger', data.error);
                        $('#load_div').hide();
                    }

                },
                error: function (data) {
                    alert('Error inesperado');
                }

            });
        });

    });


</script>
