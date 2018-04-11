<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
ini_set("memory_limit", "250M");

class ingresos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('cliente/cliente_model', 'cl');
            $this->load->model('local/local_model');
            $this->load->model('producto/producto_model');
            $this->load->model('precio/precios_model', 'precios');
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('ingreso/ingreso_model');
            $this->load->model('impuesto/impuestos_model');
            $this->load->model('detalle_ingreso/detalle_ingreso_model');
            $this->load->model('pagos_ingreso/pagos_ingreso_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
        } else {
            redirect(base_url(), 'refresh');
        }

        //$this->load->library('mpdf53/mpdf');

//pd producto pv proveedor
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');

    }


    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        //$data['locales']=$this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        /*esta la voy a usar cuando vaya a editar*/
        $idingreso = $this->input->post('idingreso');

        $data['costos'] = !empty($_GET['costos']) ? $_GET['costos'] : $_POST['costos'];


        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["lstProducto"] = $this->producto_model->select_all_producto();
        $data["lstProveedor"] = $this->proveedor_model->select_all_proveedor();
        $data["monedas"] = $this->monedas_model->get_all();
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();

        $data['dialog_compra_credito'] = $this->load->view('menu/ingreso/dialog_compra_credito', array(), true);


        /*declaro facturar en no, para que por defecto sea no, ahora los valores de si o no, vienen por post*/
        $data['facturar'] = "NO";
        if ($this->input->post('facturar')) {
            $data['facturar'] = $this->input->post('facturar', true);
        }

        $data["ingreso"] = array();
        if ($idingreso != FALSE) {

            $condicion = array(
                'ingreso.id_ingreso' => $idingreso
            );
            $data["ingreso"] = $this->ingreso_model->get_ingresos_by($condicion);
            $data["ingreso"] = $data["ingreso"][0];

            /*el detalle del ingreso, lo llamo en la siguiente funcion*/

        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/ingresos', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_detalle_ingresos()
    {
        $idingreso = $this->input->post('idingreso');

        $json["detalles"] = $this->ingreso_model->get_detalles_by($idingreso);

        $productos = $this->db->select('detalleingreso.id_producto as producto_id')
            ->from('detalleingreso')
            ->where('detalleingreso.id_ingreso', $idingreso)
            ->group_by('detalleingreso.id_producto')->get()->result();

        $unidad_minima = array();
        foreach ($productos as $prod)
            $unidad_minima[$prod->producto_id] = $this->unidades_model->get_um_min_by_producto($prod->producto_id);

        $json['um_min'] = $unidad_minima;

        echo json_encode($json);

    }

    function get_unidades_has_producto()
    {

        $id_producto = $this->input->post('id_producto');
        $id_moneda = $this->input->post('moneda_id');

        $data['unidades'] = $this->unidades_model->get_unidades_costos($id_producto, $id_moneda);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_series($prod_id)
    {
        $this->load->model('producto_serie/producto_serie_model');
        $data['producto_id'] = $prod_id;
        $data['series'] = $this->producto_serie_model->get_by(array('producto_id' => $prod_id));

        echo $this->load->view('menu/ingreso/list_series', $data, TRUE);
    }


    function registrar_ingreso()
    {
        if ($this->input->is_ajax_request()) {

            /*la declaro aqui ya que en registro de existencia no se usa*/
            $fecha_emision = null;
            if ($this->input->post('fecEmision', true)) {
                $this->form_validation->set_rules('fecEmision', 'fecEmision', 'required');
                $fecha_emision = date("Y-m-d H:i:s", strtotime($this->input->post('fecEmision', true) . " " . date("H:i:s")));
            }

            //$this->form_validation->set_rules('doc_serie', 'doc_serie', 'required');
            // $this->form_validation->set_rules('doc_numero', 'doc_numero', 'required');
            //$this->form_validation->set_rules('cboTipDoc', 'cboTipDoc', 'required');
            $this->form_validation->set_rules('cboProveedor', 'cboProveedor', 'required');
            /*$this->form_validation->set_rules('subTotal', 'subTotal', 'required');
            $this->form_validation->set_rules('montoigv', 'montoigv', 'required');
            $this->form_validation->set_rules('totApagar', 'totApagar', 'required');*/

            //echo 'campo:'.$this->input->post('tasa_id', true).':fin';

            if ($this->form_validation->run() == false):
                echo "Error de Validacion de Formularios";
            else:
                /*if (isset($_POST['subTotal']) && $_POST['subTotal'] != "" && isset($_POST['montoigv']) && $_POST['montoigv'] != "" &&
                    isset($_POST['totApagar']) && $_POST['totApagar'] != "") {*/
                $tasa_cambio = explode("_", $this->input->post('monedas', true));

                if ($this->input->post('costos') === 'true') {
                    $status = COMPLETADO;
                } else {
                    $status = "PENDIENTE";
                }
                $comp_cab_pie = array(

                    'fecReg' => date("Y-m-d H:i:s"),
                    'fecEmision' => $fecha_emision,
                    'doc_serie' => $this->input->post('doc_serie', true),
                    'doc_numero' => $this->input->post('doc_numero', true),
                    'cboTipDoc' => $this->input->post('cboTipDoc', true),
                    'cboProveedor' => $this->input->post('cboProveedor', true),
                    'subTotal' => $this->input->post('subTotal', true),
                    'costos' => $this->input->post('costos', true),
                    'montoigv' => $this->input->post('montoigv', true),
                    'totApagar' => $this->input->post('totApagar', true),
                    'tipo_ingreso' => $this->input->post('tipo_ingreso', true),
                    'pago' => $this->input->post('pago', true),
                    'local_id' => $this->input->post('local', true),
                    'ingreso_observacion' => $this->input->post('observacion', true),
                    'id_moneda' => $this->input->post('moneda_id', true),
                    'tasa_cambio' => $this->input->post('tasa_id', true),
                    'status' => $status,
                    'facturar' => $this->input->post('facturar'),
                    'tipo_impuesto' => $this->input->post('tipo_impuesto')
                );


                $credito['c_inicial'] = $this->input->post('c_saldo_inicial') != '' ? $this->input->post('c_saldo_inicial') : 0;
                $credito['c_precio_contado'] = $this->input->post('c_precio_contado');
                $credito['c_precio_credito'] = $this->input->post('c_precio_credito');
                $credito['c_tasa_interes'] = $this->input->post('c_tasa_interes');
                $credito['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
                $credito['c_fecha_giro'] = $this->input->post('c_fecha_giro');
                $credito['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');
                $cuotas = json_decode($this->input->post('cuotas', true));

                $id = $this->input->post('id_ingreso', true);
//                 var_dump($this->input->post('doc_serie', true));
//                 var_dump( json_decode($this->input->post('lst_producto', true)));
// return false;
                if (empty($id)) {

                    $rs = $this->ingreso_model->insertar_compra($comp_cab_pie, json_decode($this->input->post('lst_producto', true)), $credito, $cuotas);


                } else {
                    /*si entra aqui es solo para actualizar el ingreso*/

                    $comp_cab_pie['facturar'] = $this->input->post('facturar', true);
                    $comp_cab_pie['id_ingreso'] = $id;

                    if ($this->input->post('facturar') == 'SI') {
                        $comp_cab_pie['status'] = "FACTURADO";
                    } else {
                        $comp_cab_pie['status'] = "COMPLETADO";
                    }
                    $rs = $this->ingreso_model->update_compra($comp_cab_pie, json_decode($this->input->post('lst_producto', true)), $credito, $cuotas);
                }

                if ($rs != false) {
                    $json['success'] = 'Solicitud Procesada con exito';
                    $json['id'] = $rs;

                } else {
                    if ($this->ingreso_model->error != NULL)
                        $json['error'] = $this->ingreso_model->error;
                    else
                        $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
                }


                /* } else {
                     $json['error'] = 'Algunos campos son requeridos';

                 }*/
            endif;
        } else {


            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';


        }
        echo json_encode($json);
    }

    function lst_reg_ingreso()
    {
        if ($this->input->is_ajax_request()) {
            //$data['lstCompra'] = $this->v->select_compra(date("y-m-d", strtotime($this->input->post('fecIni',true))),date("y-m-d", strtotime($this->input->post('fecFin',true))));
            //$this->load->view('menu/ventas/tbl_listareg_compra',$data);
            echo json_encode($this->ingreso_model->select_compra(date("y-m-d", strtotime($this->input->post('fecIni', true))), date("y-m-d", strtotime($this->input->post('fecFin', true)))));
        } else {
            redirect(base_url() . 'ingresos/', 'refresh');
        }
    }

    function consultar()
    {

        $data['locales'] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/consultar_ingreso', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function consultarCompras()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/compras', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lista_compra()
    {

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        $params = array(
            'local_id' => $this->input->post('local_id'),
            'moneda_id' => $this->input->post('moneda_id'),
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin
        );

        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();
        $data['ingresos'] = $this->ingreso_model->get_compras($params);
        $data['ingreso_totales'] = $this->ingreso_model->get_totales_compra($params);

        $this->load->view('menu/ingreso/lista_compra', $data);
    }


    function lst_cuentas_porpagar()
    {
        if ($this->input->is_ajax_request()) {
            if (!empty($this->input->post('local_id'))){
                $local_id = $this->input->post('local_id');
                $data['local'] = $local_id;
            }else{
                $usu = $this->session->userdata('nUsuCodigo');
                $dataLocal = $this->local_model->get_all_usu($usu);
                $arr = array();
                foreach ($dataLocal as $value) {
                    $arr[] = $value['int_local_id'];
                }
                $local_id = implode(",", $arr);
                $data['local'] = 'TODOS';
            }
            
            $params = array(
                'local_id' => $local_id,
                'proveedor_id' => $this->input->post('proveedor'),
                'moneda_id' => $this->input->post('moneda'),
            );

            $data["lstproveedor"] = $this->proveedor_model->get_cuentas_pagar($params);
            $data["ingreso_totales"] = $this->proveedor_model->get_cuentas_pagar_totales($params);


            if ($this->input->is_ajax_request()) {

                $this->load->view('menu/proveedor/tbl_lst_cuentasporpagar', $data);

            } else {
                redirect(base_url() . 'proveedor/', 'refresh');
            }
        }
    }

    public function ver_deuda()
    {
        $id_ingreso = $this->input->post('id_ingreso');
        $this->load->model('metodosdepago/metodos_pago_model');

        if ($id_ingreso != FALSE) {

            $dataresult['cronogramas'] = $this->ingreso_model->get_cronograma_by_cuotas($id_ingreso);

            $dataresult['ingreso'] = $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda')
                ->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id')
                ->get_where('ingreso', array('id_ingreso' => $id_ingreso))->row();

            $dataresult['proveedor'] = $this->db->get_where('proveedor', array('id_proveedor' => $dataresult['ingreso']->int_Proveedor_id))->row();

            $dataresult['ingreso_detalles'] = $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto')
                ->get_where('detalleingreso', array('id_ingreso' => $id_ingreso))->result();

            $dataresult['credito'] = $this->db->get_where('ingreso_credito', array('ingreso_id' => $id_ingreso))->row();

            $dataresult['pagos_ingreso'] = $this->db->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id=pagos_ingreso.pagoingreso_ingreso_id')
                ->join('metodos_pago', 'metodos_pago.id_metodo=pagos_ingreso.medio_pago_id')
                ->join('banco', 'banco.banco_id=pagos_ingreso.banco_id', 'left')
                ->group_by('pagoingreso_id')
                ->get_where('pagos_ingreso', array('ingreso_credito_cuotas.ingreso_id' => $id_ingreso))->result();

            $dataresult['metodos'] = $this->metodos_pago_model->get_all();
            $dataresult['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();
            $dataresult['tarjetas'] = $this->db->get('tarjeta_pago')->result();
            $dataresult['cajas'] = $this->db->join('caja_desglose', 'caja_desglose.caja_id=caja.id')
                ->get_where('caja', array(
                    'moneda_id' => $dataresult['ingreso']->id_moneda,
                    'local_id' => $dataresult['ingreso']->local_id,
                    'retencion' => 0,
                    'caja_desglose.estado' => 1
                ))->result();


            $this->load->view('menu/ingreso/tbl_ingreso_cronograma_pago', $dataresult);
//            $this->load->view('menu/proveedor/form_montoapagar', $result);
        }
    }

    public function pagoCuotaCredito()
    {
        /*este metodo hace su funcion cuando se paga una cuota, bien sea pago anticipado o no*/

        /*estos dos, siempre los recibo*/
        $ingreso_id = $this->input->post('ingreso_id');

        $metodo_pago = $this->input->post('metodo_pago');
        $tipo_metodo = $this->input->post('tipo_metodo');
        $numero_ope = $this->input->post('nro_operacion');
        $banco = $this->input->post('banco');
        $cuenta_id = $this->input->post('cuenta_id');

        $idCuota = $this->input->post('cuota_id');
        $montodescontar = $this->input->post('montodescontar');
        $correlativo_cuota = $this->input->post('correlativo_cuota');
        $anticipado = false;

        $return = $this->ingreso_model->pagar_cuota($idCuota, $montodescontar, $metodo_pago, $ingreso_id, $anticipado, $numero_ope, $banco, $tipo_metodo, $cuenta_id);

        $dataresul['success'] = "El pago se ha realizado satisfactoriamente";
        echo json_encode($dataresul);

    }

    function guardarPago()
    {
        if ($this->input->is_ajax_request()) {

            $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $this->input->post('ingreso_id')))->row();
            $detalle = array(
                'pagoingreso_ingreso_id' => $this->input->post('ingreso_id'),
                'pagoingreso_fecha' => date("Y-m-d H:i:s"),
                'pagoingreso_monto' => number_format($this->input->post('cantidad_a_pagar'), 2, '.', ''),
                'pagoingreso_restante' => number_format($this->input->post('total_pendiente') - $this->input->post('cantidad_a_pagar'), 2, '.', ''),
                'medio_pago_id' => $this->input->post('pago_id'),
                'banco_id' => $this->input->post('banco_id', NULL),
                'operacion' => $this->input->post('num_oper', NULL),
                'pagoingreso_usuario' => $this->session->userdata('nUsuCodigo'),
                'id_moneda' => $ingreso->id_moneda,
                'tasa_cambio' => $ingreso->tasa_cambio
            );

            $save_historial = $this->pagos_ingreso_model->guardar($detalle);

            $json = array();
            if ($save_historial != false) {
                if ($save_historial != false) {
                    $json['success'] = 'success';
                    $json['ingreso_id'] = $detalle['pagoingreso_ingreso_id'];
                    $json['id_historial'] = $save_historial;
                } else {
                    $json['error'] = 'error';
                }
            }

            echo json_encode($json);

        }
    }

    function guardarPago1()
    {
        if ($this->input->is_ajax_request()) {

            $detalle = json_decode($this->input->post('lst_producto', true));
            // var_dump($detalle);
            $where = array(
                'pagoingreso_ingreso_id' => $detalle[0]->id_ingreso
            );
            $select = 'sum(pagoingreso_monto) as suma';
            $from = "pagos_ingreso";
            $order = "pagoingreso_fecha desc";
            $buscar = $this->pagos_ingreso_model->traer_by($select, $from, false, false, $where, false, $order, "RESULT_ARRAY");


            if (count($buscar) > 0) {

                $subtotal = 0;
                $pos = strrpos($buscar[0]['suma'] + $detalle[0]->cantidad_ingresada, '.');
                if ($pos === false) {
                    $subtotal .= ".00";
                } else {
                    $subtotal = substr($buscar[0]['suma'] + $detalle[0]->cantidad_ingresada, 0, $pos + 3);
                };

                $pos = strrpos($detalle[0]->total_ingreso, '.');
                if ($pos === false) {
                    $detalle[0]->total_ingreso .= ".00";
                } else {
                    $detalle[0]->total_ingreso = substr($detalle[0]->total_ingreso, 0, $pos + 3);
                };

                if ($subtotal <= $detalle[0]->total_ingreso) {

                    $save_historial = $this->pagos_ingreso_model->guardar($detalle);
                } else {

                    $save_historial = false;
                }

            } else {
                $save_historial = $this->pagos_ingreso_model->guardar($detalle);
            }


            if ($save_historial != false) {

                $json['exito'] = true;

            } else {

                $json['exito'] = false;
            }

            echo json_encode($json);

        }
    }

    function cargar_vistapago_proveedor()
    {
        if ($this->input->is_ajax_request()) {
            $detalle = json_decode($this->input->post('lst_producto', true));

            $where = array(
                'detalleingreso.id_ingreso' => $detalle[0]->id_ingreso
            );
            $join = false;
            $campos_join = false;
            $select = 'sum(total_detalle) as suma_detalle ';
            $dataresult['suma_detalle'] = $this->detalle_ingreso_model->get_by($select, $join, $campos_join, $where, false, false, false);


            $where = array(
                'ingreso.id_ingreso' => $detalle[0]->id_ingreso,
                'ingreso_status' => COMPLETADO
            );

            $select = 'ingreso.*, pagos_ingreso.*, proveedor.*, moneda.* ';
            $from = "ingreso";


            $join = array('proveedor', 'pagos_ingreso', 'moneda');
            $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso', 'moneda.id_moneda=ingreso.id_moneda', 'pagos_ingreso.id_moneda=moneda.id_moneda', 'pagos_ingreso.id_moneda=ingreso.id_moneda');

            $tipo_join[0] = "";
            $tipo_join[1] = "left";

            $group = "id_ingreso";
            $dataresult['cuentas'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, $group, false, "RESULT_ARRAY");

            $dataresult['cuota'] = $detalle[0]->cantidad_ingresada;
            ///////////////////busco lo que resta de deuda
            $where = array(
                'pagoingreso_ingreso_id' => $detalle[0]->id_ingreso
            );
            $select = 'pagoingreso_restante';
            $from = "pagos_ingreso";
            $order = "pagoingreso_fecha desc";
            $buscar_restante = $this->pagos_ingreso_model->traer_by($select, $from, false, false, $where, false, $order, "RESULT_ARRAY");


            $dataresult['restante'] = $buscar_restante[0]['pagoingreso_restante'];


            // var_dump($buscar_restante);
            $this->load->view('menu/proveedor/visualizarIngresoPendiente', $dataresult);

        }
    }

    function imprimir_pago_pendiente()
    {
        if ($this->input->is_ajax_request()) {

            $id_historial = $this->input->post('id_historial', true);
            $id_ingreso = $this->input->post('ingreso_id', true);

            $where = array(
                'pagoingreso_ingreso_id' => $id_ingreso,
                'pagoingreso_id' => $id_historial
            );
            $select = 'id_ingreso,ingreso_status,pagos_ingreso.*, sum(pagoingreso_monto) as suma, moneda.simbolo ';
            $from = "ingreso";
            $join = array(
                'pagos_ingreso', 'moneda');
            $campos_join = array(
                'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso',
                'moneda.id_moneda = ingreso.id_moneda'
            );

            $group = " id_ingreso";
            $data['pagos_ingreso'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, $group, false, "RESULT_ARRAY");

            //////////////////////

            $where = array(
                'ingreso.id_ingreso' => $id_ingreso,
                'ingreso_status' => COMPLETADO
            );
            $select = 'ingreso.*, proveedor.*, sum(total_detalle) AS suma_detalle, moneda.simbolo';
            $from = "ingreso";
            $join = array('proveedor', 'detalleingreso', 'moneda');
            $campos_join = array(
                'proveedor.id_proveedor=ingreso.int_Proveedor_id',
                'detalleingreso.id_ingreso=ingreso.id_ingreso',
                'moneda.id_moneda = ingreso.id_moneda');


            $group = "detalleingreso.id_ingreso";
            $data['cuentas'] = $this->pagos_ingreso_model->traer_by($select, $from, $join, $campos_join, $where, $group, false, "RESULT_ARRAY");


            //
            $data['id_historial'] = true;
            $data['cuota'] = $data['pagos_ingreso'][0]['pagoingreso_monto'];

            ///////////////////busco lo que resta de deuda

            $data['restante'] = $data['pagos_ingreso'][0]['pagoingreso_restante'];

            $this->load->view('menu/proveedor/visualizarIngresoPendiente', $data);
        }


    }

    public function vertodoingreso()
    {
        $id_ingreso = $this->input->post('id_ingreso');

        if ($id_ingreso != FALSE) {

            $dataresult['cronogramas'] = $this->ingreso_model->get_cronograma_by_cuotas($id_ingreso);

            $dataresult['ingreso'] = $this->db->join('moneda', 'moneda.id_moneda=ingreso.id_moneda')
                ->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id')
                ->get_where('ingreso', array('id_ingreso' => $id_ingreso))->row();

            $dataresult['ingreso_detalles'] = $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto')
                ->join('unidades', 'unidades.id_unidad = detalleingreso.unidad_medida')
                ->get_where('detalleingreso', array('id_ingreso' => $id_ingreso))->result();

            $dataresult['credito'] = $this->db->get_where('ingreso_credito', array('ingreso_id' => $id_ingreso))->row();

            $dataresult['pagos_ingreso'] = $this->db->join('ingreso_credito_cuotas', 'ingreso_credito_cuotas.id=pagos_ingreso.pagoingreso_ingreso_id')
                ->join('metodos_pago', 'metodos_pago.id_metodo=pagos_ingreso.medio_pago_id')
                ->join('banco', 'banco.banco_id=pagos_ingreso.banco_id', 'left')
                ->group_by('pagoingreso_id')
                ->get_where('pagos_ingreso', array('ingreso_credito_cuotas.ingreso_id' => $id_ingreso))->result();

            $this->load->view('menu/ingreso/visualizar_detalle_ingreso', $dataresult);
        }
    }

    public function cambiar_fecha()
    {
        $data['id'] = $this->input->post('id');
        $data['fecha'] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha') . " " . date('H:i:s')));

        $this->db->where('id', $data['id']);
        $this->db->update('ingreso_credito_cuotas', array('fecha_vencimiento' => $data['fecha']));
    }


    function devolucion()
    {
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/devolucion', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function anular_ingreso()
    {

        $resultado = $this->ingreso_model->anular_ingreso();

        if (!$resultado) {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode(array('error_prod' => $this->ingreso_model->error));
        }
        exit();
    }


    function get_ingresos()
    {
        $condicion = array();
        if ($this->input->post('id_local') != "seleccione") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local_id'] = $this->input->post('id_local');
        }
        if ($this->input->post('desde') != "") {
            $fecha = date('Y-m-d', strtotime($this->input->post('desde', true)));
            $condicion['fecha_registro >= '] = $fecha;

            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde')));
        }
        if ($this->input->post('hasta') != "") {
            $fecha = date('Y-m-d', strtotime($this->input->post('hasta', true)));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha_registro <'] = date('Y-m-d', $fechadespues);

            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta')));
        }

        if ($this->input->post('anular') != 0) {

            $data['anular'] = 1;
        }

        /*este es para los estados de ingresos, el selectde estado ingresos*/
        if ($this->input->post('status') != "seleccione") {
            $where_in = false;

            if ($this->input->post('status') == "PENDIENTE") {
                $where_in = array("PENDIENTE", "INGRESO_PENDIENTE");
            }

            if ($this->input->post('status') == "COMPLETADO") {
                $where_in = array("COMPLETADO", "INGRESO_COMPLETADO");
            }

            $data['status'] = $this->input->post('status');
        }

        /*pregunto si el select de estado facturacion es distinto se seleccione (vacio)
        y distinto de facturacion pendiente, ya que cuando este en facturacionpendiente, siempre va a estar el select de estado ingreso,
        en estatus completado, por lo tanto va a pasar arriba nada mas*/
        $cerrado = false;
        $condicion_facturar = false;
        if ($this->input->post('estado_facturacion') != "seleccione"
            and $this->input->post('estado_facturacion') != "FACTURACION_PENDIENTE"
        ) {
            $condicion_facturar = true;
            $where_in = false;
            if ($this->input->post('estado_facturacion') == "FACTURADO") {
                $cerrado = true;
                $where_in = array("FACTURADO", "CERRADO");
            }
            $data['status'] = $this->input->post('status');
        }

        /*si conficionfacturar es igual a true, llamo al metodo que busca en la tabla ingreso_contable*/
        if ($condicion_facturar == true) {
            $data['ingresos'] = $this->ingreso_model->get_ingresocontable_by_estatus($condicion, $where_in);
        } else {
            $data['ingresos'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
        }


        /*si $cerrado==true quiere decir que estado_facturacion es = a completado,
        por lo tanto debo buscar en la tabla ingresos, los que esten en estatus cerrado, ya que los que estan cerrados en la tabla
        ingresos_contable ya fueron buscados arriba*/
        if ($cerrado == true) {
            $where_in = array("CERRADO");
            $data['ingresos_cerrados_normales'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
        }


        $this->load->view('menu/ingreso/lista_ingreso', $data);

    }

    function get_ingresos_devolucion()
    {
        if ($this->input->post('id_local') != "seleccione") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local_id'] = $this->input->post('id_local');
        }

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        if ($fecha_ini != "") {
            $fecha = date('Y-m-d', strtotime($fecha_ini));
            $condicion['fecha_registro >= '] = $fecha;

            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde')));
        }
        if ($fecha_fin != "") {
            $fecha = date('Y-m-d', strtotime($fecha_fin));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha_registro <'] = date('Y-m-d', $fechadespues);

            $data['fecha_hasta'] = date('Y-m-d', strtotime($fecha_fin));
        }
        $condicion['ingreso_status'] = "COMPLETADO";
        if ($this->input->post('anular') != 0) {

            $data['anular'] = 1;
        }

        if ($this->input->post('moneda_id') != "seleccione") {
            $condicion['ingreso.id_moneda'] = $this->input->post('moneda_id');
            $data['moneda_id'] = $this->input->post('moneda_id');
        }

        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $this->input->post('moneda_id')))->row();
        $data['ingresos'] = $this->ingreso_model->get_ingresos_by($condicion);


        $this->load->view('menu/ingreso/lista_ingreso', $data);
    }

    function form($id = FALSE, $local = false, $ingreso = 'INGRESO')
    {

        $data = array();
        if ($id != FALSE) {

            $data['ingreso_tipo'] = $ingreso;

            $data['ingreso'] = $this->db->get_where('ingreso', array('id_ingreso' => $id))->row();

            if ($ingreso == "INGRESOCONTABLE") {
                $data['detalles'] = $this->detalle_ingreso_model->get_by_result_contable('detalleingreso_contable.id_ingreso', $id);
            } else {
                $data['detalles'] = $this->detalle_ingreso_model->get_by_result('detalleingreso.id_ingreso', $id);
            }
            $data['id_detalle'] = $id;

            $data['kardex'] = null;
            if ($data['ingreso']->ingreso_status == 'ANULADO') {
                $data['kardex'] = $this->db->get_where('kardex', array(
                    'ref_id' => $data['ingreso']->id_ingreso,
                    'io' => 1,
                    'tipo' => 7,
                    'operacion' => 6
                ))->row();
            }

        }

        $this->load->view('menu/ingreso/form_detalle_ingreso', $data);
    }

    function cerrar_ingreso()
    {

        /*este metodo coloca en estatus cerrado los ingresos*/
        if ($this->input->is_ajax_request()) {

            $id_ingreso = $this->input->post('idingreso');
            $ingreso_contable = $this->input->post('ingreso_contable');

            $condicion = array(
                'id_ingreso' => $id_ingreso
            );

            $tabla = 'ingreso';

            /*ingreso contable viene por post, para saber a cual tabla voy a actualizar si ingreso o ingreso contable*/
            if ($ingreso_contable == 'true') {
                $tabla = 'ingreso_contable';
            }
            $data = array(
                'ingreso_status' => "CERRADO"
            );

            $this->db->where($condicion);
            $this->db->update($tabla, $data);
            $json['success'] = true;
            echo json_encode($json);
        }
    }


    function pdf()
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        $local = $this->input->post('local');
        $fecha_desde = $this->input->post('fecIni2');
        $fecha_hasta = $this->input->post('fecFin2');
        $estatus = $this->input->post('estado');
        $detalle = $this->input->post('id_ingreso');

        $estado_facturacion = $this->input->post('estado_facturacion');
        $condicion = array();
        if ($local != "" and $detalle == 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha_registro >= '] = $fecha;
        }
        if ($fecha_hasta != "") {
            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha_registro <'] = date('Y-m-d', $fechadespues);
        }
        /*este es para los estados de ingresos, el selectde estado ingresos*/
        if ($estatus != "seleccione") {
            $where_in = false;

            if ($estatus == "PENDIENTE") {
                $where_in = array("PENDIENTE");
            }

            if ($estatus == "COMPLETADO") {
                $where_in = array("COMPLETADO");
            }

        }


        /*pregunto si el select de estado facturacion es distinto se seleccione (vacio)
        y distinto de facturacion pendiente, ya que cuando este en facturacionpendiente, siempre va a estar el select de estado ingreso,
        en estatus completado, por lo tanto va a pasar arriba nada mas*/
        $cerrado = false;
        $condicion_facturar = false;
        if ($estado_facturacion != "seleccione"
            and $estado_facturacion != "FACTURACION_PENDIENTE" and $estado_facturacion != false
        ) {
            $condicion_facturar = true;
            $where_in = false;
            if ($estado_facturacion == "FACTURADO") {
                $cerrado = true;
                $where_in = array("FACTURADO", "CERRADO");
            }
            $data['status'] = $this->input->post('status');
        }


        if ($detalle != "" and $local != 0) {
            $ingreso_tipo = $this->input->post('ingreso_tipo');
            if ($ingreso_tipo != "") {

                if ($ingreso_tipo == "INGRESONORMAL") {
                    $data['compras'] = $this->detalle_ingreso_model->get_by_result('detalleingreso.id_ingreso', $detalle);
                } else {
                    $data['compras'] = $this->detalle_ingreso_model->get_by_result_contable('detalleingreso_contable.id_ingreso', $detalle);
                }


                $select = array('local.*');
                $join = array('ingreso', 'local');
                $campos_join = array('ingreso.id_ingreso=detalleingreso.id_ingreso', 'local.int_local_id=ingreso.local_id');
                $where = array('detalleingreso.id_ingreso' => $detalle, 'local_id' => $local);
                $group = array('local_id');
                $data['local_detalle'] = $this->detalle_ingreso_model->get_by($select, $join, $campos_join, $where, $group, false, true);

            }


        } else {


            /*si conficionfacturar es igual a true, llamo al metodo que busca en la tabla ingreso_contable*/
            if ($condicion_facturar == true) {
                $data['ingresos'] = $this->ingreso_model->get_ingresocontable_by_estatus($condicion, $where_in);
            } else {
                $data['ingresos'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
            }
            /*si $cerrado==true quiere decir que estado_facturacion es = a completado,
            por lo tanto debo buscar en la tabla ingresos, los que esten en estatus cerrado, ya que los que estan cerrados en la tabla
            ingresos_contable ya fueron buscados arriba*/
            if ($cerrado == true) {
                $where_in = array("CERRADO");
                $data['ingresos_cerrados_normales'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
            }

        }

        $html = $this->load->view('menu/reportes/pdfReporteIngresos', $data, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("Reporte de Ingresos.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel()
    {

        $local = $this->input->post('local');
        $fecha_desde = $this->input->post('fecIni2');
        $fecha_hasta = $this->input->post('fecFin2');
        $estatus = $this->input->post('estado');
        $estado_facturacion = $this->input->post('estado_facturacion');
        $detalle = $this->input->post('id_ingreso');


        $condicion = array();
        if ($local != "" and $detalle == 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != "") {
            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha_registro >= '] = $fecha;

        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha_registro <'] = date('Y-m-d', $fechadespues);
        }


        /*este es para los estados de ingresos, el selectde estado ingresos*/
        if ($estatus != "seleccione") {
            $where_in = false;

            if ($estatus == "PENDIENTE") {
                $where_in = array("PENDIENTE");
            }

            if ($estatus == "COMPLETADO") {
                $where_in = array("COMPLETADO");
            }

        }


        /*pregunto si el select de estado facturacion es distinto se seleccione (vacio)
        y distinto de facturacion pendiente, ya que cuando este en facturacionpendiente, siempre va a estar el select de estado ingreso,
        en estatus completado, por lo tanto va a pasar arriba nada mas*/
        $cerrado = false;
        $condicion_facturar = false;
        if ($estado_facturacion != "seleccione"
            and $estado_facturacion != "FACTURACION_PENDIENTE" and $estado_facturacion != false
        ) {
            $condicion_facturar = true;
            $where_in = false;
            if ($estado_facturacion == "FACTURADO") {
                $cerrado = true;
                $where_in = array("FACTURADO", "CERRADO");
            }
            $data['status'] = $this->input->post('status');
        }


        if ($detalle != "" and $local != 0) {

            $ingreso_tipo = $this->input->post('ingreso_tipo');
            if ($ingreso_tipo != "") {

                if ($ingreso_tipo == "INGRESONORMAL") {
                    $data['compras'] = $this->detalle_ingreso_model->get_by_result('detalleingreso.id_ingreso', $detalle);
                } else {
                    $data['compras'] = $this->detalle_ingreso_model->get_by_result_contable('detalleingreso_contable.id_ingreso', $detalle);
                }


                $select = array('local.*');
                $join = array('ingreso', 'local');
                $campos_join = array('ingreso.id_ingreso=detalleingreso.id_ingreso', 'local.int_local_id=ingreso.local_id');
                $where = array('detalleingreso.id_ingreso' => $detalle, 'local_id' => $local);
                $group = array('local_id');
                $data['local_detalle'] = $this->detalle_ingreso_model->get_by($select, $join, $campos_join, $where, $group, false, true);

            }
        } else {
            /*si conficionfacturar es igual a true, llamo al metodo que busca en la tabla ingreso_contable*/
            if ($condicion_facturar == true) {
                $data['ingresos'] = $this->ingreso_model->get_ingresocontable_by_estatus($condicion, $where_in);
            } else {
                $data['ingresos'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
            }
            /*si $cerrado==true quiere decir que estado_facturacion es = a completado,
            por lo tanto debo buscar en la tabla ingresos, los que esten en estatus cerrado, ya que los que estan cerrados en la tabla
            ingresos_contable ya fueron buscados arriba*/
            if ($cerrado == true) {
                $where_in = array("CERRADO");
                $data['ingresos_cerrados_normales'] = $this->ingreso_model->get_ingresos_by_estatus($condicion, $where_in);
            }

        }


        $this->load->view('menu/reportes/excelReporteIngresos', $data);

    }


    function toExcel_cuentasPorPagar()
    {


        if ($this->input->post('proveedor1', true) != '-1') {

            $where = array(
                'int_Proveedor_id' => $this->input->post('proveedor1', true)
            );
            $data['proveedor'] = $this->input->post('proveedor1', true);

        }
        if ($this->input->post('fecIni1') != "") {

            $where['fecha_registro >='] = date('Y-m-d', strtotime($this->input->post('fecIni1')));
            $data['fecIni'] = $this->input->post('fecIni1', true);

        }

        if ($this->input->post('fecFin1') != "") {

            $where['fecha_registro <='] = date('Y-m-d', strtotime($this->input->post('fecFin1')));
            $data['fecFin'] = $this->input->post('fecFin1', true);
        }

        $select = 'sum(pagoingreso_monto)as total_pagado,pagoingreso_ingreso_id,pagoingreso_restante';
        $from = "pagos_ingreso";
        $group = 'pagoingreso_ingreso_id';
        $order = 'pagoingreso_fecha desc';
        $data['pagos_ingresos'] = $this->pagos_ingreso_model->traer_by($select, $from, false, false, false, $group, $order, "RESULT_ARRAY");

        $where['ingreso_status'] = COMPLETADO;
        $where['pago'] = "CREDITO";
        $select = 'ingreso.*, proveedor.*, sum(total_detalle) AS suma_detalle';
        $from = "ingreso";
        $join = array('proveedor', 'detalleingreso');
        $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'detalleingreso.id_ingreso=ingreso.id_ingreso');


        $group = "detalleingreso.id_ingreso";
        $data['cuentas'] = $this->pagos_ingreso_model->traer_by($select, $from, $join, $campos_join, $where, $group, false, "RESULT_ARRAY");

        $i = 0;
        if (count($data['pagos_ingresos']) > 0) {
            foreach ($data['cuentas'] as $row) {

                foreach ($data['pagos_ingresos'] as $fila) {

                    if ($row['id_ingreso'] == $fila['pagoingreso_ingreso_id']) {
                        $data['cuentas'][$i]['pagoingreso_restante'] = $row['suma_detalle'] - $fila['total_pagado'];
                        $data['cuentas'][$i]['abonado'] = $fila['total_pagado'];
                    }
                }
                $i++;
            }
        } else {
            foreach ($data['cuentas'] as $row) {


                $data['cuentas'][$i]['pagoingreso_restante'] = $row['suma_detalle'];
                $data['cuentas'][$i]['abonado'] = 0.00;

                $i++;
            }

        }
        $this->load->view('menu/reportes/reporteCuentasPorPagar', $data);
    }


    function toPdf_cuentasPorPagar()
    {

        $mpdf = new mPDF('utf-8', 'A4-L');
        $mpdf->packTableData = true;
        if ($this->input->post('proveedor2', true) != '-1') {

            $where = array(
                'int_Proveedor_id' => $this->input->post('proveedor2', true)
            );
            $data['proveedor'] = $this->input->post('proveedor2', true);

        }
        if ($this->input->post('fecIni2') != "") {

            $where['fecha_registro >='] = date('Y-m-d', strtotime($this->input->post('fecIni2')));
            $data['fecIni'] = $this->input->post('fecIni2', true);

        }

        if ($this->input->post('fecFin') != "") {

            $where['fecha_registro <='] = date('Y-m-d', strtotime($this->input->post('fecFin2')));
            $data['fecFin'] = $this->input->post('fecFin2', true);
        }

        $select = 'sum(pagoingreso_monto)as total_pagado,pagoingreso_ingreso_id,pagoingreso_restante';
        $from = "pagos_ingreso";
        $group = 'pagoingreso_ingreso_id';
        $order = 'pagoingreso_fecha desc';
        $data['pagos_ingresos'] = $this->pagos_ingreso_model->traer_by($select, $from, false, false, false, $group, $order, "RESULT_ARRAY");

        $where['ingreso_status'] = COMPLETADO;
        $where['pago'] = "CREDITO";
        $select = 'ingreso.*, proveedor.*, sum(total_detalle) AS suma_detalle';
        $from = "ingreso";
        $join = array('proveedor', 'detalleingreso');
        $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'detalleingreso.id_ingreso=ingreso.id_ingreso');


        $group = "detalleingreso.id_ingreso";
        $data['cuentas'] = $this->pagos_ingreso_model->traer_by($select, $from, $join, $campos_join, $where, $group, false, "RESULT_ARRAY");

        $i = 0;
        if (count($data['pagos_ingresos']) > 0) {
            foreach ($data['cuentas'] as $row) {

                foreach ($data['pagos_ingresos'] as $fila) {

                    if ($row['id_ingreso'] == $fila['pagoingreso_ingreso_id']) {
                        $data['cuentas'][$i]['pagoingreso_restante'] = $row['suma_detalle'] - $fila['total_pagado'];
                        $data['cuentas'][$i]['abonado'] = $fila['total_pagado'];
                    }
                }
                $i++;
            }
        } else {
            foreach ($data['cuentas'] as $row) {


                $data['cuentas'][$i]['pagoingreso_restante'] = $row['suma_detalle'];
                $data['cuentas'][$i]['abonado'] = 0.00;

                $i++;
            }

        }

        $html = $this->load->view('menu/reportes/pdfCuentasPorPagar', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    function ingreso_detallado()
    {


        $data['proveedores'] = $this->proveedor_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['todo'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/ingreso_detallado', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function get_ingresodetallado()
    {

        $data['moneda_local'] = $this->monedas_model->get_moneda_default();

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        if ($this->input->post('id_local') != "TODOS") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local'] = $this->input->post('id_local');

        }

        if ($this->input->post('desde') != "") {

            $condicion['fecha_registro >= '] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($this->input->post('hasta') != "") {

            $condicion['fecha_registro <='] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }
        if ($this->input->post('proveedor') != "TODOS") {
            $condicion['int_Proveedor_id'] = $this->input->post('proveedor');
            $data['proveedor'] = $this->input->post('proveedor');
        }

        $condicion['ingreso.id_ingreso > '] = 0;
        $order = 'detalleingreso.id_detalle_ingreso asc';

        $data['ingresos'] = $this->detalle_ingreso_model->get_detalleingresodetallado($condicion, $order);

        //var_dump($data['ingresos']);
        $this->load->view('menu/ingreso/lista_ingresodetallado', $data);

    }
}