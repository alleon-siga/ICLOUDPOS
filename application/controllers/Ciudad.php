<?php if (!defined('BASEPATH')) {
 exit('No direct script access allowed');
}

class ciudad extends MY_Controller
{

 public function __construct()
 {
  parent::__construct();
  if ($this->login_model->verify_session()) {
   $this->load->model('ciudad/ciudad_model');
   $this->load->model('estado/estado_model');
   $this->load->model('pais/pais_model');
  } else {
   redirect(base_url(), 'refresh');
  }
 }

 public function index()
 {
  //$data="";

  if ($this->session->flashdata('success') != false) {
   $data['success'] = $this->session->flashdata('success');
  }
  if ($this->session->flashdata('error') != false) {
   $data['error'] = $this->session->flashdata('error');
  }

  $data["ciudades"] = $this->ciudad_model->get_all();
  $dataCuerpo['cuerpo'] = $this->load->view('menu/ciudad/ciudad', $data, true);

  if ($this->input->is_ajax_request()) {
   echo $dataCuerpo['cuerpo'];
  } else {
   $this->load->view('menu/template', $dataCuerpo);
  }
 }

 public function form($id = false)
 {

  $data = array();
  $data['ciudad'] = array();
  $data['estados'] = $this->estado_model->get_all();
  $data['paises'] = $this->pais_model->get_all();
  if ($id != false) {
   $data['ciudad'] = $this->ciudad_model->get_by('ciudad_id', $id);
   $data['sestado'] = $this->estado_model->get_by('estados_id', $data['ciudad']['estado_id']);
   $data['spais'] = $this->pais_model->get_by('id_pais', $data['sestado']['pais_id']);
  }
  $this->load->view('menu/ciudad/form', $data);
 }

 public function guardar()
 {

  $id = $this->input->post('id');

  $ciudad = array(

   'estado_id' => $this->input->post('estado_id'),
   'ciudad_nombre' => $this->input->post('ciudad_nombre'),
  );

  if (empty($id)) {
   $resultado = $this->ciudad_model->insertar($ciudad);

  } else {
   $ciudad['ciudad_id'] = $id;
   $resultado = $this->ciudad_model->update($ciudad);
  }

  if ($resultado == true) {
   $json['success'] = 'Solicitud Procesada con exito';
  } else {
   $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
  }

  echo json_encode($json);

 }

 public function eliminar()
 {
  $id = $this->input->post('id');
  $nombre = $this->input->post('nombre');

  $metodospago = array(
   'id_metodo' => $id,
   'nombre_metodo' => $nombre . time(),
   'status_metodo' => 0,

  );

  $data['resultado'] = $this->metodos_pago_model->update($metodospago);

  if ($data['resultado'] != false) {

   $this->session->set_flashdata('success', 'Se ha eliminado exitosamente');

  } else {

   $this->session->set_flashdata('error', 'Ha ocurrido un error al eliminar el M&eacute;todos');
  }

  redirect('metodosdepago');
 }

 public function get_by_estado()
 {
  if ($this->input->is_ajax_request()) {
   $estado_id = $this->input->post('estado_id');

   $ciudades = $this->ciudad_model->get_all_by('estado_id', $estado_id);
   header('Content-Type: application/json');

   echo json_encode($ciudades);
  } else {
   redirect(base_url . 'principal');
  }
 }

}
