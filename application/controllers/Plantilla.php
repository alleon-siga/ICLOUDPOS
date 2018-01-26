<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class plantilla extends MY_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->model('plantilla/plantilla_model');
	}

	public function index()
	{

		$data['productos_header'] = $this->plantilla_model->get_header();
		$data['productos'] = $this->plantilla_model->get_plantillas();
		
		$dataCuerpo['cuerpo'] = $this->load->view('menu/plantilla/plantilla', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
	}

	function form($id = FALSE)
    {
        $data['productos_header'] = $this->plantilla_model->get_header();

        if ($id != FALSE) {
            $data['producto'] = $this->plantilla_model->get_plantillas($id);
        }

        $this->load->view('menu/plantilla/form', $data);
    }


	public function save()
	{
		$id = $this->input->post('id');
		$producto = array(
			'nombre'=>$this->input->post('nombre'),
			'descripcion'=> $this->input->post('descripcion')
			);

		if($id != "")
			$producto['id'] = $id;

		$propiedades = json_decode($this->input->post('propiedades'));

		$result = $this->plantilla_model->save($producto, $propiedades);

		if($result != FALSE){
			$data['success'] = 1;
			$data['plantilla_id'] = $result;
		}

		echo json_encode($data);
	}

}
