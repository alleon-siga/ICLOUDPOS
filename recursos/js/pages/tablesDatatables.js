/*
 *  Document   : tablesDatatables.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables Datatables page
 */

var TablesDatatables = function () {

  return {
    init: function (order, order_desc) {
      var oder_col = order || 0
      var od = order_desc || 'desc'
      /* Initialize Bootstrap Datatables Integration */
      App.datatables()

      /* Initialize Datatables */
      var table = $('.dataTable').dataTable({
        retrieve: true,
        //  "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 1, 5 ] } ],
        'iDisplayLength': 10,
        'aLengthMenu': [[10, 20, 30, -1], [10, 20, 30, 'All']],
        'order': [[oder_col, od]],
        'scrollY': '300px',
        'scrollX': true,
        'scrollCollapse': true,
        'dom': '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
        'language': {
          'emptyTable': 'No se encontraron registros',
          'info': 'Mostrando _START_ a _END_ de _TOTAL_ resultados',
          'infoEmpty': 'Mostrando 0 a 0 de 0 resultados',
          'infoFiltered': '(filtrado de _MAX_ total resultados)',
          'infoPostFix': '',
          'thousands': ',',
          'lengthMenu': 'Mostrar _MENU_ resultados',
          'loadingRecords': 'Cargando...',
          'processing': 'Procesando...',
          // "search": "Buscar:",
          'zeroRecords': 'No se encontraron resultados',
          'paginate': {
            'first': 'Primero',
            'last': 'Ultimo',
            'next': 'Siguiente',
            'previous': 'Anterior'
          },
          'aria': {
            'sortAscending': ': activar ordenar columnas ascendente',
            'sortDescending': ': activar ordenar columnas descendente'
          }
        },
        'fnInitComplete': function () {

          $('#cargando_modal').modal('hide')
          setTimeout(function () {
            $('div.dataTables_filter input').focus()
          }, 5)

        }

      })

      /* Add placeholder attribute to the search input */
      $('.dataTables_filter input').attr('placeholder', 'Buscar')

    }
  }
}()

var MyTablesDatatables = function () {

  return {
    init: function (order) {
      oder_col = order || 0
      /* Initialize Bootstrap Datatables Integration */
      App.datatables()

      /* Initialize Datatables */
      var table = $('.dataTable').dataTable({
        retrieve: true,
        //  "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 1, 5 ] } ],
        'iDisplayLength': 10,
        'aLengthMenu': [[10, 20, 30, -1], [10, 20, 30, 'All']],
        'order': [[oder_col, 'asc']],
        'scrollY': '300px',
        'scrollX': true,
        'scrollCollapse': true,
        'dom': '<"row"<"pull-left"f><"pull-right"l>>rt<"row"<"pull-left"i><"pull-right"p>>',
        'language': {
          'emptyTable': 'No se encontraron registros',
          'info': 'Mostrando _START_ a _END_ de _TOTAL_ resultados',
          'infoEmpty': 'Mostrando 0 a 0 de 0 resultados',
          'infoFiltered': '(filtrado de _MAX_ total resultados)',
          'infoPostFix': '',
          'thousands': ',',
          'lengthMenu': 'Mostrar _MENU_ resultados',
          'loadingRecords': 'Cargando...',
          'processing': 'Procesando...',
          // "search": "Buscar:",
          'zeroRecords': 'No se encontraron resultados',
          'paginate': {
            'first': 'Primero',
            'last': 'Ultimo',
            'next': 'Siguiente',
            'previous': 'Anterior'
          },
          'aria': {
            'sortAscending': ': activar ordenar columnas ascendente',
            'sortDescending': ': activar ordenar columnas descendente'
          }
        },
        'fnInitComplete': function () {

          $('#cargando_modal').modal('hide')
          setTimeout(function () {
            $('div.dataTables_filter input').focus()
          }, 5)

        }

      })

      /* Add placeholder attribute to the search input */
      $('.dataTables_filter input').attr('placeholder', 'Buscar')

    }
  }
}()