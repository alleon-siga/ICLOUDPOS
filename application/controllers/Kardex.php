<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class kardex extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('kardex/kardex_model');
        $this->load->model('local/local_model');
        $this->load->model('producto/producto_model');
        $this->load->model('unidades/unidades_model');
    }


    function index()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $dataCuerpo['cuerpo'] = $this->load->view('menu/kardex/kardex', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_productos()
    {
        $params = array();
        $data['local_id'] = "";

        if ($this->input->post('local_id') != "") {
            $params['local_id'] = $this->input->post('local_id');
            $data['local_id'] = $params['local_id'];
        }

        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $data['productos'] = $this->producto_model->get_productos($params);
        $this->load->view('menu/kardex/kardex_lista', $data);
    }

    function get_kardex($producto_id, $local_id, $mes, $year, $dia_min, $dia_max)
    {
        $data['kardex'] = $this->kardex_model->get_kardex(array(
            'producto_id' => $producto_id,
            'local_id' => $local_id,
            'mes' => $mes,
            'year' => $year,
            'dia_min' => $dia_min,
            'dia_max' => $dia_max
        ));

        $data['producto'] = $this->db->get_where('producto', array(
            'producto_id' => $producto_id
        ))->row();

        $data['local'] = $this->db->get_where('local', array('int_local_id' => $local_id))->row();
        $data['unidad'] = $this->unidades_model->get_um_min_by_producto($producto_id);
        $data['year'] = $year;
        $data['mes'] = $mes;

        $this->load->view('menu/kardex/kardex_detalle', $data);
    }

    function exportar_kardex($producto_id, $local_id, $mes, $year, $dia_min, $dia_max)
    {
        $data['kardex'] = $this->kardex_model->get_kardex(array(
            'producto_id' => $producto_id,
            'local_id' => $local_id,
            'mes' => $mes,
            'year' => $year,
            'dia_min' => $dia_min,
            'dia_max' => $dia_max
        ));

        $data['producto'] = $this->db->get_where('producto', array(
            'producto_id' => $producto_id
        ))->row();

        $data['local'] = $this->db->get_where('local', array('int_local_id' => $local_id))->row();
        $data['unidad'] = $this->unidades_model->get_um_min_by_producto($producto_id);
        $data['year'] = $year;
        $data['mes'] = $mes;

        $this->load->view('menu/kardex/kardex_lista_excel', $data);
    }


}