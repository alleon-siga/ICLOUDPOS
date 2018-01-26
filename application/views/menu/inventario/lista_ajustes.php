
<br>
    <table class="table dataTable table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
        <tr>

            <th style="text-align: center">N&uacute;mero</th>
            <th style="text-align: center">Fecha</th>
            <th style="text-align: center">Nombre</th>
            <th style="text-align: center">Cantidad Prod.</th>
            <th style="text-align: center">Acciones</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($ajustes) > 0) {

            foreach ($ajustes as $ajuste) {
                ?>
                <tr>

                    <td style="text-align: center"><?= $ajuste->id_ajusteinventario ?></td>
                    <td style="text-align: center"><?= date('d-m-Y H:i:s', strtotime($ajuste->fecha)) ?></td>
                    <td style="text-align: center"><?= $ajuste->descripcion ?></td>
                    <td style="text-align: center"><?= $ajuste->cantidad ?></td>

                    <td style="text-align: center">
                        <div class="btn-group">
                            <?php

                            echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver Detalle" data-original-title="Ver Detalle"
                                            href="#" onclick="ver(' . $ajuste->id_ajusteinventario . ');">'; ?>
                            <i class="fa fa-search"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            <?php }
        } ?>
        </tbody>
    </table>

<div class="modal fades" id="verajuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script type="text/javascript">


</script>