<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Facturaci&oacute;n</li>
    <li><a href="">Configurar Emisor</a></li>
</ul>


<div class="block">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <?= form_open_multipart(base_url() . 'facturacion/save_emisor',
                array('id' => 'formguardar', 'method' => 'post', 'class' => 'form-horizontal')) ?>
            <h3>Datos de la Empresa Emisora</h3>
            <div class="form-group">
                <label class="col-md-4 control-label" for="ruc">RUC del Emisor</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="ruc" name="ruc"
                           value="<?= isset($emisor) ? $emisor->ruc : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="razon_social">Razon Social</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="razon_social" name="razon_social"
                           value="<?= isset($emisor) ? $emisor->razon_social : '' ?>" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="nombre_comercial">Nombre Comercial</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="nombre_comercial" name="nombre_comercial" readonly
                           value="<?= isset($emisor) ? $emisor->nombre_comercial : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="direccion">Direcci&oacute;n</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="direccion" name="direccion" readonly
                           value="<?= isset($emisor) ? $emisor->direccion : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="departamento">Departamento</label>
                <div class="col-md-8">
                    <select class="form-control" id="departamento" name="departamento">
                        <option></option>
                        <?php foreach ($departamentos as $d): ?>
                            <option value="<?= $d->estados_id ?>"
                                <?= isset($emisor) && $emisor->departamento_id == $d->estados_id ? 'selected' : '' ?>
                            >
                                <?= $d->estados_nombre ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="provincia">Provincia</label>
                <div class="col-md-8">
                    <select class="form-control" id="provincia" name="provincia">
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="distrito">Distrito</label>
                <div class="col-md-8">
                    <select class="form-control" id="distrito" name="distrito">
                        <option></option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="codigo_ubigeo">Codigo UBIGEO</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="codigo_ubigeo" name="codigo_ubigeo" readonly
                           value="<?= isset($emisor) ? $emisor->ubigeo : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="distrito">Moneda</label>
                <div class="col-md-8">
                    <select class="form-control" id="moneda" name="moneda">
                        <option value="PEN">SOLES</option>
                    </select>
                </div>
            </div>

            <h3>Datos de Acceso SOL</h3>
            <div class="form-group">
                <label class="col-md-4 control-label" for="user_sol">Usuario SOL</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="user_sol" name="user_sol"
                           value="<?= isset($emisor) ? $emisor->user_sol : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="pass_sol">Contraseña SOL</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="pass_sol" name="pass_sol"
                           value="<?= isset($emisor) ? $emisor->pass_sol : '' ?>">
                </div>
            </div>

            <h3>Certificado Digital</h3>
            <div class="form-group">
                <label class="col-md-4 control-label" for="certificado">Certificado Digital (.pfx)</label>
                <div class="col-md-8">
                    <input type="file" class="form-control" name="certificado">
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-4 control-label" for="pass_sign">Contraseña del certificado</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" id="pass_sign" name="pass_sign"
                           value="<?= isset($emisor) ? $emisor->pass_sign : '' ?>">
                </div>
            </div>
            <br>
            <div class="text-center">

                <button type="button" id="btn_save" class="btn btn-primary">Guardar</button>
            </div>
            </form>

        </div>
    </div>
</div>


<script src="<?= base_url('recursos/js/jquery.maskedinput.min.js') ?>"></script>
<script type="text/javascript">

    var departamentos = [];
    var provincias = [];
    var distritos = [];

    <?php foreach ($departamentos as $d):?>
    departamentos.push({
        id: <?= $d->estados_id?>,
        nombre: '<?= $d->estados_nombre?>'
    });
    <?php endforeach;?>

    <?php foreach ($provincias as $p):?>
    provincias.push({
        id: <?= $p->ciudad_id?>,
        departamento_id: <?= $p->estado_id?>,
        nombre: '<?= $p->ciudad_nombre?>'
    });
    <?php endforeach;?>

    <?php foreach ($distritos as $d):?>
    distritos.push({
        id: <?= $d->id?>,
        provincia_id: <?= $d->ciudad_id?>,
        nombre: '<?= $d->nombre?>',
        ubigeo: '<?= $d->idUbigeo?>'
    });
    <?php endforeach;?>

    $(function () {

        <?php if(isset($emisor)):?>
        setTimeout(function () {
            $('#departamento').trigger('change');

            $('#provincia').val('<?= $emisor->provincia_id?>');
            $('#provincia').trigger('change');

            $('#distrito').val('<?= $emisor->distrito_id?>');
            $('#distrito').trigger('change');
        }, 100)
        <?php endif;?>

        $('#ruc').mask('99999999999');

        $('#btn_save').on('click', function () {

            save_emisor();

        });

        $('#ruc').on('keyup', function (e) {
            var input = $(this).val().replace(/_/gi, '');
            $('#razon_social').val('');
            $('#nombre_comercial').val('');
            $('#diireccion').val('');
            if (input.length == 11 && ((e.which >= 48 && e.which <= 57) || (e.which >= 96 && e.which <= 105) || (e.which == 13))) {

                $("#barloadermodal").modal('show');

                $.ajax({
                    url: '<?= base_url('facturacion/consultarRuc')?>',
                    data: {ruc: input},
                    type: 'POST',
                    success: function (data) {
                        console.log(data)
                        if (data.emisor != undefined) {
                            $('#razon_social').val(data.emisor.RazonSocial);
                            $('#nombre_comercial').val(data.emisor.NombreComercial);
                            $('#direccion').val(data.emisor.Direccion);
                        } else {
                            show_msg('warning', 'No se ha podido encontrar el RUC');
                        }
                    },
                    error: function () {
                        show_msg('danger', 'Error inesperado');
                    },
                    complete: function (data) {
                        $("#barloadermodal").modal('hide');
                        setTimeout(function () {
                            $('#ruc').focus();
                        }, 200);
                    }
                });

            }
        });

        $('#departamento').on('change', function () {
            var departamento = $(this);
            var provincia = $('#provincia');
            var distrito = $('#distrito');

            $('#codigo_ubigeo').val('');
            provincia.html('<option></option>');
            distrito.html('<option></option>');

            for (var i = 0; i < provincias.length; i++) {
                if (provincias[i].departamento_id == departamento.val())
                    provincia.append('<option value="' + provincias[i].id + '">' + provincias[i].nombre + '</option>');
            }
        });

        $('#provincia').on('change', function () {
            var provincia = $(this);
            var distrito = $('#distrito');

            $('#codigo_ubigeo').val('');
            distrito.html('<option></option>');

            for (var i = 0; i < distritos.length; i++) {
                if (distritos[i].provincia_id == provincia.val())
                    distrito.append('<option value="' + distritos[i].id + '" data-ubigeo="' + distritos[i].ubigeo + '">' + distritos[i].nombre + '</option>');
            }
        });

        $("#distrito").on('change', function () {
            var ubigeo = $('#codigo_ubigeo');
            ubigeo.val('');

            if ($(this).val() != "")
                ubigeo.val($('#distrito option:selected').attr('data-ubigeo'));
        })
    });

    function save_emisor() {
        var ruc = $('#ruc').val();
        if (ruc.length != 11) {
            show_msg('warning', 'Ruc no valido');
            return false;
        }

        if ($('#razon_social').val() == '') {
            show_msg('warning', 'La razon social es obligatoria');
            return false;
        }

        if ($('#codigo_ubigeo').val() == '') {
            show_msg('warning', 'Codigo ubigeo obligatorio');
            return false;
        }

        if ($('#user_sol').val() == '') {
            show_msg('warning', 'Usuario SOL obligatorio');
            return false;
        }

        if ($('#pass_sol').val() == '') {
            show_msg('warning', 'Contrase&ntilde;a SOL obligatorio');
            return false;
        }

        if ($('#pass_sign').val() == '') {
            show_msg('warning', 'Contrase&ntilde;a del certificado es obligatorio');
            return false;
        }

        var formData = new FormData($("#formguardar")[0]);
        $("#barloadermodal").modal('show');

        $.ajax({
            url: '<?= base_url()?>facturacion/save_emisor',
            type: "post",
            dataType: "json",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                show_msg('success', 'La configuracion del emisor se ha guardado correctamente');

                $.ajax({
                    url: '<?= base_url('facturacion/emisor')?>',
                    cache: false,
                    success: function (data) {
                        $("#barloadermodal").modal('hide');
                        $(".modal-backdrop").remove();
                        $('#page-content').html(data);
                    }
                });

            },
            error: function () {
                show_msg('danger', 'Error inesperado');
                $("#barloadermodal").modal('hide');

            }

        });
    }

</script>

