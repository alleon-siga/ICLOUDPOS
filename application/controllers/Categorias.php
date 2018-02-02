<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categorias extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
        if ($this->login_model->verify_session()) {
			$this->load->model('categoria/categoria_model');
        }else{
            redirect(base_url(), 'refresh');
        }
	}

	function index()
	{
		if ($this->session->flashdata('success') != FALSE) {
				$data ['success'] = $this->session->flashdata('success');
		}
		if ($this->session->flashdata('error') != FALSE) {
				$data ['error'] = $this->session->flashdata('error');
		}

		$data["categoria"] = $this->categoria_model->get_all();
		$dataCuerpo['cuerpo'] = $this->load->view('menu/categoria/categorias', $data, true);
		if ($this->input->is_ajax_request()) {
				echo $dataCuerpo['cuerpo'];
		} else {
				$this->load->view('menu/template', $dataCuerpo);
		}
	}

	function form($id = FALSE)
	{
		//$data['categoria'] = $this->categoria_model->get_all();
		$data['tipo'] = $this->categoria_model->get_all_tipo();
		if ($id !=false) {
			$data['categoria'] = $this->categoria_model->get_by('id',$id);
		}
		$this->load->view('menu/categoria/form',$data);
	}

	function guardar()
	{
			$id = $this->input->post('id');
			$categoria = array(
					//'id' => $this->input->post('id'),
					'pl_tipo_id' => $this->input->post('tipo_id'),
					'valor' => $this->input->post('valor'),
					'estado' => 1
			);

			if (empty($id)) {
					$resultado = $this->categoria_model->insertar($categoria);

			} else {
					$categoria['id'] = $id;
					$resultado = $this->categoria_model->update($categoria);
			}

			if ($resultado == TRUE) {
					$json['success'] = 'Solicitud Procesada con exito';
			} else {
					$json['error'] = 'Ha ocurrido un error al procesar la Solicitud';
			}
			echo json_encode($json);
	}

	function eliminar()
	{
			$id = $this->input->post('id');
			$categoria = array(
					'id' => $id,
					'estado' => 0
			);
			$data['resultado'] = $this->categoria_model->update($categoria);

			if ($data['resultado'] != FALSE)
			{
					$json['success'] = 'Se ha eliminado exitosamente';
			} else
			{
					$json['error'] = 'Ha ocurrido un error al eliminar el Banco';
			}
			echo json_encode($json);
	}
}
