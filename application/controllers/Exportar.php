<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class exportar extends MY_Controller
{

    public function exportar()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('cliente/cliente_model', 'cl');
            $this->load->model('producto/producto_model', 'pd');
            $this->load->model('ingreso/ingreso_model');
            $this->load->model('local/local_model', 'l');
            $this->load->model('venta/venta_model', 'v');
            $this->load->model('usuario/usuario_model');
            $this->load->model('gastos/gastos_model');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('credito_cuotas_abono/credito_cuotas_abono_model');
            $this->load->model('detalle_ingreso/detalle_ingreso_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('metodosdepago/metodos_pago_model');
            $this->load->library('mpdf53/mpdf');
        } else {
            redirect(base_url(), 'refresh');
        }
    }


    function index()
    {
    }

    /*-- SECCION EXCEL --*/


    function toExcel_estadoCuenta()
    {

        $condicion = 'v.venta_status="COMPLETADO" ';

        if ($this->input->post('cboCliente1', true) != -1) {
            $condicion .= ' and v.id_cliente =' . $this->input->post("cboCliente1", true) . " ";
        }
        if ($_POST['fecIni1'] != "") {
            $condicion .= 'and v.FechaReg >="' . date('Y-m-d', strtotime($_POST['fecIni1'])) . '" ';
        }

        if ($_POST['fecFin1'] != "") {
            $condicion .= ' and v.FechaReg <="' . date('Y-m-d', strtotime($_POST['fecFin1'])) . '" ';
        }

        if ($this->input->post("estado", true) != "TODOS") {

            $condicion .= ' and `cd`.`var_credito_estado` ="' . $this->input->post("estado", true) . '" ';
        }


        $order = " order by `v`.`fecha` desc ";
        if ($this->input->post("local", true) == "TODOS") {
            $order .= ',  `v`.`local_id` desc ';
        } else {
            $condicion .= ' and `v`.`local_id`=' . $this->input->post("local", true) . '';
        }
        $data['local'] = $this->input->post("local", true);


        $data['fecInicio'] = date("Y-m-d", strtotime($this->input->post('fecIni1', true)));
        $data['fecFin'] = date("Y-m-d", strtotime($this->input->post('fecFin1', true)));

        $data['estado_cuenta'] = $this->v->select_venta_estadocuenta($condicion, $order);
        $this->load->view('menu/reportes/reporteEstadoCuenta', $data);
    }

    function toExcel_pagoPendiente()
    {

        $condicion = 'v.venta_status="COMPLETADO" ';

        if ($this->input->post('cboCliente1', true) != -1) {
            $condicion .= ' and v.Cliente_Id =' . $this->input->post("cboCliente1", true) . " ";
        }
        if ($_POST['fecIni1'] != "") {
            $condicion .= 'and v.fecha >="' . date('Y-m-d', strtotime($_POST['fecIni1'])) . '" ';
        }

        if ($_POST['fecFin1'] != "") {
            $condicion .= ' and v.fecha <="' . date('Y-m-d', strtotime($_POST['fecFin1'])) . '" ';
        }

        $order = " order by `v`.`fecha` desc ";
        if ($this->input->post("local", true) == "TODOS") {
            $order .= ',  `v`.`local_id` desc ';
        } else {
            $condicion .= ' and `v`.`local_id`=' . $this->input->post("local", true) . '';
        }

        $data['local'] = $this->input->post("local", true);

        $data['pago_pendiente'] = $this->v->get_pagos_pendientes($condicion, $order);

        $data['fecInicio'] = date("Y-m-d", strtotime($this->input->post('fecIni1', true)));
        $data['fecFin'] = date("Y-m-d", strtotime($this->input->post('fecFin1', true)));

        $this->load->view('menu/reportes/reportePagoPendientes', $data);
    }

    /*-- SECCION PDF --*/
    function toPDF_cuadre_caja()
    {

        $id_local = $this->input->post('locales', true);
        $id_usuario = $this->input->post('usuarios', true);
        $id_moneda = $this->input->post('monedas', true);

        if ($id_moneda == '0') {
            $data['moneda_nombre'] = 'Todos';
            $data['monedas'] = $this->ingreso_model->get_monedas();
        } else {
            $data['monedas'] = array($this->ingreso_model->get_monedas($id_moneda));
            $data['moneda_nombre'] = $data['monedas'][0]['nombre'];
        }

        if ($id_local == '0')
            $data['local_nombre'] = 'Todos';
        else {
            $local = $this->l->get_by('int_local_id', $id_local);
            $data['local_nombre'] = $local['local_nombre'];
        }

        if ($id_usuario == '0') {
            $data['usuario_nombre'] = 'Todos';
        } else {
            $usuario = $this->db->get_where('usuario', array('nUsuCodigo' => $id_usuario))->row();
            $data['usuario_nombre'] = $usuario->nombre;
        }

        $metodos = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();

        $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('fecha', true))));
        $fechadespues = date('Y-m-d', strtotime('+1 day', strtotime($fecha)));
        foreach ($data['monedas'] as $mon) {

            //VENTA CONTADO
            $data['venta_contado'][$mon['id_moneda']] = $this->db->select_sum('venta.total', 'total')
                ->from('venta')
                ->where('venta.id_moneda', $mon["id_moneda"])
                ->where('venta.fecha >=', $fecha)
                ->where('venta.fecha <', $fechadespues)
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.condicion_pago', 1)
                ->get()->row();

            //VENTA INICIAL
            $data['venta_inicial'][$mon['id_moneda']] = $this->db->select_sum('venta.inicial', 'total')
                ->from('venta')
                ->where('venta.id_moneda', $mon["id_moneda"])
                ->where('venta.fecha >=', $fecha)
                ->where('venta.fecha <', $fechadespues)
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.condicion_pago', 2)
                ->get()->row();

            //VENTA CREDITO
            $data['venta_credito'][$mon['id_moneda']] = $this->db->select_sum('credito.dec_credito_montocuota', 'total')
                ->from('venta')
                ->join('credito', 'credito.id_venta = venta.venta_id')
                ->where('venta.id_moneda', $mon["id_moneda"])
                ->where('venta.fecha >=', $fecha)
                ->where('venta.fecha <', $fechadespues)
                ->where('venta.venta_status', "COMPLETADO")
                ->where('venta.condicion_pago', 2)
                ->get()->row();

            //COBRANZAS DE CUOTAS
            $data['cobranza_cuota'][$mon['id_moneda']] = $this->db->select_sum('credito_cuotas_abono.monto_abono', 'total')
                ->from('credito_cuotas_abono')
                ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                ->join('venta', 'venta.venta_id = credito_cuotas.id_venta')
                ->where('venta.id_moneda', $mon["id_moneda"])
                ->where('credito_cuotas_abono.fecha_abono >=', $fecha)
                ->where('credito_cuotas_abono.fecha_abono <', $fechadespues)
                ->get()->row();

            //COMPRAS AL CONTADO
            $data['compra_contado'][$mon['id_moneda']] = $this->db->select_sum('ingreso.total_ingreso', 'total')
                ->from('ingreso')
                ->where('ingreso.id_moneda', $mon["id_moneda"])
                ->where('ingreso.fecha_registro >=', $fecha)
                ->where('ingreso.fecha_registro <', $fechadespues)
                ->where('ingreso.ingreso_status', "COMPLETADO")
                ->where('ingreso.pago', "CONTADO")
                ->get()->row();

            //COMPRAS AL CREDITO
            $data['compra_credito'][$mon['id_moneda']] = $this->db->select_sum('ingreso.total_ingreso', 'total')
                ->from('ingreso')
                ->where('ingreso.id_moneda', $mon["id_moneda"])
                ->where('ingreso.fecha_registro >=', $fecha)
                ->where('ingreso.fecha_registro <', $fechadespues)
                ->where('ingreso.ingreso_status', "COMPLETADO")
                ->where('ingreso.pago', "CREDITO")
                ->get()->row();

            //PAGO A PROVEEDORES
            $data['pagos_cuota'][$mon['id_moneda']] = $this->db->select_sum('pagos_ingreso.pagoingreso_monto', 'total')
                ->from('pagos_ingreso')
                ->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id = pagos_ingreso.pagoingreso_ingreso_id')
                ->join('ingreso', 'ingreso.id_ingreso = ingreso_credito_cuotas.ingreso_id')
                ->where('ingreso.id_moneda', $mon["id_moneda"])
                ->where('pagos_ingreso.pagoingreso_fecha >=', $fecha)
                ->where('pagos_ingreso.pagoingreso_fecha <', $fechadespues)
                ->get()->row();

            //GASTOS
            $data['gasto'][$mon['id_moneda']] = $this->db->select_sum('gastos.total', 'total')
                ->from('gastos')
                ->where('gastos.fecha >=', $fecha)
                ->where('gastos.fecha <', $fechadespues)
                ->where('gastos.id_moneda', $mon["id_moneda"])
                ->where('gastos.status_gastos', 0)
                ->get()->row();

        }

        $mpdf = new mPDF('utf-8', array(80, 200));
        $mpdf->WriteHTML($this->load->view('menu/cajas/pdfCuadreCaja', $data, true));
        $mpdf->Output();
    }


    function toPDF_cuadre_caja_reporte()
    {

        if ($this->input->post('usuario', true) == "TODOS") {

            $data['verificarusuario'] = "todos";
        } else {

            $campo = 'nUsuCodigo';
            $valor = $this->input->post('usuario', true);

            $data['verificarusuario'] = $this->usuario_model->get_by($campo, $valor);

        }


        $select = 'sum(detalle_importe) as total_venta, nombre_condiciones,username,nombre,dias,id_vendedor';
        $from = "venta";
        $join = array('detalle_venta', 'condiciones_pago', 'usuario');
        $campos_join = array('detalle_venta.id_venta=venta.venta_id', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'usuario.nUsuCodigo=venta.id_vendedor');

        $fecha = date('Y-m-d', strtotime($this->input->post('fecha', true)));

        $fechadespues = strtotime('+1 day', strtotime($fecha));
        $where = array('venta.fecha >=' => $fecha,
            'venta.fecha <' => date('Y-m-d', $fechadespues),
            'venta_status' => "COMPLETADO");
        //var_dump($this->input->post('usuario',true));

        if ($this->input->post('usuario', true) == "TODOS") {

            $group = "venta.condicion_pago, venta.id_vendedor";
        } else {
            $where['id_vendedor'] = $this->input->post('usuario', true);
            $group = "venta.condicion_pago";

        }
        $data['ventas'] = $this->v->traer_by($select, $from, $join, $campos_join, false, $where, $group, false, "RESULT_ARRAY");

/////////////////////////////////////////////////////

        /*es una bandera para saber si voy a agruparlos o no*/
        $group = false;

        $where = array('fecha_abono >=' => $fecha,
            'fecha_abono <' => date('Y-m-d', $fechadespues)
        );

        if ($this->input->post('usuario', true) == "TODOS") {
            $group = 'usuario_pago';
        } else {
            $where['usuario_pago'] = $this->input->post('usuario', true);
        }

        /*se buscan los pagos de cuotas hechos hoy*/
        $data['cobroxcuotas'] = $this->credito_cuotas_abono_model->get_suma_cuotas_usuarios($where, $group);

        /////////////////////////////

        $select = "select sum(pagoingreso_monto) as suma_pago, pagoingreso_usuario,username,nombre from pagos_ingreso left join
        usuario on usuario.nUsuCodigo=pagos_ingreso.pagoingreso_usuario
        where pagoingreso_fecha >='" . $fecha . "' and pagoingreso_fecha<'" . date('Y-m-d', $fechadespues) . "' ";
        if ($this->input->post('usuario', true) == "TODOS") {

            $select .= " group by pagoingreso_usuario";
        } else {

            $select .= ' and pagoingreso_usuario=' . $this->input->post('usuario', true) . '';

        }
        $query = $this->db->query($select);

        $data['pagos_ingresos'] = $query->result_array();
        ///////////////////////////////
        $group = false;
        $where = array('fecha_registro >=' => $fecha,
            'fecha_registro <' => date('Y-m-d', $fechadespues),
            'pago' => "CONTADO",
            'ingreso_status' => "COMPLETADO");

        $select = ' sum(total_ingreso) as suma_pago, usuario.nUsuCodigo,username,nombre';
        $from = "ingreso";
        $join = array('usuario');
        $campos_join = array('usuario.nUsuCodigo=ingreso.nUsuCodigo');

        if ($this->input->post('usuario', true) == "TODOS") {

            $group = "nUsuCodigo";
        } else {

            $where['ingreso.nUsuCodigo'] = $this->input->post('usuario', true);

        }
        $data['ingresos'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, $group, false, "RESULT_ARRAY");

/////////////////////////////////////////////////////////////

        $select = " select sum(total) as suma_gasto,username,nombre,gasto_usuario from gastos
        left join usuario on usuario.nUsuCodigo=gastos.gasto_usuario
        where fecha >='" . $fecha . "' and fecha<'" . date('Y-m-d', $fechadespues) . "' ";
        if ($this->input->post('usuario', true) == "TODOS") {

            $select .= " group by gasto_usuario";
        } else {

            $select .= ' and gasto_usuario=' . $this->input->post('usuario', true) . '';

        }
        $query = $this->db->query($select);
        $data['gastos'] = $query->result_array();


        $data['usuarios'] = $this->usuario_model->select_all_user();
        $mpdf = new mPDF('utf-8', array(80, 200));
        /*$data["cuadreja"] =$this->caja_model->reporte_cuadre_caja(date("Y-m-d", strtotime($this->input->post('fecha',true))),
            $this->input->post('usuario',true));*/
        $mpdf->WriteHTML($this->load->view('menu/cajas/pdfCuadreCajaReporte', $data, true));
        $mpdf->Output();
    }

    function toPDF_estadoCuenta()
    {


        $mpdf = new mPDF('utf-8', 'A4-L');
        $condicion = 'v.venta_status="COMPLETADO" ';

        if ($this->input->post('cboCliente2', true) != -1) {
            $condicion .= ' and v.id_cliente =' . $this->input->post("cboCliente2", true) . " ";
        }
        if ($_POST['fecIni2'] != "") {
            $condicion .= 'and v.FechaReg >="' . date('Y-m-d', strtotime($_POST['fecIni2'])) . '" ';
        }

        if ($_POST['fecFin2'] != "") {
            $condicion .= ' and v.FechaReg <="' . date('Y-m-d', strtotime($_POST['fecFin2'])) . '" ';
        }

        if ($this->input->post("estado", true) != "TODOS") {

            $condicion .= ' and `cd`.`var_credito_estado` ="' . $this->input->post("estado", true) . '" ';
        }


        $order = " order by `v`.`fecha` desc ";
        if ($this->input->post("local", true) == "TODOS") {
            $order .= ',  `v`.`local_id` desc ';
        } else {
            $condicion .= ' and `v`.`local_id`=' . $this->input->post("local", true) . '';
        }
        $data['local'] = $this->input->post("local", true);


        $data['fecInicio'] = date("Y-m-d", strtotime($this->input->post('fecIni2', true)));
        $data['fecFin'] = date("Y-m-d", strtotime($this->input->post('fecFin2', true)));

        $data['estado_cuenta'] = $this->v->select_venta_estadocuenta($condicion, $order);

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfEstadoCuenta', $data, true));
        $mpdf->Output();
    }

    function toPDF_pagoPendiente()
    {
        $mpdf = new mPDF('utf-8', 'A4-L');
        $condicion = 'v.venta_status="COMPLETADO" ';

        if ($this->input->post('cboCliente2', true) != -1) {
            $condicion .= ' and v.Cliente_Id =' . $this->input->post("cboCliente2", true) . " ";
        }
        if ($_POST['fecIni2'] != "") {
            $condicion .= 'and v.fecha >="' . date('Y-m-d', strtotime($_POST['fecIni2'])) . '" ';
        }

        if ($_POST['fecFin2'] != "") {
            $condicion .= ' and v.fecha <="' . date('Y-m-d', strtotime($_POST['fecFin2'])) . '" ';
        }

        $order = " order by `v`.`fecha` desc ";
        if ($this->input->post("local", true) == "TODOS") {
            $order .= ',  `v`.`local_id` desc ';
        } else {
            $condicion .= ' and `v`.`local_id`=' . $this->input->post("local", true) . '';
        }

        $data['local'] = $this->input->post("local", true);

        $data['pago_pendiente'] = $this->v->get_pagos_pendientes($condicion, $order);


        $data['fecInicio'] = date("Y-m-d", strtotime($this->input->post('fecIni2', true)));
        $data['fecFin'] = date("Y-m-d", strtotime($this->input->post('fecFin2', true)));

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfPagoPendiente', $data, true));
        $mpdf->Output();
    }


    function toPDF_ingresodetalle()
    {

        $data['moneda_local'] = $this->monedas_model->get_moneda_default();

        $mpdf = new mPDF('utf-8', 'A4-L');

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->l->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->l->get_all_usu($usu);
        }

        if ($this->input->post('local2') != "TODOS") {
            $condicion = array('local_id' => $this->input->post('local2'));
            $data['local'] = $this->input->post('local2');

        }

        if ($this->input->post('fecIni2') != "") {

            $condicion['fecha_registro >= '] = date('Y-m-d', strtotime($this->input->post('fecIni2'))) . " " . date('H:i:s', strtotime('0:0:0'));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('fecIni2'))) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($this->input->post('fecFin2') != "") {

            $condicion['fecha_registro <='] = date('Y-m-d', strtotime($this->input->post('fecFin2'))) . " " . date('H:i:s', strtotime('23:59:59'));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('fecFin2'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }
        if ($this->input->post('proveedor2') != "TODOS") {
            $condicion['int_Proveedor_id'] = $this->input->post('proveedor2');
            $data['proveedor'] = $this->input->post('proveedor2');
        }

        $condicion['ingreso.id_ingreso > '] = 0;
        $order = 'detalleingreso.id_detalle_ingreso asc';

        $data['ingresos'] = $this->detalle_ingreso_model->get_detalleingresodetallado($condicion, $order);

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfIngresoDetalle', $data, true));
        $mpdf->Output();
    }


    function toExcel_ingresodetalle()
    {

        $data['moneda_local'] = $this->monedas_model->get_moneda_default();

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->l->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->l->get_all_usu($usu);
        }

        if ($this->input->post('local1') != "TODOS") {
            $condicion = array('local_id' => $this->input->post('local1'));

        }

        if ($this->input->post('fecIni1') != "") {

            $condicion['fecha_registro >= '] = date('Y-m-d', strtotime($this->input->post('fecIni1'))) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($this->input->post('fecFin1') != "") {

            $condicion['fecha_registro <='] = date('Y-m-d', strtotime($this->input->post('fecFin1'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }
        if ($this->input->post('proveedor1') != "TODOS") {
            $condicion['int_Proveedor_id'] = $this->input->post('proveedor1');
        }

        $condicion['ingreso.id_ingreso > '] = 0;
        $order = 'detalleingreso.id_detalle_ingreso asc';

        $data['ingresos'] = $this->detalle_ingreso_model->get_detalleingresodetallado($condicion, $order);

        $this->load->view('menu/reportes/excelIngresoDetalle', $data);
    }


    function toPDF_traspaso()
    {
        $mpdf = new mPDF('utf-8', 'A4-L');

        $this->load->model('historico/historico_model');
        $condicion = array(
            'movimiento_historico.tipo_movimiento' => "TRASPASO"
        );

        if ($this->input->post('local') != "TODOS") {
            $condicion['local_id'] = $this->input->post('local');
        }
        $data['local'] = $this->input->post('locales', true);
        if ($_POST['fecIni'] != "") {
            $condicion['date >= '] = date('Y-m-d', strtotime($_POST['fecIni']));
        }

        if ($_POST['fecFin'] != "") {
            $fechadespues = strtotime('+1 day', strtotime($_POST['fecFin']));

            $condicion['date <= '] = date('Y-m-d', $fechadespues);
        }

        if ($this->input->post('productos', true) != "TODOS") {
            $condicion['producto_id'] = $this->input->post('productos', true);
        }

        if ($this->input->post('tipo', true) != "TODOS") {
            $condicion['tipo_operacion'] = $this->input->post('tipo', true);
        }
        //var_dump($condicion);
        $data['movimientos'] = $this->historico_model->get_historico($condicion);

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdftraspaso', $data, true));
        $mpdf->Output();
    }


    function toExcel_traspaso()
    {

        $this->load->model('historico/historico_model');
        $condicion = array(
            'movimiento_historico.tipo_movimiento' => "TRASPASO"
        );

        if ($this->input->post('local') != "TODOS") {
            $condicion['local_id'] = $this->input->post('local');
        }
        $data['local'] = $this->input->post('locales', true);
        if ($_POST['fecIni'] != "") {
            $condicion['date >= '] = date('Y-m-d', strtotime($_POST['fecIni']));
        }

        if ($_POST['fecFin'] != "") {
            $fechadespues = strtotime('+1 day', strtotime($_POST['fecFin']));

            $condicion['date <= '] = date('Y-m-d', $fechadespues);
        }

        if ($this->input->post('productos', true) != "TODOS") {
            $condicion['producto_id'] = $this->input->post('productos', true);
        }

        if ($this->input->post('tipo', true) != "TODOS") {
            $condicion['tipo_operacion'] = $this->input->post('tipo', true);
        }
        //var_dump($condicion);
        $data['movimientos'] = $this->historico_model->get_historico($condicion);


        $this->load->view('menu/reportes/excelTraspaso', $data);
    }


}
