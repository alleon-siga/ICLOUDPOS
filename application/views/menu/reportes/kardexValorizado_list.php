<input type="hidden" id="local_selected" value="">
<div class="table-responsive">

    <table class="table table-striped dataTable table-bordered tableStyle" id="table">
        <thead>
        <tr>
            <th><?= getCodigoNombre() ?></th>
            <?= $barra_activa->activo == 1 ? '<th>Codigo Barra</th>' : '' ?>
            <th>Nombre</th>
            <th>Cantidad</th>
            <th>Fracción</th>
            <th>Cantidad Minima</th>
            <?php if ($local_id == ""): ?>
                <th>Ubicaci&oacute;n</th>
            <?php endif; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= getCodigoValue(sumCod($p->producto_id), $p->producto_ci) ?></td>
                <?= $barra_activa->activo == 1 ? '<td>' . $p->barra . '</td>' : '' ?>
                <td><?= $p->producto_nombre ?></td>
                <?php if($p->producto_cualidad=='MEDIBLE'){ ?>
                    <td><?= bcdiv($p->cantidad,'1',0) . " " . $p->unidad_max_abr ?></td>
                <?php }else{ ?>
                    <td><?= $p->cantidad . " " . $p->unidad_max_abr ?></td>
                <?php } ?>
                <?php if($p->producto_cualidad=='MEDIBLE'){ ?>
                    <td><?= bcdiv($p->fraccion,'1',0) . " " . $p->unidad_min_abr ?></td>
                <?php }else{ ?>
                    <td><?= $p->fraccion . " " . $p->unidad_min_abr ?></td>    
                <?php } ?>
                
                <td><?= $p->cantidad_min . " " . $p->unidad_min_abr ?></td>
                <?php if ($local_id == ""): ?>
                    <td><?= $p->local_nombre ?></td>
                <?php endif; ?>
                <td>
                    <a href="#" onclick="ver_detalle('<?= $p->producto_id ?>');">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script>
    $(function () {
        TablesDatatables.init(0, 'desc');
    });

    function ver_detalle(producto_id) {

        var mes, year, dia_min, dia_max;
        var local_id = $("#local_id").val();
        if ($("#mes").val() != "") {
            mes = $("#mes").val();
        }
        else
            return false;

        if ($("#year").val() != "") {
            year = $("#year").val();
        }
        else
            return false;

        if ($("#dia_min").val() != "") {
            dia_min = $("#dia_min").val();
        }
        else
            return false;

        if ($("#dia_max").val() != "") {
            dia_max = $("#dia_max").val();
        }
        else
            return false;

        $('#detalle_modal').html($('#load_div').html());
        $('#detalle_modal').modal('show');
        $("#detalle_modal").load('<?= base_url()?>reporte/get_kardex/' + producto_id + '/' + local_id + '/' + mes + '/' + year + '/' + dia_min + '/' + dia_max);
    }
</script>