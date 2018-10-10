<?php $ruta = base_url(); ?>
<?php $md = get_moneda_defecto() ?>
<style>
    table tr th{
        background-color: #2d2d2d !important;
        color: white !important;
    }
    table tr th, table tr td{
        border: 1px solid !important; 
    }
    table tr:hover {
        color: #000 !important;
        font-weight: bold !important;
    }
</style>
<div class="table-responsive">
    <table class="table dataTable table-bordered no-footer tableStyle" id="tabla">
        <thead>
            <tr>
                <td style="border-style: hidden;" colspan="5"></td>
                <td style="border-style: hidden;" align="center">
                    <input class="form-control" type="number" value="" id="txtAllCostoContMn" name="" style="width: 80px;">
                </td>
                <td style="border-style: hidden;"></td>
                <td style="border-style: hidden;" align="center">
                    <input class="form-control" type="number" value="" id="txtAllCostoContMe" name="" style="width: 80px;">
                </td>
                <td style="border-style: hidden;">
                    <input type="number" name="" id="txtAllTipoCambio" value="<?= $moneda['tasa_soles'] ?>" class="form-control" style="width: 80px;">
                </td>
                <td style="border-style: hidden;" align="center">
                    <input class="form-control" type="number" value="" id="txtAllPorcPrecio" name="" style="width: 80px;">
                </td>
                <td style="border-style: hidden;" colspan="2"></td>
            </tr>
            <tr>
                <th width="10%">Codigo</th>
                <th width="30%">Producto</th>
                <th width="10%">Marca</th>
                <th width="10%">Unidad</th>
                <th width="5%">Costo <br>Real S/</th>
                <th width="5%">Costo <br>Contable S/</th>
                <th width="5%">Costo <br>Real $</th>
                <th width="5%">Costo <br>Contable $</th>
                <th width="5%">Tipo <br>Cambio</th>
                <th width="5%">% Precio</th>
                <th width="5%">Precio <br>Comp. S/</th>
                <th width="5%">Precio <br>Comp. $</th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($lists as $list):
            $porcentaje_utilidad = $list->porcentaje_utilidad;
            $contable_costo_mn = $list->contable_costo_mn;
            $tipo_cambio = $list->tipo_cambio;
            $preCompMn = (($porcentaje_utilidad / 100) * $contable_costo_mn) + $contable_costo_mn;
            if($tipo_cambio<=0){
                $preCompMe = 0;
            }else{
                $preCompMe = $preCompMn / $tipo_cambio;
            }

            $color = "";
            if($list->nombre_unidad=='NO TIENE UNIDADES'){
                $color = "red";
            }
    ?>
            <tr>
                <td style="white-space: normal;">
                    <input name="txtIdProducto" type="hidden" value="<?= $list->producto_id ?>">
                    <?php echo getCodigoValue(sumCod($list->producto_id), $list->producto_codigo_interno) ?>
                </td>
                <td style="white-space: normal;"><?= $list->producto_nombre ?></td>
                <td style="white-space: normal;"><?= $list->nombre_marca ?></td>
                <td style="white-space: normal; color: <?= $color ?>"><?= $list->nombre_unidad ?></td>
                <td style="text-align: right; white-space: normal;"><?= number_format($list->costo_mn, 2) ?></td>
                <td style="text-align: right; white-space: normal;">
                    <input class="form-control" name="txtCostoContMn" type="number" value="<?= number_format($contable_costo_mn, 2) ?>" style="width: 80px;">
                </td>
                <td style="text-align: right; white-space: normal;"><?= number_format($list->costo_me, 2) ?></td>
                <td style="text-align: right; white-space: normal;">
                    <input class="form-control" name="txtCostoContMe" type="number" value="<?= number_format($list->contable_costo_me, 2) ?>" style="width: 80px;">
                </td>
                <td style="text-align: right; white-space: normal;">
                    <input class="form-control" name="txtTipoCambio" type="number" value="<?= number_format($list->tipo_cambio, 2) ?>" style="width: 80px;">
                </td>
                <td style="text-align: right; white-space: normal;">
                    <input class="form-control" name="txtPorcPrecio" type="number" value="<?= number_format($porcentaje_utilidad, 2) ?>" style="width: 80px;">
                </td>
                <td style="text-align: right; white-space: normal;" class="preCompMn"><?= number_format($preCompMn, 2); ?></td>
                <td style="text-align: right; white-space: normal;" class="preCompMe"><?= number_format($preCompMe, 2); ?></td>
            </tr>
    <?php
        endforeach;
    ?>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <br>
        <button type="button" id="exportar_excel" title="Exportar Excel" class="btn btn-primary">
            <i class="fa fa-file-excel-o fa-fw"></i>
        </button>
        <button type="button" id="exportar_pdf" title="Exportar Pdf" class="btn btn-primary">
            <i class="fa fa-file-pdf-o fa-fw"></i>
        </button>
    </div>
</div>
<script type="text/javascript" src="<?= $ruta ?>recursos/js/costeo_list.js"></script>