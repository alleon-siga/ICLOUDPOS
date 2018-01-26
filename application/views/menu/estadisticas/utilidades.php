<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Reportes</li>
    <li><a href="">Estadistica de utilidades</a></li>
</ul>

<div class="block">
    <div class="block-title">
        <h3>Utilidades/Estad&iacute;sticas</h3></div>
    <div class="row">

        <div class="widget">
            <div class="widget-extra themed-background-dark-default text-center">
                <h3 class="widget-content-light">Resumen de Ventas del Año
                    <div id="anio"></div>
                    </strong></h3>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1">
            <label>Desde</label>
        </div>
        <div class="col-md-2">

            <input value="" type="text" name="fecha_desde" id="fecha_desde" placeholder="Desde"
                   class='input-small form-control input-datepicker fecha'>
        </div>
        <div class="col-md-1">
            <label>Hasta</label>
        </div>
        <div class="col-md-2">
            <input value="" type="text" name="fecha_hasta" id="fecha_hasta" class='form-control input-datepicker fecha'
                   placeholder="Hasta">
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-2">
            <a href="#" onclick="semanaactual()">Semana Actual</a>
        </div>
        <div class="col-md-2">
            <a href="#" onclick="mesactual()">Mes Actual</a>
        </div>
        <div class="col-md-2">
            <a href="#" onclick="mesanterior()">Mes Anterior</a>
        </div>
        <div class="col-md-1">
            Año
        </div>
        <div class="col-md-2">
            <select onchange="annoactual()" class="form-control" id="anno">
                <option value="<?= date("Y") ?>">Año Actual</option>
                <?php for ($i = date("Y") - 1; $i >= 2014; $i--) {

                    echo "<option value='" . $i . "'> " . $i . "</option>";


                } ?>


            </select>
        </div>
    </div>
    <br>

    <div class="row">
        <div id="chart-classic" class="chart"></div>
    </div>
    <br>

    <div class="row" id="totales">
        <div id="borrar_totales">


        </div>
    </div>
    <br>

    <br>

    <div class="row">
        <div align="left">
            <h3><label class="text-info">Ventas por forma de Pago</label></h3>
        </div>
        <div id="condicion_pago" class="chart"></div>
    </div>
    <br>

    <div class="row">
        <div align="left">
            <h3><label class="text-info">Ganancias por Mes</label></h3>
        </div>
        <div class="col-md-6">
            <div class="table-responsive" id="" align="center">
                <table border="0" class='table table-striped dataTable table-bordered no-footer' id="tablagrafico4"
                       style="width: 250px; height: 250px;">


                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div id="ganancias" class="chart" style="width: 500px;" align="left"></div>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-6">
            <div align="left">
                <h3><label class="text-info">Resumen de Operaciones</label></h3>
            </div>

            <div class="table-responsive" id="tablagrafico5">


            </div>
        </div>


        <div class="col-md-6">
            <div align="left">
                <h3><label class="text-info">
                        <div id="vetasdelmes"></div>
                    </label></h3>
            </div>
            <div class="table-responsive" id="tabla">
                <table class='table table-striped dataTable table-bordered no-footer' id="lstPagP"
                       style="width: 400px; height: 250px;">


                </table>
            </div>
        </div>
    </div>
</div>

<script>


    var arregloDataGrafico3 = new Array()
    var periodo = 'annoactual'
    var desde = ''
    var hasta = ''

    var validar = false;
    var meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
    var semana = ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"]

    var chartClassic = $("#chart-classic");
    var condicionPago = $("#condicion_pago");
    var Ganancia = $("#ganancias");

    <?php  if($numero_dias == 31){?>
    var chartday = [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10], [11, 11],
        [12, 12], [13, 13], [14, 14], [15, 15], [16, 16], [17, 17], [18, 18], [19, 19], [20, 20], [21, 21], [22, 22], [23, 23], [24, 24], [25, 25],
        [26, 26], [27, 27], [28, 28], [29, 29], [30, 30], [31, 31]];
    <?php  }elseif($numero_dias == 30){ ?>
    var chartday = [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10], [11, 11],
        [12, 12], [13, 13], [14, 14], [15, 15], [16, 16], [17, 17], [18, 18], [19, 19], [20, 20], [21, 21], [22, 22], [23, 23], [24, 24], [25, 25],
        [26, 26], [27, 27], [28, 28], [29, 29], [30, 30]];

    <?php  }elseif($numero_dias == 29){ ?>
    var chartday = [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10], [11, 11],
        [12, 12], [13, 13], [14, 14], [15, 15], [16, 16], [17, 17], [18, 18], [19, 19], [20, 20], [21, 21], [22, 22], [23, 23], [24, 24], [25, 25],
        [26, 26], [27, 27], [28, 28], [29, 29]];
    <?php  }elseif($numero_dias == 28){ ?>
    var chartday = [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10], [11, 11],
        [12, 12], [13, 13], [14, 14], [15, 15], [16, 16], [17, 17], [18, 18], [19, 19], [20, 20], [21, 21], [22, 22], [23, 23], [24, 24], [25, 25],
        [26, 26], [27, 27], [28, 28]];
    <?php  } ?>

    var chartWeek = [[1, 'Dom'], [2, 'Lun'], [3, 'Mar'], [4, 'Mie'], [5, 'Jue'], [6, 'Vie'], [7, 'Sab']];

    var chartMonths = [[1, 'Ene'], [2, 'Feb'], [3, 'Mar'], [4, 'Abr'], [5, 'May'],
        [6, 'Jun'], [7, 'Jul'], [8, 'Ago'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dic']];

    var ejex = chartMonths;
    function grafico() {

        $.ajax({
            url: "<?= base_url()?>estadisticas/utilidades",
            type: "POST",
            async: false,
            data: {'utilidades': 'TODO', 'desde': desde, 'hasta': hasta, 'periodo': periodo, 'anno': $("#anno").val()},
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                <?php $md = get_moneda_defecto()?>
                //////////////aqui se colocan los totales del cuadro 1/////////
                $("#borrar_totales").remove()
                var totales = ''
                totales += '<div id="borrar_totales"><div class="row"><div  class="col-md-3"><strong> Ventas Totales: </strong> </div><div  class="col-md-4">'
                totales += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_venta'] == null) {
                    totales += ' 0'
                } else {
                    totales += '' + data.totales[0]['total_venta'] + ''
                }
                totales += '</strong> </div>'
                totales += '<div  class="col-md-2"><strong> Ganancias: </strong> </div><div  class="col-md-2"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.totales[0]['total_utilidad'] + ''
                }
                totales += '</strong> </div></div>'
                totales += '<div class="row"><div  class="col-md-3"><strong> Numero de Ventas: </strong> </div><div  class="col-md-4"><strong>'
                if (data.totales[0]['numero_venta'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.totales[0]['numero_venta'] + ''
                }
                totales += '</strong> </div>'

                totales += '<div  class="col-md-2"><strong> Margen de Ganancia Promedio: </strong> </div><div  class="col-md-2"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['numero_venta'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.margen + ''
                }
                totales += '</strong> </div></div>'

                totales += '<div class="row"><div  class="col-md-3"><strong> Venta Promedio: </strong> </div><div  class="col-md-1"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    totales += '0'
                } else {
                    totales += '' + (data.totales[0]['total_utilidad'] / data.totales[0]['numero_venta']).toFixed(2) + ''
                }
                totales += '</strong> </div></div></div>'

                $("#totales").append(totales)

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $("#borrar_tbody").remove()
                $("#borrar_tbody4").remove()
                $("#estatus").remove()

                var gananciaspormes = ''; //// para el cuadro de las ganancias por mes
                var ventaspormes = '';  /////// para el cuadro de las ventas del Mes
                var estatus = '' ///// para  el ultimo cuadro de resumen de operaciones que filtra por estatus

                ventaspormes += ' <tbody id="borrar_tbody" >'
                gananciaspormes += ' <tbody id="borrar_tbody4" >'


                var totalcompletado = 0   ////contador
                var totalenespera = 0  ////contador
                var totalanulado = 0   ////contador

                var completado = ''  //// almacena las ventas realizadas en estatus completado para el cuadro numero 5
                var enespera = ''  //// almacena las ventas realizadas en estatus En Espera para el cuadro numero 5
                var anulado = '' //// almacena las ventas realizadas en estatus Anulado para el cuadro numero 5


                estatus += ' <table class="table table-striped dataTable table-bordered no-footer" id="estatus" style="width: 400px; height: 250px;">'
                estatus += '<thead><tr><th></th><th>Completado</th><th>En Espera </th> <th>Anulado </th> </tr></thead>'
                estatus += '<tbody id="tbodygrafico5">'

                for (var i = 0; i < ejex.length; i++) {

                    ventaspormes += '<tr>'
                    ventaspormes += '<td>'
                    ventaspormes += '' + ejex[i][1] + ''
                    ventaspormes += '</td>'
                    ventaspormes += '<td>'
                    ventaspormes += '<?php echo $md->simbolo; ?> ' + data.venta[i][1] + ''
                    ventaspormes += '</td>'
                    ventaspormes += '</tr>'

                    gananciaspormes += '<tr>'
                    gananciaspormes += '<td>'
                    gananciaspormes += '' + ejex[i][1] + ''
                    gananciaspormes += '</td>'
                    gananciaspormes += '<td>'
                    gananciaspormes += '<?php echo $md->simbolo; ?> ' + data.utilidad[i][1] + ''
                    gananciaspormes += '</td>'
                    gananciaspormes += '</tr>'

                    estatus += '<tr>'
                    estatus += '<td>'
                    estatus += '' + ejex[i][1] + ''
                    estatus += '</td>'
                    for (var j = 0; j < data.estatus.length; j++) {

                        var arreglo_estatus = data.estatus[j]

                        if (ejex[i][0] == arreglo_estatus['ciclo']) {
                            if (arreglo_estatus['venta_status'] == "COMPLETADO") {
                                completado += '<td>'
                                totalcompletado = totalcompletado + parseFloat(arreglo_estatus['numero_venta'])
                                completado += '' + arreglo_estatus['numero_venta'] + ''
                                completado += '</td>'
                            }


                            if (arreglo_estatus['venta_status'] == "ESPERA") {
                                enespera += '<td>'
                                totalenespera = totalenespera + parseFloat(arreglo_estatus['numero_venta'])
                                enespera += '' + arreglo_estatus['numero_venta'] + ''
                                enespera += '</td>'
                            }


                            if (arreglo_estatus['venta_status'] == "ANULADO") {
                                anulado += '<td>'
                                totalanulado = totalanulado + parseFloat(arreglo_estatus['numero_venta'])
                                anulado += '' + arreglo_estatus['numero_venta'] + ''
                                anulado += '</td>'
                            }

                        }
                    }

                    if (completado != '') {

                        estatus += completado
                        completado = ''
                    } else {
                        estatus += '<td>0</td>'
                    }

                    if (enespera != '') {

                        estatus += enespera
                        enespera = ''
                    } else {
                        estatus += '<td>0</td>'
                    }

                    if (anulado != '') {

                        estatus += anulado
                        anulado = ''
                    } else {
                        estatus += '<td>0</td>'
                    }


                    estatus += '</tr>'

                }
                ventaspormes += '<tr>'
                ventaspormes += '<td>'
                ventaspormes += '<strong>Total</strong>'
                ventaspormes += '</td>'
                ventaspormes += '<td>'
                ventaspormes += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_venta'] == null) {
                    ventaspormes += '0'
                } else {
                    ventaspormes += '' + data.totales[0]['total_venta'] + ''
                }
                ventaspormes += ' </strong>'
                ventaspormes += '</td>'
                ventaspormes += '</tr>'
                ventaspormes += ' </tbody>'
                $("#lstPagP").append(ventaspormes)


                gananciaspormes += '<tr>'
                gananciaspormes += '<td>'
                gananciaspormes += '<strong>Total</strong>'
                gananciaspormes += '</td>'
                gananciaspormes += '<td>'
                gananciaspormes += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    gananciaspormes += '0'
                } else {
                    gananciaspormes += '' + data.totales[0]['total_utilidad'] + ''
                }
                gananciaspormes += ' </strong>'
                gananciaspormes += '</td>'
                gananciaspormes += '</tr>'
                gananciaspormes += ' </tbody>'
                $("#tablagrafico4").append(gananciaspormes)

                estatus += '<tr>'
                estatus += '<td>'
                estatus += '<strong>Total</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalcompletado + '</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalenespera + '</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalanulado + '</strong>'
                estatus += '</td>'
                estatus += '</tr>'
                estatus += '</tbody>'
                estatus += '</table>'
                $("#tablagrafico5").append(estatus)

                var datagrafico1 = [{
                    "label": "Venta",
                    "data": data.venta,
                    color: "blue",
                    bars: {
                        show: true,
                        barWidth: 0.5,
                        align: "right",
                        fill: .60
                    }
                },
                    {
                        "label": "Utilidad",
                        "data": data.utilidad,
                        color: "green",
                        bars: {
                            show: true,
                            barWidth: 0.4,
                            align: "left"
                        }
                    },
                ];

                var options = {

                    series: {

                        lines: {
                            show: false,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 2
                        },
                        bars: {
                            show: true,
                            lineWidth: 0.9,
                            fill: .75,
                            fillColor: null
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(chartClassic, datagrafico1, options);


                var previousPoint = null, ttlabel = null;
                chartClassic.bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#chart-tooltip').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#chart-tooltip').remove();
                        previousPoint = null;
                    }
                });
///////////////////////////////////////////////////////////////////////////////////////////////////
                var grafico3 = new Array()
                if (data.tabla_condiciones.length > 0) {


                    var condicion_pago = data.condicion_pago
                    var tabla_condiciones = data.tabla_condiciones

                    for (var i = 0; i < tabla_condiciones.length; i++) {

                        var grafico = {}
                        grafico.label = tabla_condiciones[i]['nombre_condiciones']
                        arregloDataGrafico3 = new Array();
                        for (var y = 0; y < condicion_pago.length; y++) {

                            var newData = new Array();
                            if (condicion_pago[y]['condicion_pago'] == tabla_condiciones[i]['id_condiciones']) {
                                newData[0] = condicion_pago[y]['ciclo'];
                                newData[1] = condicion_pago[y]['total_venta'];
                                arregloDataGrafico3.push(newData);

                            }
                        }

                        grafico.data = arregloDataGrafico3;
                        grafico3.push(grafico);
                    }
                }

                var datagrafico3 = grafico3;
                var chartData = datagrafico3;
                var chartOptions = {
                    series: {

                        lines: {
                            show: false,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 2
                        },
                        bars: {
                            show: true,
                            lineWidth: 0.9,
                            fill: .75,
                            fillColor: null,
                            align: 'center'
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(condicionPago, chartData, chartOptions);


                var previousPoint = null, ttlabel = null;
                condicionPago.bind('plothover', function (event, pos, item) {

                    if (item) {

                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#chartCondicion').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="chartCondicion" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#chartCondicion').remove();
                        previousPoint = null;
                    }
                });
                /////////////////////////////////////////////////las ganancias////////////////////


                var datagrafico4 = [
                    {

                        "data": data.utilidad,
                        color: "green",
                        bars: {
                            show: false,
                            barWidth: 0.4,
                            align: "left"
                        }
                    },
                ];

                var options = {

                    series: {

                        lines: {
                            show: true,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.70}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 4
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(Ganancia, datagrafico4, options);


                var previousPoint = null, ttlabel = null;
                Ganancia.bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#toltip_ganancia').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="toltip_ganancia" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#toltip_ganancia').remove();
                        previousPoint = null;
                    }
                });
                //////////////////////////////////////por estatus////////////////////////////////////////


            }
        });
    }

    function semanaactual() {

        $("#vetasdelmes").text(" Ventas de esta Semana")
        periodo = 'semana'
        ejex = chartWeek
        grafico()
    }

    function mesactual() {
        periodo = 'mesactual'
        ejex = chartday
        grafico()
    }

    function mesanterior() {
        periodo = 'mesanterior'
        ejex = chartday
        grafico()
    }

    function annoactual() {

        $("#anio").text($("#anno").val())
        periodo = 'annoactual'
        ejex = chartMonths
        grafico()
    }


    $(document).ready(function () {

        $("#vetasdelmes").text(" Ventas del Mes")
        $("#anio").text($("#anno").val())
        grafico()

        $('.fecha').on('change', function () {
            desde = $('#fecha_desde').val()
            hasta = $('#fecha_hasta').val()
            periodo = 'fecha'
            porfecha()
            //grafico()

        })

    });
    ////////////////////////////////// se hace una funcion a parte ya para no confundir ya que cambia mucho el codigo cuando se
    /// filtra por fechas
    function porfecha() {

        $.ajax({
            url: "<?= base_url()?>estadisticas/utilidades",
            type: "POST",
            async: false,
            data: {'utilidades': 'TODO', 'desde': desde, 'hasta': hasta, 'periodo': periodo, 'anno': $("#anno").val()},
            dataType: 'JSON',
            success: function (data) {


                var validar = false;
                var arreglototalventa = new Array();
                var arreglototalutilidad = new Array();
                var ejexdefecha = new Array();
                var contador = 1

                //// data.fecha es el rango de fechas seleccionadas

                for (var i = 0; i < data.fecha.length; i++) {
                    var newData = new Array();
                    for (var j = 0; j < data.venta.length; j++) {

                        if (data.venta[j]['fecha'] == data.fecha[i]) {

                            //// si es igual al una de el rango de fechas se almacena la venta y la utilidad
                            var eje = new Array();
                            eje[0] = contador
                            eje[1] = data.venta[j]['fecha']
                            /// primero guardo las ventas
                            newData[0] = contador
                            newData[1] = data.venta[j]['total_venta'];
                            arreglototalventa.push(newData);
                            //// luego guardo las utilidades
                            var newData = new Array();
                            newData[0] = contador
                            newData[1] = data.venta[j]['total_utilidad'];
                            arreglototalutilidad.push(newData)

                            validar = true;
                            contador++
                            ejexdefecha.push(eje);

                        }
                    }
                    if (validar == false) {
                        var eje = new Array();
                        eje[0] = contador
                        eje[1] = data.fecha[i]
                        ejexdefecha.push(eje);
                        newData[0] = contador
                        newData[1] = 0;
                        arreglototalventa.push(newData);

                        var newData = new Array();
                        newData[0] = contador
                        newData[1] = 0;
                        arreglototalutilidad.push(newData)
                        contador++

                    }
                    validar = false;
                }

                ejex = ejexdefecha

                var dataset = [{
                    "label": "Venta",
                    "data": arreglototalventa,
                    color: "blue",
                    bars: {
                        show: true,
                        barWidth: 0.5,
                        align: "right",
                        fill: .60
                    }
                },
                    {
                        "label": "Utilidad",
                        "data": arreglototalutilidad,
                        color: "green",
                        bars: {
                            show: true,
                            barWidth: 0.4,
                            align: "left"
                        }
                    },
                ];
                var options = {

                    series: {

                        lines: {
                            show: false,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 2
                        },
                        bars: {
                            show: true,
                            lineWidth: 0.9,
                            fill: .75,
                            fillColor: null
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(chartClassic, dataset, options);
                var previousPoint = null, ttlabel = null;
                chartClassic.bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#chart-tooltip').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#chart-tooltip').remove();
                        previousPoint = null;
                    }
                });
                /////////////////aqui finaliza el grafico 1//////////////////////////////////////////

                /////////////////////se colocan los totales//////

                //////////////aqui se colocan los totales del cuadro 1/////////
                $("#borrar_totales").remove()
                var totales = ''
                totales += '<div id="borrar_totales"><div class="row"><div  class="col-md-3"><strong> Ventas Totales: </strong> </div><div  class="col-md-4">'
                totales += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_venta'] == null) {
                    totales += ' 0'
                } else {
                    totales += '' + data.totales[0]['total_venta'] + ''
                }
                totales += '</strong> </div>'
                totales += '<div  class="col-md-2"><strong> Ganancias: </strong> </div><div  class="col-md-2"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.totales[0]['total_utilidad'] + ''
                }
                totales += '</strong> </div></div>'
                totales += '<div class="row"><div  class="col-md-3"><strong> Numero de Ventas: </strong> </div><div  class="col-md-4"><strong>'
                if (data.totales[0]['numero_venta'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.totales[0]['numero_venta'] + ''
                }
                totales += '</strong> </div>'

                totales += '<div  class="col-md-2"><strong> Margen de Ganancia Promedio: </strong> </div><div  class="col-md-2"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['numero_venta'] == null) {
                    totales += '0'
                } else {
                    totales += '' + data.margen + ''
                }
                totales += '</strong> </div></div>'

                totales += '<div class="row"><div  class="col-md-3"><strong> Venta Promedio: </strong> </div><div  class="col-md-1"><strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    totales += '0'
                } else {
                    totales += '' + (data.totales[0]['total_utilidad'] / data.totales[0]['numero_venta']).toFixed(2) + ''
                }
                totales += '</strong> </div></div></div>'

                $("#totales").append(totales)

                //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $("#borrar_tbody").remove()
                $("#borrar_tbody4").remove()
                $("#estatus").remove()

                var gananciaspormes = ''; //// para el cuadro de las ganancias por mes
                var ventaspormes = '';  /////// para el cuadro de las ventas del Mes
                var estatus = '' ///// para  el ultimo cuadro de resumen de operaciones que filtra por estatus

                ventaspormes += ' <tbody id="borrar_tbody" >'
                gananciaspormes += ' <tbody id="borrar_tbody4" >'


                var totalcompletado = 0   ////contador
                var totalenespera = 0  ////contador
                var totalanulado = 0   ////contador

                var completado = ''  //// almacena las ventas realizadas en estatus completado para el cuadro numero 5
                var enespera = ''  //// almacena las ventas realizadas en estatus En Espera para el cuadro numero 5
                var anulado = '' //// almacena las ventas realizadas en estatus Anulado para el cuadro numero 5


                estatus += ' <table class="table table-striped dataTable table-bordered no-footer" id="estatus" style="width: 400px; height: 250px;">'
                estatus += '<thead><tr><th></th><th>Completado</th><th>En Espera </th> <th>Anulado </th> </tr></thead>'
                estatus += '<tbody id="tbodygrafico5">'

                for (var i = 0; i < ejex.length; i++) {

                    ventaspormes += '<tr>'
                    ventaspormes += '<td>'
                    ventaspormes += '' + ejex[i][1] + ''
                    ventaspormes += '</td>'
                    ventaspormes += '<td>'
                    ventaspormes += '<?php echo $md->simbolo; ?> ' + arreglototalventa[i][1] + ''
                    ventaspormes += '</td>'
                    ventaspormes += '</tr>'

                    gananciaspormes += '<tr>'
                    gananciaspormes += '<td>'
                    gananciaspormes += '' + ejex[i][1] + ''
                    gananciaspormes += '</td>'
                    gananciaspormes += '<td>'
                    gananciaspormes += '<?php echo $md->simbolo; ?> ' + arreglototalutilidad[i][1] + ''
                    gananciaspormes += '</td>'
                    gananciaspormes += '</tr>'

                    estatus += '<tr>'
                    estatus += '<td>'
                    estatus += '' + ejex[i][1] + ''
                    estatus += '</td>'
                    for (var j = 0; j < data.estatus.length; j++) {

                        var arreglo_estatus = data.estatus[j]

                        if (ejex[i][1] == arreglo_estatus['fecha']) {
                            if (arreglo_estatus['venta_status'] == "COMPLETADO") {
                                completado += '<td>'
                                totalcompletado = totalcompletado + parseFloat(arreglo_estatus['numero_venta'])
                                completado += '' + arreglo_estatus['numero_venta'] + ''
                                completado += '</td>'
                            }


                            if (arreglo_estatus['venta_status'] == "ESPERA") {
                                enespera += '<td>'
                                totalenespera = totalenespera + parseFloat(arreglo_estatus['numero_venta'])
                                enespera += '' + arreglo_estatus['numero_venta'] + ''
                                enespera += '</td>'
                            }


                            if (arreglo_estatus['venta_status'] == "ANULADO") {
                                anulado += '<td>'
                                totalanulado = totalanulado + parseFloat(arreglo_estatus['numero_venta'])
                                anulado += '' + arreglo_estatus['numero_venta'] + ''
                                anulado += '</td>'
                            }

                        }
                    }

                    if (completado != '') {

                        estatus += completado
                        completado = ''
                    } else {
                        estatus += '<td>0</td>'
                    }

                    if (enespera != '') {

                        estatus += enespera
                        enespera = ''
                    } else {
                        estatus += '<td>0</td>'
                    }

                    if (anulado != '') {

                        estatus += anulado
                        anulado = ''
                    } else {
                        estatus += '<td>0</td>'
                    }


                    estatus += '</tr>'

                }
                ventaspormes += '<tr>'
                ventaspormes += '<td>'
                ventaspormes += '<strong>Total</strong>'
                ventaspormes += '</td>'
                ventaspormes += '<td>'
                ventaspormes += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_venta'] == null) {
                    ventaspormes += '0'
                } else {
                    ventaspormes += '' + data.totales[0]['total_venta'] + ''
                }
                ventaspormes += ' </strong>'
                ventaspormes += '</td>'
                ventaspormes += '</tr>'
                ventaspormes += ' </tbody>'
                $("#lstPagP").append(ventaspormes)


                gananciaspormes += '<tr>'
                gananciaspormes += '<td>'
                gananciaspormes += '<strong>Total</strong>'
                gananciaspormes += '</td>'
                gananciaspormes += '<td>'
                gananciaspormes += '<strong><?php echo $md->simbolo; ?> '
                if (data.totales[0]['total_utilidad'] == null) {
                    gananciaspormes += '0'
                } else {
                    gananciaspormes += '' + data.totales[0]['total_utilidad'] + ''
                }
                gananciaspormes += ' </strong>'
                gananciaspormes += '</td>'
                gananciaspormes += '</tr>'
                gananciaspormes += ' </tbody>'
                $("#tablagrafico4").append(gananciaspormes)

                estatus += '<tr>'
                estatus += '<td>'
                estatus += '<strong>Total</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalcompletado + '</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalenespera + '</strong>'
                estatus += '</td>'
                estatus += '<td>'
                estatus += '<strong>' + totalanulado + '</strong>'
                estatus += '</td>'
                estatus += '</tr>'
                estatus += '</tbody>'
                estatus += '</table>'
                $("#tablagrafico5").append(estatus)


                /////////grafico 3 /////////////////////////////////////////
                var validar = false;
                var contador = 1
                var grafico3 = new Array()
                if (data.tabla_condiciones.length > 0) {


                    var condicion_pago = data.condicion_pago
                    var tabla_condiciones = data.tabla_condiciones

                    for (var i = 0; i < tabla_condiciones.length; i++) {

                        var grafico = {}
                        grafico.label = tabla_condiciones[i]['nombre_condiciones']
                        arregloDataGrafico3 = new Array();
                        contador = 1
                        for (var p = 0; p < data.fecha.length; p++) {
                            for (var y = 0; y < condicion_pago.length; y++) {


                                if (condicion_pago[y]['condicion_pago'] == tabla_condiciones[i]['id_condiciones']
                                    && condicion_pago[y]['fecha'] == data.fecha[p]) {
                                    var newData = new Array();
                                    newData[0] = contador;
                                    newData[1] = condicion_pago[y]['total_venta'];
                                    arregloDataGrafico3.push(newData);
                                    contador++
                                    validar = true
                                }
                            }
                            if (validar == false) {
                                var newData = new Array();
                                newData[0] = contador;
                                newData[1] = 0;
                                arregloDataGrafico3.push(newData);
                                contador++

                            }
                            validar = false;

                        }

                        grafico.data = arregloDataGrafico3;
                        grafico3.push(grafico);

                    }
                }

                var chartData = grafico3;
                var chartOptions = {
                    series: {

                        lines: {
                            show: false,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 2
                        },
                        bars: {
                            show: true,
                            lineWidth: 0.9,
                            fill: .75,
                            fillColor: null,
                            align: 'center'
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(condicionPago, chartData, chartOptions);


                var previousPoint = null, ttlabel = null;
                condicionPago.bind('plothover', function (event, pos, item) {

                    if (item) {

                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#chartCondicion').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="chartCondicion" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#chartCondicion').remove();
                        previousPoint = null;
                    }
                });
                /////////////////////////////////////////////////las ganancias////////////////////


                var datagrafico4 = [
                    {

                        "data": arreglototalutilidad,
                        color: "green",
                        bars: {
                            show: false,
                            barWidth: 0.4,
                            align: "left"
                        }
                    },
                ];

                var options = {

                    series: {

                        lines: {
                            show: true,
                            fill: true,
                            fillColor: {colors: [{opacity: 0.70}, {opacity: 0.25}], lineWidth: 0.9}
                        },
                        points: {
                            show: true,
                            lineWidth: 1,
                            fill: true,
                            fillColor: null,
                            radius: 4
                        }
                    },
                    xaxis: {
                        show: true,
                        ticks: ejex,
                        autoscaleMargin: 0.10,
                        min: 0
                    },
                    yaxis: {
                        show: true,
                        tickLength: 2
                    },
                    grid: {borderWidth: 0, hoverable: true, clickable: true}
                };
                $.plot(Ganancia, datagrafico4, options);


                var previousPoint = null, ttlabel = null;
                Ganancia.bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#toltip_ganancia').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            ttlabel = '<?php echo $md->simbolo; ?><strong>' + y + '</strong> ';

                            setTimeout(function () {
                                $('<div id="toltip_ganancia" class="chart-tooltip">' + ttlabel + '</div>')
                                    .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                            }, 1)
                        }
                    }
                    else {
                        $('#toltip_ganancia').remove();
                        previousPoint = null;
                    }
                });
                //////////////////////////////////////por estatus////////////////////////////////////////

            }
        });
    }
</script>