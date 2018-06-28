<?php $ruta = base_url(); ?>
<link href='<?= $ruta ?>recursos/css/fullcalendar.min.css' rel='stylesheet' />
<link href='<?= $ruta ?>recursos/css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<style>
  h2{
    font-size: 1.5em !important;
    font-weight: bold !important;
  }
  #cuerpoCalendar {
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
    text-transform: capitalize;
  }
  #calendar {
    max-width: 900px;
    margin: 0 auto;
  }
  .fc-button{
    line-height: 0 !important;
  }
  .fc-button .fc-icon{
    font-size: 1em !important;
  }
</style>
<hr>
<div class="row" id="cuerpoCalendar">
    <div class="col-md-12">
      <div id='calendar'></div>
    </div>
</div>
<script type="text/javascript">
    var eventos = [];
    <?php foreach ($lists as $list):?>
    eventos.push({
        id_ingreso: '<?= $list->id_ingreso ?>',
        proveedor_nombre: '<?= $list->proveedor_nombre ?>',
        fecha_vencimiento: '<?= $list->fecha_vencimiento ?>',
        pago_pendiente: '<?= $list->pago_pendiente ?>',
        letra: '<?= $list->letra ?>',
        simbolo: '<?= $list->simbolo ?>',
        tipo_ingreso: '<?= $list->tipo_ingreso ?>',
    });
    <?php endforeach;?>

  $(document).ready(function() {
    var options = {
      defaultView: 'month',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month'
      },
      views: {
        month: {
          titleFormat: 'MMMM YYYY',
          columnFormat: 'dddd'
        }
      },
      events: []
    };

    for(let i=0; i < eventos.length; i++){
      var datos = [];
      datos['id'] = eventos[i].id_ingreso;
      datos['title'] = eventos[i].tipo_ingreso + '\n' + eventos[i].proveedor_nombre + '\n' + eventos[i].simbolo + ' ' + eventos[i].pago_pendiente + '\n' + eventos[i].letra;
      datos['start'] = eventos[i].fecha_vencimiento;
      options.events.push(datos);
    }    
    $('#calendar').fullCalendar(options);
  });  
</script>