<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class traspaso extends MY_Controller
{

    private $columnas = array();

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {        
            $this->load->model('traspaso/traspaso_model');
            $this->load->model('columnas/columnas_model');
            $this->load->model('producto/producto_model');
            $this->load->model('marca/marcas_model');
            $this->load->model('linea/lineas_model');
            $this->load->model('familia/familias_model');
            $this->load->model('grupos/grupos_model');
            $this->load->model('proveedor/proveedor_model');
            $this->load->model('impuesto/impuestos_model');
            $this->load->model('inventario/inventario_model');
            $this->load->model('producto/producto_model');
            $this->load->model('cliente/cliente_model');
            $this->load->model('detalle_ingreso/detalle_ingreso_model');
            $this->load->model('unidades/unidades_model');
            $this->load->model('columnas/columnas_model');
            $this->load->model('precio/precios_model');
            $this->load->model('local/local_model');
            $this->load->model('unidades_has_precio/unidades_has_precio_model');
            $this->load->library('Pdf');
            $this->load->library('phpExcel/PHPExcel.php');
        }else{
            redirect(base_url(), 'refresh');
        }

        $this->columnas = $this->columnas_model->get_by('tabla', 'producto');
    }


    function form()
    {
        $data = array();
        if ($this->session->userdata('esSuper') == 1) {
            $data['locales'] = $this->local_model->get_all();
        } else {
            $usu = $this->session->userdata('nUsuCodigo');
            $data['locales'] = $this->local_model->get_all_usu($usu);
        }

        $where = array(
            'producto_estatus' => 1,
            'producto_almacen.id_local' => $data['locales'][0]['int_local_id']
        );
        $data['productos'] = $this->producto_model->get_stock_normal($where);
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();


        echo $this->load->view('menu/traspaso/form', $data, true);
    }

    function productos_porlocal_almacen()
    {
        $data = array();
        $this->input->post('local');
        $where = array(
            'producto_estatus' => 1,
            'producto_almacen.id_local' => $this->input->post('local')
        );

        $data['productos'] = $this->producto_model->get_stock_normal($where);
        for($i = 0; $i < count($data['productos']);$i++){
            $data['productos'][$i]['producto_nombre'] = getCodigoValue(sumCod($data['productos'][$i]['producto_id']), $data['productos'][$i]['producto_codigo_interno']) . ' - ' . $data['productos'][$i]['producto_nombre'];
        }

        echo json_encode($data);
    }

    function form_buscar()
    {
        $data = array();


        $where = array(
            "id_local" => $this->input->post('local_id'),
            "id_producto" => $this->input->post('producto_id')
        );

        $data["cantidad_prod"] = $this->producto_model->traer_by("cantidad, fraccion", "producto_almacen", false, false, false, $where);

        $temp[0]['producto_id'] = $this->input->post('producto_id');
        $data["um"] = $this->_getUnidades($temp);
        $data['um'][0]['nombre_unidad'] = $this->producto_model->getUmById($this->input->post('producto_id'));
        $data['um'] = $data['um'][0];


        //$data['unidades'] = $this->unidades_model->get_unidades_cantidad($this->input->post('producto_id'), $this->input->post('local_id'));

        $old_cantidad = $this->db->get_where('producto_almacen', array('id_producto' => $this->input->post('producto_id'), 'id_local' => $this->input->post('local_id')))->row();

        $old_cantidad_min = $old_cantidad != NULL ? $this->unidades_model->convert_minimo_um($this->input->post('producto_id'), $old_cantidad->cantidad, $old_cantidad->fraccion) : 0;

        $data['stock_actual'] = $this->unidades_model->get_cantidad_fraccion($this->input->post('producto_id'), $old_cantidad_min);
        $data['stock_minimo'] = $old_cantidad_min;
        echo json_encode($data);
    }

    //Creo la consulta para asignarle las unidades y la fraccion a los productos
    function _getUnidades($productos)
    {
        $temp = $productos;

        $select = 'unidades.nombre_unidad, unidades_has_producto.*';
        $from = "unidades";
        $join = array('unidades_has_producto');
        $campos_join = array('unidades_has_producto.id_unidad=unidades.id_unidad');
        $order = "orden desc";

        for ($i = 0; $i < count($temp); $i++) {
            $where = array('unidades_has_producto.producto_id' => $temp[$i]['producto_id']);
            $buscar = $this->unidades_model->traer_by($select, $from, $join, $campos_join, $where, false, $order, "RESULT_ARRAY");
            if (isset($temp[$i]['fraccion']))
                if ($temp[$i]['fraccion'] == "") $temp[$i]['fraccion'] = "0";
            if (!empty($buscar)) {
                if ($buscar[0]['orden'] > 1) {

                    $temp[$i]['nombre_fraccion'] = $buscar[0]['nombre_unidad'];
                } else {
                    $temp[$i]['nombre_fraccion'] = "";
                }
            }
        }

        return $temp;
    }

    function index($local = "")
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

        $data['productos'] = $this->producto_model->productosporlocal_venta($data["locales"][0]["int_local_id"]);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/traspaso/traspaso', $data, true);

        if ($this->input->is_ajax_request())
            echo $dataCuerpo['cuerpo'];
        else
            $this->load->view('menu/template', $dataCuerpo);

    }

    function lst_reg_traspasos()
    {
        if ($this->input->is_ajax_request()) {

            $this->load->model('kardex/kardex_model');

            $condicion = array(
                'smovimiento_historico.tipo_movimiento' => "TRASPASO"
            );

            if ($this->input->post('locales') != "TODOS") {
                $condicion['local_id'] = $this->input->post('locales');
            }
            $data['local'] = $this->input->post('locales', true);
            if ($_POST['fecIni'] != "") {
                $condicion['date >= '] = date('Y-m-d', strtotime($_POST['fecIni']));
            }

            if ($_POST['fecFin'] != "") {
                $fechadespues = strtotime('+1 day', strtotime($_POST['fecFin']));

                $condicion['date <= '] = date('Y-m-d', $fechadespues);
            }

            if ($this->input->post('productos_traspaso', true) != "TODOS") {
                $condicion['producto_id'] = $this->input->post('productos_traspaso', true);
            }

            if ($this->input->post('tipo_mov', true) != "TODOS") {
                $condicion['tipo_operacion'] = $this->input->post('tipo_mov', true);
            }

            $data['movimientos'] = $this->historico_model->get_historico($condicion);

            $this->load->view('menu/traspaso/lst_reg_traspasos', $data);
        } else {
            redirect(base_url() . 'traspaso/', 'refresh');
        }
    }

    function getbylocal()
    {
        $local = $this->input->post('local');
        $this->index($local);
    }

    function traspasar_productos()
    {

        $productos = json_decode($this->input->post('lst_producto', true));
        $local_destino = $this->input->post('local_destino', true);
        $fecha_traspaso = $this->input->post('fecha_traspaso', date('Y-m-d'));

        $fecha_traspaso = date('Y-m-d H:i:s', strtotime($fecha_traspaso . " " . date('H:i:s')));
        $traspasar = $this->traspaso_model->traspasar_productos_traspaso($productos, $local_destino, $fecha_traspaso);

        if (true) {
            $json['success'] = 'Se ha realizado el traspaso de almacenes exitosamente';
        } else {
            $json['error'] = 'error para el traspaso de productos de almacen';
        }

        echo json_encode($json);
    }

}
