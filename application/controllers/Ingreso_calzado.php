<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingreso_calzado extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {   
            $this->load->model('ingreso/ingreso_calzado_model');
            $this->load->model('plantilla/plantilla_model');
            $this->load->model('local/local_model');
            $this->load->model('monedas/monedas_model');
        }else{
            redirect(base_url(), 'refresh');
        }  
    }

    public function index()
    {
        $data['series'] = $this->db->get_where('pl_serie', array('estado' => '1'))->result();
        $data['plantillas'] = $this->plantilla_model->get_plantilla_select();
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['proveedores'] = $this->db->get_where('proveedor', array('proveedor_status' => 1))->result();
        $data["monedas"] = $this->monedas_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso_calzado/index', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    public function save()
    {

        $ingreso = array(
            'costo' => $this->input->post('costo'),
            'utilidad' => $this->input->post('utilidad'),
            'proveedor_id' => $this->input->post('proveedor_id'),
            'local_id' => $this->input->post('local_id'),
            'fecha_emision' => $this->input->post('fecha_emision'),
            'tipo_documento' => $this->input->post('tipo_documento'),
            'documento_serie' => $this->input->post('documento_serie'),
            'documento_numero' => $this->input->post('documento_numero'),
            'pago' => $this->input->post('pago'),
            'moneda_id' => $this->input->post('moneda_id'),
            'tasa' => $this->input->post('tasa'),
            'impuesto' => $this->input->post('impuesto'),
            'subtotal' => $this->input->post('subtotal'),
            'total' => $this->input->post('total'),
        );


        $plantillas = json_decode($this->input->post('plantillas'));

        $result = $this->ingreso_calzado_model->save($ingreso, $plantillas);

        if ($result != FALSE) {
            $data['success'] = 1;
            $data['plantilla_id'] = $result;
        }

        echo json_encode($data);
    }

    public function reporte()
    {
        $data['series'] = $this->db->get_where('pl_serie', array('estado' => '1'))->result();
        $data['plantillas'] = $this->plantilla_model->get_plantilla_select();
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['proveedores'] = $this->db->get_where('proveedor', array('proveedor_status' => 1))->result();
        $data["monedas"] = $this->monedas_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso_calzado/reporte', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    public function find()
    {
        $plantilla_id = $this->input->post('plantilla_id');
        $local_id = $this->input->post('local_id');
        $_serie = $this->input->post('serie');

        if ($_serie == "")
            $_serie = "%";

        $plantillas = array();
        if ($plantilla_id == "") {
            $productos = $this->db->query("
            SELECT * FROM producto AS p 
            JOIN producto_almacen AS pa ON p.producto_id = pa.id_producto AND pa.id_local = " . $local_id . "
            GROUP BY p.producto_codigo_interno")->result();

            foreach ($productos as $producto) {
                $pieces = explode('-', $producto->producto_codigo_interno);
                $plantillas[$pieces[0]] = $pieces[0];
            }
        } else {
            $plantillas[$plantilla_id] = $plantilla_id;
        }

        $result = array();
        foreach ($plantillas as $plantilla_id) {
            $like = $plantilla_id . '-' . $_serie . '-%';
            $productos = $this->db->query("
            SELECT * FROM producto AS p 
            JOIN producto_almacen AS pa ON p.producto_id = pa.id_producto AND pa.id_local = " . $local_id . "
            WHERE p.producto_codigo_interno 
            LIKE '" . $like . "' 
            GROUP BY p.producto_codigo_interno")->result();


            $temp = new stdClass();
            if (count($productos) > 0) {
                $temp->nombre = $productos[0]->producto_nombre;


                $precio = $this->db->query("
                SELECT 
                    di.precio,
                    i.costo_por,
                    i.utilidad_por,
                    di.precio_venta
                FROM ingreso AS i 
                JOIN detalleingreso AS di ON di.id_ingreso = i.id_ingreso 
                WHERE di.id_producto = " . $productos[0]->producto_id . " 
                ORDER BY di.id_detalle_ingreso DESC LIMIT 1
            ")->row();

                $temp->costo_unitario = $precio->precio;
                $temp->precio_max = $precio->precio_venta;
                $temp->precio_min = $temp->costo_unitario;
                $temp->precio_min += $temp->costo_unitario * $precio->costo_por / 100;
                $temp->precio_min += $temp->costo_unitario * $precio->utilidad_por / 100;
            }

            foreach ($productos as $producto) {
                $pieces = explode('-', $producto->producto_codigo_interno);

                if (!isset($temp->series[$pieces[1]])) {
                    $temp->series[$pieces[1]] = array();

                    $serie = $this->db->get_where('pl_serie', array(
                        'serie' => $pieces[1]
                    ))->row();
                    $rangos = explode('|', $serie->rango);

                    foreach ($rangos as $rango) {
                        $temp->series[$pieces[1]][$rango] = 0;
                    }
                }

                $inventario = $this->db->get_where('producto_almacen', array(
                    'id_local' => $local_id,
                    'id_producto' => $producto->producto_id
                ))->row();

                $temp->series[$pieces[1]][$pieces[2]] = $inventario->cantidad;

            }

            $result[] = $temp;
        }

        $data['productos'] = $result;

        echo $this->load->view('menu/ingreso_calzado/reporte_tabla', $data, true);
    }


}
