<?php

class Reporte extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        if ($this->login_model->verify_session()) {
            $this->load->model('local/local_model');
            $this->load->model('producto/producto_model');
        }else{
            redirect(base_url(), 'refresh');
        }
    }

    function productoVendido()
    {
        $data['locales'] = $this->local_model->get_local_by_user($this->session->userdata('nUsuCodigo'));
        $data['monedas'] = $this->db->get_where('moneda', array('status_moneda' => 1))->result();
        $data['marcas'] = $this->db->get_where('marcas', array('estatus_marca' => 1))->result();
        $data['grupos'] = $this->db->get_where('grupos', array('estatus_grupo' => 1))->result();
        $data['familias'] = $this->db->get_where('familia', array('estatus_familia' => 1))->result();
        $data['lineas'] = $this->db->get_where('lineas', array('estatus_linea' => 1))->result();
        $data["productos"] = $this->producto_model->get_productos_list();
        $data['barra_activa'] = $this->db->get_where('columnas', array('id_columna' => 36))->row();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/productoVendido', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }
}