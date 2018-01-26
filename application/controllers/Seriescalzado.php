<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class seriescalzado extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('seriescalzado/seriescalzado_model');
	}

	public function index()
	{
		$data['series'] = $this->db->get_where('pl_serie', array('estado'=>'1'))->result();
		
		$dataCuerpo['cuerpo'] = $this->load->view('menu/seriescalzado/seriescalzado', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
	}

	function form($id = FALSE)
    {
    	$data = array();
    	
        if ($id != FALSE) {
            $data['serie'] = $this->db->get_where('pl_serie', array('serie'=>$id))->row();
        }

        $this->load->view('menu/seriescalzado/form', $data);
    }


	public function save()
	{
		$producto = array(
			'serie'=>$this->input->post('serie'),
			'rango'=> $this->input->post('rango')
			);

		$this->db->where('serie', $producto['serie']);
		$this->db->delete('pl_serie');

		$this->db->insert('pl_serie', $producto);
		$result = $producto['serie'];

		if($result != FALSE){
			$data['success'] = 1;
			$data['plantilla_id'] = $result;
		}

		echo json_encode($data);
	}

}

/* End of file Series_calzado */
/* Location: ./application/controllers/Series_calzado */