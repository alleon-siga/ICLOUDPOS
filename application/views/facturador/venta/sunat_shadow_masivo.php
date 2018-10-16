<div class="modal-dialog" style="width: 40%;">
    <div class="modal-content">
        <div class="modal-header " >
            <h3>FACTURAR</h3>
        </div>
        <div class="modal-body">
            <h3>Deseas Facturar todos los comprobantes?</h3>
        </div>
        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">
                    <div class="col-md-12">
                        <input type="button" class='btn btn-success' onclick="facturar()"  value="SI">
                        <input type="button" class='btn btn-danger' onclick="cerrarmodal()" value="Cerrar" >
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    function cerrarmodal() {
        $('#dialog_sunat_shadow_masivo').modal('hide');
        $(".modal-backdrop").hide();
        get_ventas();
    }
    
    var ventash = [];
<?php foreach ($ventas as $vs): ?>
    ventash.push({
        id :<?= $vs->id ?>
        });
<?php endforeach; ?>
    function facturar() {
        for (var i = 0; i < ventash.length; i++) {
            $.ajax({
            url: $('#ruta').val() + 'facturador/venta/facturar_venta/',
            type: 'POST',
            data: {'id_shadow': ventash[i].id},

            success: function (data) {
                $('#dialog_sunat_shadow_masivo').modal('hide');
                $(".modal-backdrop").hide();
                get_ventas();
            },
            error: function (resp) {
                alert(resp)
            }
        });
        }
    }
</script>