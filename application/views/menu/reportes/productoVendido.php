<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Productos m&aacute;s vendidos</a></li>
</ul>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<link rel="stylesheet" href="<?= $ruta ?>recursos/js/datepicker-range/daterangepicker.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <!-- Progress Bars Wizard Title -->
            <div class="row">
                <div class="form-group">
                    <div class="col-md-3">
                    	Desde: <input type="text" id="dateIni" class="form-control" readonly style="cursor: pointer;" name="dateIni" value="<?= date('d/m/Y')?>" />
                    </div>
                    <div class="col-md-3">
                        Hasta: <input type="text" id="dateFin" class="form-control" readonly style="cursor: pointer;" name="dateFin" value="<?= date('d/m/Y')?>" />
                    </div>
                    <div class="col-md-1">
                    	<br>
                        <button id="btn_buscar" class="btn btn-default">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="col-md-1">
                    	<br>
	                    <button type="button" class="btn btn-primary tcharm-trigger form-control">
	                        <i class="fa fa-plus"></i>
	                    </button>
                	</div>
                </div>
            </div>
            <br>
            <div class="row-fluid">
                <div class="span12">
                    <div id="historial_list" class="block">
                    </div>
                </div>
            </div>
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>

            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/moment.min.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/datepicker-range/daterangepicker.js"></script>
            <script src="<?php echo $ruta; ?>recursos/js/Validacion.js"></script>
            <!-- /.modal-dialog -->
            <script type="text/javascript">
				$(function() {
				    $('input[name="dateIni"], input[name="dateFin"]').daterangepicker({
				        singleDatePicker: true,
				        showDropdowns: true,
				        "locale": {
                            "format": "DD/MM/YYYY",
							"daysOfWeek": [
	                            "Do",
	                            "Lu",
	                            "Ma",
	                            "Mi",
	                            "Ju",
	                            "Vi",
	                            "Sa"
                            ],
                            "monthNames": [
	                            "Enero",
	                            "Febrero",
	                            "Marzo",
	                            "Abril",
	                            "Mayo",
	                            "Junio",
	                            "Julio",
	                            "Agosto",
	                            "Septiembre",
	                            "Octubre",
	                            "Noviembre",
	                            "Diciembre"
                            ],
                            "firstDay": 1
                        }
				    });
				});
            </script>
