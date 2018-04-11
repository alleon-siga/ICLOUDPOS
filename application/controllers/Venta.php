<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('venta/venta_model');
            $this->load->model('cronograma/cronograma_model');
            $this->load->model('local/local_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('producto/producto_model', 'pd');
            $this->load->model('precio/precios_model', 'precios');
            $this->load->model('proveedor/proveedor_model', 'pv');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('metodosdepago/metodos_pago_model');
            $this->load->model('historial_cronograma/historial_cronograma_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('opciones/opciones_model');
            $this->load->model('correlativos/correlativos_model');
            $this->load->model('documentos/documentos_model');
            $this->load->model('venta_devolucion/venta_devolucion_model');
            $this->load->model('credito_cuotas/credito_cuotas_model');
            $this->load->model('credito/credito_model');
            $this->load->model('credito_cuotas_abono/credito_cuotas_abono_model');
            $this->load->model('banco/banco_model');
            $this->load->helper('form');
            $this->load->library('mpdf53/mpdf');
            $this->load->library('Pdf');
            $this->load->library('session');
            $this->load->library('phpExcel/PHPExcel.php');
        } else {
            redirect(base_url(), 'refresh');
        }

    }

    function saveGuardar()
    {
        $campos = array("dni", "nombre_full", "direccion", "refe_direccion", "celular", "centro_traba", "direc_trab", "nombre_conyu", "correo");
        for ($i = 0; $i < count($campos); $i++) {
            $c = $this->input->post($campos[$i]);
            if (empty($c)) {

                die(json_encode(array('message' => 'El campo "' . str_replace("_", " ", $campos[$i]) . '" No puede ser vacio')));
            }
        }
        $dni = $this->input->post('dni');
        $gar = $this->venta_model->get_garante_by_dni($dni);
        if ($gar) {
            $message = 'Ya existe un garante con el DNI:' . $dni . ' Por favor, use otro';
            die(json_encode(array("message" => $message)));
        } else {

            $nombre_full = $this->input->post('nombre_full');
            $direccion = $this->input->post('direccion');
            $refe_direccion = $this->input->post('refe_direccion');
            $celular = $this->input->post('celular');
            $centro_traba = $this->input->post('centro_traba');
            $direc_trab = $this->input->post('direc_trab');
            $nombre_conyu = $this->input->post('nombre_conyu');
            $correo = $this->input->post('correo');
            $garante = array(
                'dni' => $dni,
                'nombre_full' => $nombre_full,
                'direccion' => $direccion,
                'refe_direccion' => $refe_direccion,
                'celular' => $celular,
                'centro_traba' => $centro_traba,
                'direc_trab' => $direc_trab,
                'nombre_conyu' => $nombre_conyu,
                'correo' => $correo,
            );

            if ($this->db->insert('garante', $garante)) {

                //$json['success'] = $resultado;
                // echo json_encode($json);
                // return;
                $data["garantes"] = $this->venta_model->get_garantes();
                $option = '';
                foreach ($data["garantes"] as $value) {
                    $option .= '<option value="' . $value['dni'] . '">' . $value['nombre_full'] . '</option>';
                }
                $message = 'El garante fue registrado con éxito';
                die(json_encode(array("message" => $message, 'html' => $option)));
            }
            $data["garantes"] = $option;
            $this->load->view('menu/ventas/select_garante', $data);
        }
    }

    function load_dialog_terminar_venta_credito()
    {

        $data["inicial_por"] = $this->opciones_model->get_opcion('INICIAL_PORCENTAJE_VTA_CRED');
        $data["tasa_interes"] = $this->opciones_model->get_opcion('TASA_INTERES');
        $data["garantes"] = $this->venta_model->get_garantes();


        $html = $this->load->view('menu/ventas/dialog_terminar_venta_credito', array(
            'inicial_por' => $data['inicial_por'],
            'tasa_interes' => $data["tasa_interes"],
            'garantes' => $data['garantes']
        ), true);
        die($html);

    }

    /*estos metodos load_dialog_ son los llamados a las vistas, que se van a hacer en su momento, para no cargarlas todas al inicio*/
    function load_dialog_terminar_venta_contado()
    {

        $tarjetas = $this->db->get('tarjeta_pago')->result();

        $html = $this->load->view('menu/ventas/dialog_terminar_venta_contado', array(
            'tarjetas' => $tarjetas
        ), true);
        die($html);

    }

    function load_dialog_venta_caja()
    {

        $html = $this->load->view('menu/ventas/dialog_venta_caja', null, true);
        die($html);


    }


    function load_dialog_nuevo_garante()
    {

        $html = $this->load->view('menu/ventas/dialog_nuevo_garante', null, true);
        die($html);


    }

    function load_dialog_existencia_producto()
    {

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();

        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $data['show_precio_new'] = $this->opciones_model->get_opcion("MODIFICADOR_PRECIO");
        $data['show_precio_new'] = isset($data['show_precio_new'][0]['config_value']) ? $data['show_precio_new'][0]['config_value'] : '';


        $html = $this->load->view('menu/ventas/dialog_existencia_producto', array(
            'locales' => $data['locales'],
            'show_precio_new' => $data['show_precio_new']
        ), true);
        die($html);
    }

    function generarventados($idventa = "")
    {


        $data["monedas"] = $this->monedas_model->get_all();
        $data["condiciones_pago"] = $this->condiciones_pago_model->get_all();
        $data['tipos_documento'] = $this->documentos_model->get_documentos();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();

        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $data["venta"] = array();
        if ($idventa != FALSE) {
            $data["venta"] = $this->venta_model->obtener_venta($idventa);
            $data['moneda'] = $this->monedas_model->get_by('id_moneda', $data['venta'][0]['id_moneda']);
            if ($this->input->post('devolver') == 1) {
                $data['devolver'] = 1;
            }
            /*echo "<pre>";
            var_dump( $data);*/
        }


        $data['show_precio_new'] = $this->opciones_model->get_opcion("MODIFICADOR_PRECIO");
        $data['show_precio_new'] = isset($data['show_precio_new'][0]['config_value']) ? $data['show_precio_new'][0]['config_value'] : '';

        $html = $this->load->view('menu/ventas/generarventados', $data, true);
        die($html);


    }

    function index($local = "")
    {
        $id_local = $local == "" ? $this->session->userdata('id_local') : $local;

        $idventa = $this->input->post('idventa');
        //$data["condiciones_pago"] = $this->condiciones_pago_model->get_all();
        $data["clientes"] = $this->cliente_model->get_all();
        $data["productos"] = $this->pd->productosporlocal_venta($id_local);
        // $data["precios"] = $this->precios->get_precios();
        // $data["monedas"] = $this->monedas_model->get_all();

        $data["codigo_barra_activo"] = $this->venta_model->codigo_barra_activo();


        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();

        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        //$data['tipos_documento'] = $this->documentos_model->get_documentos();
        $data["venta"] = array();
        if ($idventa != FALSE) {
            $data["venta"] = $this->venta_model->obtener_venta($idventa);
            $data['moneda'] = $this->monedas_model->get_by('id_moneda', $data['venta'][0]['id_moneda']);
            if ($this->input->post('devolver') == 1) {
                $data['devolver'] = 1;
            }
            /*echo "<pre>";
            var_dump( $data);*/
        }

        // $data['show_precio_new'] = $this->opciones_model->get_opcion("MODIFICADOR_PRECIO");
        //$data['show_precio_new'] = isset($data['show_precio_new'][0]['config_value']) ? $data['show_precio_new'][0]['config_value'] : '';
        $data['local_selected'] = $id_local;

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/generarVenta', $data, true);
        //$dataCuerpo['cuerpo2'] = $this->load->view('menu/ventas/generarventados', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
            echo $this->generarventados($idventa);
        } else {
            $this->load->view('menu/template', $dataCuerpo['cuerpo']);
            $this->load->view('menu/template', $this->generarventados($idventa));
        }
    }

    function update_venta_cobro()
    {
        echo cantidad_ventas_cobrar();
    }

    function cobrar()
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $tarjetas = $this->db->get('tarjeta_pago')->result();
        $data['dialog_terminar_venta_contado'] = $this->load->view('menu/ventas/dialog_terminar_venta_contado', array(
            'tarjetas' => $tarjetas,
            'cobro' => 1
        ), true);

        $data['dialog_nuevo_garante'] = $this->load->view('menu/ventas/dialog_nuevo_garante', array(
            'cobro' => 1
        ), true);

        $data['dialog_terminar_venta_credito'] = $this->load->view('menu/ventas/dialog_terminar_venta_credito', array(
            'inicial_por' => $this->opciones_model->get_opcion('INICIAL_PORCENTAJE_VTA_CRED'),
            'tasa_interes' => $this->opciones_model->get_opcion('TASA_INTERES'),
            'garantes' => $this->venta_model->get_garantes(),
            'cobro' => 1
        ), true);


        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/cobrarVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function completar_cobro()
    {
        $venta_id = $this->input->post('venta_id');
        $tipo_pago = $this->input->post('tipo_pago');
        $data['forma_pago'] = $this->input->post('forma_pago');
        $data['importe'] = $this->input->post('importe');
        $data['num_oper'] = $this->input->post('num_oper');
        $data['tipo_tarjeta'] = $this->input->post('tipo_tarjeta');
        $data['total_pagar'] = $this->input->post('total_pagar');

        $data['cuotas'] = json_decode($this->input->post('lst_cuotas', true));

        $this->venta_model->cobrar_venta($venta_id, $tipo_pago, $data);


        echo $venta_id;
    }

    function get_ventas_cobrar()
    {
        /* este metodo se usa para poder filtrar las ventas por estatus y por locales*/
        $data = '';
        if ($this->input->post('id_local') != "") {

            $estatus = array('COBRO');
            $where = array(
                'venta.local_id' => $this->input->post('id_local')
            );
            $data["ventas"] = $this->venta_model->get_venta_by_status($estatus, $where);
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/cobrarLista', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function registrar_venta()
    {

        $dataresult = array();
        if ($this->input->is_ajax_request()) {
            /*$this->form_validation->set_rules('cboCliente', 'cboCliente', 'required');
            $this->form_validation->set_rules('cboTipDoc', 'cboTipDoc', 'required');
            $this->form_validation->set_rules('cboModPag', 'cboModPag', 'required');
            $this->form_validation->set_rules('nrocuota', 'nrocuota', 'required');
            $this->form_validation->set_rules('montxcuota', 'montxcuota', 'required');
            $this->form_validation->set_rules('fec_prim_pago', 'fec_prim_pago', 'required');
            $this->form_validation->set_rules('subTotal', 'subTotal', 'required');
            $this->form_validation->set_rules('igv', 'igv', 'required');*/
            $this->form_validation->set_rules('montoigv', 'montoigv', 'required');
            $this->form_validation->set_rules('totApagar', 'totApagar', 'required');
            $this->form_validation->set_rules('importe', 'importe', 'required');


            $tasa_cambio = explode('_', $this->input->post('monedas'));


            if ($this->form_validation->run() == false) {
                echo "no guardo" . validation_errors();;
            } else {

                if ($_POST['subTotal'] != "" && $_POST['montoigv'] != "" && $_POST['totApagar'] != "") {
                    $correlativo = $this->correlativos_model->get_correlativo($this->input->post('id_local', true), $this->input->post('tipo_documento', true));
                    $moneda = $this->monedas_model->get_by("id_moneda", $this->input->post("monedas"));
                    // die(var_dump($this->input->post("monedas")));
                    $venta = array(
                        'fecha' => date("Y-m-d H:i:s"),
                        'id_cliente' => $this->input->post('id_cliente', true),
                        'id_vendedor' => $this->session->userdata('nUsuCodigo'),

                        'condicion_pago' => $this->input->post('condicion_pago', true),
                        'venta_status' => $this->input->post('venta_status', true),
                        //'local_id' => $this->session->userdata('id_local'),
                        'local_id' => $this->input->post('id_local', true),

                        'subtotal' => $this->input->post('subTotal', true),
                        'total_impuesto' => $this->input->post('montoigv', true),
                        'total' => $this->input->post('totApagar', true),

                        'vuelto' => $this->input->post('vuelto', true) ? $this->input->post('vuelto', true) : 0,
                        'importe' => $this->input->post('importe', true),
                        'pago_cuenta' => $this->input->post('pago_cuenta', true),

                        'nrocuota' => $this->input->post('nrocuota', true) ? $this->input->post('nrocuota', true) : 1,
                        'montxcuota' => $this->input->post('montxcuota', true),
                        'fec_prim_pago' => date("y-m-d", strtotime($this->input->post('fec_prim_pago', true))),

                        'diascondicionpagoinput' => $this->input->post('diascondicionpagoinput', true),

                        'tipo_documento' => $this->input->post('tipo_documento', true),


                        'id_moneda' => $this->input->post('monedas', true),
                        'tasa_cambio' => $moneda['tasa_soles'],


                        'id_documento' => $this->input->post('tipo_documento', true),
                        'correlativo' => $correlativo,

                        'forma_pago' => $this->input->post('forma_pago', true),
                        'tipo_tarjeta' => $this->input->post('tipo_tarjeta', true),
                        'num_oper' => $this->input->post('num_oper', true)
                    );


                    $detalle = json_decode($this->input->post('lst_producto', true));
                    $cuotas = json_decode($this->input->post('lst_cuotas', true));

                    $id = $this->input->post('idventa');

                    if (empty($id)) {

                        $resultado = $this->venta_model->insertar_venta($venta, $detalle, $cuotas);
                        /*pregunto si el correlativo es diferente de vacio para saber si lo voy a sumar*/
                        if ($resultado && $correlativo != 0) {
                            $this->correlativos_model->sumar_correlativo($this->input->post('id_local', true), $this->input->post('tipo_documento', true));
                        }
                    } else {
                        $venta['venta_id'] = $id;
                        $venta['devolver'] = $this->input->post('devolver');
                        $devolucion = $this->venta_devolucion_model->comparar_devolucion($venta['venta_id'], $detalle);
                        // var_dump($devolucion);

                        /*si es falso mas bien estan agregando*/
                        if ($devolucion != false) {


                            $resultado = $this->venta_model->actualizar_venta($venta, $detalle);
                            $this->venta_devolucion_model->insertar_devolucion($devolucion, $venta['venta_id'], $this->input->post('id_local', true));

                        }
                        $dataresult['devolver'] = $venta['devolver'];

                    }


                    /***GENERO UN CRONOGRAMA DE PAGO SOLO SI LA VENTA VIENE EN STATUS COMPLETADO**/
                    if ($this->input->post('venta_status') != "EN ESPERA") {

                        if ($this->input->post('diascondicionpagoinput') > 0) {

                            $fecha = $this->condiciones_pago_model->get_by('id_condiciones', $venta['condicion_pago']);

                            $fecha_hoy = date("Y-m-d H:i:s");

                            $fecha_desde_nueva = strtotime('+1 day', strtotime($fecha_hoy));
                            $fecha_desde_nueva = date('Y-m-d H:i:s', $fecha_desde_nueva);

                            $fecha_hasta_nueva = strtotime('+' . $fecha['dias'] . ' day', strtotime($fecha_desde_nueva));
                            $fecha_hasta_nueva = date('Y-m-d H:i:s', $fecha_hasta_nueva);


                            $cronogramapago = new cronograma_model();
                            $cronogramapago->setFechaInicio($fecha_desde_nueva);
                            $cronogramapago->setFechaFin($fecha_hasta_nueva);
                            $cronogramapago->setMontoCuota($this->input->post('totApagar', true));
                            $cronogramapago->setIdventa($resultado);
                            $cronogramapago->setNrocuota(1);

                            $lista[0] = $cronogramapago;
                            if (empty($id)) {

                                $rs = $this->venta_model->insertar_cronogramaPago($lista);
                            } else {
                                $detalle = $this->venta_model->get_cronograma_by_venta($id);
                                foreach ($detalle as $row) {
                                    $cronogramapago = new cronograma_model();
                                    $cronogramapago->setFechaInicio($fecha_desde_nueva);
                                    $cronogramapago->setFechaFin($fecha_hasta_nueva);
                                    $cronogramapago->setCuota($row['dec_cronpago_pagorecibido']);
                                    $cronogramapago->setMontoCuota($this->input->post('totApagar', true));
                                    $cronogramapago->setIdventa($id);
                                    $cronogramapago->setNrocuota(1);
                                    $listaupdate[] = $cronogramapago;
                                }
                                if (count($detalle) > 0) {
                                    $rs = $this->venta_model->update_cronogramaPago($listaupdate, true);
                                } else {
                                    $rs = $this->venta_model->insertar_cronogramaPago($lista);
                                }
                            }
                        }
                    }


                    if (isset($resultado) and $resultado != false) {
                        $dataresult['msj'] = "guardo";
                        $dataresult['idventa'] = $resultado;

                        $this->db->select('documento_venta.documento_serie as doc_ser, documento_venta.documento_Numero as doc_num');
                        $this->db->from('documento_venta');
                        $this->db->join('venta', 'venta.numero_documento = documento_venta.id_tipo_documento');
                        $this->db->where('venta.venta_id', $dataresult['idventa']);
                        $venta = $this->db->get()->row();
                        $dataresult['numero_doc'] = $venta != NULL ? $venta->doc_ser . ' - ' . $venta->doc_num : 'No definido';
                    } else {
                        $dataresult['msj'] = "no guardo";
                    }
                } else {
                    $dataresult['msj'] = "no guardo";
                }

                echo json_encode($dataresult);
            }
        } else {
            redirect(base_url() . 'ventas/', 'refresh');

        }

    }


    function guardarPago()
    {
        /*este metodo al parecer no se esta usando*/
        if ($this->inguardarPagoput->is_ajax_request()) {

            $detalle = json_decode($this->input->post('lst_producto', true));

            // var_dump($detalle);

            $credito = $this->venta_model->updateCredito($detalle);
            $resultado = $this->venta_model->update_cronogramaPago($detalle);
            $result['cronogramas'] = $this->venta_model->get_cronograma_by_venta($detalle[0]->id_venta);

            $detalle[0]->monto_restante = $result['cronogramas'][0]['dec_cronpago_pagocuota'] - $result['cronogramas'][0]['dec_cronpago_pagorecibido'];

            $save_historial = $this->historial_cronograma_model->guardar($detalle);

            if ($save_historial != false) {

                $json['exitohistorial'] = true;

            } else {

                $json['exitohistorial'] = false;
            }
            if ($credito != false) {

                $json['exitocredito'] = true;

            } else {

                $json['exitocredito'] = false;
            }
            if ($resultado != false) {

                $json['exitocronograma'] = true;

            } else {

                $json['exitocronograma'] = false;
            }

            echo json_encode($json);

        }


    }

    function cargar_vistapagopendiente()
    {

        if ($this->input->is_ajax_request()) {

            $detalle = json_decode($this->input->post('lst_producto', true));
            $result['cronogramas'] = $this->venta_model->get_cronograma_by_venta($detalle[0]->id_venta);
            $select = '*';
            $from = "venta";
            $join = array('cliente', 'documento_venta', 'moneda');
            $campos_join = array('cliente.id_cliente=venta.id_cliente',
                'venta.numero_documento=documento_venta.id_tipo_documento',
                'moneda.id_moneda = venta.id_moneda'
            );
            $where = array(
                'venta_id' => $detalle[0]->id_venta
            );

            $result['cliente'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, "ROW_ARRAY");
            $result['metodo_pago'] = $this->metodos_pago_model->get_by('id_metodo', $detalle[0]->metodo);

            //////////////////////////////////////////////////////////////////////////busco lo que resta de deuda
            $where = array(
                'cronogramapago_id' => $result['cronogramas'][0]['nCPagoCodigo']
            );
            $select = 'monto_restante';
            $from = "historial_cronograma";
            $order = "historialcrono_fecha desc";
            $buscar_restante = $this->venta_model->traer_by($select, $from, false, false, false, $where, false, $order, "RESULT_ARRAY");


            $result['restante'] = $buscar_restante[0]['monto_restante'];

            $result['cuota'] = $detalle[0]->cuota;
            $this->load->view('menu/ventas/visualizarCuentaPendiente', $result);
        }


    }


    function imprimir_pago_pendiente()
    {
        /*este metodo se ejecuta en el detalle de pagos pendiente, al presionar el boton imprimir sobre cada cuota*/
        if ($this->input->is_ajax_request()) {

            /*recibo estos tres parametros*/
            $id_cuota = json_decode($this->input->post('id_cuota', true));
            $id_venta = json_decode($this->input->post('id_venta', true));
            $id_credito_cuota = json_decode($this->input->post('id_credito_cuota', true));

            /*busco los datos de la cuota que seleccione*/
            $where = array(
                'abono_id' => $id_cuota
            );
            $result['cronogramas'] = $this->credito_cuotas_model->get_pagocuotas_by_venta($where);


            /*busco los restantes desde esa fecha de pago, hacia abajo*/
            $where = array(
                'id_credito_cuota' => $id_credito_cuota,
                'fecha_abono <= ' => $result['cronogramas'][0]['fecha_abono']
            );
            $result['detalles'] = $this->credito_cuotas_abono_model->get_suma_cuotas($where);

            $select = '*';
            $from = "venta";
            $join = array('cliente');
            $campos_join = array('cliente.id_cliente=venta.id_cliente');
            $where = array(
                'venta_id' => $id_venta
            );
            $result['cliente'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, "ROW_ARRAY");

            $result['id_historial'] = true;

            $this->load->view('menu/ventas/visualizarCuentaPendiente', $result);
        }


    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function ventas_by_cliente()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['ventatodos'] = "TODOS";
        $condicion = array('a.id_cliente >=' => 0);
        $data['ventas'] = $this->venta_model->get_ventas_by_cliente($condicion);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/ventas_by_cliente', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function show_venta_cliente($id = FALSE)
    {

        if ($id != FALSE) {


            $condicion = array('venta.id_cliente' => $id);
            $data['ventas'] = $this->venta_model->get_ventas_by($condicion);
            $data['ventatodos'] = "CLIENTE";
            $this->load->view('menu/ventas/show_venta_cliente', $data);

        }
    }

    function cancelar()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["locales"] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/cancelarVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_ventas_cancelar()
    {
        /* este metodo se usa para poder filtrar las ventas por estatus y por locales*/
        $data = '';
        if ($this->input->post('id_local') != "") {

            $estatus = array('COMPLETADO', 'EN ESPERA', 'COBRO');
            $where = array(
                'venta.local_id' => $this->input->post('id_local')
            );
            $data["ventas"] = $this->venta_model->get_venta_by_status($estatus, $where);
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/lista_cancelar_venta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function devolver()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $estatus = array('COMPLETADO', 'COBRO');
        // echo "<pre>";
        // var_dump($this->venta_model->get_venta_by_status($estatus));
        $data["ventas"] = $this->venta_model->get_venta_para_devoluciones();

        $data['venta_model'] = $this->venta_model;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/devolverventa', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function anular_venta()
    {

        //$id = $this->input->post('id');
        $id = $this->input->post('venta');
        $locales = $this->input->post('locales');

        $data['resultado'] = $this->venta_model->anular_venta($id, $locales);

        if ($data['resultado'] != FALSE) {
            // $this->session->set_flashdata('success', 'Se ha anulado exitosamente');
            $json['success'] = 'Se ha anulado exitosamente';
        } else {

            //$this->session->set_flashdata('error', 'Ha ocurrido un error al anular la venta');
            $json['error'] = 'Ha ocurrido un error al anular la venta';
        }

        echo json_encode($json);


    }

    function get_ventas()
    {

        if ($this->input->post('id_local') != "") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local'] = $this->input->post('id_local');
            if ($this->session->userdata('esSuper') == 1) {
                $data['locales'] = $this->local_model->get_all();
            } else {
                $usu = $this->session->userdata('nUsuCodigo');
                $data['locales'] = $this->local_model->get_all_usu($usu);
            }
        }
        if ($this->input->post('desde') != "") {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($this->input->post('hasta') != "") {

            $condicion['fecha <='] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }
        if ($this->input->post('estatus') != "") {
            $condicion['venta_status'] = $this->input->post('estatus');
            $data['estatus'] = $this->input->post('estatus');
        }

        $data['ventas'] = $this->venta_model->get_ventas_by($condicion);

        $this->load->view('menu/ventas/lista_ventas', $data);

    }

    function get_ventas_por_status()
    {

        $condicion = array('local_id' => $this->session->userdata('id_local'));
        $data['local'] = $this->session->userdata('id_local');


        $condicion['venta_status'] = $this->input->post('estatus');
        $data['estatus'] = $this->input->post('estatus');


        $data['ventas'] = $this->venta_model->get_ventas_by($condicion);

        $this->load->view('menu/ventas/lista_ventas_status', $data);

    }

    function pdfVentasporCliente($cliente)
    {

        $mpdf = new mPDF('utf-8', 'A4-L');
        /*este es el pdf por cliente*/
        $data['clientes'] = array();
        if ($cliente != 0 and $cliente != "") {
            $condicion = array('venta.id_cliente' => $cliente);
            $data['clientes'] = $this->venta_model->get_ventas_by($condicion);
        }
        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfVentasporCliente', $data, true));
        $mpdf->Output();


    }

    function pdfVentasTodosCliente($totalventas)
    {
        $data['total'] = array();
        $mpdf = new mPDF('utf-8', 'A4-L');
        if ($totalventas == "TODOS") {

            $condicion = array('a.id_cliente >=' => 0);
            $data['total'] = $this->venta_model->get_ventas_by_cliente($condicion);

        }
        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfVentasTodosCliente', $data, true));
        $mpdf->Output();


    }

    function pdfHistorialVentas($local, $fecha_desde, $fecha_hasta, $estatus, $totalventas)
    {

        $mpdf = new mPDF('utf-8', 'A4-L');
        if ($local != 0) {
            $condicion = array('local_id' => $local);
        }

        if ($fecha_desde != 0) {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($fecha_desde)) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($fecha_hasta != 0) {

            $condicion['fecha <='] = date('Y-m-d', strtotime($fecha_hasta)) . " " . date('H:i:s', strtotime('23:59:59'));
        }

        if ($estatus != 0) {
            $condicion['venta_status'] = $estatus;
        }

        if ($totalventas == "TODOS") {

            $condicion = array('a.id_cliente >=' => 0);
            $total = $this->venta_model->get_ventas_by_cliente($condicion);

        } elseif ($totalventas != 0 and $totalventas != "TODOS") {

            $condicion = array('venta.id_cliente' => $totalventas);
            $clientes = $this->venta_model->get_ventas_by($condicion);


        } else {

            $data['ventas'] = $this->venta_model->get_ventas_by($condicion);

        }

        $mpdf->WriteHTML($this->load->view('menu/reportes/pdfHistorialVentas', $data, true));
        $mpdf->Output();


    }

    function pdfReporteUtilidades($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }

        $condicion['venta_status'] = "COMPLETADO";
        $retorno = "RESULT";


        $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";

        $group = false;

        if ($utilidades == "PRODUCTO") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $group = "producto_id";

        } elseif ($utilidades == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

            $group = "cliente.id_cliente";
        } elseif ($utilidades == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";

            $group = "producto.producto_proveedor";
        }
        $result['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);
        $html = $this->load->view('menu/reportes/pdfReporteUtilidades', $result, true);
        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("ReporteUtilidades.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function pdfReporteUtilidadesProducto($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $result['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }

        $condicion['venta_status'] = "COMPLETADO";

        $select = '(SELECT SUM(detalle_utilidad)) AS suma, producto.producto_nombre,producto.producto_id as id_producto,
            producto.producto_costo_unitario';
        $from = "producto";
        $join = array('detalle_venta');
        $campos_join = array('detalle_venta.id_producto=producto.producto_id');

        $where = array('producto_estatus' => 1, 'producto_estado' => 1);
        $tipojoin = false;
        $group = "producto_id";
        $data['ventas'] = $this->pd->traer_by($select, $from, $join, $campos_join, $tipojoin, $where, $group, false, "RESULT");

        for ($i = 0; $i < count($data['ventas']); $i++) {

            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
									   FROM inventario where id_producto=' . $data['ventas'][$i]->id_producto . '
									    and id_local=' . $this->session->userdata('id_local'));
            $inventario_existente = $query->row_array();

            if (!empty($inventario_existente)) {
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='" . $data['ventas'][$i]->id_producto . "' order by orden asc");
                $unidades_producto = $query->result_array();

                $unidad_maxima = $unidades_producto[0];

                $total_unidades_minimas = ($unidad_maxima['unidades'] * $inventario_existente['cantidad']) + $inventario_existente['fraccion'];


                if ($data['ventas'][$i]->producto_costo_unitario != null) {

                    if (isset($unidades_producto[count($unidades_producto) - 1])) {
                        $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                        $data['ventas'][$i]->valorizacion = number_format((($unidad_minima['unidades'] * $data['ventas'][$i]->producto_costo_unitario) / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);

                    } else {
                        $data['ventas'][$i]->valorizacion = number_format(($data['ventas'][$i]->producto_costo_unitario / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                    }
                } else {

                    $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . ")  ");

                    $result_ingreso = $query_compra->result_array();


                    if (count($result_ingreso) > 0) {

                        $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];

                        if (isset($unidades_producto[count($unidades_producto) - 1])) {
                            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                            $data['ventas'][$i]->valorizacion = number_format((($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_minima['unidades']) * $total_unidades_minimas, 2);

                        } else {
                            $data['ventas'][$i]->valorizacion = number_format(($calcular_costo_u / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                        }

                    } else {
                        $data['ventas'][$i]->valorizacion = 0.00;
                    }
                }

            } else {

                $data['ventas'][$i]->valorizacion = 0.00;
            }


        }
        //var_dump($data['ventas']);
        $data['utilidades'] = "PRODUCTO";
        $html = $this->load->view('menu/reportes/pdfReporteUtilidadesProducto', $data, true);
        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("ReporteUtilidadesProducto.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function pdfReporteUtilidadesProveedor($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');
        $condicion = "";
        if ($local != 0) {
            $condicion .= " and local_id =" . $local;
            $data['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion .= " and fecha_registro >='" . $fecha . "' ";
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion .= " and fecha_registro < '" . date('Y-m-d', $fechadespues) . "' ";

        }

        $data['utilidades'] = "PROVEEDOR";

        $proveedor = $this->pv->get_all();

        if (count($proveedor) > 0) {

            for ($i = 0; $i < count($proveedor); $i++) {


                $prov = $proveedor[$i]['id_proveedor'];
                $sql_detalle_ingreso = "SELECT SUM(pagos_ingreso.`pagoingreso_monto`) AS suma,
                  ingreso.`int_Proveedor_id`, pagoingreso_ingreso_id
                FROM pagos_ingreso  JOIN ingreso ON ingreso.`id_ingreso`=pagos_ingreso.`pagoingreso_ingreso_id`
                where ingreso.int_Proveedor_id='" . $prov . "' ";
                $sql_detalle_ingreso .= $condicion;
                $sql_detalle_ingreso .= " GROUP BY pagos_ingreso.`pagoingreso_ingreso_id`";
                $sql = $this->db->query($sql_detalle_ingreso);

                $pagoingreso = $sql->result_array();

                $data['ventas'][$i]->id_proveedor = $proveedor[$i]['id_proveedor'];
                $data['ventas'][$i]->proveedor_nombre = $proveedor[$i]['proveedor_nombre'];

                $sumapagoingreso = 0;
                $sumadetalle = 0;
                if (count($pagoingreso) > 0) {

                    for ($j = 0; $j < count($pagoingreso); $j++) {
                        $ingreso = $pagoingreso[$j]['pagoingreso_ingreso_id'];

                        $sql = "SELECT SUM(detalleingreso.`total_detalle`) AS suma,
                  ingreso.`int_Proveedor_id`
                FROM detalleingreso  JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
                where ingreso.int_Proveedor_id='" . $proveedor[$i]['id_proveedor'] . "' and ingreso.`id_ingreso`='" . $ingreso . "' ";
                        $sql_detalle_ingreso .= $condicion;

                        $sql = $this->db->query($sql);
                        $detalle = $sql->row_array();
                        $sumapagoingreso = $sumapagoingreso + $pagoingreso[$j]['suma'];
                        $sumadetalle = $sumadetalle + $detalle['suma'];

                    }


                    if ($sumapagoingreso < $sumadetalle) {


                        $data['ventas'][$i]->suma = $sumadetalle - $sumapagoingreso;
                    } else {
                        $data['ventas'][$i]->suma = 0;
                    }
                } else {
                    $data['ventas'][$i]->suma = 0;
                }
            }
        }


        $html = $this->load->view('menu/reportes/pdfReporteUtilidadesProveedor', $data, true);
        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("ReporteUtilidadesProveedor.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function pdfReporteUtilidadesCliente($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $result['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }

        $condicion['venta_status'] = "COMPLETADO";
        $retorno = "RESULT";

        $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

        $group = "cliente.id_cliente";

        $result['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);


        $html = $this->load->view('menu/reportes/pdfReporteUtilidadesCliente', $result, true);
        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("ReporteUtilidadesCliente.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function excelVentasporCliente($cliente)
    {
        /*este es el excel por cliente*/
        $data['clientes'] = array();
        if ($cliente != 0 and $cliente != "") {
            $condicion = array('venta.id_cliente' => $cliente);
            $data['clientes'] = $this->venta_model->get_ventas_by($condicion);
        }
        $this->load->view('menu/reportes/excelVentasporCliente', $data);

    }

    function excelVentasTodosCliente($totalventas)
    {
        /*este es el excel para todos los clientes*/
        $data['total'] = array();
        if ($totalventas == "TODOS") {

            $condicion = array('a.id_cliente >=' => 0);
            $data['total'] = $this->venta_model->get_ventas_by_cliente($condicion);
        }
        $this->load->view('menu/reportes/excelVentasTodosCliente', $data);

    }

    function excelHistorialVentas($local, $fecha_desde, $fecha_hasta, $estatus)
    {
        /*este es el excel para el historial de ventas*/
        if ($local != 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != 0) {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($fecha_desde)) . " " . date('H:i:s', strtotime('0:0:0'));
        }
        if ($fecha_hasta != 0) {

            $condicion['fecha <='] = date('Y-m-d', strtotime($fecha_hasta)) . " " . date('H:i:s', strtotime('23:59:59'));
        }
        if ($estatus != 0) {
            $condicion['venta_status'] = $estatus;
        }

        $data['ventas'] = $this->venta_model->get_ventas_by($condicion);
        $this->load->view('menu/reportes/excelHistorialVentas', $data);

    }

    function excelReporteUtilidades($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }


        $condicion['venta_status'] = "COMPLETADO";
        $retorno = "RESULT";


        $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";

        $group = false;

        if ($utilidades == "PRODUCTO") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $group = "producto_id";

        } elseif ($utilidades == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

            $group = "cliente.id_cliente";
        } elseif ($utilidades == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";

            $group = "producto.producto_proveedor";
        }
        $result['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);

        $this->load->view('menu/reportes/excelReporteUtilidades', $result);

    }

    function excelReporteUtilidadesCliente($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $result['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }


        $condicion['venta_status'] = "COMPLETADO";
        $retorno = "RESULT";

        $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

        $group = "cliente.id_cliente";

        $result['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);

        $this->load->view('menu/reportes/excelReporteUtilidadesCliente', $result);

    }

    function excelReporteUtilidadesProveedor($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        $condicion = "";
        if ($local != 0) {
            $condicion .= " and local_id =" . $local;
            $data['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion .= " and fecha_registro >='" . $fecha . "' ";
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion .= " and fecha_registro < '" . date('Y-m-d', $fechadespues) . "' ";

        }

        $data['utilidades'] = "PROVEEDOR";

        $proveedor = $this->pv->get_all();

        if (count($proveedor) > 0) {

            for ($i = 0; $i < count($proveedor); $i++) {


                $prov = $proveedor[$i]['id_proveedor'];
                $sql_detalle_ingreso = "SELECT SUM(pagos_ingreso.`pagoingreso_monto`) AS suma,
                  ingreso.`int_Proveedor_id`, pagoingreso_ingreso_id
                FROM pagos_ingreso  JOIN ingreso ON ingreso.`id_ingreso`=pagos_ingreso.`pagoingreso_ingreso_id`
                where ingreso.int_Proveedor_id='" . $prov . "' ";
                $sql_detalle_ingreso .= $condicion;
                $sql_detalle_ingreso .= " GROUP BY pagos_ingreso.`pagoingreso_ingreso_id`";
                $sql = $this->db->query($sql_detalle_ingreso);

                $pagoingreso = $sql->result_array();

                $data['ventas'][$i]->id_proveedor = $proveedor[$i]['id_proveedor'];
                $data['ventas'][$i]->proveedor_nombre = $proveedor[$i]['proveedor_nombre'];

                $sumapagoingreso = 0;
                $sumadetalle = 0;
                if (count($pagoingreso) > 0) {

                    for ($j = 0; $j < count($pagoingreso); $j++) {
                        $ingreso = $pagoingreso[$j]['pagoingreso_ingreso_id'];

                        $sql = "SELECT SUM(detalleingreso.`total_detalle`) AS suma,
                  ingreso.`int_Proveedor_id`
                FROM detalleingreso  JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
                where ingreso.int_Proveedor_id='" . $proveedor[$i]['id_proveedor'] . "' and ingreso.`id_ingreso`='" . $ingreso . "' ";
                        $sql_detalle_ingreso .= $condicion;

                        $sql = $this->db->query($sql);
                        $detalle = $sql->row_array();
                        $sumapagoingreso = $sumapagoingreso + $pagoingreso[$j]['suma'];
                        $sumadetalle = $sumadetalle + $detalle['suma'];

                    }


                    if ($sumapagoingreso < $sumadetalle) {


                        $data['ventas'][$i]->suma = $sumadetalle - $sumapagoingreso;
                    } else {
                        $data['ventas'][$i]->suma = 0;
                    }
                } else {
                    $data['ventas'][$i]->suma = 0;
                }
            }
        }

        $this->load->view('menu/reportes/excelReporteUtilidadesProveedor', $data);

    }

    function excelReporteUtilidadesProducto($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $result['local'] = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != "") {

            $fecha = date('Y-m-d', strtotime($fecha_desde));
            $condicion['fecha >= '] = $fecha;
        }
        if ($fecha_hasta != "") {

            $fecha = date('Y-m-d', strtotime($fecha_hasta));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);

        }

        $condicion['venta_status'] = "COMPLETADO";

        $select = '(SELECT SUM(detalle_utilidad)) AS suma, producto.producto_nombre,producto.producto_id as id_producto,
            producto.producto_costo_unitario';
        $from = "producto";
        $join = array('detalle_venta');
        $campos_join = array('detalle_venta.id_producto=producto.producto_id');

        $where = array('producto_estatus' => 1, 'producto_estado' => 1);
        $tipojoin = false;
        $group = "producto_id";
        $data['ventas'] = $this->pd->traer_by($select, $from, $join, $campos_join, $tipojoin, $where, $group, false, "RESULT");

        for ($i = 0; $i < count($data['ventas']); $i++) {

            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
									   FROM inventario where id_producto=' . $data['ventas'][$i]->id_producto . '
									    and id_local=' . $this->session->userdata('id_local'));
            $inventario_existente = $query->row_array();

            if (!empty($inventario_existente)) {
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='" . $data['ventas'][$i]->id_producto . "' order by orden asc");
                $unidades_producto = $query->result_array();

                $unidad_maxima = $unidades_producto[0];

                $total_unidades_minimas = ($unidad_maxima['unidades'] * $inventario_existente['cantidad']) + $inventario_existente['fraccion'];


                if ($data['ventas'][$i]->producto_costo_unitario != null) {

                    if (isset($unidades_producto[count($unidades_producto) - 1])) {
                        $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                        $data['ventas'][$i]->valorizacion = number_format((($unidad_minima['unidades'] * $data['ventas'][$i]->producto_costo_unitario) / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);

                    } else {
                        $data['ventas'][$i]->valorizacion = number_format(($data['ventas'][$i]->producto_costo_unitario / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                    }
                } else {

                    $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . ")  ");

                    $result_ingreso = $query_compra->result_array();


                    if (count($result_ingreso) > 0) {

                        $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];

                        if (isset($unidades_producto[count($unidades_producto) - 1])) {
                            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                            $data['ventas'][$i]->valorizacion = number_format((($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_minima['unidades']) * $total_unidades_minimas, 2);

                        } else {
                            $data['ventas'][$i]->valorizacion = number_format(($calcular_costo_u / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                        }

                    } else {
                        $data['ventas'][$i]->valorizacion = 0.00;
                    }
                }

            } else {

                $data['ventas'][$i]->valorizacion = 0.00;
            }


        }
        //var_dump($data['ventas']);
        $data['utilidades'] = "PRODUCTO";

        $this->load->view('menu/reportes/excelReporteUtilidadesProducto', $data);

    }

    function buscar_NroVenta_credito()
    {
        $validar_cronograma = $this->input->post('validar_cronograma');

        if ($this->input->is_ajax_request()) {
            $venta = $this->venta_model->buscar_NroVenta_credito($this->input->post('nro_venta', true));

            if (count($venta) > 0) {
                if (!empty($validar_cronograma)) {
                    $cronogrma = $this->venta_model->get_cronograma_by_venta($venta[0]->venta_id);
                    if (count($cronogrma) > 0) {
                        echo json_encode(array('error' => 'Ya existe un crongrama para la venta seleccionada'));
                    } else {
                        echo json_encode($venta);
                    }
                } else {

                    echo json_encode($venta);
                }
            } else {
                echo json_encode(array('error' => 'El número de venta ingresado no existe o no es una venta a credito'));
            }
        } else {
            redirect(base_url() . 'ventas/', 'refresh');
        }


    }


    function consultar()
    {

        //$data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidades()
    {


        //$data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['todo'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesProductos()
    {


        //$data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['productos'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesCliente()
    {


        // $data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['cliente'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesProveedor()
    {


        //$data['locales'] = $this->local_model->get_all();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }
        $data['proveedor'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function getUtiidadesVentas()
    {
        $this->load->helper('url');
        $this->load->library('Form_validation');
        $condiciondeproveedor = "";

        if ($this->input->post('id_local') != "") {
            $condiciondeproveedor .= " and local_id =" . $this->input->post('id_local');
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local'] = $this->input->post('id_local');
        }
        if ($this->input->post('desde') != "") {

            $fecha = date('Y-m-d', strtotime($this->input->post('desde', true)));
            $condicion['fecha >= '] = $fecha;
            $condiciondeproveedor .= " and fecha_registro >='" . $fecha . "' ";
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s');
        }
        if ($this->input->post('hasta') != "") {

            $fecha = date('Y-m-d', strtotime($this->input->post('hasta', true)));
            $fechadespues = strtotime('+1 day', strtotime($fecha));

            $condicion['fecha < '] = date('Y-m-d', $fechadespues);
            $condiciondeproveedor .= " and fecha_registro < '" . date('Y-m-d', $fechadespues) . "' ";
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s');
        }

        $condicion['venta_status'] = "COMPLETADO";

        $retorno = "RESULT";
        if ($this->input->post('utilidades') == "TODOS") {
            $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $data['utilidades'] = "TODO";
            $group = false;

            $data['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);
        } elseif ($this->input->post('utilidades') == "PRODUCTOS") {

            $select = '(SELECT SUM(detalle_utilidad)) AS suma, producto.producto_nombre,producto.producto_id as id_producto,
            producto.producto_costo_unitario';
            $from = "producto";
            $join = array('detalle_venta');
            $campos_join = array('detalle_venta.id_producto=producto.producto_id');

            $where = array('producto_estatus' => 1, 'producto_estado' => 1);
            $tipojoin = false;
            $group = "producto_id";
            $data['ventas'] = $this->pd->traer_by($select, $from, $join, $campos_join, $tipojoin, $where, $group, false, "RESULT");

            for ($i = 0; $i < count($data['ventas']); $i++) {

                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
									   FROM inventario where id_producto=' . $data['ventas'][$i]->id_producto . '
									    and id_local=' . $this->session->userdata('id_local'));
                $inventario_existente = $query->row_array();

                if (!empty($inventario_existente)) {
                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='" . $data['ventas'][$i]->id_producto . "' order by orden asc");
                    $unidades_producto = $query->result_array();

                    $unidad_maxima = $unidades_producto[0];

                    $total_unidades_minimas = ($unidad_maxima['unidades'] * $inventario_existente['cantidad']) + $inventario_existente['fraccion'];


                    if ($data['ventas'][$i]->producto_costo_unitario != null) {

                        if (isset($unidades_producto[count($unidades_producto) - 1])) {
                            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                            $data['ventas'][$i]->valorizacion = number_format((($unidad_minima['unidades'] * $data['ventas'][$i]->producto_costo_unitario) / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);

                        } else {
                            $data['ventas'][$i]->valorizacion = number_format(($data['ventas'][$i]->producto_costo_unitario / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                        }
                    } else {

                        $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $data['ventas'][$i]->id_producto . ")  ");

                        $result_ingreso = $query_compra->result_array();


                        if (count($result_ingreso) > 0) {

                            $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];

                            if (isset($unidades_producto[count($unidades_producto) - 1])) {
                                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                                $data['ventas'][$i]->valorizacion = number_format((($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_minima['unidades']) * $total_unidades_minimas, 2);

                            } else {
                                $data['ventas'][$i]->valorizacion = number_format(($calcular_costo_u / $unidad_maxima['unidades']) * $total_unidades_minimas, 2);
                            }

                        } else {
                            $data['ventas'][$i]->valorizacion = 0.00;
                        }
                    }

                } else {

                    $data['ventas'][$i]->valorizacion = 0.00;
                }


            }
            //var_dump($data['ventas']);
            $data['utilidades'] = "PRODUCTO";

        } elseif ($this->input->post('utilidades') == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";
            $data['utilidades'] = "CLIENTE";
            $group = "cliente.id_cliente";
            $data['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);
        } elseif ($this->input->post('utilidades') == "PROVEEDOR") {
            $data['utilidades'] = "PROVEEDOR";


            $proveedor = $this->pv->get_all();
            $data['ventas'] = array();
            if (count($proveedor) > 0) {


                for ($i = 0; $i < count($proveedor); $i++) {

                    $data['ventas'][$i] = new stdClass();
                    $prov = $proveedor[$i]['id_proveedor'];
                    $sql_detalle_ingreso = "SELECT SUM(pagos_ingreso.`pagoingreso_monto`) AS suma,
                  ingreso.`int_Proveedor_id`, pagoingreso_ingreso_id
                FROM pagos_ingreso  JOIN ingreso ON ingreso.`id_ingreso`=pagos_ingreso.`pagoingreso_ingreso_id`
                where ingreso.int_Proveedor_id='" . $prov . "' ";
                    $sql_detalle_ingreso .= $condiciondeproveedor;
                    $sql_detalle_ingreso .= " GROUP BY pagos_ingreso.`pagoingreso_ingreso_id`";
                    $sql = $this->db->query($sql_detalle_ingreso);

                    $pagoingreso = $sql->result_array();
                    $data['ventas'][$i]->id_proveedor = $proveedor[$i]['id_proveedor'];
                    $data['ventas'][$i]->proveedor_nombre = $proveedor[$i]['proveedor_nombre'];

                    $sumapagoingreso = 0;
                    $sumadetalle = 0;
                    if (count($pagoingreso) > 0) {

                        for ($j = 0; $j < count($pagoingreso); $j++) {
                            $ingreso = $pagoingreso[$j]['pagoingreso_ingreso_id'];

                            $sql = "SELECT SUM(detalleingreso.`total_detalle`) AS suma,
                  ingreso.`int_Proveedor_id`
                FROM detalleingreso  JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
                where ingreso.int_Proveedor_id='" . $proveedor[$i]['id_proveedor'] . "' and ingreso.`id_ingreso`='" . $ingreso . "' ";
                            $sql_detalle_ingreso .= $condiciondeproveedor;

                            $sql = $this->db->query($sql);
                            $detalle = $sql->row_array();
                            $sumapagoingreso = $sumapagoingreso + $pagoingreso[$j]['suma'];
                            $sumadetalle = $sumadetalle + $detalle['suma'];

                        }


                        if ($sumapagoingreso < $sumadetalle) {


                            $data['ventas'][$i]->suma = $sumadetalle - $sumapagoingreso;
                        } else {
                            $data['ventas'][$i]->suma = 0;
                        }
                    } else {
                        $data['ventas'][$i]->suma = 0;
                    }
                }
            }

        }

        $this->load->view('menu/ventas/listaReporteUtilidades', $data);
    }

    function estadocuenta()
    {
        //$data["lstVenta"] =$this->v->select_venta_estadocuenta();
        $data = "";
        $data["lstCliente"] = $this->cliente_model->get_all();
        $data['locales'] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/estadocuentaVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function lst_reg_estadocuenta()
    {
        if ($this->input->is_ajax_request()) {

            $condicion = 'v.venta_status="COMPLETADO" ';

            if ($this->input->post('cboCliente', true) != -1) {
                $condicion .= ' and v.id_cliente =' . $this->input->post("cboCliente", true) . " ";
            }
            if ($_POST['fecIni'] != "") {
                $condicion .= 'and v.FechaReg >="' . date('Y-m-d', strtotime($_POST['fecIni'])) . '" ';
            }

            if ($_POST['fecFin'] != "") {
                $condicion .= ' and v.FechaReg <="' . date('Y-m-d', strtotime($_POST['fecFin'])) . '" ';
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
            $data['lstVenta'] = $this->venta_model->select_venta_estadocuenta($condicion, $order);

            $this->load->view('menu/ventas/tbl_listareg_estaodcuenta', $data);
            //echo json_encode($this->v->select_venta_estadocuenta(date("y-m-d", strtotime($this->input->post('fecIni',true))),date("y-m-d", strtotime($this->input->post('fecFin',true)))));
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }

    function pagospendientes()
    {
        $data = "";

        //$data['locales'] = $this->local_model->get_all();
        $data = _prepareFlashData();
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $data['metodos'] = $this->metodos_pago_model->get_all();
        $data["lstCliente"] = $this->cliente_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/pagospendientesVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lst_reg_pagospendientes()
    {
        if ($this->input->is_ajax_request()) {

            $data['local'] = $this->input->post("local", true);
            $data['cliente_id'] = $this->input->post("cliente_id", true);
            $data['moneda'] = $this->input->post("moneda", true);

            $params = array();
            if ($data['local'] != 'TODOS')
                $params['local_id'] = $data['local'];

            if ($data['cliente_id'] != '-1')
                $params['cliente_id'] = $data['cliente_id'];

            $params['moneda_id'] = $data['moneda'];

            //var_dump($this->input->post("vence_deuda", 2));
            $params['vence_deuda'] = $this->input->post("vence_deuda", 2);

            $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();

            $data['lstVenta'] = $this->venta_model->get_pagos_pendientes($params);
            $data['credito_totales'] = $this->venta_model->get_totales_pagos_pendientes($params);

            $this->load->view('menu/ventas/tbl_listareg_pagospendiente', $data);
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }


    function cronograma_pago()
    {
        $data = "";
        $data['nro_venta'] = "";
        $data['ventas'] = $this->venta_model->select_ventas_credito();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/cronogramaPago', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function registrar_cronogramapago()
    {

        //$cronogramaexiste = venta_model->insertar_cronogramaPago(json_decode($this->input->post('lst_cronograma',true)));

        $rs = $this->venta_model->insertar_cronogramaPago(json_decode($this->input->post('lst_cronograma', true)));

        if ($rs) {
            echo "guardo";
        } else {
            echo "no guardo";
        }

    }

    public function verVentaCredito()
    {
        /*este metodo es para ver el detalle de la venta y pagos en pagos pendiente*/
        $idventa = $this->input->post('idventa');

        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);

            $result['cronogramas'] = $this->credito_cuotas_model->get_cronograma_by_cuotas($idventa);
            $where = array(
                'id_venta' => $idventa
            );
            $result['historial'] = $this->credito_cuotas_model->get_pagocuotas_by_venta($where);
            $this->load->view('menu/ventas/visualizar_venta_credito', $result);
        }
    }

    public function verVentaVentana()
    {
        $idventa = $this->input->post('idventa');


        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);
            $result['hola'] = $this->venta_model->get_ventas_by(array('venta_id' => $idventa));
            $result['credito_cuotas'] = $this->credito_cuotas_model->get_cuotas_by_venta_id($idventa);
            $result['numero_creditos'] = $this->credito_cuotas_model->get_numero_de_creditos_by_local_actual();
            $result['id_ventas'] = $idventa;


            $this->load->view('menu/ventas/ventana_imp_credito', $result);


        }
    }

    public function cargarDatosFactura()
    {
        $idventa = $this->input->post('idventa');


        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);
            $result['gotoxy'] = $this->venta_model->obtener_gotoxy('FACTURA');
            $this->load->view('menu/ventas/impresion_factura', $result);

        }
    }


    public function verVenta()
    {

        $idventa = $this->input->post('idventa');


        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);
            $result['hola'] = $this->venta_model->get_ventas_by(array('venta_id' => $idventa));
            $result['id_venta'] = $idventa;
            $this->load->view('menu/ventas/visualizarVenta', $result);


        }
    }


    public function verVentaJson()
    {
        $idventa = $this->input->post('idventa');


        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);

            //var_dump($result);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));


        }
    }

    function truncNumber($number, $prec = 2)
    {
        return bccomp($number, 0, 10) == 0 ? $number : round($number - pow(0.1, bcadd($prec, 1)) * 5, $prec);
    }


    function generarcuota()
    {
        $maximo_cuotas_credito = $this->opciones_model->get_opcion('MAXIMO_CUOTAS_CREDITO');
        $maximo_cuotas_credito = isset($maximo_cuotas_credito[0]['config_value']) ? $maximo_cuotas_credito[0]['config_value'] : 0;
        $precio_credito = $this->input->post('precio_credito');
        $precio_contado = $this->input->post('precio_contado');
        $dias_de_pago = $this->input->post('dias_pago');

        if ($dias_de_pago < 31 and $dias_de_pago > 1) {
            if (strlen($dias_de_pago) == 1) {
                $dias_de_pago = "0" . $dias_de_pago;
            }
        } else {
            $dias_de_pago = '01';
        }
        $pago_inicial = $this->input->post('inicial');
        $tasa_interes = $this->input->post('tasa_interes');
        $numero_cuotas = $this->input->post('numero_cuotas') ? $this->input->post('numero_cuotas') : $maximo_cuotas_credito;
        $cliente_nuevo = $this->input->post('cliente_nuevo');
        $res_pago_inicial = $this->input->post('res_pago_inicial');
        //$res_pago_inicial = 180;


        $saldo = $precio_contado - $pago_inicial;
        $valor = 1.00;
        for ($i = 0; $i < $numero_cuotas; $i++) {
            $valor += $tasa_interes / 100;
        }
        // $monto_interes =  ($saldo * ($tasa_interes/100));

        $monto_por_cuota = $saldo * $valor / $numero_cuotas;

        // $mto_mes = 0;
        // $mto_mes_acu = 0;
        $numero_cuotas_proyeccion = $maximo_cuotas_credito;
        $data['proyeccion'] = array();
        $valor = 1.00;
        $tasa = $tasa_interes / 100;
        //echo "Tasa de interes:".$tasa."<br>";
        for ($i = 0; $i < $numero_cuotas_proyeccion + 1; $i++) {
            if ($i != 0) {

                $valor += $tasa;

                //echo $saldo."*".$valor."/".($i)."<br>";
                $data['proyeccion'][$i] = "S/. " . number_format($saldo * $valor / $i, 2);
            }
        }
        // echo "<pre>";
        // print_r($data['proyeccion']);
        // echo "</pre>";
        $data["cuotas"] = array();
        $data["cronogramas"] = array();
        $fecha_acum = date('Y-m-d');
        $hoy = date('d/m/Y');
        $nro_cuota = 0;

        for ($i = 0; $i < $numero_cuotas; $i++) {

            // $mto_mes =  $saldo + $monto_interes;
            // $mto_mes_acu = $mto_mes / ($i + 1);
            $nro_cuota = $i + 1;

            $fecha_ven = strtotime('+' . $nro_cuota . ' month', strtotime($fecha_acum));
            $fecha_ven = date('d/m/Y', $fecha_ven);
            $fecha_vencimiento = explode("/", $fecha_ven);
            $fecha_ven = $dias_de_pago . "/" . $fecha_vencimiento[1] . "/" . $fecha_vencimiento[2];
            $data["cuotas"][$i] = array(
                "nro_letra" => $nro_cuota,
                "nro_cuota" => $nro_cuota,
                "fecha_giro" => $hoy,
                "fecha_vencimiento" => $fecha_ven,
                // "monto"              => number_format($this->truncNumber($mto_mes_acu,2),2)
                "monto" => number_format($monto_por_cuota, 2)
            );
            //$saldo = $saldo + $monto_interes;
        }


        if ($cliente_nuevo == "SI") {
            // echo $pago_inicial;
            // echo "<br>".$res_pago_inicial;
            $saldo = $pago_inicial - $res_pago_inicial;

            $data["cuotas"][$numero_cuotas - 1]["monto"] = (float)str_replace(",", '', $data["cuotas"][$numero_cuotas - 1]["monto"]);

            $interes_acum = 1 + (($tasa_interes / 100) * 1);
            $mto_mes_acu = ($saldo * $interes_acum) + $data["cuotas"][$numero_cuotas - 1]["monto"];

            $fecha_acum = date('Y-m-d');

            for ($j = 0; $j < $numero_cuotas; $j++) {
                if ($j != 0) {

                    $fecha_acum = date('Y-m-d');

                    $fecha_ven = strtotime('+2 month', strtotime($fecha_acum));
                    $fecha_ven = date('d/m/Y', $fecha_ven);
                    $fecha_vencimiento = explode("/", $fecha_ven);
                    $fecha_ven = $dias_de_pago . "/" . $fecha_vencimiento[1] . "/" . $fecha_vencimiento[2];
                    $mto_mes_acu = $data["cuotas"][$numero_cuotas - 1]["monto"];
                }

                $fecha_ven = strtotime('+' . ($j + 1) . ' month', strtotime($fecha_acum));
                $fecha_ven = date('d/m/Y', $fecha_ven);
                $fecha_vencimiento = explode("/", $fecha_ven);
                $fecha_ven = $dias_de_pago . "/" . $fecha_vencimiento[1] . "/" . $fecha_vencimiento[2];
                $data["cronogramas"][$j] = array(
                    "nro_letra" => ($j + 1) . "/$numero_cuotas" . " 1",
                    "fecha_giro" => $hoy,
                    "fecha_vencimiento" => $fecha_ven,
                    "monto" => 'S/. ' . number_format($mto_mes_acu, 2)
                );


                // $data["cronogramas"][$j] = array(
                //     "nro_letra" => "2/$numero_cuotas" . " 1",
                //     "fecha_giro" => $hoy,
                //     "fecha_vencimiento" => $fecha_ven,
                //     "monto" => 'S/. ' . number_format($mto_mes_acu, 2)
                // );

                // $fecha_acum = date('Y-m-d');

                // $fecha_ven = strtotime('+3 month', strtotime($fecha_acum));
                // $fecha_ven = date('d/m/Y', $fecha_ven);
                // $mto_mes_acu = $data["cuotas"][2]["monto"];

                // $data["cronogramas"][$j] = array(
                //     "nro_letra" => "3/$numero_cuotas" . " 1",
                //     "fecha_giro" => $hoy,
                //     "fecha_vencimiento" => $fecha_ven,
                //     "monto" => 'S/. ' . number_format($mto_mes_acu, 2)
                // );
            }

        }
        $data['inicial'] = $pago_inicial;
        // echo "<pre>";
        // print_r($data["cuotas"]);
        // echo "</pre>";
        $this->load->view('menu/ventas/table_cuotas', $data);

    }

    public function imprimir_documento($id_venta, $tipo_documento, $cuota = NULL, $nota_credito_comprobante = NULL)
    {
        if ($id_venta) {
            switch ($tipo_documento) {
                case 'pedido_compra':
                    $productos_de_venta = $this->venta_model->obtener_venta($id_venta);
                    $cantidad_de_documentos = ceil(count($productos_de_venta) / 21);
                    $valor_de_producto = 0;
                    ob_clean();
                    for ($j = 0; $j < $cantidad_de_documentos; $j++) {

                        $str = file_get_contents(base_url() . "recursos/plantillas/compraventa.rtf");
                        $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                        $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                        $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                        $numero_creditos = $this->credito_cuotas_model->get_numero_de_creditos_by_local_actual();
                        // fecha
                        $fecha = explode(' ', $venta[0]->fecha);
                        $fecha = explode('-', $fecha[0]);
                        $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
                        $str = str_replace('$fecha', $fecha, $str);
                        $texto = $this->numero_letras->numtoletras($venta[0]->total, strtoupper($moneda['nombre']));
                        $str = str_replace('$numeroletra', $texto, $str);
//                         $text = "{\sp{\sn fHidden}{\sv 0}}{\sp{\sn pctHoriz}{\sv 400}}{\sp{\sn pctVert}{\sv 200}}{\sp{\sn sizerelh}{\sv 0}}{\sp{\sn sizerelv}{\sv 0}}{\sp{\sn fLayoutInCell}{\sv 1}}{\shptxt \ltrpar \pard\plain \ltrpar
// \ql \li0\ri0\sa200\sl276\slmult1\widctlpar\wrapdefault\aspalpha\aspnum\faauto\adjustright\rin0\lin0\itap0 \rtlch\fcs1 \af31507\afs22\alang1025 \ltrch\fcs0 \f31506\fs22\lang3082\langfe1033\cgrid\langnp3082\langfenp1033 {\rtlch\fcs1 \af31507 \ltrch\fcs0
// \insrsid3897860 " .  . "
// \par }}}";
                        $text = $this->credito_cuotas_model->get_resumen_cuotas_by_venta_id($id_venta, true);
                        $str = str_replace('$resumencuotas', $text, $str);
                        //comprador
                        $str = str_replace('$comprador', $venta[0]->razon_social, $str);
                        //dni comprador
                        $str = str_replace('$dnicomprador', $venta[0]->cliente_identificacion, $str);
                        //conyugue
                        $str = str_replace('$conyugue', '', $str);
                        $str = str_replace('$dniconyugue', '', $str);
                        //domicilio
                        $str = str_replace('$domicilio', $venta[0]->cliente_direccion, $str);
                        //modalidad pago
                        $str = str_replace('$modalidad', utf8_decode($venta[0]->nombre_condiciones), $str);
                        //telefono
                        $str = str_replace('$tlf', $venta[0]->telefono1, $str);
                        /*son 20 filas de productos por compraventa*/

                        if (count($productos_de_venta) < 22) {
                            for ($i = 0; $i < 22; $i++) {
                                if (!isset($productos_de_venta[$i])) {
                                    $productos_de_venta[$i]['producto_id'] = '';
                                    $productos_de_venta[$i]['cantidad'] = '';
                                    $productos_de_venta[$i]['abreviatura'] = '';
                                    $productos_de_venta[$i]['nombre'] = '';
                                    $productos_de_venta[$i]['preciounitario'] = '';
                                    $productos_de_venta[$i]['importe'] = '';
                                }
                            }
                        }

                        $valorsito = 0;
                        for ($i = 0; $i < 22; $i++) {
                            if ($valor_de_producto != 0) {
                                $i = $valor_de_producto;
                            }
                            $valorsito = $i;
                            /*codigo de producto*/
                            if ($i >= 10) {
                                $str = str_replace('$co' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                            } else {
                                $str = str_replace('$cod' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);
                            }
                            if ($i >= 10) {

                                /*cantidad de producto*/
                                $str = str_replace('$ca' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                            } else {
                                $str = str_replace('$can' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                            }
                            if ($i >= 10) {
                                /*unidad de medida de producto*/

                                $str = str_replace('$un' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                            } else {
                                $str = str_replace('$uni' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                            }
                            if ($i >= 10) {

                                /*descripcion de producto*/
                                $str = str_replace('$descripcio' . ($i), $productos_de_venta[$i]['nombre'], $str);
                            } else {
                                $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['nombre'], $str);
                            }
                            if ($i >= 10) {

                                /*precio unitario de producto*/
                                $str = str_replace('$p-' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                            } else {
                                $str = str_replace('$p' . ($i + 1), $productos_de_venta[$i]['preciounitario'], $str);
                            }
                            if ($i >= 10) {
                                /*total de producto*/

                                $str = str_replace('$total-' . ($i), $productos_de_venta[$i]['importe'], $str);
                            } else {
                                $str = str_replace('$total' . ($i + 1), $productos_de_venta[$i]['importe'], $str);
                            }
                        }
                        $valor_de_producto = $valorsito;
                        $valorsito = 0;
                        //inicial
                        $str = str_replace('$inicial', $venta[0]->inicial . " " . $moneda['simbolo'], $str);
                        //numero de cuotas del credito
                        $str = str_replace('$ncuotas', count($letra), $str);
                        //numero de cuotas del credito
                        $str = str_replace('$monto', $venta[0]->total . " " . $moneda['simbolo'], $str);
                        //vencimiento del credito
                        $vencimiento = explode(' ', $letra[count($letra) - 1]['fecha_vencimiento']);
                        $fecha = explode("-", $vencimiento[0]);
                        $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
                        $str = str_replace('$vencimiento', $fecha, $str);
                        //garante
                        $str = str_replace('$garante', $venta[0]->nombre_full, $str);
                        //dnigarante
                        $str = str_replace('$dnigarante', $venta[0]->dni_garante, $str);
                        //domicilio garante
                        $str = str_replace('$dirgarante', $venta[0]->refe_direccion, $str);
                        // echo "<pre>";
                        // // print_r($productos_de_venta);
                        // print_r($letra);
                        // die;

                        header("Content-type: application/msword");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header('Content-Description: File Transfer');
                        header("Content-Disposition: inline; filename=compra_venta_parte_" . ($j + 1) . "_" . date('y_m_d_h:i:s') . ".rtf");
                        header("Expires: 0");
                        header("Pragma: public");
                        header("Content-length: " . strlen($str));
                        echo $str;
                        ob_clean();

                    }
                    die;
                    // die(var_dump($valor_de_producto));
                    //die(json_encode("imprimiendo pedido de compra"));
                    break;
                case 'guia_remision':
                    ob_clean();
                    $valor_de_producto = 0;
                    $productos_de_venta = $this->venta_model->obtener_venta($id_venta);
                    $str = file_get_contents(base_url() . "recursos/plantillas/GuiaRemision.rtf");
                    $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                    $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                    $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                    $opciones = $this->opciones_model->get_opciones();

                    $str = str_replace('$puntopartida', utf8_decode($opciones[1]['config_value']), $str);
                    $str = str_replace('$puntollegada', $venta[0]->cliente_direccion, $str);
                    $fecha = explode(' ', $venta[0]->fecha);
                    $fecha = explode('-', $fecha[0]);
                    $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
                    $str = str_replace('$fechaemision', $fecha, $str);
                    $str = str_replace('$razonsocial', $venta[0]->razon_social, $str);
                    $str = str_replace('$fechatraslado', $fecha, $str);
                    $str = str_replace('$ruc', $venta[0]->cliente_identificacion, $str);
                    /*son 30 filas de productos por compraventa*/

                    if (count($productos_de_venta) < 31) {
                        for ($i = 0; $i < 31; $i++) {
                            if (!isset($productos_de_venta[$i])) {
                                $productos_de_venta[$i]['producto_id'] = '';
                                $productos_de_venta[$i]['cantidad'] = '';
                                $productos_de_venta[$i]['abreviatura'] = '';
                                $productos_de_venta[$i]['nombre'] = '';
                                $productos_de_venta[$i]['preciounitario'] = '';
                                $productos_de_venta[$i]['importe'] = '';
                            }
                        }
                    }

                    $valorsito = 0;
                    for ($i = 0; $i < 31; $i++) {

                        if ($valor_de_producto != 0) {
                            $i = $valor_de_producto;
                        }
                        $valorsito = $i;
                        /*codigo de producto*/
                        if ($i >= 10) {
                            $str = str_replace('$co' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                        } else {
                            $str = str_replace('$cod' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);
                        }
                        if ($i > 20) {

                            $str = str_replace('$c' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                        }
                        if ($i >= 10) {

                            /*cantidad de producto*/
                            $str = str_replace('$ca' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                        } else {
                            $str = str_replace('$cant' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$cat' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                        }
                        if ($i >= 10) {
                            /*unidad de medida de producto*/

                            $str = str_replace('$uni' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                        } else {
                            $str = str_replace('$unid' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$un' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                        }
                        if ($i >= 10) {

                            /*descripcion de producto*/
                            $str = str_replace('$descripcio' . ($i), $productos_de_venta[$i]['nombre'], $str);
                        } else {
                            $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['nombre'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$descripci' . ($i), $productos_de_venta[$i]['nombre'], $str);
                        }
                        if ($i >= 10) {

                            /*precio unitario de producto*/
                            $str = str_replace('$uni' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                        } else {
                            $str = str_replace('$unid' . ($i + 1), $productos_de_venta[$i]['preciounitario'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$un' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                        }
                        if ($i >= 10) {
                            /*total de producto*/

                            $str = str_replace('$pes' . ($i), $productos_de_venta[$i]['importe'], $str);
                        } else {
                            $str = str_replace('$peso' . ($i + 1), $productos_de_venta[$i]['importe'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$pe' . ($i), $productos_de_venta[$i]['importe'], $str);
                        }
                    }

                    header("Content-type: application/msword");
                    header("Content-Disposition: inline; filename=guia_remision_" . date("Y_m_d_H_i_s") . ".rtf");
                    header("Content-length: " . strlen($str));
                    echo $str;
                    die;
                    break;
                case 'factura':
                    ob_clean();
                    $valor_de_producto = 0;
                    $productos_de_venta = $this->venta_model->obtener_venta($id_venta);
                    $str = file_get_contents(base_url() . "recursos/plantillas/Factura.rtf");

                    $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                    $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                    $cuenta_guia_remision = $this->venta_model->get_numero_guia_remision_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $numero_factura = $this->venta_model->get_numero_factura_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                    $opciones = $this->opciones_model->get_opciones();
                    //$str = str_replace('$puntopartida', utf8_decode($opciones[1]['config_value']), $str);
                    $str = str_replace('$direccion', $venta[0]->cliente_direccion, $str);
                    $str = str_replace('$fecha', $venta[0]->fecha, $str);
                    $str = str_replace('$cliente', $venta[0]->razon_social, $str);
                    $str = str_replace('$guiaremision', $cuenta_guia_remision, $str);
                    $str = str_replace('$ruc', $venta[0]->cliente_identificacion, $str);
                    $str = str_replace('$subtotal', $venta[0]->subtotal, $str);
                    $str = str_replace('$total', $venta[0]->total, $str);
                    $str = str_replace('$igv', $venta[0]->total_impuesto, $str);
                    $texto = $this->numero_letras->numtoletras($venta[0]->total, strtoupper($moneda['nombre']));
                    $str = str_replace('$letrastotal', $texto, $str);
                    if (count($productos_de_venta) < 31) {
                        for ($i = 0; $i < 31; $i++) {
                            if (!isset($productos_de_venta[$i])) {
                                $productos_de_venta[$i]['producto_id'] = '';
                                $productos_de_venta[$i]['cantidad'] = '';
                                $productos_de_venta[$i]['abreviatura'] = '';
                                $productos_de_venta[$i]['nombre'] = '';
                                $productos_de_venta[$i]['preciounitario'] = '';
                                $productos_de_venta[$i]['importe'] = '';
                            }
                        }
                    }

                    $valorsito = 0;
                    for ($i = 0; $i < 31; $i++) {

                        if ($valor_de_producto != 0) {
                            $i = $valor_de_producto;
                        }
                        $valorsito = $i;
                        /*codigo de producto*/
                        if ($i >= 10) {
                            $str = str_replace('$co' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                        } else {
                            $str = str_replace('$cod' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);
                        }
                        if ($i > 20) {

                            $str = str_replace('$c' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                        }
                        if ($i >= 10) {

                            /*cantidad de producto*/
                            $str = str_replace('$can' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                        } else {
                            $str = str_replace('$cant' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$ca' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                        }
                        if ($i >= 10) {
                            /*unidad de medida de producto*/

                            $str = str_replace('$un' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                        } else {
                            $str = str_replace('$uni' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$u' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                        }
                        if ($i >= 10) {

                            /*descripcion de producto*/
                            $str = str_replace('$descripcio' . ($i), $productos_de_venta[$i]['nombre'], $str);
                        } else {
                            $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['nombre'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$descripci' . ($i), $productos_de_venta[$i]['nombre'], $str);
                        }
                        if ($i >= 10) {

                            /*precio unitario de producto*/
                            $str = str_replace('$pun' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                        } else {
                            $str = str_replace('$puni' . ($i + 1), $productos_de_venta[$i]['preciounitario'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$pu' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                        }
                        if ($i >= 10) {
                            /*total de producto*/

                            $str = str_replace('$import' . ($i), $productos_de_venta[$i]['importe'], $str);
                        } else {
                            $str = str_replace('$importe' . ($i + 1), $productos_de_venta[$i]['importe'], $str);
                        }
                        if ($i > 20) {
                            $str = str_replace('$impor' . ($i), $productos_de_venta[$i]['importe'], $str);
                        }
                    }

                    header("Content-type: application/msword");
                    header("Content-Disposition: inline; filename=factura_" . $numero_factura . "_" . date("Y_m_d_H_i_s") . ".rtf");
                    header("Content-length: " . strlen($str));
                    echo $str;
                    die;

                    break;
                case 'nota_credito':
                    ob_clean();
                    $valor_de_producto = 0;
                    $productos_de_venta = $this->venta_devolucion_model->get_devolucion_by_venta_id($id_venta);
                    /*si no es devolucion*/
                    if (!$productos_de_venta) {

                        $productos_de_venta = $this->venta_model->obtener_venta($id_venta);
                        $anulacion = 1;
                    }

                    $str = file_get_contents(base_url() . "recursos/plantillas/NotadeCredito.rtf");

                    $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                    $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                    $cuenta_guia_remision = $this->venta_model->get_numero_guia_remision_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $numero_factura = $this->venta_model->get_numero_factura_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $numero = $this->venta_model->get_cantidad_de_documento_by_local_id($this->session->userdata('id_local'), $venta[0]->id_documento);

                    $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                    $opciones = $this->opciones_model->get_opciones();
                    //$str = str_replace('$puntopartida', utf8_decode($opciones[1]['config_value']), $str);
                    $str = str_replace('$direccion', $venta[0]->cliente_direccion, $str);
                    $fecha = explode(' ', $venta[0]->fecha);
                    $fecha = explode('-', $fecha[0]);
                    $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
                    $str = str_replace('$fecha', $fecha, $str);
                    $str = str_replace('$razonsocial', $venta[0]->razon_social, $str);
                    //$str = str_replace('$guiaremision', $cuenta_guia_remision , $str);
                    $str = str_replace('$ruc', $venta[0]->cliente_identificacion, $str);
                    //$str = str_replace('$subtotal', $venta[0]->subtotal, $str);


                    if ($venta[0]->id_documento == 1) {
                        $str = str_replace('$documentomodi', 'Factura', $str);

                    } elseif ($venta[0]->id_documento == 3) {

                        $str = str_replace('$documentomodi', 'Boleta de Venta', $str);
                    }

                    $str = str_replace('$numero', $nota_credito_comprobante, $str);
                    $str = str_replace('$igv', $venta[0]->total_impuesto, $str);
                    $precio = 0;
                    $precio_sin_impuesto = 0;
                    if (count($productos_de_venta) < 7) {
                        for ($i = 0; $i < 7; $i++) {
                            if (!isset($productos_de_venta[$i])) {
                                if (isset($anulacion)) {
                                    $productos_de_venta[$i]['producto_id'] = '';
                                    $productos_de_venta[$i]['cantidad'] = '';
                                    $productos_de_venta[$i]['abreviatura'] = '';
                                    $productos_de_venta[$i]['nombre'] = '';
                                    $productos_de_venta[$i]['preciounitario'] = '';
                                    $productos_de_venta[$i]['importe'] = '';
                                } else {

                                    $productos_de_venta[$i]['producto_id'] = '';
                                    $productos_de_venta[$i]['cantidad'] = '';
                                    $productos_de_venta[$i]['abreviatura'] = '';
                                    $productos_de_venta[$i]['producto_nombre'] = '';
                                    $productos_de_venta[$i]['precio'] = '';
                                    $productos_de_venta[$i]['precio'] = '';
                                }
                            }
                        }
                    }
                    $valorsito = 0;
                    for ($i = 0; $i < 7; $i++) {

                        if ($valor_de_producto != 0) {
                            $i = $valor_de_producto;
                        }
                        $valorsito = $i;

                        /*codigo de producto*/
                        if ($i >= 10) {
                            $str = str_replace('$co' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);
                        } else {


                            $str = str_replace('$cod' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);

                        }

                        if ($i >= 10) {

                            /*cantidad de producto*/
                            $str = str_replace('$can' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                        } else {
                            $str = str_replace('$cant' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                        }

                        if ($i >= 10) {
                            /*unidad de medida de producto*/

                            $str = str_replace('$uni' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                        } else {
                            $str = str_replace('$unid' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                        }
                        if (isset($anulacion)) {

                            if ($i >= 10) {

                                /*descripcion de producto*/
                                $str = str_replace('$descripcio' . ($i), $productos_de_venta[$i]['nombre'], $str);
                            } else {
                                $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['nombre'], $str);
                            }

                            if ($i >= 10) {

                                /*precio unitario de producto*/
                                $str = str_replace('$pun' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                            } else {
                                $str = str_replace('$puni' . ($i + 1), $productos_de_venta[$i]['preciounitario'], $str);
                            }

                            if ($i >= 10) {
                                /*total de producto*/

                                $str = str_replace('$import' . ($i), $productos_de_venta[$i]['importe'], $str);
                            } else {
                                $str = str_replace('$valor' . ($i + 1), $productos_de_venta[$i]['importe'], $str);
                            }


                        } else {

                            if ($i >= 10) {

                                /*descripcion de producto*/
                                $str = str_replace('$descripcio' . ($i + 1), $productos_de_venta[$i]['producto_nombre'], $str);
                            } else {
                                $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['producto_nombre'], $str);
                            }

                            if ($i >= 10) {

                                /*precio unitario de producto*/
                                $str = str_replace('$pun' . ($i + 1), $productos_de_venta[$i]['precio'], $str);
                            } else {
                                $str = str_replace('$puni' . ($i + 1), $productos_de_venta[$i]['precio'], $str);
                            }

                            if ($i >= 10) {
                                /*total de producto*/

                                $str = str_replace('$import' . ($i + 1), $productos_de_venta[$i]['precio'], $str);
                            } else {
                                $str = str_replace('$valor' . ($i + 1), $productos_de_venta[$i]['precio'], $str);
                            }
                            $precio += $productos_de_venta[$i]['precio'];
                            $precio_sin_impuesto += $productos_de_venta[$i]['precio'];
                        }

                    }
                    if (isset($anulacion)) {
                        $str = str_replace('$subtotal', $venta[0]->subtotal, $str);
                        $str = str_replace('$total', $venta[0]->total, $str);
                        $str = str_replace('$igv', $venta[0]->total_impuesto, $str);
                    } else {

                        $str = str_replace('$subtotal', (float)$precio_sin_impuesto, $str);
                        $str = str_replace('$total', $precio, $str);
                    }

                    header("Content-type: application/msword");
                    header("Content-Disposition: inline; filename=nota_de_credito_" . $numero . "_" . date("Y_m_d_H_i_s") . ".rtf");
                    header("Content-length: " . strlen($str));
                    echo $str;
                    die;
                    break;
                case 'boleta':

                    ob_clean();
                    $valor_de_producto = 0;
                    $productos_de_venta = $this->venta_model->obtener_venta($id_venta);
                    $str = file_get_contents(base_url() . "recursos/plantillas/Boleta.rtf");

                    $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                    $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                    $cuenta_guia_remision = $this->venta_model->get_numero_guia_remision_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $numero_factura = $this->venta_model->get_numero_factura_by_venta_id($id_venta, $this->session->userdata('id_local'));
                    $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                    $opciones = $this->opciones_model->get_opciones();
                    //$str = str_replace('$puntopartida', utf8_decode($opciones[1]['config_value']), $str);
                    $str = str_replace('$direccion', $venta[0]->cliente_direccion, $str);
                    $fecha = explode(' ', $venta[0]->fecha);
                    $fecha = explode('-', $fecha[0]);
                    $fecha = $fecha[2] . '/' . $fecha[1] . '/' . $fecha[0];
                    $str = str_replace('$fecha', $fecha, $str);
                    $str = str_replace('$razonsocial', $venta[0]->razon_social, $str);
                    //$str = str_replace('$guiaremision', $cuenta_guia_remision , $str);
                    $str = str_replace('$ruc', $venta[0]->cliente_identificacion, $str);
                    //$str = str_replace('$subtotal', $venta[0]->subtotal, $str);
                    $str = str_replace('$total', $venta[0]->total, $str);
                    $texto = $this->numero_letras->numtoletras($venta[0]->total, strtoupper($moneda['nombre']));
                    $str = str_replace('$precioletra', $texto, $str);
                    //$str = str_replace('$igv', $venta[0]->total_impuesto, $str);
                    $texto = $this->numero_letras->numtoletras($venta[0]->total, strtoupper($moneda['nombre']));
                    if (count($productos_de_venta) < 16) {
                        for ($i = 0; $i < 16; $i++) {
                            if (!isset($productos_de_venta[$i])) {
                                $productos_de_venta[$i]['producto_id'] = '';
                                $productos_de_venta[$i]['cantidad'] = '';
                                $productos_de_venta[$i]['abreviatura'] = '';
                                $productos_de_venta[$i]['nombre'] = '';
                                $productos_de_venta[$i]['preciounitario'] = '';
                                $productos_de_venta[$i]['importe'] = '';
                            }
                        }
                    }

                    $valorsito = 0;
                    for ($i = 0; $i < 16; $i++) {

                        if ($valor_de_producto != 0) {
                            $i = $valor_de_producto;
                        }
                        $valorsito = $i;
                        /*codigo de producto*/
                        if ($i >= 10) {
                            $str = str_replace('$co' . ($i), $productos_de_venta[$i]['producto_id'], $str);
                        } else {
                            $str = str_replace('$cod' . ($i + 1), $productos_de_venta[$i]['producto_id'], $str);
                        }

                        if ($i >= 10) {

                            /*cantidad de producto*/
                            $str = str_replace('$can' . ($i), $productos_de_venta[$i]['cantidad'], $str);
                        } else {
                            $str = str_replace('$cant' . ($i + 1), $productos_de_venta[$i]['cantidad'], $str);
                        }

                        if ($i >= 10) {
                            /*unidad de medida de producto*/

                            $str = str_replace('$uni' . ($i), $productos_de_venta[$i]['abreviatura'], $str);
                        } else {
                            $str = str_replace('$unid' . ($i + 1), $productos_de_venta[$i]['abreviatura'], $str);
                        }

                        if ($i >= 10) {

                            /*descripcion de producto*/
                            $str = str_replace('$descripcio' . ($i), $productos_de_venta[$i]['nombre'], $str);
                        } else {
                            $str = str_replace('$descripcion' . ($i + 1), $productos_de_venta[$i]['nombre'], $str);
                        }

                        if ($i >= 10) {

                            /*precio unitario de producto*/
                            $str = str_replace('$pun' . ($i), $productos_de_venta[$i]['preciounitario'], $str);
                        } else {
                            $str = str_replace('$puni' . ($i + 1), $productos_de_venta[$i]['preciounitario'], $str);
                        }

                        if ($i >= 10) {
                            /*total de producto*/

                            $str = str_replace('$import' . ($i), $productos_de_venta[$i]['importe'], $str);
                        } else {
                            $str = str_replace('$importe' . ($i + 1), $productos_de_venta[$i]['importe'], $str);
                        }

                    }

                    header("Content-type: application/msword");
                    header("Content-Disposition: inline; filename=boleta_" . $numero_factura . "_" . date("Y_m_d_H_i_s") . ".rtf");
                    header("Content-length: " . strlen($str));
                    echo $str;
                    die;

                    break;
                case 'letra_cambio':
                    ob_clean();
                    if ($cuota != 0) {
                        # code...
                        $cuota_original = $cuota;
                        $cuota2 = explode("-", $cuota);
                        $cuota = $cuota2[1];
                    }
                    $str = file_get_contents(base_url() . "recursos/plantillas/LetradeCambio.rtf");
                    $letra = $this->credito_cuotas_model->get_cuotas_by_venta_id($id_venta);
                    $cuenta_letras = $this->credito_cuotas_model->get_numero_letras_by_venta_id($id_venta);

                    $venta = $this->venta_model->get_ventas_by(array('venta_id' => $id_venta));
                    $moneda = $this->monedas_model->get_by("id_moneda", $venta[0]->id_moneda);
                    $numero_creditos = $this->credito_cuotas_model->get_numero_de_creditos_by_local_actual();
                    $letra_actual = $cuota . "/" . $cuenta_letras;
                    // var_dump($cuota_original);
                    // die($letra_actual);
                    /*refencia de girador*/
                    $str = str_replace('$ref_girador1', '', $str);
                    $str = str_replace('$ref_girador2', $letra_actual, $str);
                    /*lugar de giro*/
                    $str = str_replace('$lugar_giro1', '', $str);
                    $str = str_replace('$lugar_giro2', $numero_creditos[0]['cuenta'], $str);
                    /*fecha de giro*/
                    $cuota_actual = $this->credito_cuotas_model->buscar_cuota_by_nro_letra($cuota_original);
                    $fecha_giro = $cuota_actual[0]['fecha_giro'];
                    $fecha_giro = explode(' ', $fecha_giro);
                    $fecha_giro = explode('-', $fecha_giro[0]);
                    $fecha_giro = $fecha_giro[2] . "/" . $fecha_giro[1] . "/" . $fecha_giro[0];
                    $str = str_replace('$fecha_giro', $fecha_giro, $str);
                    /*fecha de vencimiento*/
                    //$cuota_actual = $this->credito_cuotas_model->buscar_cuota_by_nro_letra($cuota_original);
                    $fecha_vencimiento = $cuota_actual[0]['fecha_vencimiento'];
                    $fecha_vencimiento = explode(' ', $fecha_vencimiento);
                    $fecha_vencimiento = explode("-", $fecha_vencimiento[0]);
                    $fecha_vencimiento = $fecha_vencimiento[2] . "/" . $fecha_vencimiento[1] . "/" . $fecha_vencimiento[0];;

                    $str = str_replace('$vencimiento', $fecha_vencimiento, $str);
                    /*monto*/
                    $str = str_replace('$moneda_import', $moneda['simbolo'] . ' ' . $cuota_actual[0]['monto'], $str);
                    //*numero a letras*//
                    $texto = $this->numero_letras->numtoletras($cuota_actual[0]['monto'], strtoupper($moneda['nombre']));
                    $str = str_replace('$mletras', $texto, $str);
                    /*$girado*/
                    $str = str_replace('$girado1', $venta[0]->razon_social, $str);
                    $str = str_replace('$girado0', '', $str);
                    $str = str_replace('$girado2', '', $str);
                    $str = str_replace('$girado3', '', $str);
                    /*domicilio*/
                    $str = str_replace('$domicilio', $venta[0]->direccion, $str);
                    /*doi*/
                    $str = str_replace('$doi', $venta[0]->identificacion, $str);
                    /*telefono*/
                    $str = str_replace('$tlf1', $venta[0]->telefono1, $str);
                    /*fiador*/
                    $str = str_replace('$fiador1', $venta[0]->nombre_full, $str);
                    $str = str_replace('$fiador2', '', $str);
                    /*este campo lo dejo en blanco porque no me quedo clar*/
                    $str = str_replace('$aval1', '', $str);
                    $str = str_replace('$aval2', '', $str);
                    /*domicilio del fiador*/
                    $str = str_replace('$domiaval1', $venta[0]->refe_direccion, $str);
                    $str = str_replace('$domiaval2', $venta[0]->direc_trab, $str);
                    /*doi de garante o fiador*/
                    $str = str_replace('$doaval1', $venta[0]->dni_garante, $str);
                    /*tlf fiador o garante*/
                    $str = str_replace('$tlaval', $venta[0]->celular, $str);
                    // echo "<pre>";
                    // print_r($venta);
                    // die;
                    header("Content-type: application/msword");
                    header("Content-Disposition: inline; filename=letra_cambio_" . $letra_actual . "_" . date("Y_m_d_H_i_s") . ".rtf");
                    header("Content-length: " . strlen($str));
                    echo $str;
                    die;
                    break;
                default:
                    die(json_encode("Se encontro un error!"));
                    break;
            }
        }
    }

    public function vercronograma()
    {
        /*este metodo es cuando se presiona el boton pagar en pagos pendiente*/
        $idventa = $this->input->post('idventa');
        if ($idventa != FALSE) {
            $result['metodos'] = $this->metodos_pago_model->get_all();

            /*uso este metodo para obtener  la moneda*/
            $where = array(
                'venta.venta_id' => $idventa
            );

            $venta = $this->db->get_where('venta', array('venta_id' => $idventa))->row();
            $result['cliente'] = $this->db->get_where('cliente', array('id_cliente' => $venta->id_cliente))->row();
            $result['moneda'] = $this->credito_cuotas_abono_model->get_suma_cuotas($where);

            $result['bancos'] = $this->db->join('caja_desglose', 'caja_desglose.id = banco.cuenta_id')
                ->join('caja', 'caja.id = caja_desglose.caja_id')
                ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                ->get_where('banco', array('banco_status' => 1))->result();
            $result['tarjetas'] = $this->db->get('tarjeta_pago')->result();
            $cajas = $this->db->select('caja_desglose.*, moneda.*, banco.banco_id')
                ->from('caja')
                ->join('caja_desglose', 'caja_desglose.caja_id=caja.id')
                ->join('moneda', 'moneda.id_moneda = caja.moneda_id')
                ->join('banco', 'banco.cuenta_id = caja_desglose.id', 'left')
                ->where(array(
                    'local_id' => $venta->local_id,
                    'retencion' => 0,
                    'caja_desglose.estado' => 1
                ))->get()->result();
            $result['cajas'] = array();
            foreach ($cajas as $caja) {
                if ($caja->banco_id == null)
                    $result['cajas'][] = $caja;
            }


            $result['cronogramas'] = $this->credito_cuotas_model->get_cronograma_by_cuotas($idventa);
            $result['venta'] = $venta;
            $result['id_venta'] = $idventa;
            $this->load->view('menu/ventas/tbl_venta_cronograma_pago', $result);
        }
    }

    public function pagoCuotaCredito()
    {
        /*este metodo hace su funcion cuando se paga una cuota, bien sea pago anticipado o no*/

        /*estos dos, siempre los recibo*/
        $idVenta = $this->input->post('idVenta');

        $metodo_pago = $this->input->post('metodo_pago');
        $tipo_metodo = $this->input->post('tipo_metodo');
        $numero_ope = $this->input->post('nro_operacion');
        $banco = $this->input->post('banco');
        $cuenta_id = $this->input->post('cuenta_id');

        if ($this->input->post('idCuota')) {
            $idCuota = $this->input->post('idCuota');
            $montodescontar = $this->input->post('montodescontar');
            $moneda_saldo = $this->input->post('moneda_saldo');
            $correlativo_cuota = $this->input->post('correlativo_cuota');
            $anticipado = false;
        } else {
            $anticipado = true;
            $idCuota = false;
            $montodescontar = false;
        }

        $return = $this->credito_cuotas_abono_model->registrar($idCuota, $montodescontar, $moneda_saldo, $metodo_pago, $idVenta, $anticipado, $numero_ope, $banco, $tipo_metodo, $cuenta_id);

        if ($return == true) {
            $dataresul['success'] = "El pago se ha realizado satisfactoriamente";
        } else {
            $dataresul['error'] = "Ha ocurrido un error al guardar el pago";
        }
        echo json_encode($dataresul);

    }


    function ver_productos($idventa, $flag = 0)
    {
        $data = '';
        if (isset($idventa) and !empty($idventa)) {
            /*se busca el detalle de la venta*/
            $data['ventas'] = $this->venta_model->getDetalleVenta("ARRAY", "detalle_venta.id_producto", array('detalle_venta.id_venta' => $idventa));
        }
        $data['flag'] = $flag;
        $this->load->view('menu/ventas/ver_productos_por_venta', $data);

    }

    function vista_impresion_formatos()
    {
        /*este metodo se usa para, dependiendo de si esta marcada la opcion de generar facturacion, mostrar
        la vista del ticket o mostrar la impresion de los botones que imprimen en rtf*/

        $data['estatus'] = $this->input->post('estatus');
        $data['condicion_pago'] = $this->input->post('condicion_pago');
        $data['id_documento'] = $this->input->post('id_documento');
        $data['id_venta'] = $this->input->post('idventa');
        $data['credito_cuotas'] = $this->credito_cuotas_model->get_cuotas_by_venta_id($this->input->post('idventa'));
        $data['numero_creditos'] = $this->credito_cuotas_model->get_numero_de_creditos_by_local_actual();
        $this->load->view('menu/ventas/vista_impresion_formatos', $data);
    }

    function update_numero_unico($id)
    {
        $numero = $this->input->post('numero');
        $this->db->where('id_credito_cuota', $id);
        $this->db->update('credito_cuotas', array('numero_unico' => $numero));
        $data['success'] = 1;

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function cambiar_fecha()
    {
        $data['id'] = $this->input->post('id');
        $data['fecha'] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha') . " " . date('H:i:s')));

        $this->db->where('id_credito_cuota', $data['id']);
        $this->db->update('credito_cuotas', array('fecha_vencimiento' => $data['fecha']));
    }

    public function get_nota_credito()
    {
        $data['data'] = $this->venta_model->get_nota_credito();
        $this->load->view('menu/ventas/vista_nota_credito', $data);
    }
}