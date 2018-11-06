<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class venta_new extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('venta_new/venta_new_model', 'venta');
            $this->load->model('local/local_model');
            $this->load->model('producto/producto_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('monedas/monedas_model');
            $this->load->model('condicionespago/condiciones_pago_model');
            $this->load->model('documentos/documentos_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('precio/precios_model');
            $this->load->model('correlativos/correlativos_model');
            $this->load->model('cotizar/cotizar_model');
            $this->load->model('metodosdepago/metodos_pago_model');
            $this->load->model('diccionario_termino/diccionario_termino_model');
            $this->load->model('clientesgrupos/clientes_grupos_model');
            $this->load->model('usuario/usuario_model');
            $this->load->model('banco/banco_model');
            $this->load->model('producto_costo_unitario/producto_costo_unitario_model');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    function historial($action = "")
    {

        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $data['venta_action'] = $action;
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $data['condiciones_pagos'] = $this->db->get_where('condiciones_pago', array('status_condiciones' => 1))->result();
        $data['documentos']=$this->documentos_model->get_documentos();
        $data['dialog_venta_contado'] = $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->banco_model->get_all_in_object()
        ), true);


        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/historial', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_ventas($action = "")
    {
        $local_id = $this->input->post('local_id');
        $docuemnto_id = $this->input->post('documento_id');
        $estado = $this->input->post('estado');
        $condicion_pago_id = $this->input->post('condicion_pago_id');

        $date_range = explode(" - ", $this->input->post('fecha'));
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);

        if ($action != 'caja') {
            $params = array(
                'id_documento'=>$docuemnto_id,
                'local_id' => $local_id,
                'estado' => $estado,
                'condicion_id' => $condicion_pago_id,
                'fecha_ini' => $fecha_ini,
                'fecha_fin' => $fecha_fin
            );
        } else {
            $params = array(
                'id_documento'=>$docuemnto_id,
                'local_id' => $local_id,
                'estado' => $estado
            );
        }

        $params['moneda_id'] = $this->input->post('moneda_id');
        $params['usuarios_id'] = $this->input->post('usuarios_id');
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $params['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($params, $action);
        

        $data['venta_totales'] = $this->venta->get_ventas_totales($params, $action);

        $data['venta_action'] = $action;
        if ($action != 'caja')
            $this->load->view('menu/venta/historial_list', $data);
        else
            $this->load->view('menu/venta/caja_list', $data);
    }

    function get_pendientes()
    {
        $local_id = $this->input->post('local_id');
        $estado = $this->input->post('estado');

        $params = array(
            'local_id' => $local_id,
            'estado' => $estado
        );

        $data['ventas'] = $this->venta->get_ventas($params, 'caja');

        echo count($data['ventas']);
    }

    function get_venta_detalle($action = "")
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['venta_action'] = $action;
        $data['detalle'] = 'venta';

        $data['notas_credito'] = $this->db
            ->join('usuario', 'usuario.nUsuCodigo = notas_credito.usuario_id')
            ->get_where('notas_credito', array('venta_id' => $venta_id))->result();

        $this->load->view('menu/venta/historial_list_detalle', $data);
    }

    function get_venta_facturar($action = "")
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_facturar($venta_id);
        $data['comprobante'] = $this->documentos_model->get_documentosBy('id_doc IN(1,3,6)');
        $data['venta_action'] = $action;
        $data['detalle'] = 'venta';
        $this->load->view('menu/venta/historial_list_facturar', $data);
    }

    function getDocumentoNumero()
    {
        $num = $this->venta->getDocumentoNumero();
        echo $num;
    }

    function get_venta_previa()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['facturacion_venta'] = null;
        $data['facturacion_notas'] = null;

        if (valueOption('FACTURACION', '0') == 1) {
            $tipo_doc = '';
            if ($data['venta']->documento_id == 1)
                $tipo_doc = $tipo_doc = '01';
            if ($data['venta']->documento_id == 3)
                $tipo_doc = $tipo_doc = '03';

            if ($tipo_doc != '') {
                $data['facturacion_venta'] = $this->db->get_where('facturacion', array(
                    'documento_tipo' => $tipo_doc,
                    'ref_id' => $data['venta']->venta_id,
                    'estado' => 1
                ))->row();

                $data['facturacion_notas'] = $this->db->get_where('facturacion', array(
                    'documento_tipo' => '07',
                    'ref_id' => $data['venta']->venta_id,
                    'estado' => 1
                ))->result();
            }
        }

        $data['venta_action'] = 'imprimir';
        $data['detalle'] = 'venta';
        $data['traspaso'] = $this->db->get_where('traspaso', array('ref_id' => $venta_id))->result();
        $data['dialog_detalle'] = $this->load->view('menu/venta/historial_list_detalle', $data, true);

        $this->load->view('menu/venta/dialog_venta_previa', $data);
    }

    public function get_nota_credito()
    {
        $nc_id = $this->input->post('nc_id');

        $nc = $this->db
            ->join('usuario', 'usuario.nUsuCodigo = notas_credito.usuario_id')
            ->get_where('notas_credito', array('id' => $nc_id))->row();
        $nc->detalles = $this->db->select("
            nc_d.cantidad, nc_d.precio, p.producto_nombre, p.producto_id, p.producto_codigo_interno, u.nombre_unidad as um
            ")
            ->from('notas_credito_detalle as nc_d')
            ->join('detalle_venta as dv', 'dv.id_detalle = nc_d.detalle_id')
            ->join('producto as p', 'p.producto_id = dv.id_producto')
            ->join('unidades as u', 'u.id_unidad = dv.unidad_medida')
            ->where('notas_credito_id', $nc->id)
            ->get()->result();

        $data['venta'] = $this->venta->get_ventas(array('venta_id' => $nc->venta_id));
        $data['notas_credito'] = $nc;
        $this->load->view('menu/venta/vista_nota_credito', $data);
    }

    function refresh_productos()
    {
        $data['productos'] = $this->producto_model->get_productos_list();
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_json()
    {

        header('Content-Type: application/json');
        echo $this->producto_model->get_productos_auto($this->input->get('term'));
    }

    function index($local = "", $cot_id = FALSE)
    {

        $data['facturacion'] = 'INACTIVA';
        if (valueOptionDB('FACTURACION', 0) == 1) {
            $emisor = $this->db->get_where('facturacion_emisor')->row();
            if ($emisor == NULL) {
                $data['facturacion'] = 'NO_EMISOR';
            } elseif ($emisor->env != 'PROD') {
                $data['facturacion'] = 'BETA';
            } else {
                $data['facturacion'] = 'ACTIVA';
            }
        }

        $local_id = $local == "" || $local == '-' ? $this->session->userdata('id_local') : $local;

        $data['cotizacion'] = $cot_id != FALSE ? $this->cotizar_model->prepare_cotizacion($cot_id) : NULL;

        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
//        $data['productos'] = $this->producto_model->get_productos_list();
        $data['productos'] = array();
        if ($this->session->userdata('grupo') == 2) { //perfil de administrador
            $data['usuarios'] = $this->usuario_model->select_all_user(array(8, 2, 9));
        }
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data["clientes"] = $this->cliente_model->get_all();
        $data["monedas"] = $this->monedas_model->get_all();
        $data["tipo_pagos"] = $this->condiciones_pago_model->get_all();
        $data['tipo_documentos'] = $this->db->get_where('documentos', array('ventas' => 1))->result();
        $data['precios'] = $this->precios_model->get_all_by('mostrar_precio', '1', array('campo' => 'orden', 'tipo' => 'ASC'));
        $data['comprobantes'] = $this->db->get_where('comprobantes', array('estado' => 1))->result();
        $data['comprobantes_default'] = $this->db->get_where('configuraciones', array('config_id' => '55'))->row();

        $data['dialog_venta_contado'] = $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->banco_model->get_all_in_object()
        ), true);

        $data['dialog_venta_credito'] = $this->load->view('menu/venta/dialog_venta_credito', array(
            'garantes' => $this->db->get('garante')->result()
        ), true);

        $data['dialog_venta_caja'] = $this->load->view('menu/venta/dialog_venta_caja', array(
            'next_id' => $this->venta->get_next_id()
        ), true);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    // Facturo una venta al credito ya sea manual o cuando pague la totalidad de las cuotas (2018-10-19) Antonio Martin
    function facturar_venta()
    {
        header('Content-Type: application/json');

        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $iddoc = $this->input->post('iddoc');

        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $iddoc = $iddoc != "" && is_numeric($iddoc) && ($iddoc == 1 || $iddoc == 3 || $iddoc == 6) ? $iddoc : false;

        if ($venta_id == false || $iddoc == false) {
            $data['success'] = 0;
            $data['msg'] = "Los parametros enviados no son correctos";
            echo json_encode($data);
            return false;
        }

        // Comienzo el proceso de facturacion de la venta
        $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        // Hago validaciones de logica del negocio para evitar conflictos
        if ($data['venta']->venta_status != 'COMPLETADO' || $data['venta']->serie != NULL || $data['venta']->numero != NULL) {
            $data['success'] = 0;
            $data['msg'] = "Esta venta ha sido facturada anteriormente.";
            echo json_encode($data);
            return false;
        }

        $this->db->trans_begin();

        $this->venta->facturar_venta($venta_id, $iddoc);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $data['success'] = 0;
            $data['msg'] = "Error de base de datos al inentar anular la venta.";
            echo json_encode($data);
            return false;
        }

        $this->db->trans_commit();

        $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        if (valueOptionDB('FACTURACION', 0) == 1 && ($data['venta']->id_documento == 1 || $data['venta']->id_documento == 3)) {
            $data['facturacion'] = $this->db->get_where('facturacion', array(
                'documento_tipo' => sumCod($data['venta']->id_documento, 2),
                'ref_id' => $data['venta']->venta_id
            ))->row();
        }

        $data['success'] = 1;
        $data['msg'] = "La venta ha sido facturada correctamente.";
        echo json_encode($data);
    }

    // Guardo la venta (2018-10-17) Antonio Martin
    function save_venta()
    {
        header('Content-Type: application/json');

        // Obtengo los parametros enviados
        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_documento'] = $this->input->post('tipo_documento');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['id_usuario'] = $this->input->post('vendedor_id'); //$this->session->userdata('nUsuCodigo');
        $venta['condicion_pago'] = $this->input->post('tipo_pago');
        $venta['id_moneda'] = $this->input->post('moneda_id');
        $venta['tasa_cambio'] = $this->input->post('tasa');

        $venta['venta_status'] = $this->input->post('venta_estado');
        $venta['fecha_venta'] = $this->input->post('fecha_venta');
        $venta['tipo_impuesto'] = $this->input->post('tipo_impuesto');

        $venta['subtotal'] = $this->input->post('subtotal');
        $venta['impuesto'] = $this->input->post('impuesto');
        $venta['total_importe'] = $this->input->post('total_importe');

        $venta['vc_total_pagar'] = $this->input->post('vc_total_pagar');
        $venta['vc_importe'] = $this->input->post('vc_importe');
        $venta['vc_vuelto'] = $this->input->post('vc_vuelto');
        $venta['vc_forma_pago'] = $this->input->post('vc_forma_pago');
        $venta['vc_num_oper'] = $this->input->post('vc_num_oper');
        $venta['vc_tipo_tarjeta'] = $this->input->post('vc_tipo_tarjeta');
        $venta['vc_banco_id'] = $this->input->post('vc_banco_id');


        $venta['c_dni_garante'] = $this->input->post('c_garante');
        $venta['c_inicial'] = $this->input->post('c_saldo_inicial') != '' ? $this->input->post('c_saldo_inicial') : 0;
        $venta['c_precio_contado'] = $this->input->post('c_precio_contado');
        $venta['c_precio_credito'] = $this->input->post('c_precio_credito');
        $venta['c_tasa_interes'] = $this->input->post('c_tasa_interes') != '' ? $this->input->post('c_tasa_interes') : 0;
        $venta['c_numero_cuotas'] = $this->input->post('c_numero_cuotas');
        $venta['c_fecha_giro'] = $this->input->post('c_fecha_giro');
        $venta['c_periodo_gracia'] = $this->input->post('c_periodo_gracia');

        $venta['caja_total_pagar'] = $this->input->post('caja_total_pagar');
        $venta['dni_garante'] = $this->input->post('caja_nombre');
        $venta['comprobante_id'] = $this->input->post('comprobante_id') != "" ? $this->input->post('comprobante_id') : 0;
        $venta['venta_nota'] = $this->input->post('venta_nota');

        $detalles_productos = json_decode($this->input->post('detalles_productos', true));
        $traspasos = json_decode($this->input->post('traspasos', true));
        $cuotas = json_decode($this->input->post('cuotas', true));

        // Valido que los parametros esten correctos
        // TODO hacer las validaciones de parametros restantes
        if ($venta['condicion_pago'] != 1 && $venta['condicion_pago'] != 2) {
            $data['success'] = 0;
            $data['msg'] = "El tipo de pago no es valido.";
            echo json_encode($data);
            return false;
        }

        if (count($detalles_productos) == 0) {
            $data['success'] = 0;
            $data['msg'] = "Debe crear al menos un producto para poder realizar la venta";
            echo json_encode($data);
            return false;
        }

        // Hago validaciones de logica del negocio para evitar conflictos
        $validar_detalle = array();
        foreach ($detalles_productos as $d) {
            $validar_detalle[] = array(
                'producto_id' => $d->id_producto,
                'local_id' => $venta['local_id'],
                'unidad_id' => $d->unidad_medida,
                'cantidad' => $d->cantidad,
                'moneda_id' => $venta['id_moneda']
            );
        }

        // Valido que el producto tenga costo unitario
        if (!$this->producto_costo_unitario_model->check_costo_unitario($validar_detalle)) {
            $data['success'] = 0;
            $data['msg'] = "Este producto no tiene costo unitario asignado. Verificar en el módulo de productos.";
            echo json_encode($data);
            return false;
        }

        // Valido que los productos tengan stock suficientes para realizar la venta
        $sin_stock = $this->inventario_model->check_stock($validar_detalle);
        if (count($sin_stock) > 0) {
            $data['success'] = 3;
            $data['msg'] = "Hay productos que no tienen stock suficientes para realizar la venta.";
            $data['sin_stock'] = json_encode($sin_stock);
            echo json_encode($data);
            return false;
        }

        // Si es factura el cliente debe tener un ruc valido
        $cliente = $this->db->get_where('cliente', array('id_cliente' => $venta['id_cliente']))->row();
        if ($venta['id_documento'] == 1) {
            if ($cliente->ruc != 2 || $cliente->id_cliente == 1) {
                $data['success'] = 0;
                $data['msg'] = "No se puede crear una factura a clientes que no tengan RUC.";
                echo json_encode($data);
                return false;
            }

            // TODO hacer aqui la validacion de que si esta inactivo en SUNAT no pueda crearse factura
            if ($cliente->status_sunat != 1) {
                $data['success'] = 0;
                $data['msg'] = "El cliente no esta activo en SUNAT para realizar ventas";
                echo json_encode($data);
                return false;
            }
        }

        // EL cliente frecuento no puede hacer ventas al credito
        if ($venta['condicion_pago'] == 2 && $cliente->id_cliente == 1) {
            $data['success'] = 0;
            $data['msg'] = "El cliente " . $cliente->razon_social . " no puede tener credito";
            echo json_encode($data);
            return false;
        }

        // El cliente frecuente no puede realizar ventas de boletas mayores a 700
        if ($venta['id_documento'] == 3 && $venta['vc_total_pagar'] > 700 && $cliente->id_cliente == 1) {
            $data['success'] = 0;
            $data['msg'] = "El cliente " . $cliente->razon_social . " no puede crear boletas mayor de 700 Soles.";
            echo json_encode($data);
            return false;
        }

        // Dependiendo de la condicion de pago hay dos metodos para guardar la venta
        if ($venta['condicion_pago'] == 1) {
            // Guardo la venta al contado
            $venta_id = $this->venta->save_venta_contado($venta, $detalles_productos, $traspasos);
        } elseif ($venta['condicion_pago'] == 2) {
            // Guardo la venta al credito
            $venta_id = $this->venta->save_venta_credito($venta, $detalles_productos, $traspasos, $cuotas);
        }

        if ($venta_id !== FALSE) {
            $data['success'] = 1;
            $data['msg'] = 'La venta ' . $venta_id . ' se ha guardado correctamente';

            $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

            // En caso de tener la facturacion electronica activa recupero comprobante generado
            if (valueOptionDB('FACTURACION', 0) == 1 && $data['venta']->condicion_pago == 1 && ($data['venta']->id_documento == 1 || $data['venta']->id_documento == 3)) {
                $data['facturacion'] = $this->db->get_where('facturacion', array(
                    'documento_tipo' => sumCod($data['venta']->id_documento, 2),
                    'ref_id' => $data['venta']->venta_id
                ))->row();
            }
        } else {
            $data['success'] = 0;
            $data['msg'] = "Error de base de datos al intentar guardar la venta";
            if (isset($this->venta->error)) {
                $data['msg'] = $this->venta->error;
            }
        }

        echo json_encode($data);
    }

    // Registro en caja y facturo una venta con estado CAJA (2018-10-17) Antonio Martin
    function save_venta_caja()
    {
        header('Content-Type: application/json');

        // Obtengo los parametros enviados
        $venta['venta_id'] = $this->input->post('venta_id');
        $venta['id_usuario'] = $this->session->userdata('nUsuCodigo');
        $venta['tipo_pago'] = $this->input->post('tipo_pago');
        $venta['importe'] = $this->input->post('importe');
        $venta['vuelto'] = $this->input->post('vuelto');
        $venta['tarjeta'] = $this->input->post('tarjeta');
        $venta['num_oper'] = $this->input->post('num_oper');
        $venta['banco_id'] = $this->input->post('banco');

        // Valido que los parametros esten correctos
        // TODO validar los parametros
        // Registro los datos necesarios para completar el proceso de venta
        $result = $this->venta->save_venta_caja($venta);

        if ($result !== FALSE) {
            $data['success'] = 1;
            $data['msg'] = '';

            $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta['venta_id']))->row();

            // En caso de tener la facturacion electronica activa recupero comprobante generado
            if (valueOptionDB('FACTURACION', 0) == 1 && $data['venta']->condicion_pago == 1 && ($data['venta']->id_documento == 1 || $data['venta']->id_documento == 3)) {
                $data['facturacion'] = $this->db->get_where('facturacion', array(
                    'documento_tipo' => sumCod($data['venta']->id_documento, 2),
                    'ref_id' => $data['venta']->venta_id
                ))->row();
            }
        } else {
            $data['success'] = 0;
            $data['msg'] = "Error de base de datos al intentar guardar la venta";
            if (isset($this->venta->error)) {
                $data['msg'] = $this->venta->error;
            }
        }

        echo json_encode($data);
    }

    // Anulacion de ventas, Muestro el modal para anular la venta (2018-10-16) Antonio Martin
    function anular_modal()
    {
        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $local_id = $this->input->post('local_id');
        $moneda_id = $this->input->post('moneda_id');

        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $local_id = $local_id != "" && is_numeric($local_id) ? $local_id : false;
        $moneda_id = $moneda_id != "" && is_numeric($moneda_id) ? $moneda_id : false;

        if ($venta_id == false || $local_id == false || $moneda_id == false) {
            $data['error'] = 'Los parametros enviados no estan correctos';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        $data['venta'] = $this->venta->get_venta_detalle($venta_id);
        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();

        $data['cuentas'] = $this->db->select('caja_desglose.*')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->where('caja.local_id', $local_id)
            ->where('caja.moneda_id', $moneda_id)
            ->where('caja_desglose.estado', 1)
            ->get()->result();

        // Verifico si hay cuentas validas
        if (count($data['cuentas']) == 0) {
            $data['error'] = 'No se ha podido obtener una cuenta valida.';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        echo $this->load->view('menu/venta/anular_modal', $data, TRUE);
    }

    // Anulacion de ventas, ejecuto el proceso de anular una venta (2018-10-16) Antonio Martin
    function anular_venta()
    {
        header('Content-Type: application/json');

        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $metodo_pago = $this->input->post('metodo_pago');
        $cuenta_id = $this->input->post('cuenta_id');
        $motivo = $this->input->post('motivo');

        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $metodo_pago = $metodo_pago != "" && is_numeric($metodo_pago) ? $metodo_pago : false;
        $cuenta_id = $cuenta_id != "" && is_numeric($cuenta_id) ? $cuenta_id : false;
        $motivo = $motivo != "" ? $motivo : false;

        if ($venta_id == false || $metodo_pago == false || $metodo_pago == false || $motivo == false) {
            $data['success'] = 0;
            $data['msg'] = "Los parametros enviados no son correctos";
            echo json_encode($data);
            return false;
        }

        // Comienzo con el proceso de anulacion
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        // Hago validaciones de logica del negocio para evitar conflictos
        if ($venta->venta_status == 'ANULADO') {
            $data['success'] = 0;
            $data['msg'] = "Esta venta ya fue anulada anteriormente.";
            echo json_encode($data);
            return false;
        }

        // Una venta con notas de credito creada no puede ser anulada
        $nota_credito = $this->db->get_where('notas_credito', array('venta_id' => $venta_id))->result();
        if (count($nota_credito) > 0) {
            $data['success'] = 0;
            $data['msg'] = "Esta venta tiene notas de credito y no puede ser anulada.";
            echo json_encode($data);
            return false;
        }

        //Dependiendo del estado de la venta realizo la anulacion correspondiente
        if ($venta->venta_status == 'CAJA') {
            $result = $this->venta->anular_venta_caja($venta_id, $motivo);
            if ($result !== FALSE) {
                $data['success'] = 1;
                $data['msg'] = "La anulacion se ha hecho correctamente.";
            } else {
                $data['success'] = 0;
                $data['msg'] = "Error de base de datos al inentar anular la venta.";
            }
            echo json_encode($data);
            return false;
        }

        if ($venta->venta_status == 'COMPLETADO') {
            // Hago las validaciones de facturacion electronica para boletas y facturas
            if (valueOptionDB('FACTURACION', 0) == 1) {
                // Solo facturas y boletas facturadas
                if (($venta->id_documento == 1 || $venta->id_documento == 3) && $venta->numero != null) {
                    $facturacion = $this->db->get_where('facturacion', array(
                        'documento_tipo' => sumCod($venta->id_documento, 2),
                        'ref_id' => $venta->venta_id
                    ))->row();

                    // TODO hacer las validaciones del limite de tiempo

                    // Las boletas solo pueden estar en estado generado
                    if ($venta->id_documento == 3 && $facturacion->estado != 1) {
                        $data['success'] = 0;
                        $data['msg'] = "Solo puedes anular boletas generadas.";
                        echo json_encode($data);
                        return false;
                    }

                    // Las facturas solo pueden estar en estado generado o aceptado
                    if ($venta->id_documento == 1 && $facturacion->estado != 1 && $facturacion->estado != 3) {
                        $data['success'] = 0;
                        $data['msg'] = "Solo puedes anular facturas generadas o aceptadas.";
                        echo json_encode($data);
                        return false;
                    }
                }
            }
            $result = $this->venta->anular_venta($venta_id, $metodo_pago, $cuenta_id, $motivo);
            if ($result !== FALSE) {
                $data['success'] = 1;
                $data['msg'] = "La anulacion se ha hecho correctamente.";
            } else {
                $data['success'] = 0;
                $data['msg'] = "Error de base de datos al inentar anular la venta.";
            }
            echo json_encode($data);
            return false;
        }

        // Llegado a este punto quiere decir que no se cumplio con los parametros requeridos para realizar la anulacion
        $data['success'] = 0;
        $data['msg'] = "Ha ocurrido un error inesperado al anular la venta.";
        log_message('error', 'La anulacion no cumplio con los parametros requeridos para realizar la anulacion');
        echo json_encode($data);
    }

    // Crear nota de credito a una venta, Muestro el modal para la nota de credito de la venta (2018-10-16) Antonio Martin
    function credito_modal()
    {
        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $local_id = $this->input->post('local_id');
        $moneda_id = $this->input->post('moneda_id');


        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $local_id = $local_id != "" && is_numeric($local_id) ? $local_id : false;
        $moneda_id = $moneda_id != "" && is_numeric($moneda_id) ? $moneda_id : false;

        if ($venta_id == false || $local_id == false || $moneda_id == false) {
            $data['error'] = 'Los parametros enviados no estan correctos';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        $total_pagado = $data['venta']->inicial > 0 ? $data['venta']->inicial : 0;
        $cobranzas = $this->db->select_sum('credito_cuotas_abono.monto_abono', 'total')
            ->from('credito_cuotas_abono')
            ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
            ->where('credito_cuotas.id_venta', $data['venta']->venta_id)
            ->get()->row();
        $total_pagado += $cobranzas->total;
        $data['total_pagado'] = $total_pagado;

        // Solo pueden crearse notas de credito de boletas o facturas
        if ($data['venta']->documento_id != 1 && $data['venta']->documento_id != 3) {
            $data['error'] = 'Solo pueden crearse notas de creditos a documentos fiscales';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();

        if (valueOptionDB('FACTURACION', 0) == 1) {
            if ($data['venta']->documento_id == 1) {
                $correlativo = $this->correlativos_model->get_correlativo($data['venta']->local_id, 9);
            }
            if ($data['venta']->documento_id == 3) {
                $correlativo = $this->correlativos_model->get_correlativo($data['venta']->local_id, 8);
            }
        } else {
            $correlativo = $this->correlativos_model->get_correlativo($data['venta']->local_id, 2);
        }

        if (isset($correlativo)) {
            $data['nota_credito_numero'] = $correlativo->serie . '-' . sumCod($correlativo->correlativo, 8);
        } else {
            $data['error'] = 'No se ha podido obtener el siguiente correlativo de la nota de credito';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        $data['cuentas'] = $this->db->select('caja_desglose.*')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->where('caja.local_id', $local_id)
            ->where('caja.moneda_id', $moneda_id)
            ->where('caja_desglose.estado', 1)
            ->get()->result();

        // Verifico si hay cuentas validas
        if (count($data['cuentas']) == 0) {
            $data['error'] = 'No se ha podido obtener una cuenta valida.';
            echo $this->load->view('errors/html/error_404_modal', $data, TRUE);
            return false;
        }

        echo $this->load->view('menu/venta/credito_modal', $data, TRUE);
    }

    // Crear nota de credito a una venta, Muestro el modal para la nota de credito de la venta (2018-10-16) Antonio Martin
    function nota_credito_venta()
    {
        header('Content-Type: application/json');

        // Obtengo los parametros enviados
        $venta_id = $this->input->post('venta_id');
        $metodo_pago = $this->input->post('metodo_pago');
        $cuenta_id = $this->input->post('cuenta_id');
        $motivo = $this->input->post('motivo');
        $nc_detalles = json_decode($this->input->post('nc_detalles'));

        // Valido que los parametros esten correctos
        $venta_id = $venta_id != "" && is_numeric($venta_id) ? $venta_id : false;
        $metodo_pago = $metodo_pago != "" && is_numeric($metodo_pago) ? $metodo_pago : false;
        $cuenta_id = $cuenta_id != "" && is_numeric($cuenta_id) ? $cuenta_id : false;
        $motivo = $motivo != "" ? $motivo : false;


        if ($venta_id == false || $metodo_pago == false || $metodo_pago == false || $motivo == false) {
            $data['success'] = 0;
            $data['msg'] = "Los parametros enviados no son correctos";
            echo json_encode($data);
            return false;
        }


        // valido que las cantidades enviadas a anular esten correctas
        $total_detalle_devuelto = 0;
        foreach ($nc_detalles as $d) {
            $total_detalle_devuelto += $d->cantidad;

            $detalle = $this->db->get_where('detalle_venta', array('id_detalle' => $d->detalle_id))->row();
            if ($detalle == null || $d->cantidad == "" || !is_numeric($d->cantidad) || $d->cantidad <= 0) {
                $data['success'] = 0;
                $data['msg'] = "Los parametros enviados en el detalles de los productos no son correctos";
                echo json_encode($data);
                return false;
            }

            $cantidad_devuelta = $detalle->cantidad_devuelta == null ? 0 : $detalle->cantidad_devuelta;
            if ($d->cantidad > ($detalle->cantidad - $cantidad_devuelta)) {
                $data['success'] = 0;
                $data['msg'] = "No puede devolver una cantidad mayor a la venta original";
                echo json_encode($data);
                return false;
            }
        }

        // valido que la venta cumpla con las condiciones requeridas
        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();

        // Validar que la venta este en estado completada
        if ($venta->venta_status != 'COMPLETADO') {
            $data['success'] = 0;
            $data['msg'] = "Solo pueden crearse notas de credito de una venta con un estado completado";
            echo json_encode($data);
            return false;
        }

        // validar que sea un documento fiscal
        if ($venta->id_documento != 1 && $venta->id_documento != 3) {
            $data['success'] = 0;
            $data['msg'] = "Solo pueden crearse notas de creditos a documentos fiscales";
            echo json_encode($data);
            return false;
        }

        // Validar que la venta sea facturada
        if ($venta->numero == null) {
            $data['success'] = 0;
            $data['msg'] = "Solo pueden hacerse notas de credito a ventas facturadas";
            echo json_encode($data);
            return false;
        }

        if ($venta->condicion_pago == 2) {

            // Si el motivo de la devolucion es parcial por item valido que no tenga ningun pago efectuado por el cliente
            if ($motivo == '07') {
                $total_pagado = $venta->inicial > 0 ? $venta->inicial : 0;
                $cobranzas = $this->db->select_sum('credito_cuotas_abono.monto_abono', 'total')
                    ->from('credito_cuotas_abono')
                    ->join('credito_cuotas', 'credito_cuotas.id_credito_cuota = credito_cuotas_abono.credito_cuota_id')
                    ->where('credito_cuotas.id_venta', $venta->venta_id)
                    ->get()->row();
                $total_pagado += $cobranzas->total;

                if ($total_pagado > 0) {
                    $data['success'] = 0;
                    $data['msg'] = "Esta venta al credito tiene ya pagos efectuados y no puede hacer una devolucion por item.";
                    echo json_encode($data);
                    return false;
                }
            } else {
                // Valido que la devolucion sea total
                $detalle_venta = $this->db->select_sum('cantidad', 'total_cantidad')
                    ->from('detalle_venta')
                    ->where('id_venta', $venta->venta_id)
                    ->get()->row();

                if ($detalle_venta->total_cantidad != $total_detalle_devuelto) {
                    $data['success'] = 0;
                    $data['msg'] = "Solo pueden hacerse devoluciones totales para las ventas al credito.";
                    echo json_encode($data);
                    return false;
                }
            }

        }

        // Validaciones para la facturacion electronica
        if (valueOptionDB('FACTURACION', 0) == 1){
            $facturacion = $this->db->get_where('facturacion', array(
                'documento_tipo' => sumCod($venta->id_documento, 2),
                'ref_id' => $venta->venta_id
            ))->row();

            if($facturacion == null){
                $data['success'] = 0;
                $data['msg'] = "No se encontro ningun registro en facturacion electronica";
                echo json_encode($data);
                return false;
            }

            if($facturacion->estado != 3){
                $data['success'] = 0;
                $data['msg'] = "No puede crear una nota de credito a un comprobante que no ha sido emitido";
                echo json_encode($data);
                return false;
            }
        }

        // Creo la nota de credito
        $result = $this->venta->crear_nota_credito($venta_id, $metodo_pago, $cuenta_id, $motivo, $nc_detalles);
        if ($result !== FALSE) {
            $data['success'] = 1;
            $data['msg'] = "La nota de credito " . $result->serie . "-" . $result->numero . " se ha hecho correctamente.";
        } else {
            $data['success'] = 0;
            $data['msg'] = "Error de base de datos al inentar crear la nota de credito.";
        }

        echo json_encode($data);
        return false;
    }

    function set_stock()
    {
        $stock_minimo = $this->input->post('stock_minimo');
        $stock_total_minimo = $this->input->post('stock_total_minimo');
        $producto_id = $this->input->post('producto_id');
        $local_id = $this->input->post('local_id');

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local_id))->row();
        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min - $stock_minimo);

        $locales = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $all_cantidad_min = 0;
        foreach ($locales as $local) {
            $cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local->local_id))->row();
            $temp = $cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $cantidad->cantidad, $cantidad->fraccion) : 0;
            $all_cantidad_min += $temp;
        }

        $data['stock_total'] = $this->unidades_model->get_cantidad_fraccion($producto_id, $all_cantidad_min - $stock_total_minimo);

        $data['stock_minimo'] = $old_cantidad_min;
        $data['stock_total_minimo'] = $all_cantidad_min;

        $data['stock_minimo_left'] = $old_cantidad_min - $stock_minimo;
        $data['stock_total_minimo_left'] = $all_cantidad_min - $stock_total_minimo;


        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function set_stock_desglose()
    {
        $locales = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $producto_id = $this->input->post('producto_id');


        foreach ($locales as $local) {
            $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $producto_id, 'id_local' => $local->local_id))->row();
            $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($producto_id, $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;
            $data['locales'][] = $local->local_nombre;
            $data['stock_desgloses'][] = $this->unidades_model->get_cantidad_fraccion($producto_id, $old_cantidad_min);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_unidades($moneda_id = '')
    {
        $producto_id = $this->input->post('producto_id');
        $precio_id = $this->input->post('precio_id');

        $data['unidades'] = $this->unidades_model->get_unidades_precios($producto_id, $precio_id);

        $data['moneda'] = $this->unidades_model->get_moneda_default($producto_id);

        if (validOption('ACTIVAR_SHADOW', 1)) {
            if ($moneda_id != '')
                $data['precio_contable'] = $this->shadow_model->get_precio_contable($producto_id, $moneda_id);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_productos_precios()
    {
        $producto_id = $this->input->post('producto_id');
        $precio_id = $this->input->post('precio_id');

        $data['unidades'] = $this->unidades_model->get_unidades_precios($producto_id, $precio_id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function update_cliente()
    {
        $data['clientes'] = $data["clientes"] = $this->cliente_model->get_all();

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_venta_cobro()
    {
        $venta_id = $this->input->post('venta_id');
        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function opciones($action = 'get')
    {
        $this->load->model('opciones/opciones_model');
        $keys = array(
            'CREDITO_INICIAL',
            'CREDITO_TASA',
            'CREDITO_CUOTAS',
            'VISTA_CREDITO',
            'COSTO_AUMENTO',
            'COBRAR_CAJA',
            'COTIZACION_INFORMACION',
            'COTIZACION_CONDICION',
            'COTIZACION_PIE_PAGINA',
            'COMPROBANTE',
            'DOCUMENTO_DEFECTO',
            'BOTONES_VENTA',
            'NOMBRE_PRODUCTO',
            'COTIZACION_COLOR_FORMATO',
            'EMBALAJE_IMPRESION',
            'NUMERO_DECIMALES',
            'REDONDEO_VENTAS',
            'VALOR_COMPROBANTE'
        );

        if ($action == 'get') {
            $data['configuraciones'] = $this->opciones_model->get_opciones($keys);
            $data['documentos'] = $this->documentos_model->get_documentosBy("ventas = '1'");
            $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/opciones', $data, true);

            if ($this->input->is_ajax_request()) {
                echo $dataCuerpo['cuerpo'];
            } else {
                $this->load->view('menu/template', $dataCuerpo);
            }
        } elseif ($action == 'save') {

            $configuraciones = array();
            foreach ($keys as $key) {
                if (is_array($this->input->post($key))) {
                    $config_value = json_encode($this->input->post($key));
                } else {
                    $config_value = $this->input->post($key);
                }
                $configuraciones[] = array(
                    'config_key' => $key,
                    'config_value' => $config_value
                );
            }

            $result = $this->opciones_model->guardar_configuracion($configuraciones);
            $configuraciones = $this->opciones_model->get_opciones($keys);

            if (count($configuraciones) > 0) {
                foreach ($configuraciones as $configuracion) {
                    $data[$configuracion['config_key']] = $configuracion['config_value'];
                }
                $this->session->set_userdata($data);
            }

            if ($result)
                $json['success'] = 'Las configuraciones se han guardado exitosamente';
            else
                $json['error'] = 'Ha ocurido un error al guardar las configuraciones';

            echo json_encode($json);
        }
    }

    function ofertas($action = 'get')
    {
        $this->load->model('opciones/opciones_model');
        $keys = array(
            'FECHA_VENTA_PROMO',
            'VENTA_PROMO'
        );

        if ($action == 'get') {
            $data['configuraciones'] = $this->opciones_model->get_opciones($keys);
            $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/ofertas', $data, true);

            if ($this->input->is_ajax_request()) {
                echo $dataCuerpo['cuerpo'];
            } else {
                $this->load->view('menu/template', $dataCuerpo);
            }
        } elseif ($action == 'save') {

            $configuraciones = array();
            foreach ($keys as $key) {
                $configuraciones[] = array(
                    'config_key' => $key,
                    'config_value' => urldecode($this->input->post($key, false))
                );
            }


            $result = $this->opciones_model->guardar_configuracion($configuraciones);
            $configuraciones = $this->opciones_model->get_opciones($keys);

            if (count($configuraciones) > 0) {
                foreach ($configuraciones as $configuracion) {
                    $data[$configuracion['config_key']] = $configuracion['config_value'];
                }
                $this->session->set_userdata($data);
            }

            if ($result)
                $json['success'] = 'Las configuraciones se han guardado exitosamente';
            else
                $json['error'] = 'Ha ocurido un error al guardar las configuraciones';

            echo json_encode($json);
        }
    }

    function historial_pdf()
    {
        $params = json_decode($this->input->get('data'));

        $date_range = explode(" - ", $params->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        $condition = array(
            'local_id' => $params->local_id,
            'condicion_id' => $params->condicion_pago_id,
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'moneda_id' => $params->moneda_id
        );
        $data = $condition;

        $local = $this->db->get_where('local', array('int_local_id' => $condition['local_id']))->row();
        $data['local_nombre'] = $local->local_nombre;
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $condition['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($condition);

        $data['venta_totales'] = $this->venta->get_ventas_totales($condition);
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
        $html = $this->load->view('menu/venta/historial_list_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function imprimir($venta_id, $tipo_impresion)
    {
        $venta_temp = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $moneda = $this->db->get_where('moneda', array('id_moneda' => $venta_temp->id_moneda))->row();
        if ($tipo_impresion == 'PEDIDO') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            $data['identificacion'] = $this->db->get_where('configuraciones', array('config_key' => 'EMPRESA_IDENTIFICACION'))->row();
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
            $this->load->view('menu/venta/impresiones/nota_pedido', $data);
        } elseif ($tipo_impresion == 'ALMACEN') {
            $pedido = $this->venta->get_venta_detalle($venta_id);
            $detalles = array();
            foreach ($pedido->detalles as $venta) {
                $detalles[] = $venta;
                $venta->origen = $pedido->local_nombre;

                $kardexs = $this->db->get_where('kardex', array(
                    'ref_id' => $pedido->venta_id,
                    'io' => 1,
                    'tipo' => -1,
                    'operacion' => 11,
                    'producto_id' => $venta->producto_id,
                    'unidad_id' => $venta->unidad_id
                ))->result();


                foreach ($kardexs as $kardex) {
                    $venta->cantidad -= $kardex->cantidad;
                    $venta_temp = clone $venta;
                    $venta_temp->cantidad = $kardex->cantidad;
                    $venta_temp->origen = $kardex->ref_val;
                    $venta_temp->importe = number_format($venta_temp->cantidad * $venta_temp->precio, 2);
                    $detalles[] = $venta_temp;
                }

                $venta->importe = number_format($venta->cantidad * $venta->precio, 2);
            }

            $pedido->detalles = $detalles;
            $data['venta'] = $pedido;
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
            $this->load->view('menu/venta/impresiones/pedido_almacen', $data);
            //$this->venta->imprimir_pedido($data);
        } elseif ($tipo_impresion == 'DOCUMENTO' || $tipo_impresion == 'SC') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            if ($tipo_impresion == 'SC')
                $data['venta'] = $this->shadow_model->get_venta_contable_detalle($venta_id);
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);
            $this->db->where('venta_id', $venta_id);
            $this->db->update('venta', array('factura_impresa' => '1'));

            if ($data['venta']->documento_id == 1) {
                //$this->load->view('menu/venta/impresiones/factura', $data);
                $this->venta->imprimir_factura($data);
            } elseif ($data['venta']->documento_id == 3) {
                //$this->load->view('menu/venta/impresiones/boleta', $data);
                $this->venta->imprimir_boleta($data);
            }
        } elseif ($tipo_impresion == 'TRASPASO') {
            $id_traspaso = $this->db->get_where('traspaso', array('ref_id' => $venta_id))->row();
            $data_origen = $this->venta->get_traspaso_local($id_traspaso->id);

            $x = 0;
            foreach ($data_origen as $idLocal) {
                $data['datos'][$x]['head'] = $this->venta->get_venta_traspaso($id_traspaso->id);
                $data['datos'][$x]['detalles'] = $this->venta->get_venta_detalle_traspaso($id_traspaso->id, $idLocal->local_origen);
                $x++;
            }
            $this->load->view('menu/venta/impresiones/traspaso', $data);
        } elseif ($tipo_impresion == 'A4') {
            $data['venta'] = $this->venta->get_venta_detalle($venta_id);
            $data['identificacion'] = $this->db->get_where('configuraciones', array('config_key' => 'EMPRESA_IDENTIFICACION'))->row();
            $total = $data['venta']->total;
            $data['totalLetras'] = numtoletras($total, $moneda->nombre);

            $this->load->library('mpdf53/mpdf');
            $mpdf = new mPDF('utf-8', 'A4', 0, '', 5, 5, 5, 5, 5, 5);
            if (SERVER_NAME == SERVER_CRDIGITAL) {
                $html = $this->load->view('menu/venta/impresiones/nota_pedido_crdigital', $data, true);
            } else {
                $html = $this->load->view('menu/venta/impresiones/nota_pedido_a4', $data, true);
            }
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
    }

    function imprimir_html()
    {

        $venta_id = $this->input->post('venta_id');
        $tipo_impresion = $this->input->post('tipo_impresion');

        $data['venta'] = $this->venta->get_venta_detalle($venta_id);

        if ($tipo_impresion == 'PEDIDO') {
            $documento = 'boleta';

            $this->load->view('menu/venta/impresiones/' . $documento, $data);
        }
    }

    function historial_excel()
    {

        $params = json_decode($this->input->get('data'));

        $date_range = explode(" - ", $params->fecha);
        $fecha_ini = str_replace("/", "-", $date_range[0]);
        $fecha_fin = str_replace("/", "-", $date_range[1]);


        $condition = array(
            'local_id' => $params->local_id,
            'condicion_id' => (isset($params->condicion_pago_id)) ? $params->condicion_pago_id : '',
            'fecha_ini' => $fecha_ini,
            'fecha_fin' => $fecha_fin,
            'moneda_id' => $params->moneda_id
        );
        $data = $condition;

        $local = $this->db->get_where('local', array('int_local_id' => $condition['local_id']))->row();
        $data['local_nombre'] = $local->local_nombre;
        $data['moneda'] = $this->db->get_where('moneda', array('id_moneda' => $condition['moneda_id']))->row();
        $data['ventas'] = $this->venta->get_ventas($condition);

        $data['venta_totales'] = $this->venta->get_ventas_totales($condition);

        echo $this->load->view('menu/venta/historial_list_excel', $data, true);
    }

    function recarga()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data["clientes"] = $this->cliente_model->get_all();
        $data['operadore'] = $this->diccionario_termino_model->get_all_operador();
        $data['poblados'] = $this->clientes_grupos_model->get_all();
        $data['monedas'] = $this->monedas_model->get_monedas_activas();
        $data['condPagos'] = $this->condiciones_pago_model->get_all();
        $data["documentos"] = $this->db->get_where('documentos', array('ventas' => 1))->result();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/venta/recarga', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function save_recarga()
    {
        $venta['local_id'] = $this->input->post('local_venta_id');
        $venta['id_cliente'] = $this->input->post('cliente_id');
        $venta['rec_ope'] = $this->input->post('operador_id');
        $venta['fecha_venta'] = $this->input->post('fecha_venta');
        $venta['id_moneda'] = $this->input->post('moneda_id');
        $venta['total_importe'] = $this->input->post('total_importe');
        $venta['condicion_pago'] = $this->input->post('tipo_pago');
        $venta['rec_nro'] = $this->input->post('nro_recarga');
        $venta['cod_tran'] = $this->input->post('cod_tran');
        $venta['id_usuario'] = $this->session->userdata('nUsuCodigo');
        $venta['vc_importe'] = $this->input->post('vc_importe2');
        $venta['vc_vuelto'] = $this->input->post('vc_vuelto2');
        $venta['rec_pob'] = $this->input->post('poblado_id');
        $venta['nota'] = $this->input->post('tienda');
        $venta['vc_forma_pago'] = $this->input->post('vc_forma_pago2');
        $venta['vc_banco_id'] = $this->input->post('vc_banco_id2');
        $venta['vc_num_oper'] = $this->input->post('vc_num_oper2');
        $venta['telefono1'] = $this->input->post('nro_recarga');
        $venta['venta_status'] = 'COMPLETADO';
        $venta['id_documento'] = $this->input->post('cboDocumento');
        $venta_id = false;
        if ($venta['condicion_pago'] == 2 && $venta['id_cliente'] == 1) {
            $this->venta->error = 'El Cliente frecuente no tiene credito.';
        } else {
            $venta_id = $this->venta->save_recarga($venta);
        }

        if ($venta_id) {
            $data['success'] = '1';
            $data['venta'] = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        } else {
            if (isset($this->venta->error)) {
                $data['msg'] = $this->venta->error;
            }
            $data['success'] = '0';
        }
        echo json_encode($data);
    }

    function dialog_venta_contado()
    {
        $this->load->view('menu/venta/dialog_venta_contado', array(
            'tarjetas' => $this->db->get('tarjeta_pago')->result(),
            'metodos' => $this->metodos_pago_model->get_all(),
            'bancos' => $this->banco_model->get_all_in_object()
        ));
    }

    function getCliente()
    {
        $id = $this->input->post('id');
        $datos = $this->cliente_model->get_by('id_cliente', $id);
        echo json_encode($datos);
    }

    function ultimasVentas()
    {
        $venta['id_producto'] = $this->input->post('id_producto');
        $venta['id_cliente'] = $this->input->post('id_cliente');
        $data = $this->venta->ultimasVentas($venta);
        echo json_encode($data);
    }

    function ultimasCompras()
    {
        $venta['id_producto'] = $this->input->post('id_producto');
        $data = $this->venta->ultimasCompras($venta);
        echo json_encode($data);
    }

    function verificarAnulacion($id_venta)
    {
        $dato = $this->venta->verificarAnulacion($id_venta);
        $data['num_reg'] = $dato->numReg;
        echo json_encode($data);
    }

}
